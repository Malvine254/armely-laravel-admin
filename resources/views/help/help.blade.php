 
 @extends('layouts.public')

@section('title', 'Mela Meeting Assistant Help Center')

@section('meta_description', 'Mela Meeting Assistant command guide and help documentation for Microsoft Teams.')

@section('content')

 <style>
        :root {
            --primary: #0052cc;
            --primary-dark: #0747a6;
            --primary-light: #deebff;
            --dark-bg: #091e42;
            --card-bg: #ffffff;
            --light-bg: #f4f5f7;
            --border: #dfe1e6;
            --text-main: #172b4d;
            --text-sub: #5e6c84;
            --code-bg: #ebecf0;
            --accent-green: #36b37e;
        }

       

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-main);
            background-color: var(--light-bg);
            line-height: 1.5;
        }

      


        .svg-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1em;
            width: 1.1em;
            height: 1.1em;
            vertical-align: -0.12em;
        }

        /* Header / Hero */
       .slide-in-header {
          background-color: var(--dark-bg);
          color: #ffffff;
          padding: 80px 0 70px;
          border-bottom: none !important;
      }
      .slide-in-header h1 {
      color: #ffffff !important;
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 15px;
  }
  .slide-in-header p {
    color: rgba(255,255,255,0.9) !important;
    font-size: 1.1rem;
    max-width: 750px;
    line-height: 1.7;
}
        .badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 12px;
        }

        /* Quick Notification Banner */
        .trust-strip {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-sub);
            text-align: center;
        }

        main {
            padding: 30px 0 60px;
        }

        /* Section Layout */
        .card-section {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .card-section h2 {
            font-size: 1.25rem;
            color: var(--text-main);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Command Tables */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background-color: var(--light-bg);
            color: var(--text-main);
            padding: 10px 14px;
            font-weight: 600;
            border-bottom: 2px solid var(--border);
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Code Badges */
        code {
            font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;
            background-color: var(--code-bg);
            color: var(--primary-dark);
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .category-tag {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-sub);
        }

        /* Workflow Steps */
        .flow-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 12px;
        }

        .flow-item {
            background: var(--light-bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 14px;
            font-size: 0.85rem;
        }

        .flow-num {
            display: inline-block;
            background: var(--primary);
            color: #ffffff;
            width: 22px;
            height: 22px;
            line-height: 22px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 0.75rem;
            margin-bottom: 8px;
        }

        .flow-item h5 {
            margin-bottom: 8px;
            color: var(--text-main);
            font-size: 1.05rem;
            font-weight: 650;
        }

        .flow-item p {
            color: var(--text-sub);
        }

        /* FAQ Grid */
        .faq-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .faq-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .faq-q {
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .faq-a {
            font-size: 0.9rem;
            color: var(--text-sub);
        }

      .fa-solid{
        color: #2f5597;
      }
      

        @media (max-width: 768px) {
            .flow-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
 <div class="slide-in-header">
        <div class="container">
            <span class="badge"><i class="fa-solid fa-bolt text-light svg-icon" aria-hidden="true"></i> Quick Reference Manual</span>
            <h1>Mela Meeting Assistant Help Center</h1>
            <p>Direct chat commands and workflows to manage transcription, context recaps, deliverables, and Microsoft Planner action items right from Microsoft Teams.</p>
        </div>
    </div>

    <div class="trust-strip">
        <i class="fa-solid fa-lock svg-icon" aria-hidden="true"></i> Text-Only Processing &bull; No Raw Audio/Video Saved &bull; Microsoft 365 Tenant Isolated
    </div>

    <main class="container">

        <!-- 1. QUICK COMMAND CHEAT SHEET -->
        <section class="card-section">
            <h2><i class="fa-solid fa-keyboard svg-icon" aria-hidden="true"></i> Microsoft Teams In-Chat Commands</h2>
            <p style="font-size: 0.9rem; color: var(--text-sub); margin-bottom: 16px;">
                Type these commands directly into your Microsoft Teams meeting chat window while Mela is active.
            </p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Category</th>
                            <th style="width: 40%;">Command Syntax</th>
                            <th>Action & Output</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="category-tag">Session Control</span></td>
                            <td><code>@mela please join</code></td>
                            <td>Connects Mela to the meeting to begin text capture and note extraction.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Session Control</span></td>
                            <td><code>@mela please leave</code></td>
                            <td>Exits the call, stops note capture, and generates the post-meeting action card.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Privacy Control</span></td>
                            <td><code>@mela pause</code></td>
                            <td>Temporarily halts note capture for private or off-the-record conversation.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Privacy Control</span></td>
                            <td><code>@mela resume</code></td>
                            <td>Restarts transcription when official agenda items continue.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Deliverables</span></td>
                            <td><code>@mela please send executive summary</code></td>
                            <td>Generates and emails the complete executive summary directly and privately to only the person who requested it.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Live Intelligence</span></td>
                            <td><code>@mela catch me up</code></td>
                            <td>Delivers a private 3-bullet recap of everything discussed so far (ideal for late arrivals).</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Live Intelligence</span></td>
                            <td><code>@mela summarize</code></td>
                            <td>Drafts an on-demand executive recap and list of decisions made up to that point.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Context Search</span></td>
                            <td><code>@mela what did [Name] say about [Topic]?</code></td>
                            <td>Searches real-time conversation and answers specific dialogue queries live in chat.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Task Review</span></td>
                            <td><code>@mela what are my tasks</code></td>
                            <td>Pulls and displays all active commitments and action items assigned to you.</td>
                        </tr>
                        <tr>
                            <td><span class="category-tag">Help Desk</span></td>
                            <td><code>@mela help</code></td>
                            <td>Posts the quick-start command guide directly in the meeting chat.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 2. POST-MEETING EXECUTION WORKFLOW -->
        <section class="card-section">
            <h2><i class="fa-solid fa-rocket svg-icon" aria-hidden="true"></i> The Post-Meeting Workflow</h2>
            <p style="font-size: 0.9rem; color: var(--text-sub);">
                What happens when your call wraps up:
            </p>

            <div class="flow-grid">
                <div class="flow-item">
                    <span class="flow-num">1</span>
                    <h5>Call Ends</h5>
                    <p>Mela leaves the call automatically or when requested via <code>@mela please leave</code>.</p>
                </div>
                <div class="flow-item">
                    <span class="flow-num">2</span>
                    <h5>In-Chat Card</h5>
                    <p>An interactive Adaptive Card is posted directly in Teams chat with extracted action items.</p>
                </div>
                <div class="flow-item">
                    <span class="flow-num">3</span>
                    <h5>Review & Sync</h5>
                    <p>Edit task titles, pick due dates, or check "Skip", then click <strong>"Post to Planner"</strong>.</p>
                </div>
                <div class="flow-item">
                    <span class="flow-num">4</span>
                    <h5>Email Recap</h5>
                    <p>An executive summary with decisions and speaker recaps is delivered via email.</p>
                </div>
            </div>
        </section>

        <!-- 3. FAQS & TROUBLESHOOTING -->
        <section class="card-section">
            <h2><i class="fa-solid fa-circle-question svg-icon" aria-hidden="true"></i> Troubleshooting & FAQs</h2>

            <div class="faq-item">
                <div class="faq-q">Who receives the executive summary email?</div>
                <div class="faq-a">Post-meeting summaries are distributed to all attendees, but typing <code>@mela please send executive summary</code> sends an on-demand executive recap strictly and privately to your personal inbox.</div>
            </div>

            <div class="faq-item">
                <div class="faq-q">Why isn't Mela responding when I tag it?</div>
                <div class="faq-a">Ensure you type exactly <code>@mela</code> without extra spaces or suffixes like <em>@mela AI</em>. Mela must also be added as an authorized app in your Microsoft Teams environment.</div>
            </div>

            <div class="faq-item">
                <div class="faq-q">Is my audio or video being recorded?</div>
                <div class="faq-a">No. Mela processes text transcription and does not save or store raw audio or video streams. All session data stays strictly within your Microsoft 365 tenant boundaries.</div>
            </div>

            <div class="faq-item">
                <div class="faq-q">Can anyone dismiss Mela from a meeting?</div>
                <div class="faq-a">Yes, any participant can type <code>@mela please leave</code> or <code>@mela pause</code> if the discussion shifts to confidential or off-the-record matters.</div>
            </div>

            <div class="faq-item">
                <div class="faq-q">How do I deploy Mela across my entire organization?</div>
                <div class="faq-a">Mela can be provisioned via the Microsoft Teams Admin Center by your IT Administrator with standard Microsoft Entra ID consent.</div>
            </div>
        </section>

    </main>

@endsection