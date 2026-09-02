<?php
// ============================================================
// parent_portal.php  —  Parent Portal (phone-based login)
// URL: /parent_portal.php
// ============================================================
require_once 'includes/layout.php';
layout_head('Parent Portal');
layout_nav('parent_portal');
?>

<!-- ── Parent Login Overlay ──────────────────────────────── -->
<div class="login-overlay" id="portalLoginOverlay">
  <div class="login-box" style="max-width:420px">
    <div style="text-align:center;margin-bottom:1.5rem">
      <div style="width:56px;height:56px;background:var(--gold);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto .75rem">👨‍👩‍👧</div>
      <h2 style="margin-bottom:.25rem">Parent Portal</h2>
      <p style="font-size:13px;color:var(--text-muted)">Enter the phone number you registered with.</p>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Registered Phone Number <span class="req">*</span></label>
      <input type="tel" id="portalPhone" placeholder="e.g. 0244 000 000"
        autocomplete="tel"
        onkeydown="if(event.key==='Enter') portalLogin()"/>
    </div>
    <button class="btn-primary" style="width:100%" onclick="portalLogin()">Access My Portal →</button>
    <a href="index.php" class="btn-outline"
       style="display:block;width:100%;margin-top:8px;text-align:center;color:var(--navy);border-color:var(--border)">
      Back to Home
    </a>
  </div>
</div>

