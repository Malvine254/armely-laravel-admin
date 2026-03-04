<!-- AI Data Readiness Assessment Modal -->
<div class="modal fade" id="aiReadinessModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content readiness-modal-content">
            <button type="button" class="close-readiness" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body p-0">
                <div class="readiness-wrap">
                    <!-- HUD Score (Floating in modal) -->
                    <div class="readiness-score-hud" id="readinessScoreHud" style="display:none;">
                        <span class="score-lbl">Score</span>
                        <span class="score-val" id="readinessScoreVal">0</span>
                    </div>

                    <!-- Header with Logo -->
                    <div class="readiness-head text-center">
                        <img src="{{ asset('images/logo/logo-replace.png') }}" alt="Armely" class="readiness-logo">
                        <span class="readiness-kicker">AI Data Readiness Assessment</span>
                    </div>

                    <!-- SCREEN: INTRO -->
                    <div class="readiness-screen on" id="rIntro">
                        <div class="intro-hero-box">
                            <span class="big-emoji">🧠</span>
                            <h2 class="intro-h">How AI-Ready Is <br><span class="hl">Your Data?</span></h2>
                            <p class="intro-p">Discover your data readiness score and get a personalised strategy roadmap from Armely in under 3 minutes.</p>
                            <div class="stats-row">
                                <div class="stat-item"><div class="stat-n">12</div><div class="stat-l">Questions</div></div>
                                <div class="stat-item"><div class="stat-n">5</div><div class="stat-l">Dimensions</div></div>
                                <div class="stat-item"><div class="stat-n">~3</div><div class="stat-l">Minutes</div></div>
                            </div>
                            <button class="btn-start-readiness" id="btnStartReadiness">Start Assessment &rarr;</button>
                            <button type="button" class="btn-dismiss-readiness" id="btnDismissReadiness">No thanks</button>
                        </div>
                    </div>

                    <!-- SCREEN: GAME -->
                    <div class="readiness-screen" id="rGame">
                        <div class="prog-wrap">
                            <div class="prog-track"><div class="prog-fill" id="rProgFill"></div></div>
                            <div class="prog-labs" id="rProgLabs"></div>
                        </div>
                        <div class="readiness-hud-chips">
                            <div class="hud-chip chip-s" id="rChipS">🔥 <span id="rSTxt">3 streak!</span></div>
                            <div class="hud-chip chip-combo" id="rChipC">⚡ <span id="rCTxt">2x Combo!</span></div>
                        </div>
                        <div class="phase-tag-box"><span class="phase-dot"></span><span id="rPhaseLbl">-</span></div>
                        <div class="q-card-box">
                            <div class="q-meta" id="rQMeta">Question 1 of 12</div>
                            <h3 class="q-text" id="rQText">-</h3>
                            <p class="q-context" id="rQCtx">-</p>
                            <div class="opts-grid" id="rOptsGrid"></div>
                            <div class="insight-box" id="rInsightBox">
                                <div class="ins-lbl">💡 Armely Insight</div>
                                <span id="rInsTxt"></span>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button class="btn-next-readiness" id="rBtnNext">Continue &rarr;</button>
                        </div>
                    </div>

                    <!-- SCREEN: CAPTURE -->
                    <div class="readiness-screen" id="rCapture">
                        <div class="cap-card-box text-center">
                            <div class="cap-score-preview p-3 mb-4 d-flex align-items-center justify-content-center">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm mr-3" style="width:50px; height:50px; font-size:24px;">🔒</div>
                                <div class="text-left">
                                    <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:1px;">Assessment Complete</div>
                                    <div id="rCapScoreHint" style="font-weight:800; font-size:18px; color:var(--armely-blue);">Unlock your results &rarr;</div>
                                </div>
                            </div>
                            
                            <h2 class="cap-h mb-3">Where should we send your results & strategy plan?</h2>
                            <p class="cap-p mb-4 px-lg-5">Enter your details to reveal your personalized AI data roadmap &mdash; plus tips from the Armely team.</p>

                            <form id="rCaptureForm" class="text-left mx-auto" style="max-width:480px;">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>First Name*</label>
                                        <input type="text" id="rfFirst" class="form-control" required placeholder="e.g. Jane">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Last Name</label>
                                        <input type="text" id="rfLast" class="form-control" placeholder="e.g. Smith">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Work Email*</label>
                                    <input type="email" id="rfEmail" class="form-control" required placeholder="jane.smith@company.com">
                                    <small id="rEmailError" class="text-danger mt-1" style="display:none; font-weight:600;">Please use a valid work email.</small>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Company</label>
                                        <input type="text" id="rfCompany" class="form-control" placeholder="Acme Inc">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Your Role</label>
                                        <select id="rfRole" class="form-control">
                                            <option value="" disabled selected>Select...</option>
                                            <option>C-Suite / Executive</option>
                                            <option>Data & Analytics Leader</option>
                                            <option>Data Engineer / Architect</option>
                                            <option>Product / Technology Leader</option>
                                            <option>Operations / Strategy</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-block text-white mt-3" id="rBtnReveal">Reveal My Score & Roadmap &rarr;</button>
                                <p class="text-center mt-3 small text-muted font-italic">
                                    <i class="fa fa-lock mr-1"></i> We respect your privacy. No spam &mdash; ever.
                                </p>
                            </form>
                        </div>
                    </div>

                    <!-- SCREEN: RESULTS -->
                    <div class="readiness-screen" id="rResults">
                        <div class="res-hero-box text-center">
                            <div class="res-ring-wrap mx-auto mb-4">
                                <svg width="140" height="140" viewBox="0 0 160 160">
                                    <circle cx="80" cy="80" r="66" fill="none" stroke="#e2e8f0" stroke-width="12"/>
                                    <circle id="rRingArc" cx="80" cy="80" r="66" fill="none" stroke="#1E62AD" stroke-width="12" stroke-linecap="round" stroke-dasharray="415" stroke-dashoffset="415" transform="rotate(-90 80 80)"/>
                                </svg>
                                <div class="res-ring-inner">
                                    <span class="res-ring-num" id="rRingNum">0</span><span class="res-ring-total">/100</span>
                                </div>
                            </div>
                            <div class="badge-tier mb-3 mx-auto" id="rTierChip">-</div>
                            <h2 class="res-t mb-2" id="rResT">-</h2>
                            <p class="res-s text-muted mx-auto mb-4" style="max-width:500px;" id="rResS">-</p>
                        </div>
                        
                        <div class="dim-grid-box row mx-0 mb-4" id="rDimGrid"></div>

                        <div class="cta-box">
                            <h3>Let's Build Your Roadmap Together</h3>
                            <p>Armely works end-to-end &mdash; from strategy to AI deployment.</p>
                            <a href="{{ route('contact') }}" class="btn-cta">Book a Strategy Session &rarr;</a>
                        </div>
                        <button class="btn-reset-light btn-block" onclick="location.reload()">Reset Assessment & Start Over</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Trigger Button (appears as a pop-up ad) -->
