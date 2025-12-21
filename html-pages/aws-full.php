<style>
/* ============================
   AWS MODERN PAGE STYLING
   ============================ */

/* AWS Brand Colors */
:root {
    --aws-orange: #2f5597;
    --aws-dark-orange: #1e3a6d;
    --aws-dark: #232F3E;
    --aws-light: #f9fafc;
    --aws-blue: #146EB4;
    --aws-accent: #2f5597;
    --aws-gray: #545B64;
    --gradient-primary: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    --gradient-dark: linear-gradient(135deg, #232F3E 0%, #37475A 100%);
}

/* Hero Section */
.aws-hero {
    background: var(--gradient-dark);
    color: white;
    padding: 60px 0;
    border-radius: 16px;
    margin-bottom: 50px;
    position: relative;
    overflow: hidden;
}

.aws-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(47,85,151,0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.aws-hero-content {
    position: relative;
    z-index: 2;
}

/* Page container spacing */
.ms-section {
    padding: 40px 0;
}

/* Titles */
.section-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--aws-dark);
    margin-bottom: 1.2rem;
    letter-spacing: -0.5px;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: var(--gradient-primary);
    border-radius: 2px;
}

.ms-list-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--aws-orange);
    margin-top: 40px;
}

/* Lead text */
.partner-lead {
    font-size: 1.08rem;
    line-height: 1.7;
    color: #4b5563;
}

/* ============================
   CARD STYLE SECTIONS
   ============================ */
.ms-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 35px 40px;
    margin-bottom: 40px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.ms-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(47,85,151,0.15);
    border-left-color: var(--aws-orange);
}

/* ----------------------------
   UL LIST MODERNIZATION
   ---------------------------- */
.ms-list {
    padding-left: 22px;
}
.ms-list li {
    margin-bottom: 6px;
    font-size: 1rem;
    line-height: 1.55;
    padding-left: 22px;
    position: relative;
}
.ms-list li::before {
    content: '▸';
    position: absolute;
    left: 0;
    color: var(--aws-orange);
    font-weight: bold;
    font-size: 1.1rem;
}

/* ----------------------------
   Badge Styling
   ---------------------------- */
.partner-badge {
    background: var(--gradient-primary);
    color: #fff;
    padding: 10px 20px;
    border-radius: 30px;
    margin: 5px;
    font-weight: 700;
    display: inline-block;
    font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(47,85,151,0.3);
    transition: all 0.3s ease;
}

.partner-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(47,85,151,0.4);
}

/* ----------------------------
   CTA BUTTON
   ---------------------------- */
.btn-cta {
    background: var(--gradient-primary);
    color: #ffffff !important;
    padding: 16px 40px;
    font-size: 1.1rem;
    font-weight: 700;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 24px rgba(47,85,151,0.4);
    border: none;
}
.btn-cta:hover {
    background: linear-gradient(135deg, #1e3a6d 0%, #2f5597 100%);
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(47,85,151,0.5);
    color: #ffffff !important;
}

.btn-cta::after {
    content: '→';
    font-size: 1.3rem;
    transition: transform 0.3s ease;
}

.btn-cta:hover::after {
    transform: translateX(4px);
}

/* ----------------------------
   Hero Image + Branding
   ---------------------------- */
.partner-brand-logo {
    max-width: 240px;
    filter: drop-shadow(0px 6px 16px rgba(47,85,151,0.3));
    transition: transform 0.3s ease;
}

.partner-brand-logo:hover {
    transform: scale(1.05);
}

/* ----------------------------
   Section Divider line
   ---------------------------- */
hr.my-5 {
    border: none;
    height: 3px;
    background: linear-gradient(to right, transparent, var(--aws-orange), transparent);
    margin: 3rem auto;
    max-width: 200px;
    border-radius: 2px;
}

hr {
    border-color: #e5e7eb;
}

/* ----------------------------
   Gradient Highlight Wrapper
   ---------------------------- */
.ms-highlight {
    background: var(--gradient-dark);
    color: #fff;
    border-radius: 16px;
    padding: 32px 40px;
    margin-bottom: 40px;
    box-shadow: 0 12px 40px rgba(35,47,62,0.3);
    position: relative;
    overflow: hidden;
}

.ms-highlight::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
}

