# Mela AI — Armely Website Assistant

Mela AI is the AI assistant on armely.com. It understands natural-language questions and business problems. It answers from indexed Armely website content, remembers the conversation, and escalates to the Armely team through the existing Microsoft Graph email workflow.

## Architecture

```
Browser (public/js/mela-chat.js)
  └─ POST /api/mela/conversations/{id}/messages   (token in X-Mela-Token, rate limited)
       └─ MelaChatController        validation, sanitising, config check
            └─ MelaAgent            agent loop (per-conversation lock)
                 ├─ ConversationStore   messages, recent window
                 ├─ MemoryManager       structured memory (visitor / conversation / lead / escalation)
                 ├─ ContextBuilder      system prompt + memory + summary + recent turns + message
                 ├─ AzureOpenAiClient   chat + embeddings, retries/backoff, param fallback
                 ├─ ToolRegistry        validated tool execution
                 │    ├─ search_armely_knowledge  ─┐
                 │    ├─ find_relevant_services   ─┴─ KnowledgeRetriever (semantic search)
                 │    ├─ save_visitor_details
                 │    └─ request_human_follow_up ── EscalationService ── GraphEscalationNotifier
                 ├─ OutputGuard         blocks secret / prompt disclosure
                 └─ MemoryUpdater       (after the response) JSON memory + rolling summary
```

Code lives in `app/Services/Mela/`. Configuration is centralised in `config/mela.php`, and the system prompt is in `resources/mela/system-prompt.md`.

## Request flow

1. The widget greets the visitor, rendered from `config('mela.greeting')`. A conversation is created only when the visitor first sends a message (`POST /api/mela/conversations`). The response includes a random token. Only its SHA-256 hash is stored, and every later request must present the token, which isolates sessions.
2. For each message, the controller sanitises the input (strips HTML and control characters, enforces the length limit). The agent takes a per-conversation lock and stores the user message.
3. `ContextBuilder` assembles: the system prompt, then the structured memory JSON, conversation summary and current page URL (all labelled as data), then the last N messages, then the new message.
4. The model decides whether to call tools. The loop can run for up to `MELA_MAX_TOOL_ITERATIONS` iterations; the last one forces a text answer. Each tool validates its arguments with Laravel's validator, and tool results go back to the model as JSON.
5. The reply is checked by `OutputGuard`. It is then stored with telemetry: tools used, source URLs, latency, model time, retrieval time and token counts.
6. After the response is sent, `MemoryUpdater` makes one JSON-mode call. That call updates the memory and, when the conversation is long, folds older messages into the rolling summary.

No conversational behaviour is keyword-driven. Application code only validates and executes actions.

## Memory

- **Recent window:** the last `MELA_MEMORY_WINDOW` (default 10) unsummarised messages are sent verbatim.
- **Structured memory:** stored in `mela_conversations.memory`:
  - `visitor`: name, email, company, phone. Each is stored with `source: visitor`.
  - `conversation`: goal, topic, business problem, technologies, services and products discussed, open questions.
  - `lead` flags.
  - `escalation` state and request history.
- **Rolling summary:** once there are more than `MELA_SUMMARY_THRESHOLD` unsummarised messages, older ones are folded into `mela_conversations.summary`.
- **Visitor details are verified:** a name, email, company or phone number is stored only if it literally appears in the visitor's own messages. Model inferences never overwrite details the visitor provided.
- **Retention:** conversations inactive for longer than `MELA_CONVERSATION_TTL_DAYS` (30) are deleted daily by `mela:prune`.

## Knowledge ingestion and retrieval

`php artisan mela:index` performs the following steps:

