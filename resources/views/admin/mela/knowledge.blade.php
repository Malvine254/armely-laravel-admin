@extends('admin.layouts.admin')

@section('title', 'Mela Knowledge')
@section('page-title', 'Mela Knowledge')

@push('styles')
<style>
    .mela-knowledge-page {
        max-width: 1180px;
        margin: 0 auto;
    }

    .mela-knowledge-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .mela-knowledge-header h2 {
        margin: 0 0 0.25rem;
        color: #172b4d;
        font-size: 1.45rem;
        font-weight: 700;
    }

    .mela-knowledge-header p,
    .mela-index-note {
        margin: 0;
        color: #667085;
        font-size: 0.9rem;
    }

    .mela-index-state {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.7rem;
        border: 1px solid #d0d5dd;
        border-radius: 999px;
        background: #fff;
        color: #344054;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .mela-index-state::before {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: #98a2b3;
        content: '';
    }

    .mela-index-state[data-state="running"]::before { background: #d97706; }
    .mela-index-state[data-state="completed"]::before { background: #16845b; }
    .mela-index-state[data-state="failed"]::before { background: #c43232; }

    .mela-index-panel {
        border: 1px solid #e4e7ec;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(16, 24, 40, 0.04);
    }

    .mela-index-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #eaecf0;
    }

    .mela-index-panel-header h3 {
        margin: 0;
        color: #172b4d;
        font-size: 1rem;
        font-weight: 700;
    }

    .mela-index-panel-body { padding: 1.15rem; }

    .mela-index-progress-labels {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.5rem;
        color: #475467;
        font-size: 0.88rem;
    }

    .mela-index-progress {
        height: 0.72rem;
        overflow: hidden;
        border-radius: 999px;
        background: #e9eef2;
    }

    .mela-index-progress .progress-bar {
        background: #16845b;
        transition: width 0.35s ease;
    }

    .mela-index-current-url {
        display: block;
        overflow-wrap: anywhere;
        margin-top: 0.75rem;
        color: #667085;
        font-size: 0.82rem;
    }

    .mela-index-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .mela-index-stat {
        min-width: 0;
        padding: 0.85rem;
        border: 1px solid #eaecf0;
        border-radius: 8px;
        background: #fcfcfd;
    }

    .mela-index-stat span {
        display: block;
        color: #667085;
        font-size: 0.78rem;
    }

    .mela-index-stat strong {
        display: block;
        margin-top: 0.2rem;
        color: #172b4d;
        font-size: 1.2rem;
        font-variant-numeric: tabular-nums;
    }

    .mela-index-controls {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 0.75fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .mela-schedule-form {
        display: flex;
        align-items: end;
        gap: 0.65rem;
    }

    .mela-schedule-form .form-control { min-width: 0; }

    @media (max-width: 767.98px) {
        .mela-knowledge-header { flex-direction: column; }
        .mela-index-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .mela-index-controls { grid-template-columns: 1fr; }
        .mela-index-panel-header { align-items: flex-start; flex-direction: column; }
        .mela-schedule-form { align-items: stretch; flex-direction: column; }
    }
</style>
@endpush

@section('content')
<main
    class="container-fluid py-4 mela-knowledge-page"
    id="melaKnowledgePage"
    data-status-url="{{ route('admin.mela.knowledge.status') }}"
    data-start-url="{{ route('admin.mela.knowledge.reindex') }}"
    data-schedule-url="{{ route('admin.mela.knowledge.schedule') }}"
    data-cancel-url="{{ route('admin.mela.knowledge.schedule.cancel') }}"
>
    <header class="mela-knowledge-header">
        <div>
            <h2>Knowledge Index</h2>
            <p>Monitor and control the content Mela uses to answer questions.</p>
        </div>
        <span class="mela-index-state" id="indexState" data-state="idle" role="status">Not running</span>
    </header>

    <div class="alert d-none" id="indexNotice" role="status" aria-live="polite"></div>

    <section class="mela-index-panel" aria-labelledby="indexProgressTitle">
        <div class="mela-index-panel-header">
            <div>
                <h3 id="indexProgressTitle">Index progress</h3>
                <p class="mela-index-note" id="indexLastRun">No indexing run recorded yet.</p>
            </div>
            <button class="btn btn-success" id="startIndexButton" type="button">
                <i class="fas fa-play me-2" aria-hidden="true"></i>Index now
            </button>
        </div>
        <div class="mela-index-panel-body">
            <div class="mela-index-progress-labels">
                <span id="indexPhase">Waiting for status</span>
                <strong id="indexPercent">0%</strong>
            </div>
            <div class="progress mela-index-progress" role="progressbar" aria-label="Knowledge indexing progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="indexProgressBar">
                <div class="progress-bar" id="indexProgressFill" style="width: 0%"></div>
            </div>
            <div class="d-flex justify-content-between flex-wrap gap-2 mt-2">
                <span class="mela-index-note" id="indexProgressCount">0 of 0</span>
                <span class="mela-index-note" id="indexPageTotals">0 active pages · 0 chunks</span>
            </div>
            <span class="mela-index-current-url" id="indexCurrentUrl"></span>

            <div class="mela-index-stats" aria-label="Indexing totals">
                <div class="mela-index-stat"><span>Discovered</span><strong id="statDiscovered">0</strong></div>
                <div class="mela-index-stat"><span>Indexed</span><strong id="statIndexed">0</strong></div>
                <div class="mela-index-stat"><span>Unchanged</span><strong id="statUnchanged">0</strong></div>
                <div class="mela-index-stat"><span>Failed</span><strong id="statFailed">0</strong></div>
            </div>
        </div>
    </section>

    <div class="mela-index-controls">
        <section class="mela-index-panel" aria-labelledby="scheduleIndexTitle">
            <div class="mela-index-panel-header">
                <h3 id="scheduleIndexTitle">Schedule one run</h3>
            </div>
            <div class="mela-index-panel-body">
                <form class="mela-schedule-form" id="scheduleIndexForm">
                    @csrf
                    <div class="flex-grow-1">
                        <label class="form-label" for="scheduledAt">Run at</label>
                        <input class="form-control" id="scheduledAt" name="scheduled_at" type="datetime-local" required>
                    </div>
                    <button class="btn btn-outline-success" type="submit">
                        <i class="far fa-clock me-2" aria-hidden="true"></i>Schedule
                    </button>
                </form>
                <div class="d-none mt-3" id="scheduledRunInfo">
                    <span class="text-success fw-semibold" id="scheduledRunText"></span>
                    <button class="btn btn-sm btn-outline-danger ms-2" id="cancelScheduledRun" type="button" aria-label="Cancel scheduled index">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <p class="mela-index-note mt-3">One-time runs start through the Laravel scheduler, which must run every minute on the server.</p>
            </div>
        </section>

        <section class="mela-index-panel" aria-labelledby="indexInventoryTitle">
            <div class="mela-index-panel-header">
                <h3 id="indexInventoryTitle">Indexed content</h3>
            </div>
            <div class="mela-index-panel-body">
                <div id="pageTypeBreakdown" class="d-flex flex-wrap gap-2" aria-live="polite"></div>
                <p class="mela-index-note mt-3">The index refreshes automatically after published content changes.</p>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('melaKnowledgePage');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const notice = document.getElementById('indexNotice');
    const startButton = document.getElementById('startIndexButton');
    const scheduleForm = document.getElementById('scheduleIndexForm');
    const scheduleInput = document.getElementById('scheduledAt');
    const cancelButton = document.getElementById('cancelScheduledRun');

    const showNotice = (message, kind = 'success') => {
        notice.textContent = message;
        notice.className = `alert alert-${kind}`;
    };

    const postJson = async (url, method, payload = null) => {
        const response = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: payload ? JSON.stringify(payload) : null,
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'The request could not be completed.');
        return data;
    };

    const formatDate = value => value ? new Date(value).toLocaleString() : 'Not run yet';
    const phaseLabels = {
        idle: 'Waiting to start',
        discovering: 'Discovering site pages',
        fetching: 'Fetching page content',
        indexing: 'Indexing pages',
        cleaning: 'Cleaning up removed pages',
        complete: 'Indexing complete',
        failed: 'Indexing failed',
    };

    function renderStatus(data) {
        const progress = data.progress || {};
        const running = Boolean(data.reindex_running || progress.status === 'running');
        const state = running ? 'running' : (progress.status || 'idle');
        const stateLabels = { idle: 'Not running', running: 'Indexing', completed: 'Completed', failed: 'Failed' };
        const phase = progress.phase || 'idle';
        const total = Number(progress.total || 0);
        const processed = Number(progress.processed || 0);
        const percent = progress.status === 'completed' ? 100 : (total > 0 ? Math.min(100, Math.floor(processed * 100 / total)) : 0);

        const stateElement = document.getElementById('indexState');
        stateElement.dataset.state = state;
        stateElement.textContent = stateLabels[state] || 'Not running';
        document.getElementById('indexPhase').textContent = phaseLabels[phase] || phase;
        document.getElementById('indexPercent').textContent = `${percent}%`;
        document.getElementById('indexProgressFill').style.width = `${percent}%`;
        document.getElementById('indexProgressBar').setAttribute('aria-valuenow', String(percent));
        document.getElementById('indexProgressCount').textContent = `${processed} of ${total} ${phase === 'fetching' ? 'URLs' : 'pages'}`;
        document.getElementById('indexPageTotals').textContent = `${data.active_pages || 0} active pages · ${data.chunks || 0} chunks`;
        document.getElementById('indexCurrentUrl').textContent = progress.current_url || '';
        document.getElementById('indexLastRun').textContent = progress.started_at
            ? `${progress.status === 'running' ? 'Started' : 'Last run'} ${formatDate(progress.started_at)}`
            : (data.last_indexed_at ? `Last indexed ${formatDate(data.last_indexed_at)}` : 'No indexing run recorded yet.');
        document.getElementById('statDiscovered').textContent = progress.discovered || 0;
        document.getElementById('statIndexed').textContent = progress.indexed || 0;
        document.getElementById('statUnchanged').textContent = progress.unchanged || 0;
        document.getElementById('statFailed').textContent = progress.failed || 0;
        startButton.disabled = running;

        if (progress.error) showNotice(progress.error, 'danger');

        const scheduledInfo = document.getElementById('scheduledRunInfo');
        const scheduledText = document.getElementById('scheduledRunText');
        if (data.scheduled_at) {
            scheduledInfo.classList.remove('d-none');
            scheduledText.textContent = `Scheduled for ${formatDate(data.scheduled_at)}`;
        } else {
            scheduledInfo.classList.add('d-none');
            scheduledText.textContent = '';
        }

        const breakdown = document.getElementById('pageTypeBreakdown');
        breakdown.replaceChildren();
        Object.entries(data.pages_by_type || {}).forEach(([type, count]) => {
            const badge = document.createElement('span');
            badge.className = 'badge text-bg-light border';
            badge.textContent = `${type.replaceAll('_', ' ')}: ${count}`;
            breakdown.append(badge);
        });
    }

    async function refreshStatus() {
        let running = false;
        try {
            const response = await fetch(root.dataset.statusUrl, {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
            });
            if (!response.ok) throw new Error('Could not load index status.');
            const data = await response.json();
            renderStatus(data);
            running = Boolean(data.reindex_running);
        } catch (error) {
            showNotice(error.message, 'danger');
        }
        window.setTimeout(refreshStatus, running ? 2000 : 15000);
    }

    startButton.addEventListener('click', async () => {
        startButton.disabled = true;
        try {
            const data = await postJson(root.dataset.startUrl, 'POST');
            showNotice(data.message || 'Indexing started.');
            refreshStatus();
        } catch (error) {
            showNotice(error.message, 'danger');
            startButton.disabled = false;
        }
    });

    scheduleForm.addEventListener('submit', async event => {
        event.preventDefault();
        if (!scheduleInput.value) return;
        try {
            const scheduledAt = new Date(scheduleInput.value);
            const data = await postJson(root.dataset.scheduleUrl, 'POST', { scheduled_at: scheduledAt.toISOString() });
            showNotice(`Index scheduled for ${formatDate(data.scheduled_at)}.`);
            scheduleForm.reset();
            refreshStatus();
        } catch (error) {
            showNotice(error.message, 'danger');
        }
    });

    cancelButton.addEventListener('click', async () => {
        try {
            await postJson(root.dataset.cancelUrl, 'DELETE');
            showNotice('Scheduled index cancelled.');
            refreshStatus();
        } catch (error) {
            showNotice(error.message, 'danger');
        }
    });

    const minimumTime = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    scheduleInput.min = minimumTime;
    renderStatus(@json($initialStatus));
    refreshStatus();
});
</script>
@endsection