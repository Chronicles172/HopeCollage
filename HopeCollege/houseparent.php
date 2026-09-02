<?php
require_once 'includes/layout.php';
layout_head('House Parent');
layout_nav('houseparent');
?>

<!-- ═══════════════════════════════════════════════════════
     STEP 1 — LOGIN OVERLAY
════════════════════════════════════════════════════════ -->
<div class="login-overlay" id="hpLoginOverlay">
  <div class="login-box">
    <div style="text-align:center;margin-bottom:1.25rem">
      <div style="font-size:2.5rem;margin-bottom:.25rem">🏠</div>
      <h2 style="margin:0">House Parent Login</h2>
      <p style="margin:.4rem 0 0;font-size:13px;opacity:.7">Single portal — choose your dormitory after login</p>
    </div>
    <div class="form-group" style="margin-bottom:12px">
      <label>Username</label>
      <input type="text" id="hpLoginUser" placeholder="Enter your username" autocomplete="username"/>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Password</label>
      <div class="password-field-wrap">
        <input type="password" id="hpLoginPass" placeholder="Enter your password"
          autocomplete="current-password"
          onkeydown="if(event.key==='Enter') hpDoLogin()"/>
        <button type="button" class="password-toggle-btn" aria-label="Show password"
          onclick="togglePasswordVisibility('hpLoginPass', this)">👁</button>
      </div>
    </div>
    <button class="btn-primary" style="width:100%" onclick="hpDoLogin()">Login →</button>
    <a href="index.php" class="btn-outline"
      style="display:block;width:100%;margin-top:8px;text-align:center;color:var(--navy);border-color:var(--border)">
      Cancel
    </a>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     STEP 2 — GENDER SELECTION SCREEN
════════════════════════════════════════════════════════ -->
<div class="login-overlay" id="hpGenderOverlay" style="display:none">
  <div class="login-box" style="max-width:520px;text-align:center">
    <div style="font-size:2rem;margin-bottom:.5rem">👋</div>
    <h2 style="margin:0 0 .3rem" id="hpWelcomeName">Welcome!</h2>
    <p style="font-size:13px;opacity:.7;margin:0 0 2rem">Which students would you like to view?</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
      <!-- Male Card -->
      <div class="hp-gender-card" id="hpCardMale" onclick="hpSelectGender('Male')"
        style="cursor:pointer;border:2.5px solid var(--navy);border-radius:14px;padding:2rem 1rem;
               background:rgba(11,31,58,.06);transition:all .2s;user-select:none">
        <div style="font-size:3rem;margin-bottom:.75rem">👨‍🎓</div>
        <div style="font-size:1.1rem;font-weight:700;color:var(--navy)">Male Students</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:.35rem">View boys' dormitory</div>
      </div>
      <!-- Female Card -->
      <div class="hp-gender-card" id="hpCardFemale" onclick="hpSelectGender('Female')"
        style="cursor:pointer;border:2.5px solid var(--navy-mid);border-radius:14px;padding:2rem 1rem;
               background:rgba(26,53,88,.06);transition:all .2s;user-select:none">
        <div style="font-size:3rem;margin-bottom:.75rem">👩‍🎓</div>
        <div style="font-size:1.1rem;font-weight:700;color:var(--navy-mid)">Female Students</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:.35rem">View girls' dormitory</div>
      </div>
    </div>

    <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:13px"
      onclick="hpLogout()">← Logout</button>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     STEP 3 — DASHBOARD
