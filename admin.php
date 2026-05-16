<?php
// ============================================================
// admin.php  —  Admin Dashboard (login-protected)
// URL: /admin.php
// ============================================================
require_once 'includes/layout.php';
session_start(); // ← Single session_start() for this page
layout_head('Admin');
layout_nav('admin');
?>

<!-- ── Admin Login Overlay ────────────────────────────────── -->
<div class="login-overlay" id="adminLoginOverlay">
  <div class="login-box">
    <h2>Admin Login</h2>
    <p>Enter your credentials to access the dashboard.</p>
    <div class="form-group" style="margin-bottom:12px">
      <label>Username</label>
      <input type="text" id="loginUser" placeholder="admin" autocomplete="username"/>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Password</label>
      <input type="password" id="loginPass" placeholder="••••••••"
        autocomplete="current-password"
        onkeydown="if(event.key==='Enter') doLogin()"/>
    </div>
    <button class="btn-primary" style="width:100%" onclick="doLogin()">Login →</button>
    <a href="index.php" class="btn-outline"
      style="display:block;width:100%;margin-top:8px;text-align:center;color:var(--navy);border-color:var(--border)">
      Cancel
    </a>
  </div>
</div>

<!-- ── Dashboard (hidden until login) ────────────────────── -->
<div id="adminDashboard" style="display:none">
  <div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <h2 style="padding:0 1.5rem;margin-bottom:1.25rem">Admin Panel</h2>
      <div class="admin-sidebar-section">
        <div class="sidebar-link active" id="sb-overview"  onclick="adminTab('overview')"><span class="icon">📊</span> Overview</div>
        <div class="sidebar-link"        id="sb-parents"   onclick="adminTab('parents')"><span class="icon">👥</span> Parents &amp; Wards</div>
        <div class="sidebar-link"        id="sb-events"    onclick="adminTab('events')"><span class="icon">📅</span> Events</div>
        <div class="sidebar-link"        id="sb-checkin"   onclick="adminTab('checkin')"><span class="icon">✅</span> Attendance</div>
        <div class="sidebar-link"        id="sb-qr"        onclick="openQR()"><span class="icon">📲</span> QR Code</div>
        <div class="sidebar-link"        id="sb-settings"  onclick="adminTab('settings')"><span class="icon">⚙️</span> Settings</div>
      </div>
      <div style="padding:0 1.5rem;margin-top:auto;padding-top:2rem">
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:.5rem" id="adminNameLabel"></div>
        <button class="btn-danger" style="width:100%" onclick="adminLogout()">Logout</button>
      </div>
    </aside>

    <!-- Main content -->
    <main class="admin-main">

      <!-- Tab: Overview -->
      <div class="admin-sub active" id="admin-overview">
        <div class="admin-header">
          <h1>Dashboard Overview</h1>
          <button class="btn-primary" onclick="adminTab('checkin')">+ Mark Attendance</button>
        </div>
        <div class="stats-grid">
          <div class="stat-card"><span class="num" id="a-totalParents">—</span><span class="lbl">Parents</span></div>
          <div class="stat-card"><span class="num" id="a-totalStudents">—</span><span class="lbl">Students</span></div>
          <div class="stat-card"><span class="num" id="a-totalEvents">—</span><span class="lbl">Events</span></div>
          <div class="stat-card"><span class="num" id="a-upcoming">—</span><span class="lbl">Upcoming</span></div>
        </div>
        <div style="margin-bottom:1.5rem">
          <h3 style="font-family:var(--font-display);margin-bottom:1rem;color:var(--navy)">Upcoming Events</h3>
          <div class="events-grid" id="adminEventsPreview"></div>
        </div>
      </div>

      <!-- Tab: Parents & Wards -->
      <div class="admin-sub" id="admin-parents">
        <div class="admin-header">
          <h1>Parents &amp; Wards</h1>
          <button class="btn-outline"
            style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px"
            onclick="exportCSV()">⬇ CSV</button>
        </div>
        <div class="table-wrap">
          <div class="table-toolbar">
            <input type="text" id="searchParents" placeholder="Search name or phone…"
              oninput="renderParentsTable()" style="flex:1;min-width:160px"/>
            <select id="filterClass" onchange="renderParentsTable()" style="min-width:130px">
              <option value="">All Classes</option>
            </select>
            <select id="filterRel" onchange="renderParentsTable()">
              <option value="">All Relations</option>
              <option>Father</option><option>Mother</option>
              <option>Guardian</option><option>Other</option>
            </select>
          </div>
          <div style="overflow-x:auto">
            <table>
              <thead><tr>
                <th>Student</th><th>Class</th>
                <th colspan="3">Parents / Guardians</th><th>Registered</th><th></th>
              </tr></thead>
              <tbody id="parentsTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tab: Events -->
      <div class="admin-sub" id="admin-events">
        <div class="admin-header">
          <h1>Events</h1>
          <button class="btn-primary" onclick="openAddEvent()">+ Schedule Event</button>
        </div>
        <div class="events-grid" id="adminEventCards"></div>
      </div>

      <!-- Tab: Attendance -->
      <div class="admin-sub" id="admin-checkin">
        <div class="admin-header">
          <h1>Attendance</h1>
          <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <select id="adminEventSelect" onchange="loadAdminAttendance(this.value)"
              style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border)"></select>
            <button class="btn-outline"
              style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px"
              onclick="exportAttendance()">⬇ Export</button>
          </div>
        </div>
        <div class="att-layout">
          <div>
            <p style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-muted);letter-spacing:.07em;margin-bottom:.75rem">Events</p>
            <div class="event-list" id="adminEventList"></div>
          </div>
          <div>
            <p style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-muted);letter-spacing:.07em;margin-bottom:.75rem">
              Signed-in parents – <span id="adminAttCount">0</span> attended
            </p>
            <div class="checkin-list" id="adminCheckinList"></div>
          </div>
        </div>
      </div>

      <!-- Tab: Settings -->
      <div class="admin-sub" id="admin-settings">
        <div class="admin-header"><h1>Account Settings</h1></div>
        <div class="form-card" style="max-width:480px">
          <div class="form-section-title">🔐 Change Credentials</div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Username <span style="font-weight:400;color:var(--text-muted)">(leave blank to keep current)</span></label>
            <input type="text" id="aNewUser" placeholder="New username" autocomplete="new-username"/>
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Password <span style="font-weight:400;color:var(--text-muted)">(leave blank to keep current)</span></label>
            <input type="password" id="aNewPass" placeholder="Min 8 characters" autocomplete="new-password"/>
          </div>
          <div class="form-group" style="margin-bottom:1.25rem">
            <label>Current Password <span class="req">*</span></label>
            <input type="password" id="aCurPass" placeholder="Required to confirm changes"
              autocomplete="current-password"
              onkeydown="if(event.key==='Enter') adminChangeCredentials()"/>
          </div>
          <button class="btn-primary" onclick="adminChangeCredentials()">Save Changes</button>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- ── Modals ─────────────────────────────────────────────── -->
