<?php
require_once 'includes/layout.php';
layout_head('Domestic Affairs');
layout_nav('domestic');
?>

<!-- Login Overlay -->
<div class="login-overlay" id="domesticLoginOverlay">
  <div class="login-box">
    <h2>Domestic Affairs Login</h2>
    <p>Head of Domestic Affairs access only.</p>
    <div class="form-group" style="margin-bottom:12px">
      <label>Username</label>
      <input type="text" id="dLoginUser" placeholder="Enter your username" autocomplete="username"/>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Password</label>
      <div class="pw-wrap">
        <input type="password" id="dLoginPass" placeholder="Enter your password"
          autocomplete="current-password"
          onkeydown="if(event.key==='Enter') dDoLogin()"/>
        <span class="pw-toggle" onclick="togglePw('dLoginPass',this)" role="button" aria-label="Show password" tabindex="0">👁</span>
      </div>
    </div>
    <button class="btn-primary" style="width:100%" onclick="dDoLogin()">Login →</button>
    <a href="index.php" class="btn-outline"
      style="display:block;width:100%;margin-top:8px;text-align:center;color:var(--navy);border-color:var(--border)">
      Cancel
    </a>
  </div>
</div>

<!-- Dashboard -->
<div id="domesticDashboard" style="display:none">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <h2 style="padding:0 1.5rem;margin-bottom:1.25rem">Domestic Affairs</h2>
      <div class="admin-sidebar-section">
        <div class="sidebar-link active" id="dsb-overview" onclick="dTab('overview')"><span class="icon">📊</span> Overview</div>
        <div class="sidebar-link" id="dsb-exeats" onclick="dTab('exeats')"><span class="icon">🚪</span> Exeat Requests</div>
        <div class="sidebar-link" id="dsb-students" onclick="dTab('students')"><span class="icon">🎒</span> Students</div>
        <div class="sidebar-link" id="dsb-settings" onclick="dTab('settings')"><span class="icon">⚙️</span> Settings</div>
      </div>
      <div style="padding:0 1.5rem;margin-top:auto;padding-top:2rem">
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:.5rem" id="dNameLabel"></div>
        <button class="btn-danger" style="width:100%" onclick="dLogout()">Logout</button>
      </div>
    </aside>

    <main class="admin-main">

      <!-- Overview -->
      <div class="admin-sub active" id="domestic-overview">
        <div class="admin-header"><h1>Overview</h1></div>
        <div class="stats-grid">
          <div class="stat-card"><span class="num" id="d-totalStudents">—</span><span class="lbl">Total Students</span></div>
          <div class="stat-card"><span class="num" id="d-pending">—</span><span class="lbl">Pending Exeats</span></div>
          <div class="stat-card"><span class="num" id="d-offcampus">—</span><span class="lbl">Off Campus</span></div>
          <div class="stat-card"><span class="num" id="d-oncampus">—</span><span class="lbl">On Campus</span></div>
        </div>
        <h3 style="font-family:var(--font-display);margin-bottom:1rem;color:var(--navy)">Pending Exeat Requests</h3>
        <div id="dPendingList"></div>
      </div>

      <!-- Exeat Requests -->
      <div class="admin-sub" id="domestic-exeats">
        <div class="admin-header">
          <h1>Exeat Requests</h1>
          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <select id="dExeatFilter" onchange="loadDExeats()" style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border)">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="declined">Declined</option>
            </select>
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px" onclick="exportExeats()">⬇ Export CSV</button>
          </div>
        </div>
        <div id="dExeatsList"></div>
      </div>

      <!-- Students -->
      <div class="admin-sub" id="domestic-students">
        <div class="admin-header">
          <h1>Students</h1>
          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" id="dStudentSearch" placeholder="🔍 Search student or parent…"
              oninput="renderDStudents()" style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border);min-width:200px"/>
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px" onclick="exportStudents()">⬇ Export CSV</button>
          </div>
        </div>
        <div id="dStudentsList"></div>
      </div>

      <!-- Settings -->
      <div class="admin-sub" id="domestic-settings">
        <div class="admin-header"><h1>Account Settings</h1></div>
        <div class="form-card" style="max-width:480px">
          <div class="form-section-title">🔐 Change Credentials</div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Username (leave blank to keep current)</label>
            <input type="text" id="dNewUser" placeholder="New username"/>
          </div>
          <div class="form-group" style="margin-bottom:12px">
            <label>New Password (leave blank to keep current)</label>
            <div class="pw-wrap">
              <input type="password" id="dNewPass" placeholder="Min 8 characters"/>
              <span class="pw-toggle" onclick="togglePw('dNewPass',this)" role="button" aria-label="Show password" tabindex="0">👁</span>
            </div>
          </div>
          <div class="form-group" style="margin-bottom:1.25rem">
            <label>Current Password <span class="req">*</span></label>
            <div class="pw-wrap">
              <input type="password" id="dCurPass" placeholder="Required to confirm changes"/>
              <span class="pw-toggle" onclick="togglePw('dCurPass',this)" role="button" aria-label="Show password" tabindex="0">👁</span>
            </div>
          </div>
          <button class="btn-primary" onclick="dChangeCredentials()">Save Changes</button>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- Review Exeat Modal -->