<!-- ── Portal Dashboard (hidden until login) ─────────────── -->
<div id="portalDashboard" style="display:none">
  <div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <!-- Parent profile summary -->
      <div id="portalSidebarProfile" style="padding:1.5rem 1.25rem 1rem;border-bottom:1px solid rgba(255,255,255,.1);margin-bottom:1rem">
        <div id="portalAvatarWrap" style="width:52px;height:52px;border-radius:50%;background:var(--navy-light);border:3px solid var(--gold);display:flex;align-items:center;justify-content:center;color:var(--white);font-weight:700;font-size:1.1rem;margin-bottom:.75rem;overflow:hidden"></div>
        <div id="portalSidebarName" style="font-weight:600;color:var(--white);font-size:.95rem"></div>
        <div id="portalSidebarPhone" style="font-size:12px;color:rgba(255,255,255,.45);margin-top:2px"></div>
      </div>

      <div class="admin-sidebar-section">
        <div class="sidebar-link active" id="psb-overview"      onclick="portalTab('overview')">    <span class="icon">🏠</span> Overview</div>
        <div class="sidebar-link"        id="psb-events"        onclick="portalTab('events')">      <span class="icon">📅</span> School Events</div>
        <div class="sidebar-link"        id="psb-exeats"        onclick="portalTab('exeats')">      <span class="icon">🚪</span> Exeat Requests <span id="psb-pending-badge" style="display:none;background:var(--gold);color:var(--navy);font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;margin-left:auto"></span></div>
        <div class="sidebar-link"        id="psb-announcements" onclick="portalTab('announcements')"><span class="icon">📢</span> Announcements  <span id="psb-ann-badge"    style="display:none;background:var(--danger);color:var(--white);font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;margin-left:auto"></span></div>
        <div class="sidebar-link"        id="psb-new-exeat"     onclick="portalTab('new-exeat')">    <span class="icon">✏️</span> New Exeat</div>
      </div>

      <div style="padding:0 1.5rem;margin-top:auto;padding-top:2rem">
        <button class="btn-danger" style="width:100%" onclick="portalLogout()">Logout</button>
      </div>
    </aside>

    <!-- Main content -->
    <main class="admin-main">

      <!-- ── Tab: Overview ──────────────────────────────── -->
      <div class="admin-sub active" id="portal-overview">
        <div class="admin-header">
          <div>
            <h1 id="portalWelcomeTitle">Welcome</h1>
            <p style="color:var(--text-muted);font-size:14px;margin-top:4px" id="portalWelcomeSub">Loading your dashboard…</p>
          </div>
        </div>

        <!-- Quick stats -->
        <div class="stats-grid" style="margin-bottom:2rem" id="portalStats">
          <div class="stat-card"><span class="num" id="ps-wards">—</span><span class="lbl">Ward(s)</span></div>
          <div class="stat-card"><span class="num" id="ps-pending">—</span><span class="lbl">Pending Exeats</span></div>
          <div class="stat-card"><span class="num" id="ps-approved">—</span><span class="lbl">Approved Exeats</span></div>
          <div class="stat-card"><span class="num" id="ps-events">—</span><span class="lbl">Upcoming Events</span></div>
        </div>

        <!-- My Wards -->
        <div style="margin-bottom:2rem">
          <h3 style="font-family:var(--font-display);margin-bottom:1rem;color:var(--navy)">My Ward(s)</h3>
          <div id="portalWardsList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px"></div>
        </div>

        <!-- Latest announcements preview (up to 2) -->
        <div style="margin-bottom:2rem">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <h3 style="font-family:var(--font-display);color:var(--navy)">Latest Announcements</h3>
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:12px;padding:6px 14px"
              onclick="portalTab('announcements')">View All →</button>
          </div>
          <div id="portalAnnPreview"></div>
        </div>

        <!-- Next upcoming event -->
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <h3 style="font-family:var(--font-display);color:var(--navy)">Upcoming Events</h3>
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:12px;padding:6px 14px"
              onclick="portalTab('events')">View All →</button>
          </div>
          <div class="events-grid" id="portalEventsPreview"></div>
        </div>
      </div>

      <!-- ── Tab: School Events ─────────────────────────── -->
      <div class="admin-sub" id="portal-events">
        <div class="admin-header"><h1>School Events</h1></div>

        <div id="portalEventsTabs" style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap">
          <button class="portal-evt-tab active" data-filter="upcoming" onclick="filterPortalEvents('upcoming',this)">Upcoming</button>
          <button class="portal-evt-tab" data-filter="past" onclick="filterPortalEvents('past',this)">Past Events</button>
          <button class="portal-evt-tab" data-filter="all" onclick="filterPortalEvents('all',this)">All</button>
        </div>

        <div class="events-grid" id="portalAllEvents">
          <p style="color:var(--text-muted)">Loading events…</p>
        </div>
      </div>

      <!-- ── Tab: Exeat Requests ────────────────────────── -->
      <div class="admin-sub" id="portal-exeats">
        <div class="admin-header">
          <h1>My Exeat Requests</h1>
        </div>

        <!-- Status filter pills -->
        <div style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap">
          <button class="portal-evt-tab active" data-exfilter="all"      onclick="filterPortalExeats('all',this)">All</button>
          <button class="portal-evt-tab" data-exfilter="pending"         onclick="filterPortalExeats('pending',this)">⏳ Pending</button>
          <button class="portal-evt-tab" data-exfilter="approved"        onclick="filterPortalExeats('approved',this)">✅ Approved</button>
          <button class="portal-evt-tab" data-exfilter="declined"        onclick="filterPortalExeats('declined',this)">❌ Declined</button>
        </div>

        <div id="portalExeatList">
          <p style="color:var(--text-muted)">Loading exeat requests…</p>
        </div>
      </div>

      <!-- ── Tab: Announcements ─────────────────────────── -->
      <div class="admin-sub" id="portal-announcements">
        <div class="admin-header"><h1>Announcements</h1></div>
        <div id="portalAnnList">
          <p style="color:var(--text-muted)">Loading announcements…</p>
        </div>
      </div>

      <!-- ── Tab: New Exeat Request ──────────────────────── -->
      <div class="admin-sub" id="portal-new-exeat">
        <div class="admin-header"><h1>New Exeat Request</h1></div>

        <div class="form-card" style="max-width:640px">

          <!-- Ward select -->
          <div class="form-section-title">🎒 Select Ward</div>
          <div class="form-group" style="margin-bottom:1rem">
            <label>Ward <span class="req">*</span></label>
            <select id="pex_student_id">
              <option value="">— Select ward —</option>
            </select>
          </div>

          <div class="form-divider"></div>
          <div class="form-section-title">📋 Exeat Details</div>

          <div class="form-row">
            <div class="form-group">
              <label>Departure Date <span class="req">*</span></label>
              <input type="date" id="pex_depart_date"/>
            </div>
            <div class="form-group">
              <label>Departure Time <span class="req">*</span></label>
              <input type="time" id="pex_depart_time"/>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:1rem">
            <label>Expected Return Date <span class="req">*</span></label>
            <input type="date" id="pex_return_date"/>
          </div>

          <div class="form-group" style="margin-bottom:1.25rem">
            <label>Reason for Leaving Campus <span class="req">*</span></label>
            <textarea id="pex_reason" placeholder="Describe the reason for your ward leaving campus…" rows="4"></textarea>
          </div>

          <button class="btn-primary" style="width:100%" onclick="portalSubmitExeat()">Submit Exeat Request →</button>
        </div>

        <!-- Success state (shown after submit) -->
        <div id="pex-success" style="display:none;text-align:center;padding:3rem 1rem">
          <div style="font-size:3rem;margin-bottom:1rem">✅</div>
          <h3 style="color:var(--navy);margin-bottom:.5rem">Request Submitted!</h3>
          <p id="pex-success-msg" style="color:var(--text-muted);margin-bottom:1.5rem;font-size:14px"></p>
          <p style="font-size:13px;color:var(--text-muted);margin-bottom:1.5rem">The Head of Domestic Affairs will review your request and you will be notified of the decision.</p>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="portalNewExeatReset()">Submit Another</button>
            <button class="btn-primary" onclick="portalTab('exeats')">View My Exeats →</button>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