════════════════════════════════════════════════════════ -->
<div id="hpDashboard" style="display:none">
  <div class="admin-layout">
    <aside class="admin-sidebar" id="hpSidebar">
      <h2 style="padding:0 1.5rem;margin-bottom:.25rem" id="hpTitle">House Parent</h2>
      <div style="padding:0 1.5rem;margin-bottom:1.25rem;font-size:13px;opacity:.65" id="hpSubtitle"></div>
      <div class="admin-sidebar-section">
        <div class="sidebar-link active" id="hpsb-overview"  onclick="hpTab('overview')"><span class="icon">📊</span> Overview</div>
        <div class="sidebar-link"        id="hpsb-students"  onclick="hpTab('students')"><span class="icon">🎒</span> Students</div>
        <div class="sidebar-link"        id="hpsb-exeats"    onclick="hpTab('exeats')"><span class="icon">🚪</span> Exeat History</div>
        <div class="sidebar-link"        id="hpsb-settings"  onclick="hpTab('settings')"><span class="icon">⚙️</span> Settings</div>
      </div>
      <div style="padding:0 1.5rem;margin-top:auto;padding-top:2rem">
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:.35rem" id="hpNameLabel"></div>
        <button class="btn-outline"
          style="width:100%;margin-bottom:8px;font-size:13px;color:white;border-color:rgba(255,255,255,.3)"
          onclick="hpSwitchGender()">⇄ Switch View</button>
        <button class="btn-danger" style="width:100%" onclick="hpLogout()">Logout</button>
      </div>
    </aside>

    <main class="admin-main">

      <!-- Overview -->
      <div class="admin-sub active" id="hp-overview">
        <div class="admin-header"><h1 id="hpOverviewTitle">Overview</h1></div>
        <div class="stats-grid">
          <div class="stat-card">
            <span class="num" id="hp-total">—</span>
            <span class="lbl">Total Students</span>
          </div>
          <div class="stat-card">
            <span class="num" id="hp-oncampus">—</span>
            <span class="lbl">On Campus</span>
          </div>
          <div class="stat-card">
            <span class="num" id="hp-offcampus">—</span>
            <span class="lbl">Off Campus</span>
          </div>
          <div class="stat-card">
            <span class="num" id="hp-pending">—</span>
            <span class="lbl">Pending Exeats</span>
          </div>
        </div>

        <h3 style="font-family:var(--font-display);margin-bottom:1rem;color:var(--navy)">Students Currently Off Campus</h3>
        <div id="hpOffCampusList"></div>

        <h3 style="font-family:var(--font-display);margin:1.5rem 0 1rem;color:var(--navy)">Recent Exeat Requests</h3>
        <div id="hpRecentExeats"></div>
      </div>

      <!-- Students -->
      <div class="admin-sub" id="hp-students">
        <div class="admin-header">
          <h1 id="hpStudentsTitle">Students</h1>
          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
            <input type="text" id="hpStudentSearch" placeholder="🔍 Search student or parent…"
              oninput="renderHpStudents()"
              style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border);min-width:180px"/>
            <select id="hpClassFilter" onchange="renderHpStudents()"
              style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border)">
              <option value="">All Classes</option>
            </select>
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px" onclick="hpExportStudents()">⬇ Export CSV</button>
          </div>
        </div>
        <div id="hpStudentsList"></div>
      </div>

      <!-- Exeat History -->
      <div class="admin-sub" id="hp-exeats">
        <div class="admin-header"><h1>Exeat History</h1>
          <select id="hpExeatFilter" onchange="loadHpExeats()"
            style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border)">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="declined">Declined</option>
          </select>
        </div>
        <div id="hpExeatsList"></div>
      </div>

      <!-- Settings -->
      <div class="admin-sub" id="hp-settings">
        <div class="admin-header"><h1>Account Settings</h1></div>
        <div class="form-card" style="max-width:480px">
          <div class="form-section-title">🔐 Change Credentials</div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Username (leave blank to keep current)</label>
            <input type="text" id="hpNewUser" placeholder="New username"/>
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Password (leave blank to keep current)</label>
            <div class="password-field-wrap">
              <input type="password" id="hpNewPass" placeholder="Min 8 characters"/>
              <button type="button" class="password-toggle-btn" aria-label="Show password"
                onclick="togglePasswordVisibility('hpNewPass', this)">👁</button>
            </div>
          </div>
          <div class="form-group" style="margin-bottom:1.25rem">
            <label>Current Password <span class="req">*</span></label>
            <div class="password-field-wrap">
              <input type="password" id="hpCurPass" placeholder="Required to confirm changes"/>
              <button type="button" class="password-toggle-btn" aria-label="Show password"
                onclick="togglePasswordVisibility('hpCurPass', this)">👁</button>
            </div>
          </div>
          <button class="btn-primary" onclick="hpChangeCredentials()">Save Changes</button>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- Student Data Modal -->
