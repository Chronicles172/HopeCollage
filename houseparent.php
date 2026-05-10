<?php
require_once 'includes/layout.php';
session_start(); // ← Single session_start() for this page
layout_head('House Parent');
layout_nav('houseparent');
?>

<!-- Login Overlay -->
<div class="login-overlay" id="hpLoginOverlay">
  <div class="login-box">
    <h2>House Parent Login</h2>
    <p>Male or Female House Parent access.</p>
    <div class="form-group" style="margin-bottom:12px">
      <label>Username</label>
      <input type="text" id="hpLoginUser" placeholder="houseparent_male / houseparent_female" autocomplete="username"/>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Password</label>
      <input type="password" id="hpLoginPass" placeholder="••••••••"
        autocomplete="current-password"
        onkeydown="if(event.key==='Enter') hpDoLogin()"/>
    </div>
    <button class="btn-primary" style="width:100%" onclick="hpDoLogin()">Login →</button>
    <a href="index.php" class="btn-outline"
      style="display:block;width:100%;margin-top:8px;text-align:center;color:var(--navy);border-color:var(--border)">
      Cancel
    </a>
  </div>
</div>

<!-- Dashboard -->
<div id="hpDashboard" style="display:none">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <h2 style="padding:0 1.5rem;margin-bottom:.25rem" id="hpTitle">House Parent</h2>
      <div style="padding:0 1.5rem;margin-bottom:1.25rem;font-size:13px;opacity:.65" id="hpSubtitle"></div>
      <div class="admin-sidebar-section">
        <div class="sidebar-link active" id="hpsb-overview"  onclick="hpTab('overview')"><span class="icon">📊</span> Overview</div>
        <div class="sidebar-link"        id="hpsb-students"  onclick="hpTab('students')"><span class="icon">🎒</span> Students</div>
        <div class="sidebar-link"        id="hpsb-exeats"    onclick="hpTab('exeats')"><span class="icon">🚪</span> Exeat History</div>
        <div class="sidebar-link"        id="hpsb-settings"  onclick="hpTab('settings')"><span class="icon">⚙️</span> Settings</div>
      </div>
      <div style="padding:0 1.5rem;margin-top:auto;padding-top:2rem">
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:.5rem" id="hpNameLabel"></div>
        <button class="btn-danger" style="width:100%" onclick="hpLogout()">Logout</button>
      </div>
    </aside>

    <main class="admin-main">

      <!-- Overview -->
      <div class="admin-sub active" id="hp-overview">
        <div class="admin-header"><h1 id="hpOverviewTitle">Overview</h1></div>
        <div class="stats-grid">
          <div class="stat-card" style="background:var(--gold);color:var(--navy)">
            <span class="num" id="hp-total">—</span>
            <span class="lbl">Total Students</span>
          </div>
          <div class="stat-card" style="background:var(--success,#1a7f4e);color:white">
            <span class="num" id="hp-oncampus">—</span>
            <span class="lbl">On Campus</span>
          </div>
          <div class="stat-card" style="background:var(--danger);color:white">
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
            <input type="text" id="hpStudentSearch" placeholder="🔍 Search…"
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
            <label>New Username <span style="font-weight:400;color:var(--text-muted)">(leave blank to keep current)</span></label>
            <input type="text" id="hpNewUser" placeholder="New username" autocomplete="new-username"/>
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Password <span style="font-weight:400;color:var(--text-muted)">(leave blank to keep current)</span></label>
            <input type="password" id="hpNewPass" placeholder="Min 8 characters" autocomplete="new-password"/>
          </div>
          <div class="form-group" style="margin-bottom:1.25rem">
            <label>Current Password <span class="req">*</span></label>
            <input type="password" id="hpCurPass" placeholder="Required to confirm changes" autocomplete="current-password"
              onkeydown="if(event.key==='Enter') hpChangeCredentials()"/>
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

<script>
var hpGender   = '';
var hpStudents = [];
var hpExeats   = [];