<div class="modal-overlay" id="reviewExeatModal" onclick="closeModalOnBackdrop(event,'reviewExeatModal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('reviewExeatModal')">✕</button>
    <h3>Review Exeat Request</h3>
    <div id="reviewExeatContent"></div>
    <div class="form-group" style="margin-bottom:12px;margin-top:1rem">
      <label>Confirmed Return Date</label>
      <input type="date" id="rv_actual_return"/>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Review Note (optional)</label>
      <textarea id="rv_note" placeholder="Add a note for the parent…"></textarea>
    </div>
    <div class="modal-actions">
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="closeModal('reviewExeatModal')">Cancel</button>
      <button class="btn-danger" onclick="submitReview('declined')">Decline</button>
      <button class="btn-primary" onclick="submitReview('approved')">Approve</button>
    </div>
  </div>
</div>

<!-- Student Data Modal -->
<div class="modal-overlay" id="studentDataModal" onclick="closeModalOnBackdrop(event,'studentDataModal')">
  <div class="modal" style="max-width:640px">
    <button class="modal-close" onclick="closeModal('studentDataModal')">✕</button>
    <h3>Student Full Record</h3>
    <div id="studentDataContent"></div>
    <div class="modal-actions">
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="closeModal('studentDataModal')">Close</button>
      <button class="btn-primary" id="downloadStudentBtn">⬇ Download PDF</button>
    </div>
  </div>
</div>

<script>
var dAllExeats  = [];
var dAllStudents = [];
var dCurrentExeatId = null;

document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('domesticLoginOverlay').style.display = 'flex';
});

function dDoLogin() {
  var fd = new FormData();
  fd.append('action','admin_login');
  fd.append('username', val('dLoginUser'));
  fd.append('password', val('dLoginPass'));
  apiPost('actions/insert.php', fd).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    var role = r.role || (r.data && r.data.role) || '';
    var name = r.name || (r.data && r.data.name) || '';
    if (role !== 'domestic_affairs' && role !== 'admin') {
      showToast('Access denied. This portal is for Domestic Affairs only.', 'error');
      var fd2 = new FormData(); fd2.append('action','admin_logout');
      apiPost('actions/insert.php', fd2);
      return;
    }
    document.getElementById('domesticLoginOverlay').style.display = 'none';
    document.getElementById('domesticDashboard').style.display = 'block';
    document.getElementById('dNameLabel').textContent = name;
    loadDOverview();
  });
}

function dLogout() {
  var fd = new FormData(); fd.append('action','admin_logout');
  apiPost('actions/insert.php', fd).then(function() { window.location.href = 'index.php'; });
}

function dTab(tab) {
  document.querySelectorAll('.admin-sub').forEach(function(s) { s.classList.remove('active'); });
  document.querySelectorAll('.sidebar-link').forEach(function(l) { l.classList.remove('active'); });
  var sub = document.getElementById('domestic-' + tab); if (sub) sub.classList.add('active');
  var sb  = document.getElementById('dsb-'     + tab); if (sb)  sb.classList.add('active');
  if (tab === 'exeats')   loadDExeats();
  if (tab === 'students') loadDStudents();
}

function loadDOverview() {
  apiFetch('actions/fetch.php?action=stats').then(function(r) {
    if (!r.success) return;
    setText('d-totalStudents', r.data.totalStudents);
    setText('d-pending',       r.data.pendingExeats);
    setText('d-offcampus',     r.data.offCampus);
    setText('d-oncampus',      r.data.totalStudents - r.data.offCampus);
  });
  apiFetch('actions/fetch.php?action=exeats&status=pending').then(function(r) {
    if (!r.success) return;
    renderExeatCards(r.data, 'dPendingList', true);
  });
}

