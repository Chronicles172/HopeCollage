<?php
// ============================================================
// register.php  —  Parent & Student Registration
// URL: /register.php
// ============================================================
require_once 'includes/layout.php';
layout_head('Register');
layout_nav('register');
?>

<div class="form-page" style="max-width:820px">

  <!-- ── Info Banner ─────────────────────────────────────────── -->
  <div style="background:linear-gradient(135deg,rgba(43,79,126,.08) 0%,rgba(212,153,58,.08) 100%);border:1.5px solid rgba(212,153,58,.35);border-radius:14px;padding:1.1rem 1.4rem;margin-bottom:2rem;display:flex;align-items:flex-start;gap:1rem">
    <div style="font-size:1.6rem;flex-shrink:0;margin-top:.1rem">&#128247;</div>
    <div>
      <div style="font-weight:700;font-size:.97rem;color:var(--navy);margin-bottom:.3rem">Photos &amp; Names Captured Here</div>
      <div style="font-size:13.5px;color:var(--navy-mid);line-height:1.55">
        Please have <strong>parent/guardian photos</strong> and <strong>student photos</strong> ready before filling this form.
        Full names and pictures for <em>all parents and students</em> are captured during this registration &mdash; there is no separate step afterwards.
      </div>
    </div>
  </div>

  <!-- ── Heading ─────────────────────────────────────────── -->
  <div style="margin-bottom:2rem;text-align:center">
    <div class="section-kicker">Parent / Student Space</div>
    <h2 class="section-title" style="color:var(--navy)">Register Parent &amp; Wards</h2>
    <p class="section-sub">Fill in parent details and add all wards in school.
      Fields marked <span style="color:var(--danger)">*</span> are required.</p>
  </div>

  <div class="form-card">

    <!-- ── Marital Status Gate ──────────────────────────────── -->
    <div class="form-section-title">&#128141; Marital Status</div>
    <div style="margin-bottom:1.75rem">
      <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:1rem">
        Select the marital status of the parent(s) registering. This determines how many parent forms are shown.
      </p>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap" id="maritalToggleGroup">
        <button type="button" class="marital-btn" id="mbtn-single" onclick="setMaritalStatus('single')">
          <span style="font-size:1.3rem">&#128100;</span>
          <span>Single</span>
        </button>
        <button type="button" class="marital-btn" id="mbtn-married" onclick="setMaritalStatus('married')">
          <span style="font-size:1.3rem">&#128145;</span>
          <span>Married</span>
        </button>
      </div>
    </div>

    <!-- ── Form body — hidden until marital status chosen ──── -->
    <div id="regFormBody" style="display:none">

      <div class="form-divider"></div>

      <!-- ── Parent 1 ─────────────────────────────────────── -->
      <div class="form-section-title" id="parent1Label">&#128100; Parent / Guardian Details</div>

      <div class="form-row">
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" id="reg_pfirst" placeholder="e.g. Kwame" autocomplete="given-name"/>
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" id="reg_plast" placeholder="e.g. Mensah" autocomplete="family-name"/>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="tel" id="reg_phone" placeholder="e.g. 0244 000 000" autocomplete="tel"/>
        </div>
        <div class="form-group">
          <label>Relationship to Wards <span class="req">*</span></label>
          <select id="reg_rel" onchange="onParent1RelChange(this.value)">
            <option value="Father">Father</option>
            <option value="Mother">Mother</option>
            <option value="Guardian" selected>Guardian</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Home Address</label>
          <input type="text" id="reg_address" placeholder="House no., area" autocomplete="street-address"/>
        </div>
        <div class="form-group">
          <label>National ID Type</label>
          <select id="reg_id_type">
            <option value="">&#8212; Select &#8212;</option>
            <option value="Ghana Card">Ghana Card</option>
            <option value="Passport">Passport</option>
            <option value="Driver's License">Driver's License</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>ID Number</label>
          <input type="text" id="reg_id_no" placeholder=""/>
        </div>
        <div class="form-group">
          <label>Email Address <span style="font-size:11px;color:var(--text-muted)">(Optional)</span></label>
          <input type="email" id="reg_email" placeholder="e.g. kwame@email.com" autocomplete="email"/>
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label>Photo <span style="font-size:11px;color:var(--text-muted)">(Compulsory)</span></label>
          <label class="photo-upload-label" for="reg_pphoto">
            <img id="reg_pphoto_preview" class="photo-preview" alt=""/>
            <span>&#128247; Click to upload photo</span>
            <span style="font-size:11px">JPG / PNG / WEBP &middot; max 5 MB</span>
          </label>
          <input type="file" id="reg_pphoto" accept="image/*" style="display:none"
            onchange="previewPhoto(this,'reg_pphoto_preview')"/>
        </div>
      </div>

      <!-- ── Parent 2 (married only) ──────────────────────── -->
      <div id="parent2Section" style="display:none">
        <div class="form-divider"></div>
        <div class="form-section-title" id="parent2Label">&#128100; Second Parent Details</div>

        <div class="form-row">
          <div class="form-group">
            <label>First Name <span class="req">*</span></label>
            <input type="text" id="sp_pfirst" placeholder="e.g. Abena" autocomplete="given-name"/>
          </div>
          <div class="form-group">
            <label>Last Name <span class="req">*</span></label>
            <input type="text" id="sp_plast" placeholder="e.g. Mensah" autocomplete="family-name"/>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Phone Number <span class="req">*</span></label>
            <input type="tel" id="sp_phone" placeholder="e.g. 0244 111 222" autocomplete="tel"/>
          </div>
          <div class="form-group">
            <label>Relationship to Wards <span class="req">*</span></label>
            <select id="sp_rel">
              <option value="Father">Father</option>
              <option value="Mother">Mother</option>
              <option value="Guardian">Guardian</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>National ID Type</label>
            <select id="sp_id_type">
              <option value="">&#8212; Select &#8212;</option>
              <option value="Ghana Card">Ghana Card</option>
              <option value="Passport">Passport</option>
              <option value="Driver's License">Driver's License</option>
            </select>
          </div>
          <div class="form-group">
            <label>ID Number</label>
            <input type="text" id="sp_id_no" placeholder=""/>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Email Address <span style="font-size:11px;color:var(--text-muted)">(Optional)</span></label>
            <input type="email" id="sp_email" placeholder="e.g. abena@email.com" autocomplete="email"/>
          </div>
        </div>

        <div class="form-row single">
          <div class="form-group">
            <label>Photo <span style="font-size:11px;color:var(--text-muted)">(Compulsory)</span></label>
            <label class="photo-upload-label" for="sp_pphoto">
              <img id="sp_pphoto_preview" class="photo-preview" alt=""/>
              <span>&#128247; Click to upload photo</span>
              <span style="font-size:11px">JPG / PNG / WEBP &middot; max 5 MB</span>
            </label>
            <input type="file" id="sp_pphoto" accept="image/*" style="display:none"
              onchange="previewPhoto(this,'sp_pphoto_preview')"/>
          </div>
        </div>
      </div><!-- /parent2Section -->

      <div class="form-divider"></div>

      <!-- ── Ward(s) section ────────────────────────────────── -->
      <div class="form-section-title">&#127920; Ward(s) Details</div>

      <div style="background:rgba(212,153,58,.08);border:1.5px solid rgba(212,153,58,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
          <div style="font-weight:600;font-size:.95rem;color:var(--navy);margin-bottom:.2rem">How many wards are in this school?</div>
          <div style="font-size:13px;color:var(--text-muted)">A form will be generated for each ward.</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <button type="button" class="ward-count-btn" onclick="changeWardCount(-1)">&#8722;</button>
          <span id="wardCountDisplay" style="font-family:var(--font-display);font-size:1.8rem;color:var(--navy);min-width:36px;text-align:center">1</span>
          <button type="button" class="ward-count-btn" onclick="changeWardCount(1)">+</button>
        </div>
      </div>

      <div id="wardFormsContainer"></div>

      <!-- ── Actions ─────────────────────────────────────────── -->
      <div class="submit-row">
        <a class="btn-outline" href="index.php" style="color:var(--navy);border-color:var(--border)">Cancel</a>
        <button class="btn-primary" onclick="submitRegistration()">Register &#8594;</button>
      </div>

    </div><!-- /regFormBody -->

  </div><!-- /form-card -->
