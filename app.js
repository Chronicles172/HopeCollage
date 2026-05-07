// ============================================================
// SchoolConnect — assets/js/app.js
// Shared JS for all pages. Each page calls its own init
// function via an inline <script> at the bottom of the page.
// ============================================================

// ── STATE ────────────────────────────────────────────────────
let allParents               = [];
let allEvents                = [];
let adminEvtId               = null;
let foundParent              = null;
let wardCount                = 1;
let searchTimers             = {};

// ── MOBILE NAV ───────────────────────────────────────────────
function toggleMobile() {
  document.getElementById('mobileMenu').classList.toggle('open');
}

// Close mobile menu on nav link click
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.mobile-menu a').forEach(function(a) {
    a.addEventListener('click', function() {
      document.getElementById('mobileMenu').classList.remove('open');
    });
  });
});

// ── HOME PAGE INIT ───────────────────────────────────────────
function loadHomeStats() {
  apiFetch('actions/fetch.php?action=stats').then(function(r) {
    if (!r.success) return;
    setText('statParents',  r.data.totalParents);
    setText('statStudents', r.data.totalStudents);
    setText('statEvents',   r.data.totalEvents);
  });
}

function loadEventStrip() {
  apiFetch('actions/fetch.php?action=events').then(function(r) {
    if (!r.success) return;
    allEvents = r.data;
    var upcoming = allEvents.filter(function(e) { return e.event_date >= today(); }).slice(0, 4);
    var strip = document.getElementById('stripEvents');
    if (!strip) return;
    if (!upcoming.length) { strip.innerHTML = '<span class="event-pill">No upcoming events</span>'; return; }
    strip.innerHTML = upcoming.map(function(e) {
      return '<span class="event-pill"><strong>' + e.name + '</strong> &middot; ' + fmtDate(e.event_date) + '</span>';
    }).join('');
  });
}

function loadHomeEvents() {
  apiFetch('actions/fetch.php?action=events').then(function(r) {
    if (!r.success) return;
    allEvents = r.data;
    var el = document.getElementById('homeEvents');
    if (!el) return;

    var todayStr   = today();
    var cutoffDate = new Date();
    cutoffDate.setDate(cutoffDate.getDate() - 30);
    var cutoff = cutoffDate.toISOString().slice(0, 10);

    var upcoming = allEvents.filter(function(e) { return e.event_date >= todayStr; });
    var past     = allEvents.filter(function(e) { return e.event_date < todayStr && e.event_date >= cutoff; });

    var upcomingHtml = upcoming.length
      ? upcoming.map(function(e) { return renderEvCard(e, false); }).join('')
      : '<p style="color:rgba(255,255,255,.5);text-align:center;grid-column:1/-1;padding:2rem 0">No upcoming events scheduled.</p>';

    el.innerHTML = upcomingHtml;

    // Past events section (below, collapsible)
    var pastEl = document.getElementById('pastEvents');
    if (!pastEl) return;
    if (!past.length) {
      pastEl.innerHTML = '<p style="color:var(--text-muted);text-align:center;grid-column:1/-1;padding:1.5rem 0">No recent past events.</p>';
    } else {
      pastEl.innerHTML = past.map(function(e) { return renderEvCard(e, true); }).join('');
    }
  });
}

function renderEvCard(e, isPast) {
  var cardStyle = isPast ? 'opacity:.72' : '';
  var badges = isPast
    ? '<div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.6rem"><span class="ev-type-badge" style="background:rgba(90,110,135,.15);color:var(--text-muted)">Past</span><span class="ev-type-badge">' + e.event_type + '</span></div>'
    : '<div class="ev-type-badge" style="margin-bottom:.6rem">' + e.event_type + '</div>';
  return '<div class="ev-card" style="' + cardStyle + '">' +
    badges +
    '<div class="ev-name">' + e.name + '</div>' +
    '<div class="ev-meta">' +
      '<span>&#128197; ' + fmtDate(e.event_date) + (e.event_time ? ' at ' + fmtTime(e.event_time) : '') + '</span>' +
      (e.venue       ? '<span>&#128205; ' + e.venue + '</span>' : '') +
      (e.description ? '<span style="color:var(--text-muted)">' + e.description + '</span>' : '') +
    '</div></div>';
}

// ── ATTENDANCE PAGE INIT ─────────────────────────────────────
function loadAttendanceEvents() {
  apiFetch('actions/fetch.php?action=events').then(function(r) {
    if (!r.success) return;
    allEvents = r.data;
    populateEventDropdown('sign_event', allEvents);
  });
}

// ── REGISTRATION – MULTI-WARD ────────────────────────────────
function changeWardCount(delta) {
  wardCount = Math.max(1, Math.min(10, wardCount + delta));
  document.getElementById('wardCountDisplay').textContent = wardCount;
  renderWardForms();
}