function loadDExeats() {
  var filter = (document.getElementById('dExeatFilter')||{value:''}).value;
  var url = 'actions/fetch.php?action=exeats' + (filter ? '&status='+filter : '');
  apiFetch(url).then(function(r) {
    if (!r.success) return;
    dAllExeats = r.data;
    renderExeatCards(r.data, 'dExeatsList', true);
  });
}

function renderExeatCards(list, containerId, showActions) {
  var el = document.getElementById(containerId); if (!el) return;
  if (!list || !list.length) {
    el.innerHTML = '<p style="color:var(--text-muted);padding:1.5rem 0">No exeat requests found.</p>';
    return;
  }
  el.innerHTML = list.map(function(e) {
    var statusColor = e.status === 'approved' ? 'var(--success)' : e.status === 'declined' ? 'var(--danger)' : 'var(--gold)';
    var statusLabel = e.status.charAt(0).toUpperCase() + e.status.slice(1);
    var actions = showActions && e.status === 'pending'
      ? '<button class="btn-primary" style="font-size:13px;padding:7px 16px" onclick="openReview(' + e.id + ', \'' + (e.s_first+' '+e.s_last).replace(/'/g,"\\'") + '\', \'' + e.expected_return + '\')">Review</button>'
      : '';
    return '<div class="registry-card" style="margin-bottom:.85rem">' +
      '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap">' +
        '<div style="flex:1">' +
          '<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem">' +
            '<span style="font-weight:700;color:var(--navy)">' + e.s_first + ' ' + e.s_last + '</span>' +
            '<span style="font-size:11px;background:var(--cream);color:var(--text-muted);padding:2px 8px;border-radius:10px">' + (e.student_class||'') + '</span>' +
            '<span style="font-size:11px;color:' + statusColor + ';font-weight:700;padding:2px 8px;border:1.5px solid ' + statusColor + ';border-radius:10px">' + statusLabel + '</span>' +
          '</div>' +
          '<div style="font-size:13px;color:var(--text-muted);margin-bottom:.3rem">Parents: ' +
            ((e.all_parents && e.all_parents.length)
              ? e.all_parents.map(function(p){ return p.first_name+' '+p.last_name+' ('+p.relationship+') · '+p.phone; }).join(' &nbsp;|&nbsp; ')
              : (e.p_first ? e.p_first+' '+e.p_last+' · '+e.p_phone : '—')) +
          '</div>' +
          '<div style="font-size:13px;margin-bottom:.3rem"><strong>Reason:</strong> ' + e.reason + '</div>' +
          '<div style="font-size:12px;color:var(--text-muted)">' +
            '📅 Departs: <strong>' + fmtDate(e.departure_date) + '</strong> at ' + fmtTime(e.departure_time) +
            ' &nbsp;|&nbsp; Returns: <strong>' + fmtDate(e.expected_return) + '</strong>' +
            (e.actual_return ? ' &nbsp;|&nbsp; Confirmed return: <strong>' + fmtDate(e.actual_return) + '</strong>' : '') +
          '</div>' +
          (e.review_note ? '<div style="font-size:12px;margin-top:.3rem;color:var(--text-muted)">Note: ' + e.review_note + '</div>' : '') +
        '</div>' +
        '<div style="display:flex;gap:8px;align-items:center;flex-shrink:0">' +
          actions +
          '<span style="font-size:11px;color:var(--text-muted)">' + fmtDate((e.created_at||'').split(' ')[0]) + '</span>' +
        '</div>' +
      '</div>' +
    '</div>';
  }).join('');
}

function openReview(exeatId, studentName, expectedReturn) {
  dCurrentExeatId = exeatId;
  document.getElementById('reviewExeatContent').innerHTML =
    '<div style="background:var(--cream);border-radius:10px;padding:1rem;margin-bottom:1rem">' +
    '<strong>' + studentName + '</strong> — expected return: <strong>' + fmtDate(expectedReturn) + '</strong>' +
    '</div>';
  document.getElementById('rv_actual_return').value = expectedReturn;
  document.getElementById('rv_note').value = '';
  openModal('reviewExeatModal');
}

function submitReview(status) {
  if (!dCurrentExeatId) return;
  var fd = new FormData();
  fd.append('action',        'review_exeat');
  fd.append('exeat_id',      dCurrentExeatId);
  fd.append('status',        status);
  fd.append('review_note',   val('rv_note'));
  fd.append('actual_return', val('rv_actual_return'));
  apiPost('actions/insert.php', fd).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    closeModal('reviewExeatModal');
    showToast('Exeat ' + status + '!', 'success');
    loadDOverview();
    loadDExeats();
  });
}