// ── State ─────────────────────────────────────────────────────
var _portalParent  = null;
var _portalData    = null;   // { wards, exeats, events, announcements }
var _exeatFilter   = 'all';
var _eventFilter   = 'upcoming';

// ── Login / Logout ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('portalLoginOverlay').style.display = 'flex';
});

function portalLogin() {
  var phone = val('portalPhone');
  if (!phone) { showToast('Please enter your phone number.', 'error'); return; }

  apiFetch('actions/fetch.php?action=parent_by_phone&phone=' + encodeURIComponent(phone)).then(function(r) {
    if (!r.success) { showToast(r.message || 'No account found with that phone number.', 'error'); return; }
    _portalParent = r.data;
    document.getElementById('portalLoginOverlay').style.display = 'none';
    document.getElementById('portalDashboard').style.display    = 'block';
    renderSidebarProfile(_portalParent);
    loadPortalData();
  });
}

function portalLogout() {
  _portalParent = null;
  _portalData   = null;
  document.getElementById('portalLoginOverlay').style.display = 'flex';
  document.getElementById('portalDashboard').style.display    = 'none';
  document.getElementById('portalPhone').value = '';
  portalTab('overview');
}

// ── Sidebar Profile ───────────────────────────────────────────
function renderSidebarProfile(p) {
  var wrap = document.getElementById('portalAvatarWrap');
  if (p.photo_path) {
    wrap.innerHTML = '<img src="' + p.photo_path + '" style="width:100%;height:100%;object-fit:cover"/>';
  } else {
    wrap.textContent = initials(p.first_name, p.last_name);
  }
  setText('portalSidebarName', p.first_name + ' ' + p.last_name);
  setText('portalSidebarPhone', p.phone);
}

// ── Load all portal data ──────────────────────────────────────
function loadPortalData() {
  if (!_portalParent) return;
  apiFetch('actions/fetch.php?action=portal_data&parent_id=' + _portalParent.id).then(function(r) {
    if (!r.success) { showToast('Could not load portal data.', 'error'); return; }
    _portalData = r.data;
    renderOverview();
    updateBadges();
  });
}

// ── Tabs ──────────────────────────────────────────────────────
function portalTab(tab) {
  document.querySelectorAll('.admin-sub').forEach(function(el) { el.classList.remove('active'); });
  document.querySelectorAll('[id^="psb-"]').forEach(function(el) { el.classList.remove('active'); });

  var main = document.getElementById('portal-' + tab);
  var sb   = document.getElementById('psb-' + tab);
  if (main) main.classList.add('active');
  if (sb)   sb.classList.add('active');

  if (tab === 'events')        renderPortalEvents();
  if (tab === 'exeats')        renderPortalExeats();
  if (tab === 'announcements') { renderPortalAnnouncements(); markAnnouncementsSeen(); }
  if (tab === 'new-exeat')     portalInitNewExeat();
}

// ── Announcement "seen" tracking (per parent, persisted locally) ──
function annSeenStorageKey() {
  return _portalParent ? ('sc_ann_seen_' + _portalParent.id) : null;
}

function getSeenAnnouncementIds() {
  var key = annSeenStorageKey();
  if (!key) return [];
  try {
    var raw = localStorage.getItem(key);
    return raw ? JSON.parse(raw) : [];
  } catch (e) { return []; }
}

function markAnnouncementsSeen() {
  if (!_portalData) return;
  var key = annSeenStorageKey();
  if (!key) return;
  var allIds = (_portalData.announcements || []).map(function(a){ return a.id; });
  try { localStorage.setItem(key, JSON.stringify(allIds)); } catch (e) {}
  // Badge disappears immediately — nothing left unseen
  var ab = document.getElementById('psb-ann-badge');
  if (ab) ab.style.display = 'none';
}