</div><!-- /form-page -->

<style>
.marital-btn {
  display: inline-flex;
  align-items: center;
  gap: .55rem;
  padding: .7rem 1.4rem;
  border: 2px solid var(--border);
  border-radius: 10px;
  background: var(--white, #fff);
  color: var(--navy);
  font-size: .95rem;
  font-weight: 600;
  cursor: pointer;
  transition: border-color .18s, background .18s, color .18s, box-shadow .18s;
}
.marital-btn:hover {
  border-color: var(--navy);
  background: rgba(43,79,126,.04);
}
.marital-btn.active {
  border-color: var(--navy);
  background: var(--navy);
  color: #fff;
  box-shadow: 0 3px 12px rgba(43,79,126,.25);
}
</style>

<script>
var currentMaritalStatus = null;

document.addEventListener('DOMContentLoaded', function() {
  // Form body stays hidden until marital status is chosen
});

function setMaritalStatus(status) {
  currentMaritalStatus = status;

  // Toggle active state on buttons
  document.getElementById('mbtn-single').classList.toggle('active',  status === 'single');
  document.getElementById('mbtn-married').classList.toggle('active', status === 'married');

  var body = document.getElementById('regFormBody');
  var p2   = document.getElementById('parent2Section');
  var p1Label = document.getElementById('parent1Label');

  // Show form body
  body.style.display = 'block';

  if (status === 'married') {
    p2.style.display = 'block';
    p1Label.innerHTML = '&#128100; Father / First Parent Details';
    // Auto-set Parent 2 relationship opposite of Parent 1
    onParent1RelChange(document.getElementById('reg_rel').value);
  } else {
    p2.style.display = 'none';
    p1Label.innerHTML = '&#128100; Parent / Guardian Details';
    clearParent2Fields();
  }

  // Render wards if not done yet
  if (!document.querySelector('.ward-block')) renderWardForms();
}

function onParent1RelChange(val) {
  if (currentMaritalStatus !== 'married') return;
  var spRel = document.getElementById('sp_rel');
  if (!spRel) return;
  if (val === 'Father')      spRel.value = 'Mother';
  else if (val === 'Mother') spRel.value = 'Father';
  // Update second parent label
  var lbl = document.getElementById('parent2Label');
  if (!lbl) return;
  if (val === 'Father')      lbl.innerHTML = '&#128105; Mother / Second Parent Details';
  else if (val === 'Mother') lbl.innerHTML = '&#128104; Father / Second Parent Details';
  else                       lbl.innerHTML = '&#128100; Second Parent Details';
}

function clearParent2Fields() {
  ['sp_pfirst','sp_plast','sp_phone','sp_id_no'].forEach(function(id) {
    var el = document.getElementById(id); if (el) el.value = '';
  });
  var sp = document.getElementById('sp_pphoto_preview');
  if (sp) { sp.style.display = 'none'; sp.src = ''; }
  var spf = document.getElementById('sp_pphoto'); if (spf) spf.value = '';
  var spidt = document.getElementById('sp_id_type'); if (spidt) spidt.value = '';
}

// Expose for clearRegForm in app.js
function toggleSpouseSection(show) {
  var p2 = document.getElementById('parent2Section');
  if (p2) p2.style.display = show ? 'block' : 'none';
  if (!show) clearParent2Fields();
}
</script>

<?php layout_footer(); ?>