<div class="modal-overlay" id="hpStudentModal" onclick="closeModalOnBackdrop(event,'hpStudentModal')">
  <div class="modal" style="max-width:600px">
    <button class="modal-close" onclick="closeModal('hpStudentModal')">✕</button>
    <h3>Student Record</h3>
    <div id="hpStudentContent"></div>
    <div class="modal-actions">
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="closeModal('hpStudentModal')">Close</button>
      <button class="btn-primary" id="hpDownloadBtn">⬇ Download</button>
    </div>
  </div>
</div>

<style>
.hp-gender-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,.12);
}
</style>

<script>
var hpGender      = '';
var hpStudents    = [];
var hpExeats      = [];
var hpLoggedInAs  = '';   // full_name from login

/* ─── STEP 1: LOGIN ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('hpLoginOverlay').style.display = 'flex';
});

function hpDoLogin() {
  var fd = new FormData();
  fd.append('action',   'admin_login');
  fd.append('username', val('hpLoginUser'));
  fd.append('password', val('hpLoginPass'));
  apiPost('actions/insert.php', fd).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    var role = r.role || (r.data && r.data.role) || '';
    var name = r.name || (r.data && r.data.name) || '';

    // Accept any houseparent role (male or female) or admin
    var allowed = ['houseparent','admin'];
    if (allowed.indexOf(role) === -1) {
      showToast('Access denied. This portal is for House Parents only.', 'error');
      var fd2 = new FormData(); fd2.append('action','admin_logout');
      apiPost('actions/insert.php', fd2);
      return;
    }

    hpLoggedInAs = name;

    // Always show the gender picker regardless of which houseparent account logged in.
    // (houseparent_male / houseparent_female / houseparent all land here)
    document.getElementById('hpLoginOverlay').style.display  = 'none';
    document.getElementById('hpWelcomeName').textContent     = 'Welcome, ' + name + '!';
    document.getElementById('hpGenderOverlay').style.display = 'flex';
  });
}

/* ─── STEP 2: GENDER SELECTION ──────────────────────────── */
function hpSelectGender(gender) {
  // Visual pulse on chosen card
  var cardId = gender === 'Male' ? 'hpCardMale' : 'hpCardFemale';
  var card   = document.getElementById(cardId);
  card.style.opacity = '.6';
  card.style.transform = 'scale(.96)';
  setTimeout(function() { hpEnterDashboard(gender); }, 180);
}

/* ─── STEP 3: ENTER DASHBOARD ───────────────────────────── */
function hpEnterDashboard(gender) {
  hpGender = gender;
  var isMale = gender === 'Male';
  var color  = getComputedStyle(document.documentElement).getPropertyValue('--navy').trim() || '#0B1F3A';
  var emoji  = isMale ? '👨‍💼' : '👩‍💼';

  // Hide overlays, show dashboard
  document.getElementById('hpLoginOverlay').style.display  = 'none';
  document.getElementById('hpGenderOverlay').style.display = 'none';
  document.getElementById('hpDashboard').style.display     = 'block';

  // Populate sidebar labels
  document.getElementById('hpNameLabel').textContent     = hpLoggedInAs;
  document.getElementById('hpTitle').textContent         = emoji + ' ' + gender + ' House Parent';
  document.getElementById('hpSubtitle').textContent      = 'Viewing ' + gender.toLowerCase() + ' students only';
  document.getElementById('hpOverviewTitle').textContent = gender + ' Students Overview';
  document.getElementById('hpStudentsTitle').textContent = gender + ' Students';

  // Tint sidebar
  document.getElementById('hpSidebar').style.background = color;

  // Reset to overview tab
  document.querySelectorAll('.admin-sub').forEach(function(s){ s.classList.remove('active'); });
  document.querySelectorAll('.sidebar-link').forEach(function(l){ l.classList.remove('active'); });
  document.getElementById('hp-overview').classList.add('active');
  document.getElementById('hpsb-overview').classList.add('active');

  loadHpOverview();
}

