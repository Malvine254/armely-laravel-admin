<style>
/* ===== Armely Power Apps Modern Compact Page ===== */
.powerapps-page,
.powerapps-page * { box-sizing: border-box; }

.powerapps-page {
    --blue: #2f5597;
    --blue-dark: #173b67;
    --blue-mid: #234f86;
    --blue-light: #4779bd;
    --bg-soft: #f6f8fc;
    --card: #ffffff;
    --text: #334155;
    --muted: #667085;
    --border: rgba(47, 85, 151, 0.14);
    font-family: 'Poppins', sans-serif;
    color: var(--text);
    background: #ffffff;
    line-height: 1.6;
}

.powerapps-page .container {
    width: min(1120px, calc(100% - 48px));
    margin: 0 auto;
}

.powerapps-hero {
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-mid) 100%);
    color: #ffffff;
    padding: 86px 0 70px;
    position: relative;
    overflow: hidden;
}

.powerapps-hero::before {
    content: '';
    position: absolute;
    inset: auto -120px -180px auto;
    width: 420px;
    height: 420px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.09);
    pointer-events: none;
}

.powerapps-hero-content {
    position: relative;
    z-index: 1;
    max-width: 860px;
}

.powerapps-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.10);
    border: 1px solid rgba(255, 255, 255, 0.22);
    color: rgba(255, 255, 255, 0.88);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

.powerapps-hero h1 {
    max-width: 900px;
    margin: 0 0 18px;
    color: #ffffff;
    font-size: clamp(2.5rem, 5vw, 4.9rem);
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.04em;
}

.powerapps-hero .hero-lead {
    max-width: 760px;
    margin: 0 0 12px;
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.08rem;
    line-height: 1.65;
    font-weight: 600;
}

.powerapps-hero .hero-sub {
    max-width: 760px;
    margin: 0 0 28px;
    color: rgba(255, 255, 255, 0.82);
    font-size: 1rem;
    line-height: 1.7;
}

.powerapps-hero-actions,
.cta-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.powerapps-section {
    padding: 68px 0;
    background: #ffffff;
}

.powerapps-section.alt {
    background: var(--bg-soft);
}

.section-header {
    max-width: 850px;
    margin: 0 auto;
    text-align: center;
}

.section-eyebrow {
    display: inline-flex;
    margin: 0 auto 10px;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(47, 85, 151, 0.09);
    border: 1px solid rgba(47, 85, 151, 0.18);
    color: var(--blue);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
}

.powerapps-section h2,
.process-section h2,
.cta-section h2 {
    margin: 0 0 14px;
    color: #162b49;
    font-size: clamp(1.7rem, 3.2vw, 2.6rem);
    line-height: 1.12;
    font-weight: 800;
    letter-spacing: -0.025em;
}

.section-description {
    max-width: 820px;
    margin: 0 auto;
    color: var(--text);
    font-size: 0.98rem;
    line-height: 1.7;
}

.powerapps-grid,
.benefits-grid,
.process-steps {
    display: grid;
    gap: 20px;
    margin-top: 34px;
}

.powerapps-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.benefits-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.app-card,
.benefit-card,
.process-step {
    height: 100%;
    border-radius: 14px;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.app-card,
.benefit-card {
    background: var(--card);
    border: 1px solid var(--border);
    box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08);
}

.app-card {
    padding: 24px 22px;
}

.benefit-card {
    padding: 24px 22px;
    text-align: center;
}

.app-card:hover,
.benefit-card:hover {
    transform: translateY(-4px);
    border-color: rgba(47, 85, 151, 0.3);
    box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}

.app-card h4,
.benefit-card h5,
.process-step h5 {
    margin: 0 0 10px;
    color: #162b49;
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.35;
}

.app-card h4 i {
    margin-right: 6px;
    color: var(--blue);
}

.app-card p,
.benefit-card p,
.process-step p {
    margin: 0;
    color: var(--text);
    font-size: 0.875rem;
    line-height: 1.65;
}

.app-card .feature-list {
    display: grid;
    gap: 7px;
    margin: 14px 0 0;
    padding: 0;
    list-style: none;
}

.app-card .feature-list li {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    color: var(--muted);
    font-size: 0.84rem;
    line-height: 1.45;
}

.app-card .feature-list li::before {
    content: '✓';
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    margin-top: 1px;
    border-radius: 999px;
    background: rgba(47, 85, 151, 0.09);
    color: var(--blue);
    font-size: 0.7rem;
    font-weight: 800;
    flex: 0 0 18px;
}

.benefit-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(47, 85, 151, 0.09);
    border: 1px solid rgba(47, 85, 151, 0.18);
    color: var(--blue);
    font-size: 1.25rem;
}