function loadDStudents() {
  apiFetch('actions/fetch.php?action=parents').then(function(r) {
    if (!r.success) return;
    dAllStudents = [];
    // Use the new student-centric shape if available, fall back to parent-centric
    var studentList = r.students || [];
    if (studentList.length) {
      studentList.forEach(function(s) {
        dAllStudents.push(Object.assign({}, s, {
          p_name:  (s.parents||[]).map(function(p){ return p.first_name+' '+p.last_name; }).join(' · ') || '—',
          p_phone: (s.parents||[]).map(function(p){ return p.phone; }).join(' / ') || '—'
        }));
      });
    } else {
      // Legacy fallback
      r.data.forEach(function(p) {
        (p.wards||[]).forEach(function(w) {
          dAllStudents.push(Object.assign({}, w, { p_name: p.first_name+' '+p.last_name, p_phone: p.phone }));
        });
      });
    }
    renderDStudents();
    initDomesticSearchDropdown();
  });
}

var _dSearchTimer = null;
function initDomesticSearchDropdown() {
  var input = document.getElementById('dStudentSearch');
  if (!input || input.dataset.liveSearch) return;
  input.dataset.liveSearch = '1';

  if (!input.parentElement.classList.contains('search-box')) {
    var box = document.createElement('div');
    box.className = 'search-box';
    box.style.cssText = 'position:relative;min-width:200px';
    input.parentNode.insertBefore(box, input);
    box.appendChild(input);
    input.style.width = '100%';
    input.style.minWidth = '';
    var dd = document.createElement('div');
    dd.className = 'search-results';
    dd.id = 'dSearchResults';
    box.appendChild(dd);
  }

  input.addEventListener('input', function() {
    var q = this.value.trim();
    clearTimeout(_dSearchTimer);
    var results = document.getElementById('dSearchResults');
    if (!results) return;
    renderDStudents();
    if (q.length < 2) { results.innerHTML = ''; results.classList.remove('open'); return; }
    _dSearchTimer = setTimeout(function() {
      apiFetch('actions/fetch.php?action=search_parents&q=' + encodeURIComponent(q)).then(function(r) {
        results.innerHTML = '';
        if (!r.success || !r.data.length) { results.classList.remove('open'); return; }
        var seen = {};
        var hits = r.data.filter(function(s) { if (seen[s.s_id]) return false; seen[s.s_id]=1; return true; });
        hits.forEach(function(s) {
          var item = document.createElement('div');
          item.className = 'search-result-item';
          var gIcon = s.gender === 'Male' ? '👨‍🎓' : s.gender === 'Female' ? '👩‍🎓' : '🎓';
          item.innerHTML =
            '<div style="width:32px;height:32px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0">' + gIcon + '</div>' +
            '<div style="flex:1;min-width:0">' +
              '<div class="s-name">' + s.s_first + ' ' + s.s_last + '</div>' +
              '<div class="s-meta">' + (s.student_class||'') + (s.nhis_id ? ' · NHIS: '+s.nhis_id : '') + '</div>' +
              '<div class="s-meta" style="color:var(--navy-light)">👥 ' + s.p_first + ' ' + s.p_last + ' <span style="opacity:.7">(' + s.relationship + ')</span></div>' +
            '</div>';
          item.addEventListener('pointerdown', function(e) {
            e.preventDefault();
            var inp = document.getElementById('dStudentSearch');
            if (inp) { inp.value = s.s_first + ' ' + s.s_last; renderDStudents(); }
            results.innerHTML = ''; results.classList.remove('open');
          });
          results.appendChild(item);
        });
        results.classList.add('open');
      });
    }, 250);
  });

  input.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      var r = document.getElementById('dSearchResults');
      if (r) { r.innerHTML = ''; r.classList.remove('open'); }
    }
  });
}