/* Switch back to gender picker without logging out */
function hpSwitchGender() {
  document.getElementById('hpDashboard').style.display     = 'none';
  document.getElementById('hpGenderOverlay').style.display = 'flex';
  // Reset card state
  ['hpCardMale','hpCardFemale'].forEach(function(id){
    var c = document.getElementById(id);
    c.style.opacity = '1'; c.style.transform = '';
  });
  hpStudents = []; hpExeats = [];
}

function hpLogout() {
  var fd = new FormData(); fd.append('action','admin_logout');
  apiPost('actions/insert.php', fd).then(function() { window.location.href = 'index.php'; });
}

/* ─── TAB NAVIGATION ────────────────────────────────────── */
function hpTab(tab) {
  document.querySelectorAll('.admin-sub').forEach(function(s) { s.classList.remove('active'); });
  document.querySelectorAll('.sidebar-link').forEach(function(l) { l.classList.remove('active'); });
  var sub = document.getElementById('hp-'   + tab); if (sub) sub.classList.add('active');
  var sb  = document.getElementById('hpsb-' + tab); if (sb)  sb.classList.add('active');
  if (tab === 'students') { loadHpStudents(); }
  if (tab === 'exeats')   { loadHpExeats();   }
}

/* ─── DATA LOADING ──────────────────────────────────────── */
function loadHpOverview() {
  loadHpStudentsData(function(students) {
    hpStudents = students;
    var offCampus = students.filter(function(s) { return !s.on_campus; });
    var onCampus  = students.filter(function(s) { return s.on_campus; });

    setText('hp-total',     students.length);
    setText('hp-oncampus',  onCampus.length);
    setText('hp-offcampus', offCampus.length);

    apiFetch('actions/fetch.php?action=exeats&status=pending').then(function(r) {
      if (!r.success) return;
      var genderExeats = (r.data||[]).filter(function(e) { return e.s_gender === hpGender; });
      setText('hp-pending', genderExeats.length);
    });

    var offEl = document.getElementById('hpOffCampusList');
    if (offCampus.length === 0) {
      offEl.innerHTML = '<p style="color:var(--text-muted)">All ' + hpGender.toLowerCase() + ' students are currently on campus.</p>';
    } else {
      offEl.innerHTML = offCampus.map(function(s) { return renderHpStudentCard(s, true); }).join('');
    }

    apiFetch('actions/fetch.php?action=exeats').then(function(r) {
      if (!r.success) return;
      var gEx = (r.data||[]).filter(function(e){ return e.s_gender === hpGender; }).slice(0,5);
      renderHpExeatMini(gEx, 'hpRecentExeats');
    });
  });
}

function loadHpStudentsData(cb) {
  apiFetch('actions/fetch.php?action=students_by_gender&gender=' + encodeURIComponent(hpGender)).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); cb([]); return; }
    cb(r.data || []);
  });
}

function loadHpStudents() {
  loadHpStudentsData(function(students) {
    hpStudents = students;
    var classes = [];
    students.forEach(function(s) {
      if (s.student_class && classes.indexOf(s.student_class) === -1) classes.push(s.student_class);
    });
    var cf  = document.getElementById('hpClassFilter');
    var cur = cf.value;
    cf.innerHTML = '<option value="">All Classes</option>' + classes.map(function(c) {
      return '<option' + (c===cur?' selected':'') + '>' + c + '</option>';
    }).join('');
    renderHpStudents();
    initHpSearchDropdown();
  });
}

