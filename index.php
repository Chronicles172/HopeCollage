<?php
// ============================================================
// index.php  —  Home page
// URL: /index.php  or  /
// ============================================================
require_once 'includes/layout.php';
layout_head('Home');
layout_nav('home');
?>


<!-- ── Decorative strip ──────────────────────────────────────── -->
<div style="background:linear-gradient(to bottom, var(--white) 0%, rgba(248,246,243,0.4) 100%);
            height:80px;
            border-bottom:1px solid rgba(11,31,58,0.08);
            box-shadow:0 2px 12px rgba(11,31,58,0.04)"></div>


<!-- ── Hero ──────────────────────────────────────────────── -->
<div class="hero">
  <div class="hero-kicker">Parent &amp; Student Registry</div>
  <h1>Welcome to <em>SchoolConnect</em></h1>
  <p>A seamless platform connecting parents, students, and school administration — from registration to attendance.</p>
  <div class="hero-btns">
    <a class="btn-primary"  href="register.php">Register Now</a>
    <a class="btn-outline"  href="attendance.php">Sign Attendance</a>
  </div>
</div>

<!-- ── Features grid ─────────────────────────────────────── -->
<div class="section">
  <div class="section-header">
    <div class="section-kicker">What you can do</div>
    <h2 class="section-title">Everything in one place</h2>
    <p class="section-sub">From registration to QR attendance — SchoolConnect keeps parents and administration in sync.</p>
  </div>
  <div class="features-grid">
    <a class="feat-card" href="register.php">
      <div class="feat-icon gold">👨‍👩‍👧</div>
      <div class="feat-title">Parent &amp; Student Registration</div>
      <div class="feat-desc">Register your child with full parent and student details, including photos.</div>
    </a>
    <a class="feat-card" href="attendance.php">
      <div class="feat-icon green">✅</div>
      <div class="feat-title">Attendance &amp; Visitation</div>
      <div class="feat-desc">Sign attendance for PTA meetings, visitation days, and walk-in visits.</div>
    </a>
    <a class="feat-card" href="admin.php">
      <div class="feat-icon navy">🛡️</div>
      <div class="feat-title">Admin Dashboard</div>
      <div class="feat-desc">View all parents, wards, event attendance history, and schedule new events.</div>
    </a>
    <div class="feat-card" onclick="openQR()">
      <div class="feat-icon blue">📲</div>
      <div class="feat-title">QR Code Access</div>
      <div class="feat-desc">Generate a QR code that parents can scan to jump straight to attendance signing.</div>
    </div>
  </div>
</div>

<!-- ── Upcoming events full section ──────────────────────── -->
<div style="background:var(--navy);padding:3rem clamp(1rem,5vw,2.5rem)">
  <div style="max-width:1100px;margin:0 auto">
    <div style="text-align:center;margin-bottom:2rem">
      <div class="section-kicker" style="color:var(--gold-light)">Calendar</div>
      <h2 class="section-title" style="color:var(--white)">Upcoming Events</h2>
    </div>
    <div class="events-grid" id="homeEvents">
      <p style="color:rgba(255,255,255,.6);text-align:center">Loading…</p>
    </div>

    <!-- Past events (within 30 days) -->
    <div id="pastEventsWrap" style="margin-top:2.5rem">
      <button onclick="togglePastEvents()" id="pastToggleBtn"
        style="background:none;border:1.5px solid rgba(255,255,255,.25);color:rgba(255,255,255,.7);
               padding:.5rem 1.25rem;border-radius:20px;cursor:pointer;font-size:.85rem;
               display:flex;align-items:center;gap:.5rem;margin:0 auto 1.25rem">
        <span id="pastToggleIcon">▾</span> Recent Past Events
      </button>
      <div class="events-grid" id="pastEvents" style="display:none">
        <p style="color:rgba(255,255,255,.4);text-align:center">Loading…</p>
      </div>
    </div>
  </div>
</div>

<!-- ── QR Modal ───────────────────────────────────────────── -->
<div class="modal-overlay" id="qrModal" onclick="closeModalOnBackdrop(event,'qrModal')">
  <div class="modal" style="text-align:center">
    <button class="modal-close" onclick="closeModal('qrModal')">✕</button>
    <h3>Attendance QR Code</h3>
    <p style="font-size:13px;color:var(--text-muted);margin-bottom:1rem">
      Parents scan this to go directly to the Attendance / Visitation page.
    </p>
    <div id="qrTarget"></div>
    <p id="qrUrl" style="font-size:11px;color:var(--text-muted);margin-top:.5rem;word-break:break-all"></p>
    <div class="modal-actions" style="justify-content:center">
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="downloadQR()">⬇ Download PNG</button>
      <button class="btn-primary" onclick="closeModal('qrModal')">Done</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  loadHomeEvents();
});

function openQR() {
  var url = window.location.origin + window.location.pathname.replace('index.php','') + 'attendance.php';
  document.getElementById('qrUrl').textContent = url;
  openModal('qrModal');
  setTimeout(function(){ drawQR(url); }, 80);
}

function togglePastEvents() {
  var el   = document.getElementById('pastEvents');
  var icon = document.getElementById('pastToggleIcon');
  if (el.style.display === 'none') {
    el.style.display = '';
    icon.textContent = '▴';
  } else {
    el.style.display = 'none';
    icon.textContent = '▾';
  }
}
</script>

<?php layout_footer(); ?>