function renderDStudents() {
  var q = (document.getElementById('dStudentSearch')||{value:''}).value.toLowerCase();
  var filtered = q ? dAllStudents.filter(function(s) {
    var parentText = (s.parents||[]).map(function(p){ return p.first_name+' '+p.last_name+' '+(p.phone||''); }).join(' ');
    return (s.first_name+' '+s.last_name+' '+(s.student_class||'')+' '+s.p_name+' '+s.p_phone+' '+parentText).toLowerCase().indexOf(q) !== -1;
  }) : dAllStudents;

  var el = document.getElementById('dStudentsList'); if (!el) return;
  if (!filtered.length) { el.innerHTML = '<p style="color:var(--text-muted);padding:1.5rem 0">No students found.</p>'; return; }

  el.innerHTML = '<div style="overflow-x:auto"><table><thead><tr>' +
    '<th>Student</th><th>Class</th><th>Gender</th><th>NHIS No.</th><th>Parent</th><th>Phone</th><th></th>' +
    '</tr></thead><tbody>' +
    filtered.map(function(s) {
      var av = s.photo_path
        ? '<div class="avatar-sm"><img src="'+s.photo_path+'" alt=""/></div>'
        : '<div class="avatar-sm">'+initials(s.first_name,s.last_name)+'</div>';
      return '<tr><td><div class="parent-cell">'+av+'<span>'+s.first_name+' '+s.last_name+'</span></div></td>' +
        '<td>'+(s.student_class||'—')+'</td><td>'+(s.gender||'—')+'</td><td>'+(s.nhis_id||'—')+'</td>' +
        '<td>'+s.p_name+'</td><td>'+s.p_phone+'</td>' +
        '<td><button class="btn-outline" style="font-size:12px;padding:5px 12px;color:var(--navy);border-color:var(--border)" onclick="viewStudentData('+s.id+')">View / Download</button></td></tr>';
    }).join('') + '</tbody></table></div>';
}

function viewStudentData(studentId) {
  apiFetch('actions/fetch.php?action=student_full_data&student_id='+studentId).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    var s = r.data.student;
    var exeats = r.data.exeats || [];

    var exHtml = exeats.length ? exeats.map(function(e) {
      var sc = e.status==='approved'?'var(--success)':e.status==='declined'?'var(--danger)':'var(--gold)';
      return '<div style="border:1px solid var(--border);border-radius:8px;padding:.75rem 1rem;margin-bottom:.5rem">' +
        '<div style="display:flex;justify-content:space-between;margin-bottom:.3rem">' +
          '<span style="font-size:12px;font-weight:700;color:'+sc+'">' + (e.status||'').toUpperCase() + '</span>' +
          '<span style="font-size:11px;color:var(--text-muted)">' + fmtDate((e.created_at||'').split(' ')[0]) + '</span>' +
        '</div>' +
        '<div style="font-size:13px;margin-bottom:.2rem"><strong>Reason:</strong> '+e.reason+'</div>' +
        '<div style="font-size:12px;color:var(--text-muted)">Departs: '+fmtDate(e.departure_date)+' | Returns: '+fmtDate(e.expected_return)+(e.actual_return?' | Confirmed: '+fmtDate(e.actual_return):'')+
        (e.reviewer_name?' | Reviewed by: '+e.reviewer_name:'')+
        '</div>' +
        (e.review_note ? '<div style="font-size:12px;margin-top:.25rem">Note: '+e.review_note+'</div>' : '') +
      '</div>';
    }).join('') : '<p style="color:var(--text-muted);font-size:13px">No exeat requests recorded.</p>';

    var parentsHtml = (s.parents && s.parents.length) ? s.parents.map(function(p) {
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

    document.getElementById('studentDataContent').innerHTML =
      '<div style="display:flex;align-items:center;gap:1.1rem;padding:1rem;background:var(--cream);border-radius:12px;border:1px solid var(--border);margin-bottom:1.1rem">' +
        sAvatar +
        '<div>' +
          '<div style="font-weight:700;font-size:1.15rem;color:var(--navy)">'+s.first_name+' '+s.last_name+'</div>' +
          '<div style="font-size:.82rem;color:var(--text-muted);margin-top:.2rem">'+(s.student_class||'—')+' &nbsp;·&nbsp; '+(s.gender||'—')+'</div>' +
          (s.date_of_birth ? '<div style="font-size:.78rem;color:var(--text-muted);margin-top:.15rem">DOB: '+fmtDate(s.date_of_birth)+'</div>' : '') +
        '</div>' +
      '</div>' +
      '<div style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin-bottom:.6rem">Parents / Guardians</div>' +
      parentsHtml +
      '<div style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin:.75rem 0 .6rem">Exeat History ('+exeats.length+')</div>' +
      exHtml;

    document.getElementById('downloadStudentBtn').onclick = function() { downloadStudentPDF(s, exeats); };
    openModal('studentDataModal');
  });
}