// Helper: apply role-based UI (gender, title, sidebar colour)
function hpApplyRole(role, name) {
  hpGender    = (role === 'houseparent_female') ? 'Female' : 'Male';
  var isMale  = hpGender === 'Male';
  var color   = isMale ? '#1565c0' : '#ad1457';
  var emoji   = isMale ? '👨‍💼' : '👩‍💼';

  document.getElementById('hpNameLabel').textContent     = name;
  document.getElementById('hpTitle').textContent         = emoji + ' ' + hpGender + ' House Parent';
  document.getElementById('hpSubtitle').textContent      = 'Viewing ' + hpGender.toLowerCase() + ' students only';
  document.getElementById('hpOverviewTitle').textContent = hpGender + ' Students Overview';
  document.getElementById('hpStudentsTitle').textContent = hpGender + ' Students';
  document.querySelector('.admin-sidebar').style.background = color;
}

document.addEventListener('DOMContentLoaded', function () {
  // If a valid house-parent session already exists server-side, skip the login overlay
  var serverSession = <?php echo json_encode(!empty($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null); ?>;
  var serverName    = <?php echo json_encode($_SESSION['admin_name'] ?? ''); ?>;
  var serverRole    = <?php echo json_encode($_SESSION['admin_role'] ?? ''); ?>;

  var validRoles = ['houseparent_male', 'houseparent_female', 'admin'];
  if (serverSession && validRoles.indexOf(serverRole) !== -1) {
    document.getElementById('hpLoginOverlay').style.display = 'none';
    document.getElementById('hpDashboard').style.display    = 'block';
    hpApplyRole(serverRole, serverName);
    loadHpOverview();
  } else {
    document.getElementById('hpLoginOverlay').style.display = 'flex';
  }
});

function hpDoLogin() {
  var fd = new FormData();
  fd.append('action',   'admin_login');
  fd.append('username', val('hpLoginUser'));
  fd.append('password', val('hpLoginPass'));

  apiPost('actions/insert.php', fd).then(function (r) {
    if (!r.success) { showToast(r.message, 'error'); return; }

    var role = (r.data && r.data.role) ? r.data.role : (r.role || '');
    var name = (r.data && r.data.name) ? r.data.name : (r.name || '');

    var validRoles = ['houseparent_male', 'houseparent_female', 'admin'];
    if (validRoles.indexOf(role) === -1) {
      showToast('Access denied. This portal is for House Parents only.', 'error');
      var fd2 = new FormData(); fd2.append('action', 'admin_logout');
      apiPost('actions/insert.php', fd2);
      return;
    }

    document.getElementById('hpLoginOverlay').style.display = 'none';
    document.getElementById('hpDashboard').style.display    = 'block';
    hpApplyRole(role, name);
    loadHpOverview();
  });
}

function hpLogout() {
  var fd = new FormData();
  fd.append('action', 'admin_logout');
  apiPost('actions/insert.php', fd).then(function () {
    window.location.href = 'index.php';
  });
}

function hpTab(tab) {
  document.querySelectorAll('.admin-sub').forEach(function (s) { s.classList.remove('active'); });
  document.querySelectorAll('.sidebar-link').forEach(function (l) { l.classList.remove('active'); });
  var sub = document.getElementById('hp-'   + tab); if (sub) sub.classList.add('active');
  var sb  = document.getElementById('hpsb-' + tab); if (sb)  sb.classList.add('active');
  if (tab === 'students') loadHpStudents();
  if (tab === 'exeats')   loadHpExeats();
}

function loadHpOverview() {
  loadHpStudentsData(function (students) {
    hpStudents = students;
    var offCampus = students.filter(function (s) { return !s.on_campus; });
    var onCampus  = students.filter(function (s) { return s.on_campus;  });

    setText('hp-total',     students.length);
    setText('hp-oncampus',  onCampus.length);
    setText('hp-offcampus', offCampus.length);

    apiFetch('actions/fetch.php?action=exeats&status=pending').then(function (r) {
      if (!r.success) return;
      var genderExeats = (r.data || []).filter(function (e) { return e.s_gender === hpGender; });
      setText('hp-pending', genderExeats.length);
    });

    var offEl = document.getElementById('hpOffCampusList');
    if (offCampus.length === 0) {
      offEl.innerHTML = '<p style="color:var(--text-muted)">All ' + hpGender.toLowerCase() + ' students are currently on campus.</p>';
    } else {
      offEl.innerHTML = offCampus.map(function (s) { return renderHpStudentCard(s, true); }).join('');
    }

    apiFetch('actions/fetch.php?action=exeats').then(function (r) {
      if (!r.success) return;
      var gEx = (r.data || []).filter(function (e) { return e.s_gender === hpGender; }).slice(0, 5);
      renderHpExeatMini(gEx, 'hpRecentExeats');
    });
  });
}

function loadHpStudentsData(cb) {
  apiFetch('actions/fetch.php?action=students_by_gender&gender=' + encodeURIComponent(hpGender)).then(function (r) {
    if (!r.success) { showToast(r.message, 'error'); cb([]); return; }
    cb(r.data || []);
  });
}

function loadHpStudents() {
  loadHpStudentsData(function (students) {
    hpStudents = students;
    var classes = [];
    students.forEach(function (s) {
      if (s.student_class && classes.indexOf(s.student_class) === -1) classes.push(s.student_class);
    });
    var cf  = document.getElementById('hpClassFilter');
    var cur = cf.value;
    cf.innerHTML = '<option value="">All Classes</option>' + classes.map(function (c) {
      return '<option' + (c === cur ? ' selected' : '') + '>' + c + '</option>';
    }).join('');
    renderHpStudents();
  });
}

function renderHpStudents() {
  var q   = (document.getElementById('hpStudentSearch') || { value: '' }).value.toLowerCase();
  var cls = (document.getElementById('hpClassFilter')   || { value: '' }).value;
  var filtered = hpStudents.filter(function (s) {
    return (!q   || (s.first_name + ' ' + s.last_name + ' ' + (s.student_class || '')).toLowerCase().indexOf(q) !== -1) &&
           (!cls || s.student_class === cls);
  });
  var el = document.getElementById('hpStudentsList'); if (!el) return;
  if (!filtered.length) { el.innerHTML = '<p style="color:var(--text-muted);padding:1.5rem 0">No students found.</p>'; return; }
  el.innerHTML = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem">' +
    filtered.map(function (s) { return renderHpStudentCard(s, false); }).join('') + '</div>';
}

function renderHpStudentCard(s, compact) {
  var onCampusColor = s.on_campus ? '#1a7f4e' : 'var(--danger)';
  var onCampusLabel = s.on_campus ? '✅ On Campus' : '🚪 Off Campus';
  var av = s.photo_path
    ? '<img src="' + s.photo_path + '" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--gold);flex-shrink:0"/>'
    : '<div style="width:48px;height:48px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0">' + initials(s.first_name, s.last_name) + '</div>';

  var parentsLine = (s.parents && s.parents.length)
    ? s.parents.map(function (p) {
        return p.first_name + ' ' + p.last_name + ' <span style="font-size:.68rem;color:var(--gold)">(' + p.relationship + ')</span>';
      }).join('  ·  ')
    : (s.p_first ? s.p_first + ' ' + s.p_last : '—');

  return '<div class="registry-card" style="' + (compact ? 'margin-bottom:.5rem' : '') + '">' +
    '<div style="display:flex;align-items:center;gap:12px">' +
      av +
      '<div style="flex:1;min-width:0">' +
        '<div style="font-weight:700;color:var(--navy)">' + s.first_name + ' ' + s.last_name + '</div>' +
        '<div style="font-size:12px;color:var(--text-muted)">' + (s.student_class || '') + (s.student_id_no ? ' · ID: ' + s.student_id_no : '') + '</div>' +
        '<div style="font-size:11.5px;color:var(--text-muted);margin-top:.15rem">👥 ' + parentsLine + '</div>' +
        '<div style="font-size:12px;margin-top:.25rem"><span style="color:' + onCampusColor + ';font-weight:600">' + onCampusLabel + '</span></div>' +
      '</div>' +
      '<button class="btn-outline" style="font-size:12px;padding:5px 10px;color:var(--navy);border-color:var(--border);flex-shrink:0" onclick="hpViewStudent(' + s.id + ')">View</button>' +
    '</div>' +
  '</div>';
}

function loadHpExeats() {
  var filter = (document.getElementById('hpExeatFilter') || { value: '' }).value;
  var url = 'actions/fetch.php?action=exeats' + (filter ? '&status=' + filter : '');
  apiFetch(url).then(function (r) {
    if (!r.success) return;
    var gEx = (r.data || []).filter(function (e) { return e.s_gender === hpGender; });
    hpExeats = gEx;
    renderHpExeatMini(gEx, 'hpExeatsList');
  });
}

function renderHpExeatMini(list, containerId) {
  var el = document.getElementById(containerId); if (!el) return;
  if (!list || !list.length) { el.innerHTML = '<p style="color:var(--text-muted);padding:1rem 0">No exeat requests found.</p>'; return; }
  el.innerHTML = list.map(function (e) {
    var sc = e.status === 'approved' ? '#1a7f4e' : e.status === 'declined' ? 'var(--danger)' : 'var(--gold)';
    return '<div class="registry-card" style="margin-bottom:.5rem">' +
      '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap">' +
        '<div><span style="font-weight:700;color:var(--navy)">' + e.s_first + ' ' + e.s_last + '</span>' +
          ' <span style="font-size:12px;color:var(--text-muted)">' + (e.student_class || '') + '</span><br/>' +
          '<span style="font-size:12px;color:var(--text-muted)">Departs ' + fmtDate(e.departure_date) + ' &nbsp;|&nbsp; Returns ' + fmtDate(e.expected_return) + '</span><br/>' +
          '<span style="font-size:12px">' + e.reason + '</span></div>' +
        '<span style="font-size:12px;font-weight:700;color:' + sc + '">' + (e.status || '').toUpperCase() + '</span>' +
      '</div></div>';
  }).join('');
}

function hpViewStudent(studentId) {
  apiFetch('actions/fetch.php?action=student_full_data&student_id=' + studentId).then(function (r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    var s      = r.data.student;
    var exeats = r.data.exeats || [];

    var exHtml = exeats.length ? exeats.map(function (e) {
      var sc = e.status === 'approved' ? '#1a7f4e' : e.status === 'declined' ? 'var(--danger)' : 'var(--gold)';
      return '<div style="border:1px solid var(--border);border-radius:8px;padding:.7rem;margin-bottom:.5rem;font-size:13px">' +
        '<strong style="color:' + sc + '">' + (e.status || '').toUpperCase() + '</strong> &nbsp;' +
        fmtDate(e.departure_date) + ' → ' + fmtDate(e.expected_return) + '<br/>' +
        e.reason +
        (e.review_note ? '<br/><em style="color:var(--text-muted)">Note: ' + e.review_note + '</em>' : '') +
      '</div>';
    }).join('') : '<p style="font-size:13px;color:var(--text-muted)">No exeats recorded.</p>';

    var parentsHtml = (s.parents && s.parents.length) ? s.parents.map(function (p) {
      return '<div style="display:flex;align-items:center;gap:.6rem;padding:.45rem .7rem;background:var(--cream);border-radius:8px;border:1px solid var(--border);margin-bottom:.35rem">' +
        (p.photo_path
          ? '<img src="' + p.photo_path + '" style="width:32px;height:32px;border-radius:50%;object-fit:cover"/>'
          : '<div style="width:32px;height:32px;border-radius:50%;background:var(--navy-light,#2a3a6e);color:white;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700">' + ((p.first_name || '')[0] || '').toUpperCase() + ((p.last_name || '')[0] || '').toUpperCase() + '</div>') +
        '<div><div style="font-weight:600;font-size:.85rem">' + p.first_name + ' ' + p.last_name +
          ' <span style="font-size:.7rem;background:rgba(212,153,58,.15);color:var(--gold);padding:1px 7px;border-radius:10px">' + p.relationship + '</span></div>' +
          '<div style="font-size:.73rem;color:var(--text-muted)">📞 ' + p.phone + '</div>' +
        '</div>' +
      '</div>';
    }).join('') : '<p style="font-size:13px;color:var(--text-muted)">No parent recorded.</p>';

    document.getElementById('hpStudentContent').innerHTML =
      '<dl class="parent-detail-grid" style="margin-bottom:1rem">' +
        '<dt>Name</dt><dd>' + s.first_name + ' ' + s.last_name + '</dd>' +
        '<dt>Class</dt><dd>' + (s.student_class || '—') + '</dd>' +
        '<dt>ID No.</dt><dd>' + (s.student_id_no || '—') + '</dd>' +
      '</dl>' +
      '<div style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin-bottom:.5rem">Parents / Guardians</div>' +
      parentsHtml +
      '<div style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin:.75rem 0 .5rem">Exeat History</div>' +
      exHtml;

    document.getElementById('hpDownloadBtn').onclick = function () {
      var parentLine = (s.parents || []).map(function (p) {
        return p.first_name + ' ' + p.last_name + ' (' + p.relationship + ') ' + p.phone;
      }).join(' | ');
      var csv = 'Student,' + s.first_name + ' ' + s.last_name + '\nClass,' + s.student_class + '\nParents,' + parentLine + '\n\n';
      csv += 'EXEAT HISTORY\nStatus,Reason,Departure,Return\n';
      exeats.forEach(function (e) {
        csv += '"' + e.status + '","' + e.reason + '","' + e.departure_date + '","' + e.expected_return + '"\n';
      });
      var a = document.createElement('a');
      a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
      a.download = (s.first_name + '-' + s.last_name).replace(/\s+/g, '-') + '-record.csv';
      a.click();
      showToast('Downloaded!', 'success');
    };
    openModal('hpStudentModal');
  });
}

function hpExportStudents() {
  if (!hpStudents.length) { loadHpStudents(); showToast('Loading students, try again.'); return; }
  var h    = 'Name,Class,Status,ID No.,Parents\n';
  var rows = hpStudents.map(function (s) {
    var parentInfo = (s.parents && s.parents.length)
      ? s.parents.map(function (p) { return p.first_name + ' ' + p.last_name + ' (' + p.relationship + ') ' + p.phone; }).join(' | ')
      : (s.p_first ? s.p_first + ' ' + s.p_last + ' ' + s.p_phone : '—');
    return '"' + s.first_name + ' ' + s.last_name + '","' + (s.student_class || '') + '","' + (s.on_campus ? 'On Campus' : 'Off Campus') + '","' + (s.student_id_no || '') + '","' + parentInfo + '"';
  }).join('\n');
  var a = document.createElement('a');
  a.href     = URL.createObjectURL(new Blob([h + rows], { type: 'text/csv' }));
  a.download = hpGender.toLowerCase() + '-students.csv';
  a.click();
  showToast('Exported!', 'success');
}

function hpChangeCredentials() {
  var newUser = val('hpNewUser');
  var newPass = val('hpNewPass');
  var curPass = val('hpCurPass');

  if (!curPass) {
    showToast('Please enter your current password.', 'error');
    document.getElementById('hpCurPass').focus();
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
      document.getElementById('hpNewUser').value = '';
      document.getElementById('hpNewPass').value = '';
      document.getElementById('hpCurPass').value = '';
    } else {
      showToast(r.message, 'error');
    }
  });
}
</script>
<?php layout_footer(); ?>