// ── Update sidebar badges ─────────────────────────────────────
function updateBadges() {
  if (!_portalData) return;

  // Pending exeats badge
  var pending = (_portalData.exeats || []).filter(function(e){ return e.status === 'pending'; }).length;
  var pb = document.getElementById('psb-pending-badge');
  if (pending > 0) { pb.textContent = pending; pb.style.display = 'inline-block'; }
  else             { pb.style.display = 'none'; }

  // Unseen announcements badge — only counts announcements not yet viewed
  var seenIds  = getSeenAnnouncementIds();
  var unseen   = (_portalData.announcements || []).filter(function(a){ return seenIds.indexOf(a.id) === -1; });
  var ab = document.getElementById('psb-ann-badge');
  if (unseen.length > 0) { ab.textContent = unseen.length; ab.style.display = 'inline-block'; }
  else                   { ab.style.display = 'none'; }
}

// ── OVERVIEW ─────────────────────────────────────────────────
function renderOverview() {
  if (!_portalData || !_portalParent) return;
  var p = _portalParent;

  setText('portalWelcomeTitle', 'Welcome, ' + p.first_name + '!');
  setText('portalWelcomeSub',   'Here\'s an overview of your portal as of today.');

  var wards     = _portalData.wards       || [];
  var exeats    = _portalData.exeats      || [];
  var events    = _portalData.events      || [];
  var anns      = _portalData.announcements || [];
  var todayStr  = today();
  var upcoming  = events.filter(function(e){ return e.event_date >= todayStr; });
  var pend      = exeats.filter(function(e){ return e.status === 'pending'; });
  var appr      = exeats.filter(function(e){ return e.status === 'approved'; });

  setText('ps-wards',    wards.length);
  setText('ps-pending',  pend.length);
  setText('ps-approved', appr.length);
  setText('ps-events',   upcoming.length);

  // Wards cards
  var wardsEl = document.getElementById('portalWardsList');
  wardsEl.innerHTML = wards.length
    ? wards.map(renderWardCard).join('')
    : '<p style="color:var(--text-muted)">No wards linked to your account.</p>';

  // Announcement preview (2 most recent)
  var annEl = document.getElementById('portalAnnPreview');
  annEl.innerHTML = anns.length
    ? anns.slice(0, 2).map(renderAnnCard).join('')
    : '<p style="color:var(--text-muted);font-size:14px">No announcements yet.</p>';

  // Events preview (3 upcoming)
  var evEl = document.getElementById('portalEventsPreview');
  evEl.innerHTML = upcoming.length
    ? upcoming.slice(0, 3).map(function(e){ return renderEvCard(e, false); }).join('')
    : '<p style="color:var(--text-muted)">No upcoming events scheduled.</p>';
}

// ── Ward card ─────────────────────────────────────────────────
function renderWardCard(s) {
  var photo = s.photo_path
    ? '<img src="' + s.photo_path + '" style="width:100%;height:100%;object-fit:cover"/>'
    : '<span style="font-size:1.1rem;font-weight:700;color:var(--white)">' + initials(s.first_name, s.last_name) + '</span>';
  return '<div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;display:flex;align-items:center;gap:14px">' +
    '<div style="width:52px;height:52px;border-radius:50%;background:var(--navy-light);border:3px solid var(--gold);flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden">' + photo + '</div>' +
    '<div style="min-width:0">' +
      '<div style="font-weight:600;font-size:1rem;color:var(--navy)">' + s.first_name + ' ' + s.last_name + '</div>' +
      '<div style="font-size:13px;color:var(--text-muted);margin-top:2px">' + (s.student_class || '—') + (s.house ? ' &bull; ' + s.house + ' House' : '') + '</div>' +
      (s.student_id_no ? '<div style="font-size:12px;color:var(--text-muted)">ID: ' + s.student_id_no + '</div>' : '') +
    '</div>' +
  '</div>';
}

// ── EVENTS ───────────────────────────────────────────────────
function renderPortalEvents() {
  if (!_portalData) return;
  filterPortalEvents(_eventFilter, document.querySelector('[data-filter="' + _eventFilter + '"]'));
}

function filterPortalEvents(filter, btn) {
  _eventFilter = filter;
  if (btn) {
    document.querySelectorAll('.portal-evt-tab').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
  }
  if (!_portalData) return;
  var events   = _portalData.events || [];
  var todayStr = today();
  var filtered;
  if (filter === 'upcoming') filtered = events.filter(function(e){ return e.event_date >= todayStr; });
  else if (filter === 'past') filtered = events.filter(function(e){ return e.event_date < todayStr; });
  else filtered = events;

  var el = document.getElementById('portalAllEvents');
  el.innerHTML = filtered.length
    ? filtered.map(function(e){ return renderEvCard(e, e.event_date < todayStr); }).join('')
    : '<p style="color:var(--text-muted);padding:2rem 0;text-align:center">No ' + filter + ' events found.</p>';
}

