<div class="tab-pane fade" id="sessions-pane" role="tabpanel" aria-labelledby="sessions-tab">
    <div class="mela-index-panel p-3">
        <div class="d-flex justify-content-between flex-wrap gap-3 mb-3">
            <div><h3 class="h5">Visitor sessions</h3><p class="mela-index-note">Active means activity within the last 5 minutes. Updates every 15 seconds.</p></div>
            <div><label for="sessionFilter" class="form-label">Show</label><select id="sessionFilter" class="form-select"><option value="active">Active sessions</option><option value="all">All sessions</option></select></div>
        </div>
        <div id="sessionCounts" class="d-flex gap-3 flex-wrap mb-3" aria-live="polite"></div>
        <div id="sessionList"></div>
    </div>
</div>
<div class="tab-pane fade" id="escalations-pane" role="tabpanel" aria-labelledby="escalations-tab">
    <div class="mela-index-panel p-3"><h3 class="h5">Human follow-ups</h3><p class="mela-index-note mb-3">Review submitted requests, details still being collected, and failed notifications.</p><div id="escalationList"></div></div>
</div>
<div class="modal fade" id="melaSessionModal" tabindex="-1" aria-labelledby="melaSessionTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><h3 class="modal-title fs-5" id="melaSessionTitle">Session details</h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body" id="sessionDetail" style="white-space: pre-wrap; overflow-wrap: anywhere"></div>
    </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const endpoint = @json(route('admin.mela.sessions.index'));
    const pages = {sessionList: 1, escalationList: 1};
    let busy = false;
    const element = (tag, text, className = '') => {
        const node = document.createElement(tag); node.textContent = text; node.className = className; return node;
    };
    const label = value => (value || 'Unknown').replaceAll('_', ' ');
    const date = value => value ? new Date(value).toLocaleString() : 'Unknown';
    async function get(url) {
        const response = await fetch(url, {headers: {Accept: 'application/json'}, cache: 'no-store'});
        if (!response.ok) throw new Error('Unable to load sessions. Check your connection and admin login.');
        return response.json();
    }
    async function details(id) {
        const body = document.getElementById('sessionDetail');
        body.textContent = 'Loading session…';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('melaSessionModal')).show();
        try {
            const data = await get(`${endpoint}/${encodeURIComponent(id)}`);
            body.replaceChildren(element('p', `Session: ${data.id}\nLanding page: ${data.landing_page || 'Unknown'}`));
            body.append(element('h4', 'Follow-up history', 'h6'));
            const escalation = data.escalation || {};
            body.append(element('p', `Status: ${label(escalation.status)}${escalation.last_error ? '\nError: ' + label(escalation.last_error) : ''}`));
            const requests = [...(escalation.requests || []), ...(escalation.pending ? [escalation.pending] : [])];
            requests.forEach(item => body.append(element('p', [item.reference, label(item.request_type), item.topic, item.submitted_at ? date(item.submitted_at) : 'Pending', item.lead_id ? `Lead #${item.lead_id}` : '', item.notification_sent ? 'Notification delivered' : 'Notification not confirmed'].filter(Boolean).join(' · '), 'border rounded p-3')));
            body.append(element('h4', 'Conversation', 'h6'));
            data.messages.forEach(message => {
                const block = element('div', '', 'border rounded p-3 mb-2');
                block.append(element('strong', `${message.role === 'user' ? 'Visitor' : 'Mela'} · ${date(message.created_at)}`), element('p', message.content, 'mb-0 mt-2'));
                body.append(block);
            });
        } catch (error) { body.textContent = error.message; }
    }
    async function load(target, filter) {
        const container = document.getElementById(target);
        const data = await get(`${endpoint}?filter=${filter}&page=${pages[target]}`);
        if (pages[target] > data.sessions.last_page) { pages[target] = data.sessions.last_page; return load(target, filter); }
        container.replaceChildren();
        const counts = document.getElementById('sessionCounts'); counts.replaceChildren();
        Object.entries(data.counts).forEach(([name, count]) => counts.append(element('span', `${label(name)}: ${count}`, 'badge text-bg-light border')));
        if (!data.sessions.data.length) { container.append(element('p', 'No sessions match this view.', 'text-muted')); return; }
        const wrapper = element('div', '', 'table-responsive'); const table = element('table', '', 'table table-hover align-middle');
        const head = document.createElement('thead'); const headings = document.createElement('tr');
        ['Visitor / shared details', 'Country', 'Activity', 'Topic', 'Follow-up', 'Last activity', ''].forEach(text => headings.append(element('th', text)));
        head.append(headings); table.append(head); const rows = document.createElement('tbody');
        data.sessions.data.forEach(session => {
            const row = document.createElement('tr'); const visitor = session.visitor || {}; const contact = element('td', '');
            contact.append(element('strong', visitor.name || 'Anonymous visitor'));
            [visitor.email, visitor.phone, visitor.company].filter(Boolean).forEach(value => contact.append(element('div', value, 'small text-muted')));
            contact.append(element('div', `Session ${session.id.slice(0, 8)} · ${session.user_message_count} messages`, 'small text-muted'));
            row.append(contact, element('td', session.country_code || 'Unknown'), element('td', session.active ? 'Active' : 'Inactive'), element('td', session.topic || '—'), element('td', label(session.escalation_status)), element('td', date(session.last_activity_at)));
            const action = element('td', ''); const button = element('button', 'View', 'btn btn-sm btn-outline-primary'); button.type = 'button'; button.addEventListener('click', () => details(session.id)); action.append(button); row.append(action); rows.append(row);
        });
        table.append(rows); wrapper.append(table); container.append(wrapper);
        const pager = element('div', '', 'd-flex align-items-center gap-3');
        [-1, 1].forEach(direction => { const button = element('button', direction < 0 ? 'Previous' : 'Next', 'btn btn-sm btn-outline-secondary'); button.type = 'button'; button.disabled = direction < 0 ? pages[target] <= 1 : pages[target] >= data.sessions.last_page; button.addEventListener('click', () => { pages[target] += direction; refresh(); }); pager.append(button); });
        pager.append(element('span', `Page ${data.sessions.current_page} of ${data.sessions.last_page} · ${data.sessions.total} sessions`, 'small text-muted')); container.append(pager);
    }
    async function refresh() {
        if (busy || document.hidden) return;
        const escalations = document.getElementById('escalations-pane').classList.contains('active');
        const sessions = document.getElementById('sessions-pane').classList.contains('active');
        if (!escalations && !sessions) return;
        busy = true;
        const target = escalations ? 'escalationList' : 'sessionList';
        try { await load(target, escalations ? 'escalated' : document.getElementById('sessionFilter').value); }
        catch (error) { document.getElementById(target).replaceChildren(element('p', error.message, 'alert alert-danger')); }
        finally { busy = false; }
    }
    document.getElementById('sessionFilter').addEventListener('change', () => { pages.sessionList = 1; refresh(); });
    document.querySelectorAll('#melaTabs button').forEach(button => button.addEventListener('shown.bs.tab', refresh));
    setInterval(refresh, 15000);
});
</script>