function renderHpStudents() {
  var q   = (document.getElementById('hpStudentSearch')||{value:''}).value.toLowerCase();
  var cls = (document.getElementById('hpClassFilter')  ||{value:''}).value;
  var filtered = hpStudents.filter(function(s) {
    var parentText = (s.parents||[]).map(function(p){ return p.first_name+' '+p.last_name+' '+(p.phone||''); }).join(' ');
    var searchText = (s.first_name+' '+s.last_name+' '+(s.student_class||'')+(s.student_id_no||'')+' '+parentText).toLowerCase();
    return (!q   || searchText.indexOf(q) !== -1) &&
           (!cls || s.student_class === cls);
  });
  var el = document.getElementById('hpStudentsList'); if (!el) return;
  if (!filtered.length) { el.innerHTML = '<p style="color:var(--text-muted);padding:1.5rem 0">No students found.</p>'; return; }

  var rows = filtered.map(function(s) {
    var av = s.photo_path
      ? '<div class="avatar-sm"><img src="'+s.photo_path+'" alt=""/></div>'
      : '<div class="avatar-sm">'+initials(s.first_name, s.last_name)+'</div>';
    var onCampusColor = s.on_campus ? '#1a7f4e' : 'var(--danger)';
    var onCampusLabel = s.on_campus ? '✅ On Campus' : '🚪 Off Campus';
    var parentName = (s.parents && s.parents.length)
      ? s.parents.map(function(p){ return p.first_name+' '+p.last_name; }).join(', ')
      : (s.p_first ? s.p_first+' '+s.p_last : '—');
    var parentPhone = (s.parents && s.parents.length)
      ? s.parents.map(function(p){ return p.phone||''; }).filter(Boolean).join(', ')
      : (s.p_phone || '—');
    return '<tr>' +
      '<td><div class="parent-cell">'+av+'<span>'+s.first_name+' '+s.last_name+'</span></div></td>' +
      '<td>'+(s.student_class||'—')+'</td>' +
      '<td>'+(s.gender||'—')+'</td>' +
      '<td>'+(s.nhis_id||'—')+'</td>' +
      '<td>'+parentName+'</td>' +
      '<td>'+parentPhone+'</td>' +
      '<td><span style="color:'+onCampusColor+';font-weight:600;font-size:12px">'+onCampusLabel+'</span></td>' +
      '<td><button class="btn-outline" style="font-size:12px;padding:5px 12px;color:var(--navy);border-color:var(--border)" onclick="hpViewStudent('+s.id+')">View</button></td>' +
    '</tr>';
  }).join('');

  el.innerHTML = '<div style="overflow-x:auto"><table><thead><tr>' +
    '<th>Student</th><th>Class</th><th>Gender</th><th>NHIS No.</th><th>Parent</th><th>Phone</th><th>Status</th><th></th>' +
    '</tr></thead><tbody>' + rows + '</tbody></table></div>';
}

function renderHpStudentCard(s, compact) {
  // Used for off-campus list on overview tab — keep card style there
  var onCampusColor = s.on_campus ? '#1a7f4e' : 'var(--danger)';
  var onCampusLabel = s.on_campus ? '✅ On Campus' : '🚪 Off Campus';
  var av = s.photo_path
    ? '<img src="'+s.photo_path+'" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--gold);flex-shrink:0"/>'
    : '<div style="width:48px;height:48px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0">'+initials(s.first_name,s.last_name)+'</div>';

  var parentsLine = (s.parents && s.parents.length)
    ? s.parents.map(function(p){ return p.first_name+' '+p.last_name+' <span style="font-size:.68rem;color:var(--gold)">('+p.relationship+')</span>'; }).join('  ·  ')
    : (s.p_first ? s.p_first+' '+s.p_last : '—');

  return '<div class="registry-card" style="'+(compact?'margin-bottom:.5rem':'')+'">' +
    '<div style="display:flex;align-items:center;gap:12px">' +
      av +
      '<div style="flex:1;min-width:0">' +
        '<div style="font-weight:700;color:var(--navy)">'+s.first_name+' '+s.last_name+'</div>' +
        '<div style="font-size:12px;color:var(--text-muted)">'+(s.student_class||'')+(s.nhis_id?' · NHIS: '+s.nhis_id:'')+'</div>' +
        '<div style="font-size:11.5px;color:var(--text-muted);margin-top:.15rem">👥 '+parentsLine+'</div>' +
        '<div style="font-size:12px;margin-top:.25rem"><span style="color:'+onCampusColor+';font-weight:600">'+onCampusLabel+'</span></div>' +
      '</div>' +
      '<button class="btn-outline" style="font-size:12px;padding:5px 10px;color:var(--navy);border-color:var(--border);flex-shrink:0" onclick="hpViewStudent('+s.id+')">View</button>' +
    '</div>' +
  '</div>';
}

