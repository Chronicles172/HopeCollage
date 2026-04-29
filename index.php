<?php
// ============================================================
// index.php  —  Home page
// URL: /index.php  or  /
// ============================================================
require_once 'includes/layout.php';
layout_head('Home');
layout_nav('home');
?>

<!-- ── Upcoming events strip ─────────────────────────────── -->
<div class="events-strip">
  <div class="events-strip-inner">
    <span class="events-strip-label">📅 Upcoming</span>
    <div id="stripEvents"><span class="event-pill">Loading events…</span></div>
  </div>
</div>

<!-- ── Hero ──────────────────────────────────────────────── -->
<div class="hero">
  <div class="hero-kicker">Parent &amp; Student Registry</div>
  <h1>Welcome to <em>SchoolConnect</em></h1>
  <p>A seamless platform connecting parents, students, and school administration — from registration to attendance.</p>
  <div class="hero-btns">
    <a class="btn-primary"  href="register.php">Register Now</a>
    <a class="btn-outline"  href="attendance.php">Sign Attendance</a>
  </div>
  <div class="stats-row">
    <div class="stat"><span class="stat-num" id="statParents">—</span><span class="stat-label">Parents</span></div>
    <div class="stat"><span class="stat-num" id="statStudents">—</span><span class="stat-label">Students</span></div>
    <div class="stat"><span class="stat-num" id="statEvents">—</span><span class="stat-label">Events</span></div>
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

<!-- ── Registry: Parents & Students ──────────────────────── -->
<div style="background:var(--cream);padding:clamp(2.5rem,7vw,5rem) clamp(1rem,5vw,2.5rem)">
  <div style="max-width:1100px;margin:0 auto">
    <div class="section-header">
      <div class="section-kicker">Registry</div>
      <h2 class="section-title">Parents &amp; Students</h2>
      <p class="section-sub">All registered families and their wards in the system.</p>
    </div>

    <!-- Search bar -->
    <div style="margin-bottom:1.5rem;max-width:420px">
      <input type="text" id="registrySearch" oninput="filterRegistry(this.value)"
        placeholder="🔍 Search by name, class, or phone…"
        style="width:100%;padding:.65rem 1.1rem;border:1.5px solid var(--border);
               border-radius:30px;font-size:.9rem;background:var(--white);
               outline:none;box-sizing:border-box;box-shadow:0 1px 4px rgba(11,31,58,.06)"/>
    </div>

    <div id="registryList" style="display:flex;flex-direction:column;gap:.85rem">
      <p style="color:var(--text-muted)">Loading…</p>
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
  loadHomeStats();
  loadHomeEvents();
  loadEventStrip();
  loadRegistry();
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