// ── EXEATS ───────────────────────────────────────────────────
function renderPortalExeats() {
  if (!_portalData) return;
  filterPortalExeats(_exeatFilter, document.querySelector('[data-exfilter="' + _exeatFilter + '"]'));
}

function filterPortalExeats(filter, btn) {
  _exeatFilter = filter;
  if (btn) {
    document.querySelectorAll('[data-exfilter]').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
  }
  if (!_portalData) return;
  var exeats   = _portalData.exeats || [];
  var filtered = (filter === 'all') ? exeats : exeats.filter(function(e){ return e.status === filter; });

  var el = document.getElementById('portalExeatList');
  if (!filtered.length) {
    el.innerHTML = '<div style="text-align:center;padding:3rem 0;color:var(--text-muted)"><div style="font-size:2.5rem;margin-bottom:.5rem">📋</div><p>No ' + filter + ' exeat requests found.</p></div>';
    return;
  }
  el.innerHTML = filtered.map(renderExeatCard).join('');
}

function renderExeatCard(e) {
  var statusMap = {
    pending:  { bg: 'rgba(212,153,58,.15)',  color: '#8a6010',        icon: '⏳', label: 'Pending Review' },
    approved: { bg: 'rgba(27,126,90,.12)',   color: 'var(--success)', icon: '✅', label: 'Approved' },
    declined: { bg: 'rgba(192,57,43,.12)',   color: 'var(--danger)',  icon: '❌', label: 'Declined' }
  };
  var s = statusMap[e.status] || statusMap.pending;
  var wardName = e.s_first ? (e.s_first + ' ' + e.s_last) : '—';
  var reviewNote = (e.status !== 'pending' && e.review_note)
    ? '<div style="background:var(--cream);border-radius:8px;padding:.75rem 1rem;margin-top:1rem;font-size:13px"><strong style="color:var(--navy-mid)">Review note:</strong> <span style="color:var(--text-muted)">' + escHtml(e.review_note) + '</span></div>'
    : '';

  return '<div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem 1.5rem;margin-bottom:12px;position:relative;overflow:hidden">' +
    '<div style="position:absolute;top:0;left:0;width:4px;height:100%;background:' + s.color + '"></div>' +
    '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">' +
      '<div>' +
        '<div style="display:inline-flex;align-items:center;gap:6px;background:' + s.bg + ';color:' + s.color + ';font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;margin-bottom:.6rem">' + s.icon + ' ' + s.label + '</div>' +
        '<div style="font-weight:600;font-size:1rem;color:var(--navy);margin-bottom:4px">Ward: ' + escHtml(wardName) + (e.student_class ? ' <span style="font-weight:400;color:var(--text-muted);font-size:13px">(' + escHtml(e.student_class) + ')</span>' : '') + '</div>' +
        '<div style="font-size:13px;color:var(--text-muted)">📅 ' + fmtDate(e.departure_date) + ' at ' + fmtTime(e.departure_time) + ' &rarr; Return: ' + fmtDate(e.expected_return) + '</div>' +
      '</div>' +
      '<div style="font-size:12px;color:var(--text-muted);white-space:nowrap">Submitted ' + fmtDate((e.created_at||'').split(' ')[0]) + '</div>' +
    '</div>' +
    '<div style="margin-top:.75rem;font-size:13px;color:var(--text);background:var(--cream);border-radius:8px;padding:.6rem .9rem"><strong>Reason:</strong> ' + escHtml(e.reason) + '</div>' +
    (e.reviewer_name ? '<div style="margin-top:.5rem;font-size:12px;color:var(--text-muted)">Reviewed by: ' + escHtml(e.reviewer_name) + (e.reviewed_at ? ' on ' + fmtDate(e.reviewed_at.split(' ')[0]) : '') + '</div>' : '') +
    reviewNote +
  '</div>';
}

// ── ANNOUNCEMENTS ─────────────────────────────────────────────
function renderPortalAnnouncements() {
  if (!_portalData) return;
  var anns = _portalData.announcements || [];
  var el   = document.getElementById('portalAnnList');
  el.innerHTML = anns.length
    ? anns.map(renderAnnCard).join('')
    : '<div style="text-align:center;padding:3rem 0;color:var(--text-muted)"><div style="font-size:2.5rem;margin-bottom:.5rem">📢</div><p>No announcements at this time.</p></div>';
}