function loadHpExeats() {
  var filter = (document.getElementById('hpExeatFilter')||{value:''}).value;
  var url = 'actions/fetch.php?action=exeats' + (filter ? '&status='+filter : '');
  apiFetch(url).then(function(r) {
    if (!r.success) return;
    var gEx = (r.data||[]).filter(function(e){ return e.s_gender === hpGender; });
    hpExeats = gEx;
    renderHpExeatMini(gEx, 'hpExeatsList');
  });
}

function renderHpExeatMini(list, containerId) {
  var el = document.getElementById(containerId); if (!el) return;
  if (!list || !list.length) { el.innerHTML = '<p style="color:var(--text-muted);padding:1rem 0">No exeat requests found.</p>'; return; }
  el.innerHTML = list.map(function(e) {
    var sc = e.status==='approved'?'#1a7f4e':e.status==='declined'?'var(--danger)':'var(--gold)';
    return '<div class="registry-card" style="margin-bottom:.5rem">' +
      '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap">' +
        '<div><span style="font-weight:700;color:var(--navy)">'+e.s_first+' '+e.s_last+'</span>' +
          ' <span style="font-size:12px;color:var(--text-muted)">'+(e.student_class||'')+'</span><br/>' +
          '<span style="font-size:12px;color:var(--text-muted)">Departs '+fmtDate(e.departure_date)+' &nbsp;|&nbsp; Returns '+fmtDate(e.expected_return)+'</span><br/>' +
          '<span style="font-size:12px">'+e.reason+'</span></div>' +
        '<span style="font-size:12px;font-weight:700;color:'+sc+'">'+(e.status||'').toUpperCase()+'</span>' +
      '</div></div>';
  }).join('');
}

function hpViewStudent(studentId) {
  apiFetch('actions/fetch.php?action=student_full_data&student_id='+studentId).then(function(r) {
    if (!r.success) { showToast(r.message,'error'); return; }
    var s = r.data.student;
    var exeats = r.data.exeats || [];

    var exHtml = exeats.length ? exeats.map(function(e) {
      var sc = e.status==='approved'?'#1a7f4e':e.status==='declined'?'var(--danger)':'var(--gold)';
      return '<div style="border:1px solid var(--border);border-radius:8px;padding:.7rem;margin-bottom:.5rem;font-size:13px">' +
        '<strong style="color:'+sc+'">'+(e.status||'').toUpperCase()+'</strong> &nbsp;' +
        fmtDate(e.departure_date)+' → '+fmtDate(e.expected_return)+'<br/>'+
        e.reason +
        (e.review_note ? '<br/><em style="color:var(--text-muted)">Note: '+e.review_note+'</em>' : '') +
      '</div>';
    }).join('') : '<p style="font-size:13px;color:var(--text-muted)">No exeats recorded.</p>';

    var parentsHtml = (s.parents && s.parents.length) ? s.parents.map(function(p){
      var avatar = p.photo_path
        ? '<img src="'+p.photo_path+'" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--gold);flex-shrink:0"/>'
        : '<div style="width:72px;height:72px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0">'+((p.first_name||'')[0]||'').toUpperCase()+((p.last_name||'')[0]||'').toUpperCase()+'</div>';
      return '<div style="display:flex;align-items:center;gap:1rem;padding:.85rem 1rem;background:var(--cream);border-radius:10px;border:1px solid var(--border);margin-bottom:.6rem">' +
        avatar +
        '<div style="flex:1;min-width:0">' +
          '<div style="font-weight:700;font-size:1rem;color:var(--navy)">'+p.first_name+' '+p.last_name+
            ' <span style="font-size:.7rem;background:rgba(212,153,58,.18);color:var(--gold);padding:2px 9px;border-radius:12px;font-weight:600">'+p.relationship+'</span></div>' +
          '<div style="font-size:.82rem;color:var(--text-muted);margin-top:.3rem">📞 <a href="tel:'+p.phone+'" style="color:inherit;text-decoration:none">'+p.phone+'</a></div>' +
          (p.email ? '<div style="font-size:.82rem;color:var(--text-muted);margin-top:.15rem">✉ <a href="mailto:'+p.email+'" style="color:inherit;text-decoration:none">'+p.email+'</a></div>' : '') +
        '</div>' +
      '</div>';
    }).join('') : '<p style="font-size:13px;color:var(--text-muted)">No parent recorded.</p>';

    var sAvatar = s.photo_path
      ? '<img src="'+s.photo_path+'" style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--gold);flex-shrink:0"/>'
      : '<div style="width:88px;height:88px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;flex-shrink:0">'+((s.first_name||'')[0]||'').toUpperCase()+((s.last_name||'')[0]||'').toUpperCase()+'</div>';

    document.getElementById('hpStudentContent').innerHTML =
      '<div style="display:flex;align-items:center;gap:1.1rem;padding:1rem;background:var(--cream);border-radius:12px;border:1px solid var(--border);margin-bottom:1.1rem">' +
        sAvatar +
        '<div>' +
          '<div style="font-weight:700;font-size:1.15rem;color:var(--navy)">'+s.first_name+' '+s.last_name+'</div>' +
          '<div style="font-size:.82rem;color:var(--text-muted);margin-top:.2rem">'+(s.student_class||'—')+' &nbsp;·&nbsp; '+(s.gender||'—')+'</div>' +
          (s.student_id_no ? '<div style="font-size:.78rem;color:var(--text-muted);margin-top:.15rem">ID: '+s.student_id_no+'</div>' : '') +
        '</div>' +
      '</div>' +
      '<div style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin-bottom:.5rem">Parents / Guardians</div>' +
      parentsHtml +
      '<div style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin:.75rem 0 .5rem">Exeat History</div>' +
      exHtml;

    document.getElementById('hpDownloadBtn').onclick = function() {
      var parentLine = (s.parents||[]).map(function(p){ return p.first_name+' '+p.last_name+' ('+p.relationship+') '+p.phone; }).join(' | ');
      var csv = 'Student,'+s.first_name+' '+s.last_name+'\nClass,'+s.student_class+'\nParents,'+parentLine+'\n\n';
      csv += 'EXEAT HISTORY\nStatus,Reason,Departure,Return\n';
      exeats.forEach(function(e){ csv+='"'+e.status+'","'+e.reason+'","'+e.departure_date+'","'+e.expected_return+'"\n'; });
      var a=document.createElement('a');
      a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv'}));
      a.download=(s.first_name+'-'+s.last_name).replace(/\s+/g,'-')+'-record.csv'; a.click();
      showToast('Downloaded!','success');
    };
    openModal('hpStudentModal');
  });
}