<div class="modal-overlay" id="addEventModal" onclick="closeModalOnBackdrop(event,'addEventModal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('addEventModal')">✕</button>
    <h3>Schedule New Event</h3>
    <div class="form-group" style="margin-bottom:12px">
      <label>Event Name <span class="req">*</span></label>
      <input type="text" id="evName" placeholder="e.g. End-of-Term PTA"/>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Type <span class="req">*</span></label>
        <select id="evType">
          <option>PTA Meeting</option><option>Visitation Day</option>
          <option>Sports Day</option><option>Open Day</option><option>Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Date <span class="req">*</span></label>
        <input type="date" id="evDate"/>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Time</label>
        <input type="time" id="evTime" value="10:00"/>
      </div>
      <div class="form-group">
        <label>Venue</label>
        <input type="text" id="evVenue" placeholder="School hall"/>
      </div>
    </div>
    <div class="form-group" style="margin-bottom:12px">
      <label>Description</label>
      <textarea id="evDesc" placeholder="Brief description…"></textarea>
    </div>
    <div class="modal-actions">
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)"
        onclick="closeModal('addEventModal')">Cancel</button>
      <button class="btn-primary" onclick="saveEvent()">Create Event</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="parentDetailModal" onclick="closeModalOnBackdrop(event,'parentDetailModal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('parentDetailModal')">✕</button>
    <h3>Parent Details</h3>
    <div id="parentDetailContent"></div>
    <div class="modal-actions">
      <button class="btn-primary" onclick="closeModal('parentDetailModal')">Close</button>
    </div>
  </div>