function downloadStudentPDF(s, exeats) {
  var csv = 'SchoolConnect Student Record\n';
  csv += 'Generated: ' + new Date().toLocaleString('en-GB') + '\n\n';
  csv += 'STUDENT DETAILS\n';
  csv += 'Name,' + s.first_name + ' ' + s.last_name + '\n';
  csv += 'Class,' + (s.student_class||'') + '\n';
  csv += 'Gender,' + (s.gender||'') + '\n';
  csv += 'DOB,' + (s.date_of_birth||'') + '\n';
  if (s.parents && s.parents.length) {
    s.parents.forEach(function(p, i) {
      csv += (i===0?'Parent':'') + ',' + p.first_name + ' ' + p.last_name + ' (' + p.relationship + ') ' + p.phone + '\n';
    });
  } else {
    csv += 'Parent,' + (s.p_first||'') + ' ' + (s.p_last||'') + '\n';
    csv += 'Phone,' + (s.phone||'') + '\n';
  }
  csv += '\nEXEAT HISTORY\n';
  csv += 'Status,Reason,Departure,Expected Return,Actual Return,Review Note,Reviewed By\n';
  exeats.forEach(function(e) {
    csv += '"'+e.status+'","'+e.reason+'","'+e.departure_date+'","'+e.expected_return+'","'+(e.actual_return||'')+'","'+(e.review_note||'')+'","'+(e.reviewer_name||'')+'"\n';
  });
  var a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([csv], {type:'text/csv'}));
  a.download = (s.first_name+'-'+s.last_name+'-record').replace(/\s+/g,'-').toLowerCase() + '.csv';
  a.click();
  showToast('Downloaded!', 'success');
}

function exportExeats() {
  var list = dAllExeats;
  if (!list.length) { showToast('No data to export.'); return; }
  var h = 'Student,Class,Gender,Status,Reason,Departure Date,Departure Time,Expected Return,Actual Return,Parents,Review Note,Reviewed By\n';
  var rows = list.map(function(e) {
    var parentsStr = (e.all_parents && e.all_parents.length)
      ? e.all_parents.map(function(p){ return p.first_name+' '+p.last_name+' ('+p.relationship+') '+p.phone; }).join(' | ')
      : (e.p_first ? e.p_first+' '+e.p_last+' '+e.p_phone : '—');
    return '"'+e.s_first+' '+e.s_last+'","'+(e.student_class||'')+'","'+(e.s_gender||'')+'","'+e.status+'","'+e.reason+'","'+e.departure_date+'","'+e.departure_time+'","'+e.expected_return+'","'+(e.actual_return||'')+'","'+parentsStr+'","'+(e.review_note||'')+'","'+(e.reviewer_name||'')+'"';
  }).join('\n');
  var a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([h+rows],{type:'text/csv'}));
  a.download = 'exeat-requests.csv'; a.click();
  showToast('Exported!', 'success');
}

function exportStudents() {
  if (!dAllStudents.length) { showToast('Load students first.'); return; }
  var h = 'First Name,Last Name,Class,Gender,Parent,Phone\n';
  var rows = dAllStudents.map(function(s) {
    return '"'+s.first_name+'","'+s.last_name+'","'+(s.student_class||'')+'","'+(s.gender||'')+'","'+s.p_name+'","'+s.p_phone+'"';
  }).join('\n');
  var a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([h+rows],{type:'text/csv'}));
  a.download = 'students.csv'; a.click();
  showToast('Exported!', 'success');
}

function dChangeCredentials() {
  var fd = new FormData();
  fd.append('action',           'change_credentials');
  fd.append('new_username',     val('dNewUser'));
  fd.append('new_password',     val('dNewPass'));
  fd.append('current_password', val('dCurPass'));
  apiPost('actions/insert.php', fd).then(function(r) {
    if (r.success) {
      showToast('Credentials updated!', 'success');
      document.getElementById('dNewUser').value = '';
      document.getElementById('dNewPass').value = '';
      document.getElementById('dCurPass').value = '';
    } else { showToast(r.message, 'error'); }
  });
}
</script>
<?php layout_footer(); ?>