1. Discovers URLs from `{MELA_KNOWLEDGE_BASE_URL}/sitemap.xml`, recursing into sitemap indexes, plus the seed paths in `config/mela.php`. Only hosts in `MELA_KNOWLEDGE_ALLOWED_HOSTS` are accepted, and excluded patterns (admin, store, login, files) are skipped.
2. Fetches pages concurrently, honours canonical URLs, and deactivates pages that return 404/410.
3. Extracts content from `<main>`. It removes nav, header, footer, scripts, forms, cookie banners and modals, and keeps headings and lists.
4. Splits the content into sections at h1–h3 headings, then packs the sections into chunks of about 1,100 characters. Small neighbouring sections are merged; long ones are split by line and sentence.
5. Treats sections repeated on 3 or more pages (shared CTAs and banners) as boilerplate and indexes them only on the home and contact pages.
6. Skips unchanged pages using a per-page content hash, so only changed pages are re-embedded. `--force` re-embeds everything.
7. Embeds each chunk with its page title, available blog author, and section path (`text-embedding-3-large`, 1,024 dimensions). The vectors are unit-normalised and stored as packed float32 in `mela_knowledge_chunks.embedding`. Each chunk's metadata stores the title, author when present, URL, page type, section, heading, service or product, and last-updated date.

Retrieval (`KnowledgeRetriever`) embeds the query and caches the embedding by hash. It then:

1. Scores every chunk on the first 256 dimensions.
2. Re-ranks the top 120 at full precision.
3. Applies page-type weights, plus a soft boost for preferred page types.
4. Keeps at most 2 chunks per page.
5. Reports a confidence level (`high`, `low` or `none`).

If embeddings are unavailable, retrieval falls back to lexical search with `low` confidence.

The index refreshes daily at 03:15 (`MELA_REINDEX_SCHEDULE=daily|weekly|off`). An admin can trigger a refresh with `POST /admin/mela/knowledge/reindex` and check status with `GET /admin/mela/knowledge`. Both require an admin session.

Successful admin changes to published website content automatically request an incremental reindex after the response is sent. If an index is already running, one follow-up pass is scheduled so concurrent content changes are picked up. cPanel Git deployments also run `php artisan mela:index` through `.cpanel.yml`.

Admins can monitor progress, start an index, or schedule a one-time run at `/admin/mela/knowledge/manage`. One-time schedules are checked every minute, so the production server must run `php artisan schedule:run` every minute.

## Tools

| Tool | Purpose |
|------|---------|
| `search_armely_knowledge` | Semantic search over approved Armely content, with optional page-type preference. |
| `find_relevant_services` | Maps a plain-language business challenge to relevant services, solutions and products, with evidence. |
| `save_visitor_details` | Remembers contact details the visitor typed. |
| `request_human_follow_up` | Human escalation: collects missing details, records the lead, notifies the team, and prevents duplicates. |

## Escalation

The escalation state machine moves through `not_requested → collecting_information → submitting → submitted`. If notification fails it moves to `failed`, and a retry is allowed.

- **Required details:** name and email (`config('mela.escalation.required_fields')`). The tool reports which ones are missing, so the model asks only for those.
- **Lead record:** a row is written to the existing `consultation` table, with `service_type` set to `Mela AI Website Assistant: <topic>`.
- **Team notification:** sent via `AzureMailService` (Graph) to `NewsletterNotificationService::adminRecipientEmails()`, the same recipient list the contact forms use. The template is `emails/mela/assistant-escalation.blade.php`. It contains:
  - the visitor's details
  - the topic
  - the business need and requested action
  - the conversation summary, technologies and services discussed
  - the last 14 messages
  - the conversation ID, reference and timestamp
- **Visitor confirmation:** sent when `MELA_SEND_VISITOR_CONFIRMATION` is true.
- **Duplicate prevention:** a cache lock is held per conversation. A follow-up request that matches one already submitted returns `already_submitted`, and a new request needs the model to set `is_new_request` plus a clearly different topic. The limit is 3 per conversation. Contact details shared after submission are sent to the team as an update email.
- **No false confirmations:** the model is told the outcome and must not claim success unless `status` is `submitted`.

## Security