function renderWardForms() {
  var container = document.getElementById('wardFormsContainer');
  if (!container) return;
  var existing = [];
  document.querySelectorAll('.ward-block').forEach(function(blk, i) {
    existing[i] = {
      first:  (blk.querySelector('[data-field="first"]')  || {}).value || '',
      last:   (blk.querySelector('[data-field="last"]')   || {}).value || '',
      cls:    (blk.querySelector('[data-field="class"]')  || {}).value || '',
      house:  (blk.querySelector('[data-field="house"]')  || {}).value || '',
      nhis:   (blk.querySelector('[data-field="nhis"]')   || {}).value || '',
      idno:   (blk.querySelector('[data-field="idno"]')   || {}).value || '',
      dob:    (blk.querySelector('[data-field="dob"]')    || {}).value || '',
      gender: (blk.querySelector('[data-field="gender"]') || {}).value || '',
    };
  });
  container.innerHTML = '';
  for (var i = 0; i < wardCount; i++) {
    var prev  = existing[i] || {};
    var label = wardCount === 1 ? 'Ward Details' : 'Ward ' + (i+1) + ' of ' + wardCount;
    var rmBtn = wardCount > 1
      ? '<button type="button" onclick="removeWard(' + i + ')" style="background:none;border:none;color:var(--danger);font-size:18px;cursor:pointer">&#10005;</button>'
      : '';
    container.insertAdjacentHTML('beforeend',
      '<div class="ward-block" id="ward-block-' + i + '">' +
        '<div class="ward-block-header">' +
          '<div class="ward-block-title"><div class="ward-block-num">' + (i+1) + '</div>' + label + '</div>' +
          rmBtn +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>First Name <span class="req">*</span></label>' +
            '<input type="text" data-ward="' + i + '" data-field="first" placeholder="e.g. Ama" value="' + (prev.first||'') + '"/></div>' +
          '<div class="form-group"><label>Last Name <span class="req">*</span></label>' +
            '<input type="text" data-ward="' + i + '" data-field="last" placeholder="e.g. Mensah" value="' + (prev.last||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>Class <span class="req">*</span></label>' +
            '<input type="text" data-ward="' + i + '" data-field="class" placeholder="e.g. JHS 2B" value="' + (prev.cls||'') + '"/></div>' +
          '<div class="form-group"><label>House / Hall</label>' +
            '<input type="text" data-ward="' + i + '" data-field="house" placeholder="e.g. Aggrey House" value="' + (prev.house||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>NHIS ID Number</label>' +
            '<input type="text" data-ward="' + i + '" data-field="nhis" placeholder="e.g. NHIS-123456" value="' + (prev.nhis||'') + '"/></div>' +
          '<div class="form-group"><label>Student ID No.</label>' +
            '<input type="text" data-ward="' + i + '" data-field="idno" placeholder="Optional" value="' + (prev.idno||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>Gender <span class="req">*</span></label>' +
            '<select data-ward="' + i + '" data-field="gender">' +
              '<option value="">&#8212; Select &#8212;</option>' +
              '<option value="Male"'   + (prev.gender === 'Male'   ? ' selected' : '') + '>Male</option>' +
              '<option value="Female"' + (prev.gender === 'Female' ? ' selected' : '') + '>Female</option>' +
              '<option value="Other"'  + (prev.gender === 'Other'  ? ' selected' : '') + '>Other</option>' +
            '</select></div>' +
          '<div class="form-group"><label>Date of Birth <span class="req">*</span></label>' +
            '<input type="date" data-ward="' + i + '" data-field="dob" value="' + (prev.dob||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row single">' +
          '<div class="form-group"><label>Student Photo (optional)</label>' +
            '<label class="photo-upload-label" for="ward_photo_' + i + '">' +
              '<img id="ward_photo_preview_' + i + '" class="photo-preview" alt=""/>' +
              '<span>&#128247; Click to upload photo</span>' +
              '<span style="font-size:11px">JPG / PNG / WEBP &middot; max 5 MB</span>' +
            '</label>' +
            '<input type="file" id="ward_photo_' + i + '" accept="image/*" style="display:none" ' +
              'onchange="previewPhoto(this,\'ward_photo_preview_' + i + '\')"/>' +
          '</div>' +
        '</div>' +
      '</div>');
  }
}

function removeWard(index) {
  var blocks = document.querySelectorAll('.ward-block');
  var all = [];
  blocks.forEach(function(blk, i) {
    if (i !== index) all.push({
      first:  (blk.querySelector('[data-field="first"]')  || {}).value || '',
      last:   (blk.querySelector('[data-field="last"]')   || {}).value || '',
      cls:    (blk.querySelector('[data-field="class"]')  || {}).value || '',
      house:  (blk.querySelector('[data-field="house"]')  || {}).value || '',
      nhis:   (blk.querySelector('[data-field="nhis"]')   || {}).value || '',
      idno:   (blk.querySelector('[data-field="idno"]')   || {}).value || '',
      dob:    (blk.querySelector('[data-field="dob"]')    || {}).value || '',
      gender: (blk.querySelector('[data-field="gender"]') || {}).value || '',
    });
  });
  wardCount = Math.max(1, wardCount - 1);
  document.getElementById('wardCountDisplay').textContent = wardCount;
  var container = document.getElementById('wardFormsContainer');
  container.innerHTML = '';
  for (var i = 0; i < wardCount; i++) {
    var prev  = all[i] || {};
    var label = wardCount === 1 ? 'Ward Details' : 'Ward ' + (i+1) + ' of ' + wardCount;
    var rmBtn = wardCount > 1
      ? '<button type="button" onclick="removeWard(' + i + ')" style="background:none;border:none;color:var(--danger);font-size:18px;cursor:pointer">&#10005;</button>'
      : '';
    container.insertAdjacentHTML('beforeend',
      '<div class="ward-block" id="ward-block-' + i + '">' +
        '<div class="ward-block-header">' +
          '<div class="ward-block-title"><div class="ward-block-num">' + (i+1) + '</div>' + label + '</div>' + rmBtn +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>First Name <span class="req">*</span></label>' +
            '<input type="text" data-ward="' + i + '" data-field="first" placeholder="e.g. Ama" value="' + (prev.first||'') + '"/></div>' +
          '<div class="form-group"><label>Last Name <span class="req">*</span></label>' +
            '<input type="text" data-ward="' + i + '" data-field="last" value="' + (prev.last||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>Class <span class="req">*</span></label>' +
            '<input type="text" data-ward="' + i + '" data-field="class" value="' + (prev.cls||'') + '"/></div>' +
          '<div class="form-group"><label>House / Hall</label>' +
            '<input type="text" data-ward="' + i + '" data-field="house" placeholder="e.g. Aggrey House" value="' + (prev.house||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>NHIS ID Number</label>' +
            '<input type="text" data-ward="' + i + '" data-field="nhis" placeholder="e.g. NHIS-123456" value="' + (prev.nhis||'') + '"/></div>' +
          '<div class="form-group"><label>Student ID No.</label>' +
            '<input type="text" data-ward="' + i + '" data-field="idno" value="' + (prev.idno||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group"><label>Gender <span class="req">*</span></label>' +
            '<select data-ward="' + i + '" data-field="gender">' +
              '<option value="">&#8212; Select &#8212;</option>' +
              '<option value="Male"'   + (prev.gender === 'Male'   ? ' selected' : '') + '>Male</option>' +
              '<option value="Female"' + (prev.gender === 'Female' ? ' selected' : '') + '>Female</option>' +
              '<option value="Other"'  + (prev.gender === 'Other'  ? ' selected' : '') + '>Other</option>' +
            '</select></div>' +
          '<div class="form-group"><label>Date of Birth <span class="req">*</span></label>' +
            '<input type="date" data-ward="' + i + '" data-field="dob" value="' + (prev.dob||'') + '"/></div>' +
        '</div>' +
        '<div class="form-row single">' +
          '<div class="form-group"><label>Student Photo (optional)</label>' +
            '<label class="photo-upload-label" for="ward_photo_' + i + '">' +
              '<img id="ward_photo_preview_' + i + '" class="photo-preview" alt=""/>' +
              '<span>&#128247; Click to upload photo</span>' +
            '</label>' +
            '<input type="file" id="ward_photo_' + i + '" accept="image/*" style="display:none" ' +
              'onchange="previewPhoto(this,\'ward_photo_preview_' + i + '\')"/>' +
          '</div>' +
        '</div>' +
      '</div>');
  }
}

// ── SUBMIT REGISTRATION ───────────────────────────────────────
function submitRegistration() {
  submitNewWards();
}

function submitNewWards() {
  if (!currentMaritalStatus) { showToast('Please select a marital status before registering.', 'error'); return; }
  var pfirst = val('reg_pfirst'), plast = val('reg_plast'), phone = val('reg_phone');
  if (!pfirst || !plast || !phone) { showToast('Please fill in all required parent fields.', 'error'); return; }

  // Validate second parent fields if married
  var includeSpouse = (typeof currentMaritalStatus !== 'undefined') && currentMaritalStatus === 'married';
  if (includeSpouse) {
    var spFirst = val('sp_pfirst'), spLast = val('sp_plast'), spPhone = val('sp_phone');
    if (!spFirst || !spLast || !spPhone) { showToast('Please fill in all required spouse fields.', 'error'); return; }
  }

  var wards = [];
  for (var i = 0; i < wardCount; i++) {
    var first = ((document.querySelector('[data-ward="' + i + '"][data-field="first"]') || {}).value || '').trim();
    var last  = ((document.querySelector('[data-ward="' + i + '"][data-field="last"]')  || {}).value || '').trim();
    var cls   = ((document.querySelector('[data-ward="' + i + '"][data-field="class"]') || {}).value || '').trim();
    if (!first || !last || !cls) { showToast('Please complete required fields for Ward ' + (i+1) + '.', 'error'); return; }
    wards.push({
      first:  first, last: last, cls: cls,
      house:  ((document.querySelector('[data-ward="' + i + '"][data-field="house"]')  || {}).value || '').trim(),
      nhis:   ((document.querySelector('[data-ward="' + i + '"][data-field="nhis"]')   || {}).value || '').trim(),
      dob:    ((document.querySelector('[data-ward="' + i + '"][data-field="dob"]')    || {}).value || ''),
      gender: ((document.querySelector('[data-ward="' + i + '"][data-field="gender"]') || {}).value || ''),
      idno:   ((document.querySelector('[data-ward="' + i + '"][data-field="idno"]')   || {}).value || ''),
      photo:  (document.getElementById('ward_photo_' + i) || {files:[]}).files[0] || null,
    });
  }

  // Build primary parent FormData
  var fd = new FormData();
  fd.append('action','register_parent'); fd.append('first_name',pfirst); fd.append('last_name',plast);
  fd.append('phone',phone); fd.append('email',val('reg_email')); fd.append('address',val('reg_address'));
  fd.append('relationship',val('reg_rel'));
  fd.append('national_id_type', val('reg_id_type'));
  fd.append('national_id_no',   val('reg_id_no'));
  fd.append('ward_count',wardCount);
  wards.forEach(function(w, i) {
    fd.append('students[' + i + '][first]',  w.first); fd.append('students[' + i + '][last]',   w.last);
    fd.append('students[' + i + '][class]',  w.cls);   fd.append('students[' + i + '][house]',  w.house);
    fd.append('students[' + i + '][nhis]',   w.nhis);  fd.append('students[' + i + '][dob]',    w.dob);
    fd.append('students[' + i + '][gender]', w.gender); fd.append('students[' + i + '][idno]',  w.idno);
    if (w.photo) fd.append('student_photo_' + i, w.photo);
  });
  var pFile = (document.getElementById('reg_pphoto') || {files:[]}).files[0];
  if (pFile) fd.append('parent_photo', pFile);

  apiPost('actions/insert.php', fd).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }

    // If spouse included, register them and link to same students
    if (includeSpouse) {
      var primaryStudentIds = r.student_ids || [];
      var spFd = new FormData();
      spFd.append('action','register_parent');
      spFd.append('first_name', val('sp_pfirst')); spFd.append('last_name', val('sp_plast'));
      spFd.append('phone', val('sp_phone')); spFd.append('email', val('sp_email')); spFd.append('address', val('reg_address'));
      spFd.append('relationship', val('sp_rel'));
      spFd.append('national_id_type', val('sp_id_type'));
      spFd.append('national_id_no', val('sp_id_no'));
      // Register spouse with no wards directly (will link below)
      spFd.append('ward_count', '0');
      var spFile = (document.getElementById('sp_pphoto') || {files:[]}).files[0];
      if (spFile) spFd.append('parent_photo', spFile);

      apiPost('actions/insert.php', spFd).then(function(sr) {
        if (!sr.success) {
          showToast('Primary parent registered! But spouse could not be saved: ' + sr.message, 'error');
          clearRegForm();
          return;
        }
        // Link spouse to the same students
        var spouseId = sr.parent_id;
        var linkPromises = primaryStudentIds.map(function(sid) {
          var lfd = new FormData();
          lfd.append('action','link_parent_student');
          lfd.append('parent_id', spouseId);
          lfd.append('student_id', sid);
          return apiPost('actions/insert.php', lfd);
        });
        Promise.all(linkPromises).then(function() {
          showToast('Both parents registered and linked to ' + wardCount + ' ward(s) successfully!', 'success');
          clearRegForm();
        });
      });
    } else {
      showToast(wardCount > 1 ? 'Registration successful! ' + wardCount + ' wards registered.' : 'Registration successful!', 'success');
      clearRegForm();
    }
  });
}

function clearRegForm() {
  ['reg_pfirst','reg_plast','reg_phone','reg_email','reg_address','reg_id_no'].forEach(function(id) {
    var el = document.getElementById(id); if (el) el.value = '';
  });
  var rel = document.getElementById('reg_rel'); if (rel) rel.value = 'Guardian';
  var idt = document.getElementById('reg_id_type'); if (idt) idt.value = '';
  var pp  = document.getElementById('reg_pphoto_preview');
  if (pp) { pp.style.display = 'none'; pp.src = ''; }
  // Reset marital status selection
  ['mbtn-single','mbtn-married'].forEach(function(id) {
    var b = document.getElementById(id); if (b) b.classList.remove('active');
  });
  if (typeof currentMaritalStatus !== 'undefined') currentMaritalStatus = null;
  var body = document.getElementById('regFormBody');
  if (body) body.style.display = 'none';
  // Clear second parent fields
  toggleSpouseSection(false);
  // Reset wards
  wardCount = 1;
  var wcd = document.getElementById('wardCountDisplay'); if (wcd) wcd.textContent = '1';
  renderWardForms();
}

function previewPhoto(input, previewId) {
  var prev = document.getElementById(previewId);
  if (!prev || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) { prev.src = e.target.result; prev.style.display = 'block'; };
  reader.readAsDataURL(input.files[0]);
}

// ── ATTENDANCE ────────────────────────────────────────────────

// Close any open search-results dropdowns when clicking outside
document.addEventListener('pointerdown', function(e) {
  document.querySelectorAll('.search-results').forEach(function(r) {
    if (!r.contains(e.target)) r.classList.remove('open');
  });
});

// Spouse section toggle — called from register.php inline script and clearRegForm
function toggleSpouseSection(show) {
  var fields = document.getElementById('spouseFormFields');
  if (fields) fields.style.display = show ? 'block' : 'none';
  if (!show) {
    ['sp_pfirst','sp_plast','sp_phone','sp_email','sp_id_no'].forEach(function(id) {
      var el = document.getElementById(id); if (el) el.value = '';
    });
    var sp = document.getElementById('sp_pphoto_preview');
    if (sp) { sp.style.display = 'none'; sp.src = ''; }
    var spf = document.getElementById('sp_pphoto'); if (spf) spf.value = '';
    var spidt = document.getElementById('sp_id_type'); if (spidt) spidt.value = '';
  }
}
function lookupParent() {
  var phone = val('sign_phone');
  if (!phone) { showToast('Please enter your phone number.', 'error'); return; }
  apiFetch('actions/fetch.php?action=parent_by_phone&phone=' + encodeURIComponent(phone)).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    foundParent = r.data;
    var av = foundParent.photo_path
      ? '<div class="big-av"><img src="' + foundParent.photo_path + '" alt=""/></div>'
      : '<div class="big-av" style="background:var(--navy-light)">' + initials(foundParent.first_name, foundParent.last_name) + '</div>';
    document.getElementById('foundParentBox').innerHTML = av +
      '<div><div style="font-weight:600">' + foundParent.first_name + ' ' + foundParent.last_name + '</div>' +
      '<div style="font-size:13px;color:var(--text-muted)">' + foundParent.relationship +
        ' of ' + (foundParent.s_first||'') + ' ' + (foundParent.s_last||'') +
        ' (' + (foundParent.student_class||'') + ')</div></div>';
    showSignStep('signStep2');
  });
}

function submitAttendance() {
  if (!foundParent) return;
  var eventId = val('sign_event');
  if (!eventId) { showToast('Please select an event.', 'error'); return; }
  var fd = new FormData();
  fd.append('action','sign_attendance'); fd.append('event_id',eventId);
  fd.append('parent_id',foundParent.id); fd.append('visit_type',val('sign_vtype')); fd.append('notes',val('sign_notes'));
  apiPost('actions/insert.php', fd).then(function(r) {
    if (r.success) {
      var ev = allEvents.find(function(e) { return e.id == eventId; });
      document.getElementById('signedInMsg').textContent =
        foundParent.first_name + ' ' + foundParent.last_name + ' has been signed in for "' + (ev ? ev.name : 'the event') + '".';
      showSignStep('signStep3');
    } else { showToast(r.message, 'error'); }
  });
}

function resetSignIn() {
  foundParent = null;
  var ph = document.getElementById('sign_phone'); if (ph) ph.value = '';
  var sn = document.getElementById('sign_notes'); if (sn) sn.value = '';
  showSignStep('signStep1');
  checkGPS();
}

function showSignStep(id) {
  document.querySelectorAll('.sign-step').forEach(function(s) { s.classList.remove('active'); });
  var el = document.getElementById(id); if (el) el.classList.add('active');
}

// ── ADMIN ─────────────────────────────────────────────────────
function adminTab(tab) {
  document.querySelectorAll('.admin-sub').forEach(function(s) { s.classList.remove('active'); });
  document.querySelectorAll('.sidebar-link').forEach(function(l) { l.classList.remove('active'); });
  var sub = document.getElementById('admin-' + tab); if (sub) sub.classList.add('active');
  var sb  = document.getElementById('sb-'    + tab); if (sb)  sb.classList.add('active');
  if (tab === 'parents') loadAdminParents();
  if (tab === 'events')  loadAdminEvents();
  if (tab === 'checkin') loadAdminCheckin();
}

function loadAdminData() {
  apiFetch('actions/fetch.php?action=stats').then(function(r) {
    if (!r.success) return;
    setText('a-totalParents', r.data.totalParents); setText('a-totalStudents', r.data.totalStudents);
    setText('a-totalEvents',  r.data.totalEvents);  setText('a-upcoming',      r.data.upcomingCount);
  });
  apiFetch('actions/fetch.php?action=events').then(function(r) {
    if (!r.success) return; allEvents = r.data; renderAdminEventsPreview();
  });
}

function loadAdminParents() {
  apiFetch('actions/fetch.php?action=parents').then(function(r) {
    if (!r.success) return; allParents = r.data; buildClassFilter(); renderParentsTable();
  });
}

function buildClassFilter() {
  var classes = [];
  allParents.forEach(function(p) {
    (p.wards||[]).forEach(function(w) {
      if (w.student_class && classes.indexOf(w.student_class) === -1) classes.push(w.student_class);
    });
  });
  var sel = document.getElementById('filterClass'); if (!sel) return;
  var cur = sel.value;
  sel.innerHTML = '<option value="">All Classes</option>' + classes.map(function(c) {
    return '<option' + (c===cur?' selected':'') + '>' + c + '</option>';
  }).join('');
}

// Group allParents by canonical student ID (same logic as home registry)
function buildStudentGroups(parents) {
  var studentMap = {};
  var unlinked   = [];
  parents.forEach(function(p) {
    var wards = p.wards || [];
    if (!wards.length) { unlinked.push(p); return; }
    wards.forEach(function(w) {
      var cid = w.canonical_id || w.id;
      if (!studentMap[cid]) studentMap[cid] = { student: w, parents: [] };
      var already = studentMap[cid].parents.some(function(x){ return x.id === p.id; });
      if (!already) studentMap[cid].parents.push(p);
    });
  });
  return { groups: Object.values(studentMap), unlinked: unlinked };
}

function renderParentsTable() {
  var q   = (document.getElementById('searchParents') || {value:''}).value.toLowerCase();
  var cls = (document.getElementById('filterClass')   || {value:''}).value;
  var rel = (document.getElementById('filterRel')     || {value:''}).value;

  // Filter parents that match the search/filter criteria
  var filtered = allParents.filter(function(p) {
    var wardText = (p.wards||[]).map(function(w){ return w.first_name+' '+w.last_name+' '+(w.student_class||''); }).join(' ');
    return (!q   || (p.first_name+' '+p.last_name+' '+p.phone+' '+wardText).toLowerCase().indexOf(q) !== -1) &&
           (!cls || (p.wards||[]).some(function(w){ return w.student_class === cls; }) || (!p.wards||!p.wards.length)) &&
           (!rel || p.relationship === rel);
  });

  var tbody = document.getElementById('parentsTableBody'); if (!tbody) return;
  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">No records found.</td></tr>';
    return;
  }

  var result = buildStudentGroups(filtered);
  var rows = '';

  // ── One row per student, listing all their parents ────────
  result.groups.forEach(function(group) {
    var w  = group.student;
    var wa = w.photo_path
      ? '<div class="avatar-sm"><img src="' + w.photo_path + '" alt=""/></div>'
      : '<div class="avatar-sm">' + initials(w.first_name, w.last_name) + '</div>';

    var parentNames = group.parents.map(function(p) {
      return '<span style="display:inline-flex;align-items:center;gap:5px;background:var(--cream);border:1px solid var(--border);border-radius:20px;padding:2px 10px;font-size:12px;margin:2px">' +
        (p.photo_path
          ? '<img src="' + p.photo_path + '" style="width:18px;height:18px;border-radius:50%;object-fit:cover"/>'
          : '<span style="width:18px;height:18px;border-radius:50%;background:var(--navy-light);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700">' + ((p.first_name||'')[0]||'').toUpperCase() + '</span>') +
        p.first_name + ' ' + p.last_name +
        ' <span class="badge badge-' + p.relationship.toLowerCase() + '" style="font-size:10px;padding:1px 6px">' + p.relationship + '</span>' +
        '<button onclick="viewParentById(' + p.id + ')" style="background:none;border:none;color:var(--navy);cursor:pointer;font-size:11px;padding:0 2px;opacity:.6" title="View">👁</button>' +
      '</span>';
    }).join('');

    rows += '<tr>' +
      '<td><div class="parent-cell">' + wa + '<div><div style="font-weight:600">' + w.first_name + ' ' + w.last_name + '</div>' +
        (w.student_id_no ? '<div style="font-size:11px;color:var(--text-muted)">ID: ' + w.student_id_no + '</div>' : '') + '</div></div></td>' +
      '<td>' + (w.student_class||'&mdash;') + '</td>' +
      '<td colspan="3"><div style="display:flex;flex-wrap:wrap;gap:2px;align-items:center">' + parentNames + '</div></td>' +
      '<td>' + fmtDate(((group.parents[0]||{}).registered_at||'').split(' ')[0]) + '</td>' +
      '<td></td>' +
    '</tr>';
  });

  // ── Orphan rows: parents with no ward ────────────────────
  result.unlinked.forEach(function(p) {
    var av = p.photo_path ? '<div class="avatar-sm"><img src="' + p.photo_path + '" alt=""/></div>'
                          : '<div class="avatar-sm">' + initials(p.first_name, p.last_name) + '</div>';
    var pJs = JSON.stringify(p).replace(/"/g, '&quot;');
    rows += '<tr style="opacity:.7"><td><div class="parent-cell">' + av + '<span>' + p.first_name + ' ' + p.last_name + '</span></div></td>' +
      '<td>&mdash;</td>' +
      '<td><span class="badge badge-' + p.relationship.toLowerCase() + '">' + p.relationship + '</span></td>' +
      '<td style="color:var(--text-muted);font-style:italic;font-size:12px">No ward</td><td>&mdash;</td>' +
      '<td>' + fmtDate((p.registered_at||'').split(' ')[0]) + '</td>' +
      '<td><button class="btn-outline" style="font-size:12px;padding:5px 12px;color:var(--navy);border-color:var(--border)" onclick="viewParent(' + pJs + ')">View</button></td>' +
    '</tr>';
  });

  tbody.innerHTML = rows;
}

function viewParentById(id) {
  var p = allParents.find(function(x){ return x.id === id; });
  if (p) viewParent(p);
}

// Track the parent currently open in the detail modal and the student selected for linking
var _detailParent = null;
var _linkSelectedStudent = null;

function viewParent(p) {
  _detailParent = p;
  _linkSelectedStudent = null;
  var wc = (p.wards && p.wards.length) || 0;
  var wardsHtml = wc > 0 ? p.wards.map(function(w, i) {
    return '<div style="background:var(--cream);border-radius:8px;padding:.75rem 1rem;margin-bottom:.5rem;display:flex;align-items:center;gap:10px">' +
      (w.photo_path ? '<img src="' + w.photo_path + '" style="width:36px;height:36px;border-radius:50%;object-fit:cover"/>'
        : '<div style="width:36px;height:36px;border-radius:50%;background:var(--navy-light);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">' + ((w.first_name||'')[0]||'') + ((w.last_name||'')[0]||'') + '</div>') +
      '<div><div style="font-weight:600;font-size:.9rem">' + (w.first_name||'') + ' ' + (w.last_name||'') + '</div>' +
        '<div style="font-size:12px;color:var(--text-muted)">' + (w.student_class||'') + '</div></div>' +
      (wc > 1 ? '<span style="margin-left:auto;font-size:11px;color:var(--text-muted)">Ward ' + (i+1) + '</span>' : '') +
    '</div>';
  }).join('') : '<p style="color:var(--text-muted);font-size:13px">No wards recorded.</p>';

  document.getElementById('parentDetailContent').innerHTML =
    '<div style="display:flex;align-items:center;gap:14px;margin-bottom:1rem">' +
      (p.photo_path ? '<img src="' + p.photo_path + '" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--gold)"/>'
        : '<div style="width:64px;height:64px;border-radius:50%;background:var(--navy-light);color:white;display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:700">' + initials(p.first_name,p.last_name) + '</div>') +
      '<div><strong>' + p.first_name + ' ' + p.last_name + '</strong><br/><span style="font-size:13px;color:var(--text-muted)">' + p.relationship + '</span>' +
        (wc > 1 ? '<span style="margin-left:8px;background:rgba(212,153,58,.15);color:var(--gold);font-size:11px;padding:2px 8px;border-radius:10px">' + wc + ' wards</span>' : '') +
      '</div></div>' +
    '<dl class="parent-detail-grid"><dt>Phone</dt><dd>' + p.phone + '</dd><dt>Email</dt><dd>' + (p.email||'&mdash;') + '</dd>' +
      '<dt>Address</dt><dd>' + (p.address||'&mdash;') + '</dd><dt>Registered</dt><dd>' + fmtDate((p.registered_at||'').split(' ')[0]) + '</dd></dl>' +
    '<div style="margin-top:1rem"><div style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-muted);margin-bottom:.6rem">Wards (' + wc + ')</div>' + wardsHtml + '</div>' +
    // ── Link Student section ──────────────────────────────────
    '<div style="margin-top:1.25rem;border-top:1.5px solid var(--border);padding-top:1.1rem">' +
      '<div style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-muted);margin-bottom:.75rem;letter-spacing:.07em">🔗 Link Existing Student</div>' +
      '<div class="search-box" style="position:relative">' +
        '<input type="text" id="linkStudentSearch" placeholder="Search student name or ID…" oninput="linkStudentSearch(this.value)" ' +
          'style="width:100%;box-sizing:border-box"/>' +
        '<div class="search-results" id="linkStudentResults"></div>' +
      '</div>' +
      '<div id="linkStudentSelected" style="margin-top:.6rem"></div>' +
      '<button id="linkStudentBtn" class="btn-primary" onclick="confirmLinkStudent()" ' +
        'style="margin-top:.75rem;display:none;width:100%">Link Student →</button>' +
    '</div>';

  openModal('parentDetailModal');
}

var _linkSearchTimer = null;
function linkStudentSearch(q) {
  _linkSelectedStudent = null;
  document.getElementById('linkStudentSelected').innerHTML = '';
  document.getElementById('linkStudentBtn').style.display = 'none';
  clearTimeout(_linkSearchTimer);
  var results = document.getElementById('linkStudentResults');
  if (!results) return;
  if (q.length < 2) { results.innerHTML = ''; results.classList.remove('open'); return; }
  _linkSearchTimer = setTimeout(function() {
    apiFetch('actions/fetch.php?action=search_students&q=' + encodeURIComponent(q)).then(function(r) {
      results.innerHTML = '';
      if (!r.success || !r.data.length) {
        var empty = document.createElement('div');
        empty.style.cssText = 'padding:.75rem 1rem;font-size:13px;color:var(--text-muted)';
        empty.textContent = 'No students found.';
        results.appendChild(empty);
        results.classList.add('open');
        return;
      }
      r.data.forEach(function(student) {
        var item = document.createElement('div');
        item.className = 'search-result-item';
        item.innerHTML =
          '<div><div class="s-name">' + student.first_name + ' ' + student.last_name + '</div>' +
          '<div class="s-meta">' + student.student_class +
            (student.student_id_no ? ' · ID: ' + student.student_id_no : '') +
            ' · linked to ' + student.p_first + ' ' + student.p_last +
            ' (' + student.relationship + ')</div></div>';
        item.addEventListener('pointerdown', function(e) {
          e.preventDefault();
          selectLinkStudent(student);
        });
        results.appendChild(item);
      });
      results.classList.add('open');
    });
  }, 300);
}

function selectLinkStudent(student) {
  _linkSelectedStudent = student;
  var searchEl  = document.getElementById('linkStudentSearch');
  var resultsEl = document.getElementById('linkStudentResults');
  var selectedEl = document.getElementById('linkStudentSelected');
  var btn = document.getElementById('linkStudentBtn');
  if (searchEl)  searchEl.value = student.first_name + ' ' + student.last_name;
  if (resultsEl) { resultsEl.innerHTML = ''; resultsEl.classList.remove('open'); }
  if (selectedEl) {
    selectedEl.innerHTML =
      '<div style="background:var(--cream);border:1.5px solid var(--gold);border-radius:8px;padding:.6rem 1rem;display:flex;align-items:center;gap:10px">' +
        '<span style="font-size:1.1rem;color:var(--gold)">✓</span>' +
        '<div><div style="font-weight:600;font-size:.9rem">' + student.first_name + ' ' + student.last_name + '</div>' +
          '<div style="font-size:12px;color:var(--text-muted)">' + student.student_class + '</div></div>' +
        '<button type="button" onclick="clearLinkStudent()" style="margin-left:auto;background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:16px">✕</button>' +
      '</div>';
  }
  if (btn) btn.style.display = 'block';
}

function clearLinkStudent() {
  _linkSelectedStudent = null;
  var sel = document.getElementById('linkStudentSelected');
  var src = document.getElementById('linkStudentSearch');
  var btn = document.getElementById('linkStudentBtn');
  if (sel) sel.innerHTML = '';
  if (src) src.value = '';
  if (btn) btn.style.display = 'none';
}

async function confirmLinkStudent() {
  if (!_detailParent || !_linkSelectedStudent) return;
  var btn = document.getElementById('linkStudentBtn');
  if (btn) { btn.disabled = true; btn.textContent = 'Linking…'; }
  var lfd = new FormData();
  lfd.append('action', 'link_parent_student');
  lfd.append('parent_id', _detailParent.id);
  lfd.append('student_id', _linkSelectedStudent.id);
  var r = await apiPost('actions/insert.php', lfd);
  if (btn) { btn.disabled = false; btn.textContent = 'Link Student →'; }
  if (r.success) {
    showToast(_linkSelectedStudent.first_name + ' ' + _linkSelectedStudent.last_name + ' linked successfully!', 'success');
    clearLinkStudent();
    loadAdminParents(); // refresh the table
  } else {
    showToast(r.message || 'Could not link student.', 'error');
  }
}

function exportCSV() {
  if (!allParents.length) { showToast('No data to export.'); return; }
  var h = 'First Name,Last Name,Phone,Email,Relationship,Student,Class,Registered\n';
  var rows = allParents.map(function(p) {
    return '"'+p.first_name+'","'+p.last_name+'","'+p.phone+'","'+(p.email||'')+'","'+p.relationship+'","'+(p.s_first||'')+' '+(p.s_last||'')+'","'+(p.student_class||'')+'","'+(p.registered_at||'')+'"';
  }).join('\n');
  var a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([h+rows],{type:'text/csv'})); a.download = 'parents.csv'; a.click();
  showToast('CSV downloaded!', 'success');
}
function loadAdminEvents() {

  apiFetch('actions/fetch.php?action=events').then(function(r) {

    if (!r.success) return;

    allEvents = r.data;

    var el = document.getElementById('adminEventCards');

    if (!el) return;

    // No events
    if (!allEvents.length) {
      el.innerHTML = '<p style="color:var(--text-muted)">No events yet.</p>';
      return;
    }

    el.innerHTML = allEvents.map(function(e) {

      return `
        <div class="ev-card" style="position:relative">

          <!-- DELETE BUTTON -->
          <button
            onclick="deleteEvent(${e.id}, '${e.name.replace(/'/g, "\\'")}')"
            style="
              position:absolute;
              top:12px;
              right:12px;
              width:32px;
              height:32px;
              border:none;
              border-radius:8px;
              background:#dc2626;
              color:white;
              font-size:18px;
              font-weight:bold;
              cursor:pointer;
              z-index:100;
            "
            title="Delete Event"
          >
            ×
          </button>

          <div class="ev-type-badge">${e.event_type}</div>

          <div class="ev-name">${e.name}</div>

          <div class="ev-meta">
            <span>📅 ${fmtDate(e.event_date)}
              ${e.event_time ? ' at ' + fmtTime(e.event_time) : ''}
            </span>

            <span>📍 ${e.venue || 'TBD'}</span>

            ${e.description ? `<span>${e.description}</span>` : ''}
          </div>

        </div>
      `;

    }).join('');

  });

}
function deleteEvent(eventId, eventName) {

  if (!confirm('Delete "' + eventName + '"?')) {
    return;
  }

  var fd = new FormData();

  fd.append('action', 'delete_event');
  fd.append('event_id', eventId);

  apiPost('actions/insert.php', fd).then(function(r) {

    if (r.success) {

      showToast('Event deleted successfully', 'success');

      loadAdminEvents();

    } else {

      showToast(r.message || 'Delete failed', 'error');

    }

  });

}

function renderAdminEventsPreview() {
  var upcoming = allEvents.filter(function(e){return e.event_date>=today();}).slice(0,4);
  var el = document.getElementById('adminEventsPreview'); if (!el) return;
  el.innerHTML = upcoming.length ? upcoming.map(function(e) {
    return '<div class="ev-card"><div class="ev-type-badge">'+e.event_type+'</div><div class="ev-name">'+e.name+'</div>' +
      '<div class="ev-meta"><span>&#128197; '+fmtDate(e.event_date)+'</span><span>&#128205; '+(e.venue||'TBD')+'</span></div></div>';
  }).join('') : '<p style="color:var(--text-muted)">No upcoming events.</p>';
}

function openAddEvent() {
  document.getElementById('evDate').value = new Date().toISOString().split('T')[0];
  document.getElementById('evName').value=''; document.getElementById('evVenue').value='';
  document.getElementById('evDesc').value=''; document.getElementById('evType').value='PTA Meeting';
  document.getElementById('evTime').value='10:00';
  openModal('addEventModal');
}

function saveEvent() {
  var name=val('evName'), date=val('evDate');
  if (!name||!date) { showToast('Name and date required.','error'); return; }
  var fd=new FormData();
  fd.append('action','create_event'); fd.append('name',name); fd.append('event_type',val('evType'));
  fd.append('event_date',date); fd.append('event_time',val('evTime')); fd.append('venue',val('evVenue')); fd.append('description',val('evDesc'));
  apiPost('actions/insert.php',fd).then(function(r) {
    if (r.success) { closeModal('addEventModal'); showToast('Event created!','success'); loadAdminData(); loadAdminEvents(); }
    else showToast(r.message,'error');
  });
}

function deleteEvent(eventId, eventName) {
  if (!confirm('Are you sure you want to delete "' + eventName + '"?\n\nThis will also remove all attendance records associated with this event.')) {
    return;
  }
  
  var fd = new FormData();
  fd.append('action', 'delete_event');
  fd.append('event_id', eventId);
  
  apiPost('actions/insert.php', fd).then(function(r) {
    if (r.success) {
      showToast(r.message, 'success');
      loadAdminData();
      loadAdminEvents();
      loadAdminCheckin(); // Refresh attendance list if on that tab
    } else {
      showToast(r.message, 'error');
    }
  });
}

function loadAdminCheckin() {
  apiFetch('actions/fetch.php?action=events').then(function(r) {
    if (!r.success) return;
    allEvents = r.data;
    var sel = document.getElementById('adminEventSelect'); if (!sel) return;
    sel.innerHTML = allEvents.map(function(e) {
      return '<option value="'+e.id+'">'+e.name+' ('+fmtDate(e.event_date)+')</option>';
    }).join('');
    if (allEvents.length) { adminEvtId = allEvents[0].id; renderAdminEventList(); loadAdminAttendance(adminEvtId); }
  });
}

function renderAdminEventList() {
  var el = document.getElementById('adminEventList'); if (!el) return;
  el.innerHTML = allEvents.map(function(e) {
    return '<div class="event-list-item'+(e.id==adminEvtId?' active':'')+'\" onclick=\"loadAdminAttendance('+e.id+')\">' +
      '<div class="ev-n">'+e.name+'</div><div class="ev-d">'+fmtDate(e.event_date)+'</div>' +
      '<div class="ev-progress"><div class="ev-fill" style="width:0%"></div></div></div>';
  }).join('');
}

function loadAdminAttendance(evtId) {
  adminEvtId = parseInt(evtId);
  renderAdminEventList();
  var sel = document.getElementById('adminEventSelect'); if (sel) sel.value = adminEvtId;
  apiFetch('actions/fetch.php?action=attendance&event_id='+adminEvtId).then(function(r) {
    setText('adminAttCount',(r.data&&r.data.length)||0);
    var el = document.getElementById('adminCheckinList'); if (!el) return;
    if (!r.success||!r.data||!r.data.length) { el.innerHTML='<p style="color:var(--text-muted);padding:1rem">No one signed in yet.</p>'; return; }
    el.innerHTML = r.data.map(function(a) {
      var av = a.photo_path ? '<div class="avatar-sm"><img src="'+a.photo_path+'" alt=""/></div>'
                            : '<div class="avatar-sm">'+initials(a.first_name,a.last_name)+'</div>';
      return '<div class="checkin-item">'+av+'<div class="info"><div class="name">'+a.first_name+' '+a.last_name+'</div>' +
        '<div class="sub">'+(a.s_first||'')+' '+(a.s_last||'')+' &middot; '+(a.student_class||'')+' &middot; '+a.visit_type+'</div>' +
        '<div class="sub" style="font-size:11px">'+fmtDatetime(a.signed_at)+'</div></div>' +
        '<span class="badge badge-guardian">&#10003;</span></div>';
    }).join('');
  });
}

function exportAttendance() {
  if (!adminEvtId) return;
  apiFetch('actions/fetch.php?action=attendance&event_id='+adminEvtId).then(function(r) {
    if (!r.success||!r.data||!r.data.length) { showToast('No data to export.'); return; }
    var h='Parent,Phone,Student,Class,Visit Type,Signed At\n';
    var rows=r.data.map(function(a){return '"'+a.first_name+' '+a.last_name+'","'+a.phone+'","'+(a.s_first||'')+' '+(a.s_last||'')+'","'+(a.student_class||'')+'","'+a.visit_type+'","'+a.signed_at+'"';}).join('\n');
    var ev=allEvents.find(function(e){return e.id==adminEvtId;});
    var a=document.createElement('a');
    a.href=URL.createObjectURL(new Blob([h+rows],{type:'text/csv'}));
    a.download='attendance-'+((ev?ev.name:'event').replace(/\s+/g,'-'))+'.csv'; a.click();
    showToast('Exported!','success');
  });
}

// ── QR CODE ───────────────────────────────────────────────────
function drawQR(url) {
  var t = document.getElementById('qrTarget'); t.innerHTML = '';
  if (typeof QRCode !== 'undefined') {
    new QRCode(t, {text:url, width:200, height:200, colorDark:'#0B1F3A', colorLight:'#FFFFFF', correctLevel:QRCode.CorrectLevel.M});
  } else { t.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:2rem">QR loading...</p>'; }
}

function downloadQR() {
  var t=document.getElementById('qrTarget');
  var canvas=t.querySelector('canvas'), img=t.querySelector('img');
  var src = canvas ? canvas.toDataURL('image/png') : (img ? img.src : null);
  if (!src) { showToast('QR not ready yet.'); return; }
  var a=document.createElement('a'); a.download='attendance-qr.png'; a.href=src; a.click();
  showToast('QR downloaded!','success');
}

// ── MODAL UTILS ───────────────────────────────────────────────
function openModal(id)  { var el=document.getElementById(id); if(el){el.classList.add('open');    document.body.style.overflow='hidden';} }
function closeModal(id) { var el=document.getElementById(id); if(el){el.classList.remove('open'); document.body.style.overflow='';} }
function closeModalOnBackdrop(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

// ── GPS CAMPUS CHECK ──────────────────────────────────────────
var SCHOOL_LAT      =  5.508;
var SCHOOL_LNG      = -0.657;
var SCHOOL_RADIUS_M =  350;

function degToRad(d) { return d * Math.PI / 180; }

function getDistanceMetres(lat1, lng1, lat2, lng2) {
  var R=6371000, dLat=degToRad(lat2-lat1), dLng=degToRad(lng2-lng1);
  var a=Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(degToRad(lat1))*Math.cos(degToRad(lat2))*Math.sin(dLng/2)*Math.sin(dLng/2);
  return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
}

function checkGPS() {
  var banner=document.getElementById('gpsBanner'), blocked=document.getElementById('gpsBlockedMsg'), allowed=document.getElementById('gpsAllowedForm');
  if (!banner) return;
  banner.className='gps-banner checking';
  banner.innerHTML='<span class="gps-icon">&#128225;</span><div><b>Checking your location...</b> Please allow location access when prompted.</div>';
  if (blocked) blocked.style.display='none';
  if (allowed) allowed.style.display='none';
  if (!navigator.geolocation) {
    banner.className='gps-banner blocked';
    banner.innerHTML='<span class="gps-icon">&#10060;</span><div><b>Location not supported</b> Use a modern mobile browser.</div>';
    if (blocked) blocked.style.display='block'; return;
  }
  navigator.geolocation.getCurrentPosition(
    function(pos) {
      var dist=Math.round(getDistanceMetres(pos.coords.latitude,pos.coords.longitude,SCHOOL_LAT,SCHOOL_LNG));
      if (dist <= SCHOOL_RADIUS_M) {
        banner.className='gps-banner allowed';
        banner.innerHTML='<span class="gps-icon">&#10003;</span><div><b>You are on campus</b> '+dist+'m from school &mdash; you may sign in.</div>';
        if (allowed) allowed.style.display='block'; if (blocked) blocked.style.display='none';
      } else {
        banner.className='gps-banner blocked';
        banner.innerHTML='<span class="gps-icon">&#128683;</span><div><b>You are not on campus</b> You are '+dist+'m away. Must be within '+SCHOOL_RADIUS_M+'m.</div>';
        if (allowed) allowed.style.display='none'; if (blocked) blocked.style.display='block';
      }
    },
    function(err) {
      banner.className='gps-banner blocked';
      var msg='Could not get your location.';
      if (err.code===1) msg='Location permission denied. Enable location in your browser settings.';
      if (err.code===2) msg='Location unavailable. Please try again.';
      if (err.code===3) msg='Location request timed out. Please try again.';
      banner.innerHTML='<span class="gps-icon">&#9888;</span><div><b>Location Error</b> '+msg+'</div>';
      if (blocked) blocked.style.display='block';
    },
    {enableHighAccuracy:true, timeout:10000, maximumAge:0}
  );
}

// ── API HELPERS ───────────────────────────────────────────────
async function apiFetch(url) {
  try { return await (await fetch(url)).json(); }
  catch(e) { showToast('Network error.','error'); return {success:false,message:'Network error'}; }
}

async function apiPost(url, fd) {
  try { return await (await fetch(url,{method:'POST',body:fd})).json(); }
  catch(e) { showToast('Network error.','error'); return {success:false,message:'Network error'}; }
}

// ── UTILS ─────────────────────────────────────────────────────
function val(id)   { return ((document.getElementById(id)||{value:''}).value||'').trim(); }
function setText(id,v) { var el=document.getElementById(id); if(el) el.textContent=v; }
function initials(f,l) { return ((f||'')[0]||'').toUpperCase()+((l||'')[0]||'').toUpperCase(); }
function today()   { return new Date().toISOString().split('T')[0]; }
function fmtDate(d) {
  if (!d) return '&mdash;';
  var dt=new Date(d+(d.indexOf('T')===-1?'T00:00:00':''));
  return dt.toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'});
}
function fmtTime(t) {
  if (!t) return '';
  var p=t.split(':'), h=parseInt(p[0]);
  return (h%12||12)+':'+p[1]+' '+(h>=12?'PM':'AM');
}
function fmtDatetime(dt) {
  if (!dt) return '&mdash;';
  return new Date(dt).toLocaleString('en-GB',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
}
function populateEventDropdown(id, events) {
  var sel=document.getElementById(id); if (!sel) return;
  var upcoming=events.filter(function(e){return e.event_date>=today();});
  sel.innerHTML=upcoming.length
    ? upcoming.map(function(e){return '<option value="'+e.id+'">'+e.name+' ('+fmtDate(e.event_date)+')</option>';}).join('')
    : '<option value="">No upcoming events</option>';
}
function showToast(msg, type) {
  var t=document.getElementById('toast'); if (!t) return;
  t.className='toast'+(type==='error'?' error':type==='success'?' success-toast':'');
  document.getElementById('toastMsg').textContent=msg;
  t.classList.add('show');
  setTimeout(function(){t.classList.remove('show');},3200);
}

// ── HOME REGISTRY ─────────────────────────────────────────────
var _allParents  = [];
var _allStudents = [];   // new student-centric shape

function loadRegistry() {
  var el = document.getElementById('registryList');
  if (!el) return;
  apiFetch('actions/fetch.php?action=parents').then(function(r) {
    if (!r.success) { el.innerHTML = '<p style="color:var(--text-muted)">Could not load registry.</p>'; return; }
    _allParents  = r.data;      // legacy shape (parent-centric)
    _allStudents = r.students || [];  // new shape (student-centric)
    renderRegistry(_allParents);
  });
}

function filterRegistry(q) {
  if (!q.trim()) { renderRegistry(_allParents); return; }
  var lq = q.toLowerCase();
  var filtered = _allParents.filter(function(p) {
    var haystack = (p.first_name + ' ' + p.last_name + ' ' + (p.phone||'') + ' ' +
      (p.wards||[]).map(function(w){ return w.first_name+' '+w.last_name+' '+(w.student_class||''); }).join(' ')).toLowerCase();
    return haystack.indexOf(lq) !== -1;
  });
  renderRegistry(filtered);
}

function renderRegistry(parents) {
  var el = document.getElementById('registryList');
  if (!el) return;
  if (!parents.length) {
    el.innerHTML = '<p style="color:var(--text-muted);text-align:center;padding:2rem">No records found.</p>';
    return;
  }

  // ── Group by canonical student ID ──────────────────────────
  // studentMap: canonicalId → { student info, parents[] }
  // unlinked: parents with no wards
  var studentMap = {};
  var unlinked   = [];

  parents.forEach(function(p) {
    var wards = p.wards || [];
    if (!wards.length) {
      unlinked.push(p);
      return;
    }
    wards.forEach(function(w) {
      var cid = w.canonical_id || w.id;
      if (!studentMap[cid]) {
        studentMap[cid] = {
          student: w,
          parents: []
        };
      }
      // avoid duplicate parent entries
      var already = studentMap[cid].parents.some(function(x){ return x.id === p.id; });
      if (!already) studentMap[cid].parents.push(p);
    });
  });

  var html = '';

  // ── Render student-grouped cards ───────────────────────────
  Object.values(studentMap).forEach(function(group) {
    var w = group.student;
    var wa = w.photo_path
      ? '<img src="' + w.photo_path + '" alt="" style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2.5px solid var(--gold);flex-shrink:0"/>'
      : '<div style="width:52px;height:52px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0">' + initials(w.first_name, w.last_name) + '</div>';

    var parentsHtml = group.parents.map(function(p) {
      var pa = p.photo_path
        ? '<img src="' + p.photo_path + '" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0"/>'
        : '<div style="width:32px;height:32px;border-radius:50%;background:var(--cream-dark,#e8e0d0);color:var(--navy);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.7rem;flex-shrink:0">' + initials(p.first_name, p.last_name) + '</div>';
      return '<div style="display:flex;align-items:center;gap:.55rem;padding:.4rem .7rem;background:var(--white);border-radius:8px;border:1px solid var(--border)">' +
        pa +
        '<div>' +
          '<div style="font-weight:600;font-size:.83rem;color:var(--navy)">' + p.first_name + ' ' + p.last_name +
            ' <span style="font-size:.72rem;font-weight:500;background:rgba(212,153,58,.15);color:var(--gold);padding:1px 7px;border-radius:10px;vertical-align:middle">' + (p.relationship||'Guardian') + '</span></div>' +
          '<div style="font-size:.73rem;color:var(--text-muted)">📞 ' + (p.phone||'—') + (p.email ? '  ✉ ' + p.email : '') + '</div>' +
        '</div>' +
      '</div>';
    }).join('');

    html +=
      '<div class="registry-card">' +
        // Student header
        '<div class="registry-parent-row">' +
          wa +
          '<div style="flex:1;min-width:0">' +
            '<div style="font-weight:700;font-size:1.05rem;color:var(--navy)">' + w.first_name + ' ' + w.last_name + '</div>' +
            '<div style="font-size:.82rem;color:var(--text-muted);margin-top:.1rem">' +
              (w.student_class||'') + (w.student_id_no ? '  ·  ID: ' + w.student_id_no : '') +
            '</div>' +
          '</div>' +
        '</div>' +
        // Parents section
        '<div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin:.75rem 0 .45rem">👥 Parents / Guardians (' + group.parents.length + ')</div>' +
        '<div style="display:flex;flex-direction:column;gap:.4rem">' + parentsHtml + '</div>' +
      '</div>';
  });

  // ── Render parents with no ward (orphan cards) ─────────────
  if (unlinked.length) {
    html += '<div style="font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin:.75rem 0 .4rem">⚠ No Ward Linked</div>';
    unlinked.forEach(function(p) {
      var pa = p.photo_path
        ? '<img src="' + p.photo_path + '" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0"/>'
        : '<div style="width:48px;height:48px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0">' + initials(p.first_name, p.last_name) + '</div>';
      html +=
        '<div class="registry-card" style="opacity:.75">' +
          '<div class="registry-parent-row">' +
            pa +
            '<div style="flex:1;min-width:0">' +
              '<div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">' +
                '<span style="font-weight:700;font-size:1rem;color:var(--navy)">' + p.first_name + ' ' + p.last_name + '</span>' +
                '<span class="rel-badge">' + (p.relationship||'Guardian') + '</span>' +
              '</div>' +
              '<div style="font-size:.82rem;color:var(--text-muted);margin-top:.15rem">📞 ' + (p.phone||'—') + (p.email ? '  ·  ✉ ' + p.email : '') + '</div>' +
            '</div>' +
          '</div>' +
          '<span style="font-size:.8rem;color:var(--text-muted);font-style:italic">No wards registered</span>' +
        '</div>';
    });
  }

  el.innerHTML = html;
}