function hpExportStudents() {
  if (!hpStudents.length) { loadHpStudents(); showToast('Loading students, try again.'); return; }
  var h = 'Name,Class,Status,ID No.,Parents\n';
  var rows = hpStudents.map(function(s){
    var parentInfo = (s.parents && s.parents.length)
      ? s.parents.map(function(p){ return p.first_name+' '+p.last_name+' ('+p.relationship+') '+p.phone; }).join(' | ')
      : (s.p_first ? s.p_first+' '+s.p_last+' '+s.p_phone : '—');
    return '"'+s.first_name+' '+s.last_name+'","'+(s.student_class||'')+'","'+(s.on_campus?'On Campus':'Off Campus')+'","'+(s.student_id_no||'')+'","'+parentInfo+'"';
  }).join('\n');
  var a=document.createElement('a');
  a.href=URL.createObjectURL(new Blob([h+rows],{type:'text/csv'}));
  a.download=hpGender.toLowerCase()+'-students.csv'; a.click();
  showToast('Exported!','success');
}

function hpChangeCredentials() {
  var fd = new FormData();
  fd.append('action',           'change_credentials');
  fd.append('new_username',     val('hpNewUser'));
  fd.append('new_password',     val('hpNewPass'));
  fd.append('current_password', val('hpCurPass'));
  apiPost('actions/insert.php', fd).then(function(r) {
    if (r.success) {
      showToast('Credentials updated!','success');
      document.getElementById('hpNewUser').value='';
      document.getElementById('hpNewPass').value='';
      document.getElementById('hpCurPass').value='';
    } else { showToast(r.message,'error'); }
  });
}
</script>
<?php layout_footer(); ?>
