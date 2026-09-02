<?php
require_once 'includes/layout.php';
layout_head('Exeat Request');
layout_nav('exeat');
?>

<div class="form-page" style="max-width:680px">
  <div style="margin-bottom:2rem;text-align:center">
    <div class="section-kicker">Off-Campus Permission</div>
    <h2 class="section-title" style="color:var(--navy)">Exeat Request</h2>
    <p class="section-sub">Fill in your phone number to look up your ward, then complete the exeat form. Fields marked <span style="color:var(--danger)">*</span> are required.</p>
  </div>

  <!-- Step 1: Parent lookup -->
  <div class="sign-step active" id="exStep1">
    <div class="form-card">
      <div class="form-section-title">📞 Find Your Account</div>
      <div class="form-group" style="margin-bottom:1rem">
        <label>Your Phone Number <span class="req">*</span></label>
        <input type="tel" id="ex_phone" placeholder="e.g. 0244 000 000" autocomplete="tel"
          onkeydown="if(event.key==='Enter') exLookup()"/>
      </div>
      <button class="btn-primary" onclick="exLookup()" style="width:100%">Find My Account →</button>
    </div>
  </div>

  <!-- Step 2: Ward select + form -->
  <div class="sign-step" id="exStep2">
    <div class="form-card">
      <div class="form-section-title">👤 Parent Found</div>
      <div id="exParentBox" style="display:flex;align-items:center;gap:12px;padding:1rem;background:var(--cream);border-radius:10px;margin-bottom:1.25rem"></div>

      <div class="form-section-title" style="margin-top:1rem">🎒 Select Ward</div>
      <div class="form-group" style="margin-bottom:1rem">
        <label>Ward <span class="req">*</span></label>
        <select id="ex_student_id">
          <option value="">— Select ward —</option>
        </select>
      </div>

      <div class="form-divider"></div>
      <div class="form-section-title">📋 Exeat Details</div>

      <div class="form-row">
        <div class="form-group">
          <label>Departure Date <span class="req">*</span></label>
          <input type="date" id="ex_depart_date"/>
        </div>
        <div class="form-group">
          <label>Departure Time <span class="req">*</span></label>
          <input type="time" id="ex_depart_time"/>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1rem">
        <label>Expected Return Date <span class="req">*</span></label>
        <input type="date" id="ex_return_date"/>
      </div>

      <div class="form-group" style="margin-bottom:1.25rem">
        <label>Reason for Leaving Campus <span class="req">*</span></label>
        <textarea id="ex_reason" placeholder="Describe the reason for your ward leaving campus…" rows="4"></textarea>
      </div>

      <div style="display:flex;gap:10px">
        <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="exReset()">← Back</button>
        <button class="btn-primary" style="flex:1" onclick="submitExeat()">Submit Exeat Request →</button>
      </div>
    </div>
  </div>

  <!-- Step 3: Confirmation -->
  <div class="sign-step" id="exStep3">
    <div class="form-card" style="text-align:center;padding:3rem 2rem">
      <div style="font-size:3rem;margin-bottom:1rem">✅</div>
      <h3 style="color:var(--navy);margin-bottom:.5rem">Request Submitted!</h3>
      <p id="exConfirmMsg" style="color:var(--text-muted);margin-bottom:1.5rem"></p>
      <p style="font-size:13px;color:var(--text-muted);margin-bottom:1.5rem">
        The Head of Domestic Affairs will review your request. You will be notified of the decision.
      </p>
      <button class="btn-primary" onclick="exReset()">Submit Another Request</button>
    </div>
  </div>
</div>

<script>
var exParent = null;

function exLookup() {
  var phone = val('ex_phone');
  if (!phone) { showToast('Please enter your phone number.', 'error'); return; }
  apiFetch('actions/fetch.php?action=parent_by_phone&phone=' + encodeURIComponent(phone)).then(function(r) {
    if (!r.success) { showToast(r.message, 'error'); return; }
    exParent = r.data;

    var av = exParent.photo_path
      ? '<img src="' + exParent.photo_path + '" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--gold)"/>'
      : '<div style="width:48px;height:48px;border-radius:50%;background:var(--navy);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem">' + initials(exParent.first_name, exParent.last_name) + '</div>';

    document.getElementById('exParentBox').innerHTML = av +
      '<div><div style="font-weight:600">' + exParent.first_name + ' ' + exParent.last_name + '</div>' +
      '<div style="font-size:13px;color:var(--text-muted)">' + (exParent.relationship||'Guardian') + '</div></div>';

    var wards = exParent.wards || [];
    var sel = document.getElementById('ex_student_id');
    sel.innerHTML = '<option value="">— Select ward —</option>';
    wards.forEach(function(w) {
      sel.innerHTML += '<option value="' + w.id + '">' + w.first_name + ' ' + w.last_name + ' (' + (w.student_class||'') + ')</option>';
    });

    // Set min dates
    var todayStr = new Date().toISOString().split('T')[0];
    document.getElementById('ex_depart_date').min = todayStr;
    document.getElementById('ex_depart_date').value = todayStr;
    document.getElementById('ex_return_date').min  = todayStr;

    showExStep('exStep2');
  });
}

async function submitExeat() {
  var studentId  = val('ex_student_id');
  var reason     = val('ex_reason');
  var departDate = val('ex_depart_date');
  var departTime = val('ex_depart_time');
  var returnDate = val('ex_return_date');

  if (!studentId)  { showToast('Please select a ward.', 'error'); return; }
  if (!departDate) { showToast('Departure date is required.', 'error'); return; }
  if (!departTime) { showToast('Departure time is required.', 'error'); return; }
  if (!returnDate) { showToast('Return date is required.', 'error'); return; }
  if (!reason)     { showToast('Reason is required.', 'error'); return; }

  var fd = new FormData();
  fd.append('action',          'submit_exeat');
  fd.append('student_id',      studentId);
  fd.append('parent_id',       exParent.id);
  fd.append('reason',          reason);
  fd.append('departure_date',  departDate);
  fd.append('departure_time',  departTime);
  fd.append('expected_return', returnDate);

  var r = await apiPost('actions/insert.php', fd);
  if (!r.success) { showToast(r.message, 'error'); return; }

  var ward = (exParent.wards||[]).find(function(w) { return w.id == studentId; });
  var name = ward ? (ward.first_name + ' ' + ward.last_name) : 'your ward';
  document.getElementById('exConfirmMsg').textContent =
    'Your exeat request for ' + name + ' (departing ' + departDate + ') has been submitted and is awaiting review.';
  showExStep('exStep3');
}

function exReset() {
  exParent = null;
  var ph = document.getElementById('ex_phone'); if (ph) ph.value = '';
  var re = document.getElementById('ex_reason'); if (re) re.value = '';
  showExStep('exStep1');
}

function showExStep(id) {
  document.querySelectorAll('.sign-step').forEach(function(s) { s.classList.remove('active'); });
  var el = document.getElementById(id); if (el) el.classList.add('active');
}
</script>
<?php layout_footer(); ?>