function renderAnnCard(a) {
  var pinned = a.is_pinned == 1
    ? '<span style="background:rgba(212,153,58,.15);color:var(--gold);font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;letter-spacing:.05em;text-transform:uppercase;margin-left:8px">📌 Pinned</span>'
    : '';
  var bodyHtml = escHtml(a.body || '').replace(/\n/g, '<br/>');
  return '<div class="portal-ann-card">' +
    '<div class="portal-ann-header">' +
      '<div style="flex:1;min-width:0">' +
        '<div style="font-family:var(--font-display);font-size:1.1rem;color:var(--navy);margin-bottom:2px">' + escHtml(a.title) + pinned + '</div>' +
        '<div style="font-size:12px;color:var(--text-muted)">' + fmtDatetime(a.created_at) + (a.author_name ? ' &bull; ' + escHtml(a.author_name) : '') + '</div>' +
      '</div>' +
    '</div>' +
    '<div class="portal-ann-body">' + bodyHtml + '</div>' +
  '</div>';
}

// ── NEW EXEAT (inside portal) ─────────────────────────────────
function portalInitNewExeat() {
  if (!_portalParent) return;
  var wards = (_portalData && _portalData.wards) || _portalParent.wards || [];
  var sel = document.getElementById('pex_student_id');
  sel.innerHTML = '<option value="">— Select ward —</option>';
  wards.forEach(function(w) {
    sel.innerHTML += '<option value="' + w.id + '">' + w.first_name + ' ' + w.last_name + ' (' + (w.student_class||'') + ')</option>';
  });
  var todayStr = new Date().toISOString().split('T')[0];
  document.getElementById('pex_depart_date').min   = todayStr;
  document.getElementById('pex_depart_date').value = todayStr;
  document.getElementById('pex_return_date').min   = todayStr;

  // Show form, hide success
  document.querySelector('.form-card','#portal-new-exeat');
  var fc = document.querySelector('#portal-new-exeat .form-card');
  var sc = document.getElementById('pex-success');
  if (fc) fc.style.display = 'block';
  if (sc) sc.style.display = 'none';
}

async function portalSubmitExeat() {
  var studentId  = val('pex_student_id');
  var reason     = val('pex_reason');
  var departDate = val('pex_depart_date');
  var departTime = val('pex_depart_time');
  var returnDate = val('pex_return_date');

  if (!studentId)  { showToast('Please select a ward.', 'error'); return; }
  if (!departDate) { showToast('Departure date is required.', 'error'); return; }
  if (!departTime) { showToast('Departure time is required.', 'error'); return; }
  if (!returnDate) { showToast('Return date is required.', 'error'); return; }
  if (!reason)     { showToast('Please enter a reason.', 'error'); return; }

  var fd = new FormData();
  fd.append('action',          'submit_exeat');
  fd.append('student_id',      studentId);
  fd.append('parent_id',       _portalParent.id);
  fd.append('reason',          reason);
  fd.append('departure_date',  departDate);
  fd.append('departure_time',  departTime);
  fd.append('expected_return', returnDate);

  var r = await apiPost('actions/insert.php', fd);
  if (!r.success) { showToast(r.message, 'error'); return; }

  // Find ward name
  var wards = (_portalData && _portalData.wards) || _portalParent.wards || [];
  var ward  = wards.find(function(w){ return w.id == studentId; });
  var name  = ward ? (ward.first_name + ' ' + ward.last_name) : 'your ward';

  document.getElementById('pex-success-msg').textContent =
    'Your exeat request for ' + name + ' departing on ' + departDate + ' has been submitted and is awaiting review.';

  var fc = document.querySelector('#portal-new-exeat .form-card');
  if (fc) fc.style.display = 'none';
  document.getElementById('pex-success').style.display = 'block';

  // Refresh portal data so Exeats tab is up to date
  loadPortalData();
}

function portalNewExeatReset() {
  var fc = document.querySelector('#portal-new-exeat .form-card');
  if (fc) fc.style.display = 'block';
  document.getElementById('pex-success').style.display = 'none';
  document.getElementById('pex_student_id').value = '';
  document.getElementById('pex_reason').value = '';
  portalInitNewExeat();
}

// ── Helpers ───────────────────────────────────────────────────
function escHtml(s) {
  return (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php layout_footer(); ?>