<div class="ai-readiness-trigger" id="aiReadinessTrigger">
    <div class="trigger-close" id="closeTrigger">&times;</div>
    <div class="trigger-content">
        <div class="trigger-icon">🧠</div>
        <div class="trigger-text">
            <strong>Check Your AI Readiness</strong>
            <span>Get your 2026 data strategy score now!</span>
        </div>
    </div>
</div>

<style>
/* CSS Variables for better control */
:root {
  --armely-blue: #1E62AD;
  --armely-teal: #0891b2;
  --armely-ink: #0d1f3c;
  --armely-bg: #f4f8fd;
}

/* Floating Trigger */
.ai-readiness-trigger {
    position: fixed;
    bottom: 20px;
    left: 20px; /* Moved to left to avoid ScrollUp and Chat overlap */
    width: 300px;
    background: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-radius: 15px;
    border-left: 5px solid var(--armely-blue);
    z-index: 100000; /* Super high z-index */
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 15px;
    display: none; /* Shown via JS */
    animation: slideInUp 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}
@keyframes slideInUp {
    from { transform: translateY(100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.ai-readiness-trigger:hover { transform: translateY(-5px); }
.trigger-close {
    position: absolute;
    top: -10px;
    left: -10px;
    background: #333;
    color: white;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: 2px solid white;
}
.trigger-content { display: flex; align-items: center; }
.trigger-icon { font-size: 32px; margin-right: 15px; }
.trigger-text strong { display: block; font-size: 14px; color: var(--armely-ink); }
.trigger-text span { font-size: 12px; color: #666; }

/* Modal Content Styling */
.readiness-modal-content {
    border-radius: 24px;
    overflow: hidden;
    border: none;
    box-shadow: 0 24px 60px rgba(13, 31, 60, 0.2);
}

.readiness-head {
    padding: 16px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 6px;
}

.readiness-logo { max-height: 40px; }

.readiness-kicker {
    font-size: 10px;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
}

.close-readiness {
    position: absolute;
    top: 12px;
    right: 14px;
    z-index: 10;
    font-size: 24px;
    color: #64748b;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    width: 34px;
    height: 34px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    outline: none !important;
}

.close-readiness:hover { color: var(--armely-blue); border-color: #bfdbfe; }

.readiness-wrap {
    min-height: 500px;
    background: #ffffff;
    position: relative;
    font-family: 'Inter', sans-serif;
}

/* Screens */
.readiness-screen { display: none; padding: 40px; }
.readiness-screen.on { display: block; animation: fadeInReady 0.4s ease; }
@keyframes fadeInReady { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }

/* Score HUD */
.readiness-score-hud {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #eef4fb;
    padding: 5px 15px;
    border-radius: 50px;
    border: 1px solid #d0e1f5;
    z-index: 5;
}
.score-lbl { font-size: 10px; text-transform: uppercase; color: #777; margin-right: 5px; }
.score-val { font-weight: 800; color: var(--armely-blue); font-size: 18px; }

/* Intro screen */
.intro-hero-box {
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
    padding: 24px;
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 12px 28px rgba(30, 98, 173, 0.08);
}

.intro-h { font-weight: 850; font-size: 42px; margin-top: 16px; line-height: 1.1; color: var(--armely-ink); }
.intro-h .hl { color: var(--armely-blue); }
.intro-p { font-size: 16px; color: #475569; max-width: 640px; margin: 14px auto 4px; line-height: 1.6; }
.big-emoji {
    font-size: 44px;
    display: inline-flex;
    width: 82px;
    height: 82px;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at 35% 35%, #ffd1eb 0%, #f9a8d4 45%, #f472b6 100%);
    box-shadow: 0 10px 22px rgba(244, 114, 182, 0.25);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(110px, 1fr));
    gap: 12px;
    margin: 26px 0;
}

.stat-item {
    background: #ffffff;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    padding: 12px 8px;
    box-shadow: 0 6px 16px rgba(30, 98, 173, 0.08);
}

.stat-item .stat-n { font-weight: 800; font-size: 40px; line-height: 1; color: var(--armely-blue); }
.stat-item .stat-l { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-top: 4px; }
.btn-start-readiness {
    background: linear-gradient(135deg, #2f67b4 0%, #1E62AD 55%, #155191 100%);
    color: white;
    border: none;
    padding: 17px 44px;
    border-radius: 14px;
    font-weight: 800;
    font-size: 18px;
    box-shadow: 0 12px 24px rgba(30, 98, 173, 0.28);
    transition: all 0.2s ease;
}

.btn-start-readiness:hover { transform: translateY(-2px); box-shadow: 0 16px 28px rgba(30, 98, 173, 0.34); }

.btn-dismiss-readiness {
    margin-top: 14px;
    background: transparent;
    border: none;
    color: #6b7280;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-dismiss-readiness:hover { color: #334155; background: #f1f5f9; }

/* Game Elements */
.prog-wrap { margin-bottom: 25px; }
.prog-track { height: 6px; background: #eee; border-radius: 10px; overflow: hidden; margin-bottom: 8px; }
.prog-fill { height: 100%; width: 0%; background: linear-gradient(90deg, var(--armely-blue), var(--armely-teal)); transition: width 0.3s; }
.prog-labs { display: flex; justify-content: space-between; font-size: 9px; color: #999; text-transform: uppercase; }
.prog-labs span.active { color: var(--armely-blue); font-weight: 700; }
.prog-labs span.done { color: #16a34a; }

.q-card-box { background: #f9fbff; border-radius: 15px; padding: 25px; border: 1px solid #e1e8f5; }
.q-meta { color: #888; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; }
.q-text { font-weight: 700; font-size: 20px; margin-bottom: 10px; line-height: 1.3; color: var(--armely-ink); }
.q-context { font-size: 14px; color: #555; margin-bottom: 20px; }

.opts-grid { display: grid; gap: 10px; }
.opt-btn {
    background: white;
    border: 2px solid #e1e8f5;
    padding: 12px 18px;
    border-radius: 10px;
    text-align: left;
    display: flex;
    align-items: center;
    transition: 0.2s;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    outline: none !important;
}
.opt-btn:hover:not(:disabled) { border-color: var(--armely-blue); background: #f4f8fd; }
.opt-btn.sel-correct { border-color: #16a34a; background: #e8f7ed; }
.opt-btn.sel-partial { border-color: #d97706; background: #fffcf0; }
.opt-btn.sel-wrong { border-color: #dc2626; background: #fef2f2; }
.opt-btn:disabled { cursor: default; }
.opt-key {
    width: 24px; height: 24px; border: 1px solid #ddd;
    border-radius: 5px; display: inline-flex; align-items: center; justify-content: center;
    margin-right: 12px; font-weight: 700; font-size: 12px; color: #777; flex-shrink: 0;
}

.insight-box { margin-top: 15px; background: #e8f1fb; padding: 12px; border-radius: 8px; font-size: 13px; border-left: 3px solid var(--armely-blue); display: none; }
.ins-lbl { font-size: 10px; font-weight: 700; color: var(--armely-blue); margin-bottom: 3px; text-transform: uppercase; }

.btn-next-readiness { display: none; background: var(--armely-blue); color: white; border: none; padding: 10px 30px; border-radius: 8px; font-weight: 600; }

/* Results */
.res-ring-wrap { width: 140px; height: 140px; position: relative; }
.res-ring-inner { position: absolute; top:0; left:0; width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.res-ring-num { font-size: 40px; font-weight: 800; color: var(--armely-blue); line-height: 1; }
.res-ring-total { font-size: 14px; color: #999; }
.badge-tier { display: inline-block; padding: 5px 15px; border-radius: 50px; font-weight: 700; font-size: 14px; }

.dim-card { background: #f8f9fa; border: 1px solid #eee; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
.dim-name { font-weight: 600; font-size: 13px; margin-bottom: 8px; }
.dim-bar-wrap { height: 6px; background: #e9ecef; border-radius: 10px; overflow: hidden; }
.dim-bar-fill { height: 100%; background: var(--armely-blue); border-radius: 10px; transition: width 1s ease-in-out; }
.dim-pct { font-weight: 700; color: var(--armely-blue); }

/* Results Screen Theming */
#rResults .res-t { font-weight: 850; color: var(--armely-ink); font-size: 28px; }
#rResults .res-s { color: #64748b; font-size: 15px; }

.cta-box { 
    text-align: center; 
    padding: 35px 25px !important; 
    border-radius: 16px; 
    color: #ffffff !important; 
    margin-bottom: 20px;
    background: linear-gradient(135deg, #1E62AD, #0891b2);
    box-shadow: 0 10px 25px rgba(30, 98, 173, 0.2);
}
.cta-box h3 { color: #ffffff !important; font-weight: 800; margin-bottom: 12px; font-size: 24px; }
.cta-box p { color: rgba(255, 255, 255, 0.9) !important; margin-bottom: 25px; font-size: 15px; }
.cta-box .btn-cta {
    background: #ffffff !important;
    color: var(--armely-blue) !important;
    border: none !important;
    padding: 12px 30px !important;
    font-weight: 800 !important;
    border-radius: 10px !important;
    display: inline-block;
    transition: transform 0.2s ease;
    text-decoration: none !important;
}
.cta-box .btn-cta:hover { transform: scale(1.05); background: #f8fafc !important; }

.btn-reset-light {
    color: #94a3b8 !important;
    font-size: 12px !important;
    text-decoration: underline !important;
    background: transparent !important;
    border: none !important;
    padding: 10px !important;
    transition: color 0.2s;
}
.btn-reset-light:hover { color: var(--armely-blue) !important; }

/* Capture Screen Enhancements */
#rCapture .cap-card-box { padding: 10px 0; }
#rCapture .cap-score-preview { 
    background: #f0f7ff; 
    border: 1px dashed #bdd7f7; 
    border-radius: 12px !important; 
}
#rCapture .cap-h { font-size: 30px; font-weight: 850; letter-spacing: -0.5px; line-height: 1.2; color: var(--armely-ink); }
#rCapture .cap-p { color: #64748b; font-size: 15px; line-height: 1.5; }

#rCaptureForm .form-group { margin-bottom: 18px; }
#rCaptureForm .form-control {
    height: 52px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.2s ease;
    background: #f8fafc;
    color: var(--armely-ink);
}
#rCaptureForm .form-control::placeholder { color: #94a3b8; font-weight: 400; }
#rCaptureForm .form-control:focus {
    border-color: var(--armely-blue);
    background: white;
    box-shadow: 0 0 0 4px rgba(30, 98, 173, 0.1);
    outline: none;
}
#rCaptureForm label {
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
#rCaptureForm select.form-control {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 18px;
}
#rBtnReveal {
    height: 62px;
    border-radius: 14px;
    background: var(--armely-blue);
    border: none;
    font-size: 19px;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(30, 98, 173, 0.25);
    transition: all 0.3s ease;
}
#rBtnReveal:hover {
    background: #154d8a;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(30, 98, 173, 0.35);
}

/* Responsive */
@media (max-width: 576px) {
    .ai-readiness-trigger { width: calc(100% - 40px); }
    .intro-hero-box { padding: 18px 14px; }
    .intro-h { font-size: 32px; }
    .intro-p { font-size: 15px; }
    .stat-item .stat-n { font-size: 30px; }
    .stat-item .stat-l { font-size: 10px; }
    .btn-start-readiness { font-size: 17px; padding: 14px 26px; }
    .readiness-screen { padding: 25px; }
}
</style>

<script>
(function() {
    const KEY_COMPLETED = 'aiReadinessCompleted';
    const KEY_DISMISSED_FOREVER = 'aiReadinessDismissedForever';
    const KEY_NEXT_ELIGIBLE_AT = 'aiReadinessNextEligibleAt';
    const KEY_WEEKLY_IMPRESSIONS = 'aiReadinessWeeklyImpressions';
    const KEY_SESSION_IMPRESSIONS = 'aiReadinessSessionImpressions';
    const KEY_SESSION_PAGE_VIEWS = 'aiReadinessSessionPageViews';
    const THIRTY_DAYS_MS = 30 * 24 * 60 * 60 * 1000;
    const SEVEN_DAYS_MS = 7 * 24 * 60 * 60 * 1000;

    // ── DATA ──────────────────────────────────────────────────────────────────
    const PHASES = ["Collection", "Quality", "Infra", "Governance", "AI Ready"];
    const QUESTIONS = [
        { ph: "Collection", pi: 0, q: "When your team needs data, how easy is it to access?", c: "Ease of access defines readiness.", o: ["Locked in silos/emails", "Takes real effort/time", "Mostly accessible", "Instant self-serve"], ins: "Self-serve access is key. Armely builds centralized platforms for direct governed access." },
        { ph: "Collection", pi: 0, q: "How consistently do you capture customer touchpoints?", c: "Gaps in capture mean gaps in AI understanding.", o: ["Ad-hoc/Manual", "Some systems track", "Most automated", "End-to-end automated"], ins: "Armely helps build event-driven pipelines to capture every meaningful signal." },
        { ph: "Quality", pi: 1, q: "If you pulled critical data now, what would it look like?", c: "Inaccurate data leads to underperforming AI.", o: ["Gaps everywhere", "Known inconsistencies", "Mostly clean", "Validated & Monitored"], ins: "We target <5% null rates for AI readiness using automated profiling." },
        { ph: "Quality", pi: 1, q: "How do you handle duplicate records across systems?", c: "Duplicates silently corrupt model training.", o: ["No process", "Manual cleanup", "Some logic exists", "Automated entity resolution"], ins: "Entity resolution is one of the highest-ROI investments before AI." },
        { ph: "Infra", pi: 2, q: "How current is the data powering your decisions?", c: "Stale data means outdated AI logic.", o: ["Monthly or older", "Weekly batch", "Daily refreshes", "Real-time streaming"], ins: "Daily-fresh is a baseline; real-time is for fraud/personalisation. Armely builds both." },
        { ph: "Infra", pi: 2, q: "Could your infra handle 10x today's volume?", c: "Scalability is a ceiling for AI growth.", o: ["No - would break", "With manual effort", "Yes - cloud elastic", "Auto-scaling built-in"], ins: "Scalability separates PoCs from products. We design for 10x from day one." },
        { ph: "Governance", pi: 3, q: "Is your data catalogued and ownership defined?", c: "Without a catalogue, AI projects stall for weeks.", o: ["No - tribal knowledge", "Partially documented", "Exists but unmaintained", "Full catalogue & lineage"], ins: "A data catalogue is the biggest accelerator for AI projects." },
        { ph: "Governance", pi: 3, q: "How prepared are you for GDPR/CCPA regulations?", c: "AI at scale requires rigorous privacy controls.", o: ["Informal policies", "Inconsistent compliance", "Mostly compliant", "Fully audited & automated"], ins: "Privacy-by-design builds trust. We embed masking and consent from day one." },
        { ph: "AI Ready", pi: 4, q: "Is there clear leadership buy-in for AI goals?", c: "Change management is usually the real bottleneck.", o: ["No backing", "Internal champion only", "Executive budget/silos", "Full C-suite commitment"], ins: "Alignment is key. We run workshops to create shared vision from the start." },
        { ph: "AI Ready", pi: 4, q: "Do you have labeled ground truth data for your use case?", c: "AI needs examples; without them, you're in the dark.", o: ["No labels", "Historical proxies", "Small dataset (<5k)", "Rich curated data"], ins: "Labeling is 80% of the work. We use LLMs to auto-label at scale." },
        { ph: "AI Ready", pi: 4, q: "What's the plan to keep models performing after launch?", c: "Accuracy erodes silently as data drifts.", o: ["No plan", "Manual reviews", "Basic dashboards", "Full MLOps automation"], ins: "MLOps separates demos from products. We build retraining pipelines that keep models performing." },
        { ph: "AI Ready", pi: 4, q: "How clearly defined is the business problem for AI?", c: "Answering the wrong question is the #1 failure mode.", o: ["Goal-less direction", "No prioritized use cases", "Clear metrics set", "Prioritized ROI portfolio"], ins: "We map data assets to business outcomes before writing code." }
    ];

    // ── STATE ─────────────────────────────────────────────────────────────────
    let G = {
        qi: 0, score: 0, streak: 0, 
        pScores: [0,0,0,0,0], pMax: [0,0,0,0,0],
        answered: false, submitting: false
    };

    // ── DOM HELPERS ───────────────────────────────────────────────────────────
    const el = (id) => document.getElementById(id);
    const showScreen = (id) => {
        document.querySelectorAll('.readiness-screen').forEach(s => s.classList.remove('on'));
        el(id).classList.add('on');
    };

    // ── LOGIC ─────────────────────────────────────────────────────────────────
    function renderQ() {
        const q = QUESTIONS[G.qi];
        el('rProgFill').style.width = ((G.qi / QUESTIONS.length) * 100) + '%';
        
        let labsHtml = '';
        PHASES.forEach((p, i) => {
            const cls = i < q.pi ? 'done' : (i === q.pi ? 'active' : '');
            labsHtml += `<span class="${cls}">${p}</span>`;
        });
        el('rProgLabs').innerHTML = labsHtml;

        el('rPhaseLbl').innerText = q.ph;
        el('rQMeta').innerText = `Question ${G.qi + 1} of ${QUESTIONS.length}`;
        el('rQText').innerText = q.q;
        el('rQCtx').innerText = q.c;

        let optsHtml = '';
        const keys = ['A','B','C','D'];
        q.o.forEach((opt, idx) => {
            optsHtml += `
                <button class="opt-btn" data-pts="${idx}">
                    <span class="opt-key">${keys[idx]}</span>
                    <span>${opt}</span>
                </button>
            `;
        });
        el('rOptsGrid').innerHTML = optsHtml;
        el('rOptsGrid').querySelectorAll('.opt-btn').forEach(btn => {
            btn.onclick = () => selectOpt(btn);
        });

        el('rInsightBox').style.display = 'none';
        el('rBtnNext').style.display = 'none';
        G.answered = false;
    }

    function selectOpt(btn) {
        if (G.answered) return;
        G.answered = true;

        const pts = parseInt(btn.getAttribute('data-pts'));
        const q = QUESTIONS[G.qi];

        G.pScores[q.pi] += pts;
        G.pMax[q.pi] += 3;
        G.score += pts * 10;
        
        // Update HUD
        el('readinessScoreVal').innerText = G.score;
        
        // Animate selection
        const btns = el('rOptsGrid').querySelectorAll('.opt-btn');
        btns.forEach(b => {
            b.disabled = true;
            if (b === btn) {
                if (pts === 3) b.classList.add('sel-correct');
                else if (pts === 2) b.classList.add('sel-partial');
                else b.classList.add('sel-wrong');
            } else {
                b.style.opacity = '0.5';
            }
        });

        // Show Insight
        el('rInsTxt').innerText = q.ins;
        el('rInsightBox').style.display = 'block';
        el('rBtnNext').style.display = 'inline-block';
        if (G.qi === QUESTIONS.length - 1) el('rBtnNext').innerText = 'Finish Assessment';
    }

    async function submitLeads() {
        if (G.submitting) return;
        G.submitting = true;
        el('rBtnReveal').innerText = 'Submitting...';
        el('rBtnReveal').disabled = true;

        const data = {
            first: el('rfFirst').value,
            last: el('rfLast').value,
            email: el('rfEmail').value,
            company: el('rfCompany').value,
            role: el('rfRole').value,
            score: G.score,
            pScores: G.pScores,
            _token: '{{ csrf_token() }}'
        };

        try {
            const resp = await fetch('{{ route('data-readiness.submit') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await resp.json();
            if (res.success) {
                // Mark as completed so user doesn't see popup again
                localStorage.setItem('aiReadinessCompleted', 'true');
                showResultsScreen();
            } else {
                alert(res.message || 'Error submitting results.');
            }
        } catch (err) {
            console.error(err);
            // Fallback: show results even if DB fail so user isn't stuck
            // Mark as completed
            localStorage.setItem('aiReadinessCompleted', 'true');
            showResultsScreen();
        } finally {
            G.submitting = false;
            el('rBtnReveal').innerText = 'Reveal My Score & Roadmap &rarr;';
            el('rBtnReveal').disabled = false;
        }
    }

    function showResultsScreen() {
        const pct = Math.round((G.score / (QUESTIONS.length * 30)) * 100);
        showScreen('rResults');
        el('readinessScoreHud').style.display = 'none';

        // Animate Circle
        const arc = el('rRingArc');
        arc.style.strokeDashoffset = 415 - (pct / 100 * 415);
        
        // Counter animate
        let start = 0;
        const interval = setInterval(() => {
            if (start >= pct) clearInterval(interval);
            el('rRingNum').innerText = start;
            start++;
        }, 20);

        // Tier evaluation
        const tier = pct >= 75 ? {l:'AI Vanguard', c:'#16a34a', bg:'#e8f7ed', t:`You're an AI leader, ${el('rfFirst').value}!`, s:"Your data foundations are world-class. Armely can help you sharpen your models for maximum ROI."}
                   : pct >= 50 ? {l:'AI Building',  c:'#1E62AD', bg:'#e8f1fb', t:"Strong foundations, targeted gaps.", s:"You're on the right track. A few focused engineering sprints will get you ready for production AI."}
                   : {l:'Getting Ready', c:'#d97706', bg:'#fffcf0', t:"Prime for transformation.", s:"The right foundations now will pay off massively. Our strategy team can help bridge these gaps."};

        const chip = el('rTierChip');
        chip.innerText = tier.l;
        chip.style.color = tier.c;
        chip.style.backgroundColor = tier.bg;
        el('rResT').innerText = tier.t;
        el('rResS').innerText = tier.s;

        // Dimensions
        let dimHtml = '';
        G.pScores.forEach((s, i) => {
            const m = G.pMax[i];
            const p = Math.round((s / m) * 100);
            dimHtml += `
                <div class="col-6 mb-3">
                    <div class="dim-card h-100">
                        <div class="d-flex justify-content-between">
                            <span class="dim-name">${PHASES[i]}</span>
                            <span class="dim-pct">${p}%</span>
                        </div>
                        <div class="dim-bar-wrap">
                            <div class="dim-bar-fill" style="width:${p}%"></div>
                        </div>
                    </div>
                </div>
            `;
        });
        el('rDimGrid').innerHTML = dimHtml;
    }

    function dismissReadinessForever() {
        localStorage.setItem(KEY_DISMISSED_FOREVER, 'true');
        $('#aiReadinessModal').modal('hide');
        const trigger = el('aiReadinessTrigger');
        if (trigger) {
            trigger.style.display = 'none';
        }
    }

    function getWeeklyImpressions() {
        const now = Date.now();
        let raw = [];
        try {
            raw = JSON.parse(localStorage.getItem(KEY_WEEKLY_IMPRESSIONS) || '[]');
        } catch (_) {
            raw = [];
        }
        const filtered = Array.isArray(raw)
            ? raw.filter(ts => Number.isFinite(ts) && now - ts < SEVEN_DAYS_MS)
            : [];
        localStorage.setItem(KEY_WEEKLY_IMPRESSIONS, JSON.stringify(filtered));
        return filtered;
    }

    function recordImpression() {
        const sessionCount = parseInt(sessionStorage.getItem(KEY_SESSION_IMPRESSIONS) || '0', 10) + 1;
        sessionStorage.setItem(KEY_SESSION_IMPRESSIONS, String(sessionCount));

        const weekly = getWeeklyImpressions();
        weekly.push(Date.now());
        localStorage.setItem(KEY_WEEKLY_IMPRESSIONS, JSON.stringify(weekly));
    }

    function snoozeReadiness(days = 30) {
        const nextAt = Date.now() + (days * 24 * 60 * 60 * 1000);
        localStorage.setItem(KEY_NEXT_ELIGIBLE_AT, String(nextAt));
    }

    function isEligibleForAutoOpen() {
        if (localStorage.getItem(KEY_COMPLETED) === 'true') return false;
        if (localStorage.getItem(KEY_DISMISSED_FOREVER) === 'true') return false;

        const nextEligibleAt = parseInt(localStorage.getItem(KEY_NEXT_ELIGIBLE_AT) || '0', 10);
        if (nextEligibleAt && Date.now() < nextEligibleAt) return false;

        const sessionImpressions = parseInt(sessionStorage.getItem(KEY_SESSION_IMPRESSIONS) || '0', 10);
        if (sessionImpressions >= 1) return false;

        if (getWeeklyImpressions().length >= 2) return false;

        return true;
    }

    function openReadinessModal(trackImpression = false) {
        const modal = el('aiReadinessModal');
        if (!modal) return;
        $(modal).modal('show');
        if (trackImpression) {
            recordImpression();
        }
    }

    function closeAndSnoozeReadiness() {
        $('#aiReadinessModal').modal('hide');
        snoozeReadiness(30);

        const trigger = el('aiReadinessTrigger');
        if (trigger && localStorage.getItem(KEY_DISMISSED_FOREVER) !== 'true') {
            trigger.style.display = 'block';
        }
    }

    // ── INITIALIZE ───────────────────────────────────────────────────────────
    function initReadiness() {
        const modal = el('aiReadinessModal');
        const trigger = el('aiReadinessTrigger');

        const completed = localStorage.getItem(KEY_COMPLETED) === 'true';
        const dismissedForever = localStorage.getItem(KEY_DISMISSED_FOREVER) === 'true';

        // Never disturb completed or permanently dismissed users
        if (completed || dismissedForever) {
            if (trigger) {
                trigger.style.display = 'none';
            }
            return;
        }

        // Keep a lightweight manual trigger available
        if (trigger) {
            trigger.style.display = 'block';
        }

        // Track in-session page views for engagement rule
        const pageViews = parseInt(sessionStorage.getItem(KEY_SESSION_PAGE_VIEWS) || '0', 10) + 1;
        sessionStorage.setItem(KEY_SESSION_PAGE_VIEWS, String(pageViews));

        // Engagement-based auto-open: 25s, 40% scroll, or second page in this session
        let autoOpened = false;
        const tryAutoOpen = () => {
            if (autoOpened) return;
            if (!isEligibleForAutoOpen()) return;
            autoOpened = true;
            openReadinessModal(true);
        };

        if (pageViews >= 2) {
            tryAutoOpen();
        }

        setTimeout(tryAutoOpen, 25000);

        const onScroll = () => {
            const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
            const viewport = window.innerHeight || document.documentElement.clientHeight || 1;
            const fullHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, viewport);
            const progress = (scrollTop + viewport) / fullHeight;
            if (progress >= 0.4) {
                tryAutoOpen();
                window.removeEventListener('scroll', onScroll);
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });

        // Trigger controls
        const triggerContent = trigger ? trigger.querySelector('.trigger-content') : null;
        if (triggerContent) {
            triggerContent.addEventListener('click', function(evt) {
                evt.preventDefault();
                openReadinessModal(false);
            });
        }

        const closeTriggerBtn = el('closeTrigger');
        if (closeTriggerBtn) {
            closeTriggerBtn.addEventListener('click', function(evt) {
                evt.preventDefault();
                evt.stopPropagation();
                if (trigger) {
                    trigger.style.display = 'none';
                }
                snoozeReadiness(30);
            });
        }

        if (modal) {
            $(modal).on('show.bs.modal', function() {
                showScreen('rIntro');
                el('readinessScoreHud').style.display = 'none';
            });
        }

        // Modal Logic
        el('btnStartReadiness').onclick = () => {
            showScreen('rGame');
            el('readinessScoreHud').style.display = 'block';
            renderQ();
        };

        const dismissBtn = el('btnDismissReadiness');
        if (dismissBtn) {
            dismissBtn.onclick = dismissReadinessForever;
        }

        const closeBtn = document.querySelector('#aiReadinessModal .close-readiness');
        if (closeBtn) {
            closeBtn.addEventListener('click', function(evt) {
                evt.preventDefault();
                closeAndSnoozeReadiness();
            });
        }

        el('rBtnNext').onclick = () => {
            G.qi++;
            if (G.qi < QUESTIONS.length) renderQ();
            else {
                const pct = Math.round((G.score / (QUESTIONS.length * 30)) * 100);
                el('rCapScoreHint').innerText = `Your score: ${pct}/100 — unlock report &rarr;`;
                showScreen('rCapture');
                el('readinessScoreHud').style.display = 'none';
            }
        };

        el('rBtnReveal').onclick = () => {
            const email = el('rfEmail').value;
            const first = el('rfFirst').value;
            if (!first || !email || !el('rCaptureForm').checkValidity()) {
                el('rCaptureForm').reportValidity();
                return;
            }

            // Simple work email check
            const personal = ['@gmail.','@yahoo.','@hotmail.','@outlook.','@icloud.'];
            if (personal.some(p => email.toLowerCase().includes(p))) {
                el('rEmailError').style.display = 'block';
                return;
            }
            el('rEmailError').style.display = 'none';
            submitLeads();
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReadiness);
    } else {
        initReadiness();
    }
})();
</script>