<style>
.fabric-capacity-hero {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.fabric-capacity-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: -1;
}
.fabric-capacity-hero h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 15px;
}
.fabric-capacity-hero p {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}
.fabric-capacity-section {
    padding: 60px 0;
}
.fabric-capacity-section h2 {
    color: #2f5597;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 40px;
    text-align: center;
}
.capacity-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-left: 5px solid #2f5597;
}
.capacity-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(47, 85, 151, 0.2);
}
.capacity-card h3 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 1.25rem;
}
.capacity-card p {
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}
.capacity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}
.feature-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
    transition: all 0.3s ease;
}
.feature-item:hover {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    transform: scale(1.05);
}
.feature-item h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 1.1rem;
}
.feature-item:hover h4 {
    color: white;
}
.feature-item p {
    font-size: 0.9rem;
    margin: 0;
}
.calculator-section {
    background: white;
    border-radius: 8px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 50px;
}
.calculator-section h3 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 30px;
    text-align: center;
}
.calculator-item {
    margin-bottom: 25px;
}
.calculator-item label {
    color: #2f5597;
    font-weight: bold;
    display: block;
    margin-bottom: 8px;
}
.calculator-item input,
.calculator-item select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.95rem;
}
.calculator-item input:focus,
.calculator-item select:focus {
    outline: none;
    border-color: #2f5597;
    box-shadow: 0 0 5px rgba(47, 85, 151, 0.2);
}
.tier-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-top: 4px solid #2f5597;
    transition: all 0.3s ease;
}
.tier-card:hover {
    box-shadow: 0 6px 14px rgba(47, 85, 151, 0.15);
    transform: translateY(-3px);
}
.tier-card h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 12px;
}
.tier-card p {
    color: #555;
    font-size: 0.9rem;
    line-height: 1.5;
}
.tier-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}
.cta-section {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 50px;
    border-radius: 8px;
    text-align: center;
    margin-top: 60px;
}
.cta-section h3 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    font-weight: bold;
}
.cta-section p {
    font-size: 1.05rem;
    margin-bottom: 30px;
    line-height: 1.6;
}
.cta-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-primary-cta {
    background: white;
    color: #2f5597;
    border: none;
    padding: 12px 30px;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}
.btn-primary-cta:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
.btn-secondary-cta {
    background: transparent;
    color: white;
    border: 2px solid white;
    padding: 12px 30px;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}
.btn-secondary-cta:hover {
    background: white;
    color: #2f5597;
}
@media (max-width: 768px) {
    .fabric-capacity-hero h1 {
        font-size: 1.8rem;
    }
    .fabric-capacity-hero p {
        font-size: 1rem;
    }
    .capacity-grid {
        grid-template-columns: 1fr;
    }
    .tier-grid {
        grid-template-columns: 1fr;
    }
    .cta-buttons {
        flex-direction: column;
    }
    .btn-primary-cta, .btn-secondary-cta {
        width: 100%;
    }
}
</style>

<div class="fabric-capacity-hero">
    <h1 class="text-light">Estimate Your Fabric Capacity Needs</h1>
    <p class="text-light">Adopt a unified data platform infused with AI at every layer. Accurately estimate and scale your Microsoft Fabric capacity</p>
</div>

<div class="container-fluid fabric-capacity-section">
    <div class="container">
        <h2>Microsoft Fabric Capacity Planning</h2>
        <p style="text-align: center; color: #555; font-size: 1.05rem; max-width: 800px; margin: 0 auto 40px;">Accurately estimate and scale your Microsoft Fabric capacity to ensure optimized performance, informed decisions, and accelerated productivity. Our experts help you right-size capacity for your unique workload requirements.</p>

        <div class="capacity-grid">
            <div class="capacity-card">
                <h3>Workload Analysis</h3>
                <p>Assess your current data workloads, growth projections, and performance requirements to determine optimal capacity.</p>
            </div>
            <div class="capacity-card">
                <h3>Cost Optimization</h3>
                <p>Balance performance needs with budget constraints. Find the right capacity tier to maximize ROI.</p>
            </div>
            <div class="capacity-card">
                <h3>Scalability Planning</h3>
                <p>Design flexible capacity strategies that grow with your business. Scale up or down as needs evolve.</p>
            </div>
            <div class="capacity-card">
                <h3>Performance Tuning</h3>
                <p>Optimize queries and workload management to maximize capacity utilization and reduce costs.</p>
            </div>
            <div class="capacity-card">
                <h3>Migration Support</h3>
                <p>Plan capacity for data migration from legacy systems. Ensure smooth transitions without performance impact.</p>
            </div>
            <div class="capacity-card">
                <h3>Monitoring & Alerts</h3>
                <p>Continuous capacity monitoring with alerts for optimization opportunities and threshold breaches.</p>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Fabric Capacity Features</h2>
        <div class="capacity-grid">
            <div class="feature-item">
                <h4>Auto-Scale</h4>
                <p>Automatically scale capacity based on real-time demand</p>
            </div>
            <div class="feature-item">
                <h4>Workload Management</h4>
                <p>Distribute resources across multiple workloads efficiently</p>
            </div>
            <div class="feature-item">
                <h4>Premium Performance</h4>
                <p>Dedicated capacity for mission-critical analytics</p>
            </div>
            <div class="feature-item">
                <h4>Cost Transparency</h4>
                <p>Detailed cost tracking and optimization recommendations</p>
            </div>
            <div class="feature-item">
                <h4>Flexible SKUs</h4>
                <p>Choose from multiple capacity options for your needs</p>
            </div>
            <div class="feature-item">
                <h4>Regional Options</h4>
                <p>Deploy capacity in regions closest to your data</p>
            </div>
        </div>

        <!-- <div class="calculator-section">
            <h3>Quick Capacity Calculator</h3>
            <div style="max-width: 600px; margin: 0 auto;">
                <div class="calculator-item">
                    <label>Expected Monthly Data Volume (GB)</label>
                    <input type="number" placeholder="Enter data volume" min="0">
                </div>
                <div class="calculator-item">
                    <label>Number of Concurrent Users</label>
                    <input type="number" placeholder="Enter user count" min="1">
                </div>
                <div class="calculator-item">
                    <label>Primary Workload Type</label>
                    <select>
                        <option value="">Select workload type</option>
                        <option value="analytics">Analytics & Reporting</option>
                        <option value="dataengineering">Data Engineering</option>
                        <option value="realtime">Real-Time Processing</option>
                        <option value="mixed">Mixed Workloads</option>
                    </select>
                </div>
                <div class="calculator-item">
                    <label>Growth Rate (% annually)</label>
                    <input type="number" placeholder="Enter growth rate" min="0" max="100">
                </div>
                <button class="btn-primary-cta" style="width: 100%; margin-top: 20px;">Calculate Capacity</button>
            </div>
        </div> -->

        