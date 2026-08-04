<div class="mega-panel-inner">
    <a class="mega-feature-card" href="{{ route('services') }}">
        <img src="{{ asset('images/blog/advisory_services.png') }}" alt="Consulting team working with data dashboards">
        <div class="mega-feature-card-content">
            <h3>Modernize with Armely</h3>
            <p>Explore Advisory, Data, AI, Cloud, and Business App services built for measurable outcomes.</p>
        </div>
    </a>

    <div class="mega-columns">
        <div>
            <h3 class="mega-column-title">Strategy & Advisory</h3>
            <ul class="mega-link-list">
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'data-strategy']) }}"><span class="mega-link-icon"><i class="fa-solid fa-compass" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Data &amp; AI Strategy</span><span class="mega-link-description">Set the roadmap for analytics, AI, and governance.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'm365-governance']) }}"><span class="mega-link-icon"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">M365 Governance</span><span class="mega-link-description">Improve Microsoft 365 control, compliance, and adoption.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('assessments') }}"><span class="mega-link-icon"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Assessments</span><span class="mega-link-description">Free health checks and structured discovery engagements.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'training']) }}"><span class="mega-link-icon"><i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Training</span><span class="mega-link-description">Live enablement for Power BI, Power Platform, Copilot, and AI.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'managed-services']) }}"><span class="mega-link-icon"><i class="fa-solid fa-gear" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Managed Services</span><span class="mega-link-description">Ongoing support, optimization, and governance for your environment.</span></span></a></li>
            </ul>
        </div>

        <div>
            <h3 class="mega-column-title">Data &amp; AI Platforms</h3>
            <ul class="mega-link-list">
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'microsoft-fabric']) }}"><span class="mega-link-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Microsoft Fabric</span><span class="mega-link-description">Unified analytics and BI in one platform.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'sql-data-warehousing']) }}"><span class="mega-link-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">SQL Data Warehousing</span><span class="mega-link-description">Reliable data and warehouse foundations.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'snowflake']) }}"><span class="mega-link-icon"><i class="fa-solid fa-snowflake" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Snowflake</span><span class="mega-link-description">Cloud data warehousing and sharing.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'generative-ai']) }}"><span class="mega-link-icon"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Generative AI</span><span class="mega-link-description">Build grounded AI experiences fast.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'copilot']) }}"><span class="mega-link-icon"><i class="fa-solid fa-robot" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Microsoft Copilot</span><span class="mega-link-description">Boost productivity with AI assistance.</span></span></a></li>
            </ul>
        </div>

        <div>
            <h3 class="mega-column-title">Business Applications</h3>
            <ul class="mega-link-list">
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'microsoft-power-pages']) }}"><span class="mega-link-icon"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Power Platform</span><span class="mega-link-description">Low-code apps, automation, and analytics.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'microsoft-dynamics-365']) }}"><span class="mega-link-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Dynamics 365</span><span class="mega-link-description">Connect sales, service, finance, and ops.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'sharepoint-online']) }}"><span class="mega-link-icon"><i class="fa-solid fa-share-nodes" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">SharePoint</span><span class="mega-link-description">Modern collaboration and content control.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'custom-development']) }}"><span class="mega-link-icon"><i class="fa-solid fa-code" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Custom Development</span><span class="mega-link-description">Build the app or integration you need.</span></span></a></li>
                <li><a class="mega-link-with-icon" href="{{ route('services.show', ['name' => 'api-data-access']) }}"><span class="mega-link-icon"><i class="fa-solid fa-plug" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">API Development</span><span class="mega-link-description">Expose systems cleanly for apps and AI.</span></span></a></li>
            </ul>
        </div>

        <div>
            <h3 class="mega-column-title mega-column-title--solutions">Solutions</h3>
            <div class="mega-solution-grid">
                <div class="mega-solution-group">
                    <div class="mega-solution-group-title">Everyone</div>
                    <ul class="mega-link-list">
                        <li><a class="mega-link-with-icon" href="{{ route('mela-meeting-assistant') }}"><span class="mega-link-icon"><i class="fa-solid fa-microphone-lines" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">Mela Meeting Assistant</span><span class="mega-link-description">Turn Teams meetings into summaries, tasks, and follow-ups.</span></span></a></li>
                    </ul>
                </div>
                <div class="mega-solution-group">
                    <div class="mega-solution-group-title">Oil &amp; Gas</div>
                    <ul class="mega-link-list">
                        <li><a class="mega-link-with-icon" href="{{ route('invoice-lens') }}"><span class="mega-link-icon"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span><span class="mega-link-copy"><span class="mega-link-label">InvoiceLens</span><span class="mega-link-description">Quick invoice visibility and tracking.</span></span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