.process-section {
    padding: 68px 0;
    background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-mid) 100%);
    color: #ffffff;
}

.process-section .section-header h2,
.process-section .section-description {
    color: #ffffff;
}

.process-section .section-description {
    color: rgba(255, 255, 255, 0.82);
}

.process-section .section-eyebrow {
    background: rgba(255, 255, 255, 0.10);
    border-color: rgba(255, 255, 255, 0.22);
    color: rgba(255, 255, 255, 0.88);
}

.process-steps {
    grid-template-columns: repeat(5, minmax(0, 1fr));
}

.process-step {
    padding: 24px 18px;
    background: rgba(255, 255, 255, 0.09);
    border: 1px solid rgba(255, 255, 255, 0.16);
    text-align: left;
}

.step-number {
    width: 42px;
    height: 42px;
    margin: 0 0 14px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    color: var(--blue);
    font-size: 1rem;
    font-weight: 800;
}

.process-step h5 {
    color: #ffffff;
}

.process-step p {
    color: rgba(255, 255, 255, 0.82);
}

.cta-section {
    padding: 68px 0;
    background: var(--bg-soft);
    color: var(--text);
    text-align: center;
}

.cta-section h2 {
    max-width: 760px;
    margin-left: auto;
    margin-right: auto;
}

.cta-section p {
    max-width: 720px;
    margin: 0 auto 24px;
    color: var(--text);
    font-size: 0.98rem;
    line-height: 1.7;
}

.cta-buttons {
    justify-content: center;
}

.btn-primary,
.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 13px 28px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.15s ease, background 0.2s ease, border-color 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--blue), var(--blue-light));
    color: #ffffff;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}

.btn-primary:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #274a83, #3c6dac);
    color: #ffffff;
}

.btn-secondary {
    background: transparent;
    color: var(--blue);
    border: 1px solid rgba(47, 85, 151, 0.35);
}

.btn-secondary:hover {
    transform: translateY(-2px);
    background: #ffffff;
    border-color: rgba(47, 85, 151, 0.5);
    color: var(--blue);
}

.powerapps-hero .btn-secondary {
    color: rgba(255, 255, 255, 0.9);
    border-color: rgba(255, 255, 255, 0.35);
}

.powerapps-hero .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.10);
    color: #ffffff;
}