- **Secrets:** Azure OpenAI credentials are used only on the server and never reach the browser, responses or logs. `MelaLogger` redacts the name, email, phone, message, content, api_key and token fields.
- **Untrusted content:** website content, tool output and memory are labelled as data. Injection-like messages are flagged for extra caution. `OutputGuard` blocks replies that contain configured secrets or verbatim lines from the system prompt.
- **Session isolation:** conversation IDs are UUIDs protected by bearer tokens. A wrong token returns a 404.
- **Rate limits** (`MelaServiceProvider`):
  - `MELA_RATE_PER_MINUTE` (12) and `MELA_RATE_PER_DAY` (200) messages per IP
  - `MELA_CONVERSATIONS_PER_HOUR` (20) new conversations per IP
  - 80 messages per conversation
- **Safe rendering:** the widget escapes model output before applying minimal Markdown (bold, lists, and http(s) or site-relative links).

## Environment variables

Required (server-side only):

```
AZURE_OPENAI_ENDPOINT              # classic https://<res>.openai.azure.com or Foundry https://<res>.services.ai.azure.com/openai/v1
AZURE_OPENAI_API_KEY
AZURE_OPENAI_DEPLOYMENT            # chat deployment, e.g. gpt-4.1
AZURE_OPENAI_API_VERSION           # used by classic endpoints only
AZURE_OPENAI_EMBEDDING_DEPLOYMENT  # e.g. text-embedding-3-large
AZURE_TENANT_ID / AZURE_CLIENT_ID / AZURE_CLIENT_SECRET / MAIL_FROM_ADDRESS   # existing Graph mail
```

Optional overrides are listed in `.env.example` under `MELA_*`. If required configuration is missing, the chat API returns 503 with a fallback contact message and logs `CONFIGURATION_MISSING`. `php artisan mela:check` lists exactly what is missing.

## Operations

```
php artisan migrate                       # creates mela_* tables
php artisan mela:index                    # build/refresh the knowledge index (incremental)
php artisan mela:index --url=/services/fabric   # reindex specific pages
php artisan mela:check --query="..."      # config, connectivity, index health, sample retrieval
php artisan mela:eval                     # live multi-turn scenarios (emails recorded, not sent)
php artisan mela:stats --days=7           # usage, latency, tokens, escalations, top topics
php artisan mela:prune                    # retention cleanup
```

The scheduler (`php artisan schedule:run` every minute via cron) runs the daily reindex and pruning.

Logs are written to `storage/logs/mela-*.log` as structured events:
`CHAT_STARTED`, `MESSAGE_RECEIVED`, `PROMPT_INJECTION_SUSPECTED`, `MODEL_CALL_STARTED/COMPLETED/FAILED`, `RETRIEVAL_STARTED/COMPLETED/DEGRADED`, `TOOL_SELECTED/EXECUTED/FAILED`, `INTENT_RESOLVED`, `MEMORY_UPDATED`, `ESCALATION_REQUESTED/SUBMITTED/FAILED/UPDATED`, `OUTPUT_BLOCKED`, `RESPONSE_SENT`, `CHAT_ERROR`.

## Testing

- `tests/Feature/MelaAssistantTest.php` uses a faked Azure OpenAI and a recording notifier. It covers:
  - conversation start
  - token isolation
  - the tool loop and sources
  - escalation collect → submit → dedupe
  - escalation failure
  - rejection of details the visitor never typed
  - secret-leak blocking
  - model outage
  - input validation
  - rate limiting
  - missing configuration
  - content extraction
- `php artisan mela:eval` runs the realistic multi-turn scenarios against the live model and index:
  - overview
  - natural-language problems
  - context carry-over
  - Mela product follow-ups
  - unknown pricing
  - escalation with dedupe and a phone update
  - prompt injection
  - cross-session privacy

## Deploying

1. Pull the code.
2. Add the Azure OpenAI variables to the production `.env`.
3. Run `php artisan migrate --force`.
4. Run `php artisan config:clear` (or `config:cache`).
5. Run `php artisan mela:index`. The first run embeds all pages and takes about 20 minutes; later runs are incremental.
6. Run `php artisan mela:check`.
7. Make sure cron runs `php artisan schedule:run` every minute.