</div>

<!-- Student Detail Modal (from doc 6) -->
<div class="modal-overlay" id="studentDetailModal" onclick="closeModalOnBackdrop(event,'studentDetailModal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('studentDetailModal')">✕</button>
    <h3>Student Details</h3>
    <div id="studentDetailContent"></div>
    <div class="modal-actions">
      <button class="btn-primary" onclick="closeModal('studentDetailModal')">Close</button>
    </div>
  </div>
</div>

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
document.addEventListener('DOMContentLoaded', function () {
  // If a valid admin session already exists server-side, skip the login overlay
  var serverSession = <?php echo json_encode(!empty($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null); ?>;
  var serverName    = <?php echo json_encode($_SESSION['admin_name'] ?? ''); ?>;
  var serverRole    = <?php echo json_encode($_SESSION['admin_role'] ?? ''); ?>;

  if (serverSession && serverRole === 'admin') {
    document.getElementById('adminLoginOverlay').style.display = 'none';
    document.getElementById('adminDashboard').style.display    = 'block';
    var lbl = document.getElementById('adminNameLabel');
    if (lbl) lbl.textContent = serverName;
    loadAdminData();
  } else {
    document.getElementById('adminLoginOverlay').style.display = 'flex';
  }
});

function doLogin() {
  var fd = new FormData();
  fd.append('action',   'admin_login');
  fd.append('username', val('loginUser'));
  fd.append('password', val('loginPass'));

  apiPost('actions/insert.php', fd).then(function (r) {
    if (!r.success) { showToast(r.message, 'error'); return; }

    var role = (r.data && r.data.role) ? r.data.role : (r.role || '');
    var name = (r.data && r.data.name) ? r.data.name : (r.name || '');

    if (role !== 'admin') {
      showToast('Access denied. This portal is for Admins only.', 'error');
      var fd2 = new FormData(); fd2.append('action', 'admin_logout');
      apiPost('actions/insert.php', fd2);
      return;
    }

    document.getElementById('adminLoginOverlay').style.display = 'none';
    document.getElementById('adminDashboard').style.display    = 'block';
    var lbl = document.getElementById('adminNameLabel');
    if (lbl) lbl.textContent = name;
    loadAdminData();
  });
}

function adminLogout() {
  var fd = new FormData();
  fd.append('action', 'admin_logout');
  apiPost('actions/insert.php', fd).then(function () {
    window.location.href = 'index.php';
  });
}

function openQR() {
  var url = window.location.origin +
            window.location.pathname.replace('admin.php', '') +
            'attendance.php';
  document.getElementById('qrUrl').textContent = url;
  openModal('qrModal');
  setTimeout(function () { drawQR(url); }, 80);
}

function deleteEvent(eventId, eventName) {
  if (!confirm('Are you sure you want to delete "' + eventName + '"?\n\nThis will also remove all attendance records for this event.')) {
    return;
  }

  var fd = new FormData();
  fd.append('action',   'delete_event');
  fd.append('event_id', eventId);

  apiPost('actions/insert.php', fd).then(function (r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    showToast(r.message, 'success');
    loadAdminData(); // Refresh overview, events tab, and attendance lists
  });
}

function adminChangeCredentials() {
  var newUser = val('aNewUser');
  var newPass = val('aNewPass');
  var curPass = val('aCurPass');

  if (!curPass) {
    showToast('Please enter your current password.', 'error');
    document.getElementById('aCurPass').focus();
    return;
  }
  if (!newUser && !newPass) {
    showToast('Please enter a new username or new password to update.', 'error');
    return;
  }

  var fd = new FormData();
  fd.append('action',           'change_credentials');
  fd.append('new_username',     newUser);
  fd.append('new_password',     newPass);
  fd.append('current_password', curPass);

  apiPost('actions/insert.php', fd).then(function (r) {
    if (r.success) {
      showToast('Credentials updated successfully!', 'success');
      document.getElementById('aNewUser').value = '';
      document.getElementById('aNewPass').value = '';
      document.getElementById('aCurPass').value = '';
    } else {
      showToast(r.message, 'error');
    }
  });
}
</script>

<?php layout_footer(); ?>