/* ----------------------------
   MODERN CARD GRID
   ---------------------------- */
.modern-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 28px;
    margin-bottom: 40px;
}

.modern-card {
    background: linear-gradient(135deg, #f5f8fc 0%, #ffffff 100%);
    border: 1px solid rgba(15, 108, 189, 0.1);
    border-radius: 14px;
    padding: 32px;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
    transition: height 0.35s ease;
}

.modern-card:hover {
    transform: translateY(-10px);
    border-color: var(--aws-orange);
    box-shadow: 0 20px 60px rgba(47,85,151,0.2);
}

.modern-card:hover::before {
    height: 6px;
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    filter: drop-shadow(0 4px 8px rgba(47,85,151,0.2));
}

.card-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--ms-dark);
    margin-bottom: 14px;
    line-height: 1.4;
}

.card-desc {
    font-size: 0.95rem;
    color: #4b5563;
    line-height: 1.6;
    margin-bottom: 16px;
}

.card-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.card-features li {
    font-size: 0.9rem;
    color: #5a6270;
    margin-bottom: 8px;
    padding-left: 22px;
    position: relative;
    line-height: 1.5;
}

.card-features li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: var(--aws-orange);
    font-weight: bold;
    font-size: 1.2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    margin: 40px 0;
}

.stat-card {
    background: var(--gradient-primary);
    color: white;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    box-shadow: 0 12px 32px rgba(47,85,151,0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 16px 48px rgba(47,85,151,0.4);
}

.stat-number {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 8px;
    color: white;
}

.stat-label {
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.5;
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-header .section-title {
    margin-bottom: 16px;
}

.section-header .partner-lead {
    max-width: 600px;
    margin: 0 auto;
    color: #666;
}

/* ----------------------------
   Responsive Tweaks
   ---------------------------- */
@media(max-width: 768px){
    .ms-card { padding: 25px 22px; }
    .section-title { font-size: 1.65rem; }
    .modern-grid { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: 1fr; }
}

/* Force hide any divider lines (blue horizontal lines) */
.divider,
.divider::before,
.divider::after,
.section-title .divider,
.section-title:after,
.heading-line,
.heading-line::before,
.heading-line::after {
    display: none !important;
    background: transparent !important;
    border: none !important;
}

/* ===== Full-width Partner Hero ===== */
.partner-hero {
    background: linear-gradient(135deg, #232F3E 0%, #2f5597 55%, #146EB4 100%);
    color: #fff;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}
.partner-hero::before {
    content: '';
    position: absolute;
    top: -25%;
    right: -25%;
    width: 60%;
    height: 160%;
    background: radial-gradient(closest-side, rgba(255,255,255,0.12), rgba(255,255,255,0));
    filter: blur(30px);
}
.hero-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; }
.hero-title { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; line-height: 1.15; margin-bottom: 18px; color: #fff; }
.hero-sub { font-size: 1.1rem; line-height: 1.8; opacity: 0.95; color: #fff; }
.hero-ctas { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 24px; }
.hero-cta { background: transparent; color: #ffffff; border: 1.6px solid rgba(255,255,255,0.6); border-radius: 999px; padding: 12px 18px; font-weight: 700; box-shadow: 0 6px 18px rgba(0,0,0,0.2); text-decoration: none; }
.hero-cta:hover { background: rgba(255,255,255,0.12); }
.hero-logo { max-width: 260px; filter: drop-shadow(0 12px 32px rgba(0,0,0,0.4)) drop-shadow(0 4px 12px rgba(0,0,0,0.3)); opacity: 0.95; transition: all 0.3s ease; border-radius: 16px; }
@media(max-width: 992px){ .hero-grid { grid-template-columns: 1fr; text-align: center; } .hero-logo { margin: 20px auto 0; } }
.container { padding: 50px 24px; }

</style>

<!-- Full-width Hero -->
<section class="partner-hero">
    <div class="hero-inner">
        <div class="hero-grid">
            <div>
                <h1 class="hero-title">Build. Modernize. Innovate with AWS</h1>
                <p class="hero-sub">Your cloud journey goals are attainable—no matter the size or scope. As an AWS Partner Network member and growing services partner, we apply future-focused best practices and hands-on experience to help you thrive in a technology-centric world.</p>
                <div class="hero-ctas">
                    <a class="hero-cta" href="#services">AWS Services</a>
                    <a class="hero-cta" href="#migration">Cloud Migration</a>
                    <a class="hero-cta" href="/contact">Start Your Journey</a>
                </div>
            </div>
            <div class="text-center">
                <img class="hero-logo" alt="AWS Logo" src="/images/partners/aws.png">
            </div>
        </div>
    </div>
</section>

<div class="container">

<div class="mb-5">
    <h3 class="section-title">AWS Partner Network Services Partner</h3>
    <p class="partner-lead">Achieve your business transformation goals and create limitless possibilities for the future with our comprehensive AWS services and expertise.</p>
</div>

<hr class="my-5">

<div class="ms-highlight default-background">
    <h3 class="section-title text-white mb-4">Why Partner with Us?</h3>
    <p class="partner-lead text-white mb-3" style="opacity: 0.95;">The AWS Partner Network (APN) brings together technology and consulting partners who leverage AWS to build solutions and services for customers. As an active APN member, we're committed to growing our AWS expertise and delivering successful customer outcomes.</p>
    <p class="partner-lead text-white mb-0" style="opacity: 0.95;">We meet customers where they are on their cloud journey—whether just starting or executing on enterprise-wide transformation. Taking a customer-centric approach to understanding your business goals and priorities, we work collaboratively to jumpstart business transformation and maximize returns on technology investments.</p>
</div>

<h4 class="ms-list-title mt-5">Our Comprehensive Approach</h4>
<p class="partner-lead">From advisory, design, migration and implementation to adoption and innovation, we deliver end-to-end solutions that align with your strategic objectives.</p>

<hr class="my-5">
<h3 class="section-title mt-3">AWS Service Capabilities</h3>
<p class="partner-lead">Our team is actively building expertise across core AWS services and solution areas to support the breadth of your business goals. We're committed to continuous learning and certification to deliver high-quality AWS solutions.</p>

<div class="modern-grid">
    <div class="modern-card">
        <div class="card-icon">🛫</div>
        <h5 class="card-title">Cloud Migration & Modernization</h5>
        <p class="card-desc">Navigate your path from legacy infrastructure to cloud-native solutions.</p>
        <p class="card-desc">Move to the cloud with confidence using proven methodologies and AWS best practices. We help organizations migrate applications, databases, and workloads to AWS while minimizing risk and ensuring business continuity.</p>
        <ul class="card-features">
            <li>Cloud readiness assessment and planning</li>
            <li>Application discovery & portfolio analysis</li>
            <li>Migration strategy (6 R's framework)</li>
            <li>Proof-of-concept and pilot migrations</li>
            <li>Database migration & application execution</li>
            <li>Testing, cutover planning, and post-migration optimization</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">📊</div>
        <h5 class="card-title">Data & Analytics</h5>
        <p class="card-desc">Transform data into actionable insights using AWS analytics services.</p>
        <ul class="card-features">
            <li>Data lake architecture with Amazon S3</li>
            <li>Data warehousing with Amazon Redshift</li>
            <li>ETL with AWS Glue & real-time streaming with Kinesis</li>
            <li>BI with Amazon QuickSight and big data processing with EMR</li>
            <li>Data governance and security best practices</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🛠️</div>
        <h5 class="card-title">Application Development & Modernization</h5>
        <p class="card-desc">Build and modernize cloud-native applications with AWS.</p>
        <ul class="card-features">
            <li>Microservices and containerization (ECS, EKS)</li>
            <li>Serverless with AWS Lambda & API Gateway</li>
            <li>CI/CD pipelines and application refactoring</li>
            <li>Legacy application modernization and re-platforming</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🖧</div>
        <h5 class="card-title">Infrastructure & Cloud Operations</h5>
        <p class="card-desc">Design and operate scalable cloud infrastructure on AWS.</p>
        <ul class="card-features">
            <li>VPC, network design and connectivity</li>
            <li>Compute optimization (EC2, ECS, EKS, Lambda)</li>
            <li>Storage (S3, EBS, EFS, FSx) and HA/DR patterns</li>
            <li>IaC with CloudFormation and Terraform; monitoring & FinOps</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🔐</div>
        <h5 class="card-title">Security & Compliance</h5>
        <p class="card-desc">Protect your AWS environment with proven controls and automation.</p>
        <ul class="card-features">
            <li>Security assessments & Well-Architected security reviews</li>
            <li>IAM, network segmentation, encryption, and DLP</li>
            <li>Security Hub, monitoring, incident response & automation</li>
            <li>Compliance frameworks and audit readiness</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">⚙️</div>
        <h5 class="card-title">DevOps & Automation</h5>
        <p class="card-desc">Accelerate delivery with DevOps practices and automation.</p>
        <ul class="card-features">
            <li>CI/CD pipeline design & automated testing</li>
            <li>Infrastructure as Code and config management</li>
            <li>Monitoring, observability and release management</li>
            <li>ChatOps and collaboration tooling</li>
        </ul>
    </div>
</div>

<hr class="my-5">
<h3 class="section-title">AWS Service Areas</h3>

<div class="modern-grid">
    <div class="modern-card">
        <div class="card-icon">🖥️</div>
        <h5 class="card-title">Compute Services</h5>
        <ul class="card-features">
            <li>Amazon EC2, AWS Lambda, ECS & EKS</li>
            <li>AWS Batch, Lightsail, Elastic Beanstalk</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">💾</div>
        <h5 class="card-title">Storage Services</h5>
        <ul class="card-features">
            <li>Amazon S3, EBS, EFS, FSx</li>
            <li>AWS Storage Gateway & centralized backup</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🗄️</div>
        <h5 class="card-title">Database Services</h5>
        <ul class="card-features">
            <li>Amazon RDS, Aurora, DynamoDB, ElastiCache</li>
            <li>Neptune, DocumentDB and managed database options</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🌐</div>
        <h5 class="card-title">Networking & CDN</h5>
        <ul class="card-features">
            <li>Amazon VPC, Direct Connect, CloudFront</li>
            <li>Route 53, Transit Gateway, ELB</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">📈</div>
        <h5 class="card-title">Analytics Services</h5>
        <ul class="card-features">
            <li>Redshift, Athena, Glue, EMR, Kinesis, QuickSight</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🤖</div>
        <h5 class="card-title">Machine Learning & AI</h5>
        <ul class="card-features">
            <li>SageMaker, Bedrock, Rekognition, Comprehend, Forecast</li>
        </ul>
    </div>
</div>

<hr class="my-5">
<h3 class="section-title">Industry Solutions</h3>

<div class="modern-grid">
    <div class="modern-card">
        <div class="card-icon">⚕️</div>
        <h5 class="card-title">Healthcare & Life Sciences</h5>
        <p class="card-desc">HIPAA-compliant solutions, secure data exchange and research acceleration.</p>
    </div>
    <div class="modern-card">
        <div class="card-icon">🏦</div>
        <h5 class="card-title">Financial Services</h5>
        <p class="card-desc">Secure, compliant platforms for trading, risk, and customer apps.</p>
    </div>
    <div class="modern-card">
        <div class="card-icon">🏭</div>
        <h5 class="card-title">Manufacturing</h5>
        <p class="card-desc">IoT, analytics and ML for predictive maintenance and optimization.</p>
    </div>
    <div class="modern-card">
        <div class="card-icon">🛍️</div>
        <h5 class="card-title">Retail & E-Commerce</h5>
        <p class="card-desc">Scalable commerce platforms, personalization and inventory systems.</p>
    </div>
    <div class="modern-card">
        <div class="card-icon">🏛️</div>
        <h5 class="card-title">Public Sector</h5>
        <p class="card-desc">Secure, compliant modernization for government and education.</p>
    </div>
    <div class="modern-card">
        <div class="card-icon">🎬</div>
        <h5 class="card-title">Media & Entertainment</h5>
        <p class="card-desc">Content creation, processing and global delivery at scale.</p>
    </div>
</div>

<hr class="my-5">
<h3 class="section-title">Our Service Offerings</h3>

<div class="modern-grid">
    <div class="modern-card">
        <div class="card-icon">🧭</div>
        <h5 class="card-title">Cloud Strategy & Advisory</h5>
        <ul class="card-features">
            <li>Cloud readiness assessment & TCO analysis</li>
            <li>Roadmaps, architecture workshops & PoCs</li>
        </ul>
    </div>
    <div class="modern-card">
        <div class="card-icon">🚚</div>
        <h5 class="card-title">Migration Services</h5>
        <ul class="card-features">
            <li>Application discovery, AWS Migration Hub, DMS, MGN</li>
            <li>Cutover planning, testing and go-live support</li>
        </ul>
    </div>
    <div class="modern-card">
        <div class="card-icon">🏗️</div>
        <h5 class="card-title">Implementation Services</h5>
        <ul class="card-features">
            <li>Solution architecture, deployment, integration & security</li>
            <li>Monitoring, logging and documentation</li>
        </ul>
    </div>
    <div class="modern-card">
        <div class="card-icon">🛡️</div>
        <h5 class="card-title">Managed Services</h5>
        <ul class="card-features">
            <li>24/7 monitoring, incident response, patching & reporting</li>
            <li>Performance optimization and cost management</li>
        </ul>
    </div>
    <div class="modern-card">
        <div class="card-icon">🎓</div>
        <h5 class="card-title">Training & Enablement</h5>
        <ul class="card-features">
            <li>AWS fundamentals, role-based training and cert prep</li>
            <li>Hands-on labs, workshops and architecture training</li>
        </ul>
    </div>
</div>

<hr class="my-5">
<h3 class="section-title">AWS Well-Architected Framework</h3>
<p class="partner-lead">We design and review solutions using the AWS Well-Architected Framework to ensure best practices across six pillars.</p>

<div class="modern-grid">
    <div class="modern-card"><div class="card-icon">⚙️</div><h5 class="card-title">Operational Excellence</h5><p class="card-desc">Run and monitor systems to deliver business value.</p></div>
    <div class="modern-card"><div class="card-icon">🔐</div><h5 class="card-title">Security</h5><p class="card-desc">Protect information, systems and assets.</p></div>
    <div class="modern-card"><div class="card-icon">🔁</div><h5 class="card-title">Reliability</h5><p class="card-desc">Ensure workloads perform consistently.</p></div>
    <div class="modern-card"><div class="card-icon">⚡</div><h5 class="card-title">Performance Efficiency</h5><p class="card-desc">Use computing resources efficiently.</p></div>
    <div class="modern-card"><div class="card-icon">💰</div><h5 class="card-title">Cost Optimization</h5><p class="card-desc">Deliver business value at lowest cost.</p></div>
    <div class="modern-card"><div class="card-icon">🌱</div><h5 class="card-title">Sustainability</h5><p class="card-desc">Minimize environmental impact and optimize energy use.</p></div>
</div>

<hr class="my-5">
<h3 class="section-title">Why AWS?</h3>

<div class="modern-grid">
    <div class="modern-card"><div class="card-icon">🌍</div><h5 class="card-title">Global Infrastructure</h5><p class="card-desc">Deploy across many regions and availability zones for low latency and high availability.</p></div>
    <div class="modern-card"><div class="card-icon">🧩</div><h5 class="card-title">Comprehensive Services</h5><p class="card-desc">200+ fully featured services across compute, storage, analytics, ML and more.</p></div>
    <div class="modern-card"><div class="card-icon">🔐</div><h5 class="card-title">Security & Compliance</h5><p class="card-desc">Build on secure infrastructure with broad compliance certifications.</p></div>
    <div class="modern-card"><div class="card-icon">🚀</div><h5 class="card-title">Innovation Velocity</h5><p class="card-desc">Continuous innovation with frequent service updates and new features.</p></div>
    <div class="modern-card"><div class="card-icon">💵</div><h5 class="card-title">Cost Efficiency</h5><p class="card-desc">Pay-as-you-go pricing and tools for cost management.</p></div>
    <div class="modern-card"><div class="card-icon">🏅</div><h5 class="card-title">Proven Track Record</h5><p class="card-desc">Trusted by millions of customers worldwide.</p></div>
</div>

<hr class="my-5">
<h3 class="section-title">Our Differentiators</h3>

<div class="modern-grid">
    <div class="modern-card"><div class="card-icon">🎓</div><h5 class="card-title">AWS Certified Professionals</h5><p class="card-desc">Certified architects, developers and sysops committed to continuous learning.</p></div>
    <div class="modern-card"><div class="card-icon">🤝</div><h5 class="card-title">Customer-First Approach</h5><p class="card-desc">Recommendations aligned to your business outcomes.</p></div>
    <div class="modern-card"><div class="card-icon">🧰</div><h5 class="card-title">Practical Experience</h5><p class="card-desc">Real-world delivery across industries and use cases.</p></div>
    <div class="modern-card"><div class="card-icon">⚡</div><h5 class="card-title">Agile Methodology</h5><p class="card-desc">Iterative delivery and rapid feedback loops.</p></div>
    <div class="modern-card"><div class="card-icon">🔁</div><h5 class="card-title">End-to-End Capabilities</h5><p class="card-desc">Strategy through managed services with single-partner accountability.</p></div>
    <div class="modern-card"><div class="card-icon">📈</div><h5 class="card-title">Growing AWS Partnership</h5><p class="card-desc">Active investment in certifications, competencies and customer success.</p></div>
</div>

<hr class="my-5">
<h3 class="section-title">Getting Started with AWS</h3>

<div class="modern-grid">
    <div class="modern-card"><div class="card-icon">🔎</div><h5 class="card-title">Assessment</h5><p class="card-desc">Understand environment, objectives and technical requirements.</p></div>
    <div class="modern-card"><div class="card-icon">🗺️</div><h5 class="card-title">Planning</h5><p class="card-desc">Migration plans, architecture designs and implementation roadmaps.</p></div>
    <div class="modern-card"><div class="card-icon">🚀</div><h5 class="card-title">Implementation</h5><p class="card-desc">Execute migration and deploy solutions using AWS best practices.</p></div>
    <div class="modern-card"><div class="card-icon">🔧</div><h5 class="card-title">Optimization & Support</h5><p class="card-desc">Continuously improve performance, security and cost efficiency.</p></div>
</div>

<hr class="my-5">
<h3 class="section-title">Ready to Start Your AWS Journey?</h3>
<p class="partner-lead mb-4">Whether you're exploring cloud possibilities, planning your first migration, or looking to optimize existing AWS workloads, we're here to help. Our team brings AWS expertise, proven methodologies, and commitment to your success.</p>

<div class="text-center mb-5">
    <a href="/contact.php" class="btn btn-cta">Get Started Today</a>
</div>

<div class="ms-card">
    <h5 class="ms-list-title">Connect with our AWS team to discuss your cloud goals and discover how we can help you:</h5>
    <ul class="ms-list mt-3">
        <li>Reduce infrastructure costs with cloud migration</li>
        <li>Improve application performance and scalability</li>
        <li>Accelerate innovation with cloud-native development</li>
        <li>Enhance security and compliance posture</li>
        <li>Build data analytics and ML capabilities</li>
        <li>Modernize legacy applications</li>
        <li>Establish DevOps practices and automation</li>
        <li>Optimize existing AWS environments</li>
    </ul>

    <p class="partner-lead mt-4">Contact us to schedule a consultation and learn how our AWS partnership can accelerate your cloud transformation.</p>
</div>

<p class="partner-lead mt-4"><strong>As an AWS Partner Network member, we're committed to helping organizations of all sizes achieve their cloud goals.</strong> With growing expertise, proven capabilities, and a customer-first approach, we're your trusted partner for building, modernizing, and innovating on AWS.</p>

</div>