@media (max-width: 980px) {
    .powerapps-grid,
    .benefits-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .process-steps {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .powerapps-page .container {
        width: min(100% - 44px, 1120px);
    }

    .powerapps-hero {
        padding: 88px 0 58px;
    }

    .powerapps-section,
    .process-section,
    .cta-section {
        padding: 56px 0;
    }

    .powerapps-grid,
    .benefits-grid {
        grid-template-columns: 1fr;
        margin-top: 28px;
    }

    .powerapps-hero-actions,
    .cta-buttons {
        flex-direction: column;
    }

    .powerapps-hero-actions a,
    .cta-buttons a {
        width: 100%;
    }
}
</style>

<div class="powerapps-page">
    <section class="powerapps-hero">
        <div class="container">
            <div class="powerapps-hero-content">
                <div class="powerapps-eyebrow">Microsoft Power Platform</div>
                <h1>Microsoft Power Apps</h1>
                <p class="hero-lead">Build custom applications without heavy coding.</p>
                <p class="hero-sub">Empower your team with low-code and no-code solutions. Create powerful business applications in days, not months, with intuitive tools designed around your real business workflows.</p>
                <div class="powerapps-hero-actions">
                    <a href="#consultation" class="btn-primary">Request a Power Apps Solution</a>
                    <a href="#solutions" class="btn-secondary">Explore Solutions</a>
                </div>
            </div>
        </div>
    </section>

    <section class="powerapps-section" id="solutions">
        <div class="container">
            <div class="section-header">
                <div class="section-eyebrow">Power Apps Solutions</div>
                <h2>Business applications designed around how your teams work.</h2>
                <p class="section-description">From operations tracking to approvals and reporting, Armely builds Power Apps that simplify manual processes, connect business data, and give teams faster ways to complete daily work.</p>
            </div>

            <div class="powerapps-grid">
                <div class="app-card">
                    <h4><i class="fas fa-clock"></i> Timesheet Applications</h4>
                    <p>Automate employee time tracking and project billing with custom timesheet apps designed for your specific workflow.</p>
                    <ul class="feature-list">
                        <li>Mobile time entry</li>
                        <li>Real-time approvals</li>
                        <li>Project integration</li>
                        <li>Export and reporting</li>
                    </ul>
                </div>

                <div class="app-card">
                    <h4><i class="fas fa-boxes"></i> Inventory Management</h4>
                    <p>Streamline inventory operations with intelligent tracking, forecasting, and automated reorder capabilities.</p>
                    <ul class="feature-list">
                        <li>Real-time tracking</li>
                        <li>Automated alerts</li>
                        <li>Barcode integration</li>
                        <li>Multi-location support</li>
                    </ul>
                </div>

                <div class="app-card">
                    <h4><i class="fas fa-file-contract"></i> Approval Workflows</h4>
                    <p>Implement flexible approval workflows that route requests to the right people at the right time.</p>
                    <ul class="feature-list">
                        <li>Multi-level approvals</li>
                        <li>Conditional routing</li>
                        <li>Audit trails</li>
                        <li>Escalation rules</li>
                    </ul>
                </div>

                <div class="app-card">
                    <h4><i class="fas fa-users-cog"></i> Customer Management</h4>
                    <p>Build comprehensive customer relationship solutions tailored to your business processes.</p>
                    <ul class="feature-list">
                        <li>Contact management</li>
                        <li>Pipeline tracking</li>
                        <li>Activity logging</li>
                        <li>Reporting dashboards</li>
                    </ul>
                </div>

                <div class="app-card">
                    <h4><i class="fas fa-chart-bar"></i> Business Analytics</h4>
                    <p>Create custom analytics applications that provide real-time insights into your business metrics.</p>
                    <ul class="feature-list">
                        <li>Real-time dashboards</li>
                        <li>Custom visualizations</li>
                        <li>Data aggregation</li>
                        <li>Predictive analytics</li>
                    </ul>
                </div>

                <div class="app-card">
                    <h4><i class="fas fa-tasks"></i> Project Management</h4>
                    <p>Centralize project management with task tracking, resource allocation, and team collaboration.</p>
                    <ul class="feature-list">
                        <li>Task management</li>
                        <li>Resource planning</li>
                        <li>Team collaboration</li>
                        <li>Progress tracking</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="powerapps-section alt">
        <div class="container">
            <div class="section-header">
                <div class="section-eyebrow">Why Choose Armely</div>
                <h2>Power Apps built for adoption, security, and scale.</h2>
                <p class="section-description">We do more than build screens. We design secure, maintainable applications connected to Microsoft 365, Dataverse, SharePoint, Dynamics 365, and the data sources your business already uses.</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-bolt"></i></div>
                    <h5>Rapid Development</h5>
                    <p>Deploy applications in days instead of months with a proven Power Apps delivery approach.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-lock"></i></div>
                    <h5>Enterprise Security</h5>
                    <p>Built with compliance, data protection, and role-based access control from the start.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h5>Mobile Ready</h5>
                    <p>Responsive applications that work smoothly across phones, tablets, and desktops.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-plug"></i></div>
                    <h5>Seamless Integration</h5>
                    <p>Connect with Microsoft 365, Dynamics 365, SharePoint, Dataverse, and existing systems.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-chart-line"></i></div>
                    <h5>Scalable Solutions</h5>
                    <p>Grow your applications with your business, from departmental tools to enterprise scale.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                    <h5>Expert Support</h5>
                    <p>Ongoing support, optimization, and guidance from experienced Microsoft platform specialists.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="process-section">
        <div class="container">
            <div class="section-header">
                <div class="section-eyebrow">Delivery Process</div>
                <h2>From process discovery to a working business app.</h2>
                <p class="section-description">Our process keeps the build focused, reduces rework, and ensures your team receives an app that is easy to use and ready for real business operations.</p>
            </div>

            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h5>Discovery and Planning</h5>
                    <p>We understand your process, users, data, and success criteria before development begins.</p>
                </div>

                <div class="process-step">
                    <div class="step-number">2</div>
                    <h5>UI/UX Design</h5>
                    <p>We create simple, intuitive screens that make daily work easier for your users.</p>
                </div>

                <div class="process-step">
                    <div class="step-number">3</div>
                    <h5>Development</h5>
                    <p>We build the app, connect data sources, apply security rules, and automate key steps.</p>
                </div>

                <div class="process-step">
                    <div class="step-number">4</div>
                    <h5>Testing and QA</h5>
                    <p>We validate functionality, performance, security, and user flows before deployment.</p>
                </div>

                <div class="process-step">
                    <div class="step-number">5</div>
                    <h5>Deployment and Training</h5>
                    <p>We launch the app, train users, and support adoption so the solution delivers value.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section" id="consultation">
        <div class="container">
            <h2>Ready to build your custom application?</h2>
            <p>Let Armely transform your manual business processes into secure, efficient, and user-friendly Power Apps solutions.</p>
            <div class="cta-buttons">
                <a href="#contact" class="btn-primary">Request a Power Apps Solution</a>
                <a href="#solutions" class="btn-secondary">View Solution Areas</a>
            </div>
        </div>
    </section>
</div>
