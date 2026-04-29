<?php
// ============================================================
// register.php  —  Parent & Student Registration
// URL: /register.php
// ============================================================
require_once 'includes/layout.php';
layout_head('Register');
layout_nav('register');
?>

<div class="form-page" style="max-width:780px">

  <!-- ── Heading ─────────────────────────────────────────── -->
  <div style="margin-bottom:2rem;text-align:center">
    <div class="section-kicker">Parent / Student Space</div>
    <h2 class="section-title" style="color:var(--navy)">Register Parent &amp; Wards</h2>
    <p class="section-sub">Fill in parent details and add all wards in school.
      Fields marked <span style="color:var(--danger)">*</span> are required.</p>
  </div>

  <div class="form-card">

    <!-- ── Parent / Guardian details ───────────────────────── -->
    <div class="form-section-title">👤 Parent / Guardian Details</div>

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
        <select id="reg_rel">
          <option value="Father">Father</option>
          <option value="Mother">Mother</option>
          <option value="Guardian" selected>Guardian</option>
          <option value="Other">Other</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" id="reg_email" placeholder="parent@email.com" autocomplete="email"/>
      </div>
      <div class="form-group">
        <label>Home Address</label>
        <input type="text" id="reg_address" placeholder="House no., area" autocomplete="street-address"/>
      </div>
    </div>

    <div class="form-row single">
      <div class="form-group">
        <label>Parent Photo (optional)</label>
        <label class="photo-upload-label" for="reg_pphoto">
          <img id="reg_pphoto_preview" class="photo-preview" alt=""/>
          <span>📷 Click to upload photo</span>
          <span style="font-size:11px">JPG / PNG / WEBP · max 5 MB</span>
        </label>
        <input type="file" id="reg_pphoto" accept="image/*" style="display:none"
          onchange="previewPhoto(this,'reg_pphoto_preview')"/>
      </div>
    </div>

    <div class="form-divider"></div>

    <!-- ── Ward(s) section ──────────────────────────────────── -->
    <div class="form-section-title">🎒 Ward(s) Details</div>

    <div class="ward-mode-tabs">
      <button type="button" class="ward-mode-tab active" id="tab-new"
        onclick="setWardMode('new')">➕ Register New Ward</button>
      <button type="button" class="ward-mode-tab" id="tab-existing"
        onclick="setWardMode('existing')">🔍 Link Existing Student</button>
    </div>

    <!-- New ward(s) -->
    <div id="modeNew">
      <div style="background:rgba(212,153,58,.08);border:1.5px solid rgba(212,153,58,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
          <div style="font-weight:600;font-size:.95rem;color:var(--navy);margin-bottom:.2rem">How many wards are in this school?</div>
          <div style="font-size:13px;color:var(--text-muted)">A form will be generated for each ward.</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <button type="button" class="ward-count-btn" onclick="changeWardCount(-1)">−</button>
          <span id="wardCountDisplay" style="font-family:var(--font-display);font-size:1.8rem;color:var(--navy);min-width:36px;text-align:center">1</span>
          <button type="button" class="ward-count-btn" onclick="changeWardCount(1)">+</button>
        </div>
      </div>
      <div id="wardFormsContainer"></div>
    </div>

    <!-- Link existing student -->
    <div id="modeExisting" style="display:none">
      <div style="background:rgba(43,79,126,.06);border:1.5px solid rgba(43,79,126,.15);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1rem;font-size:13px;color:var(--navy-mid)">
        ℹ️ Search for your child already registered under another parent. You will be linked to that same student record — no duplicate entry.
      </div>
      <div id="existingSearchBlocks"></div>
      <button type="button" class="btn-outline"
        style="font-size:13px;padding:8px 16px;color:var(--navy);border-color:var(--border);margin-top:.5rem"
        onclick="addExistingSearch()">+ Add Another Student</button>
    </div>

    <!-- ── Actions ──────────────────────────────────────────── -->
    <div class="submit-row">
      <a class="btn-outline" href="index.php" style="color:var(--navy);border-color:var(--border)">Cancel</a>
      <button class="btn-primary" onclick="submitRegistration()">Register →</button>
    </div>

  </div><!-- /form-card -->
</div><!-- /form-page -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  setWardMode('new');
  renderWardForms();
});
</script>

<?php layout_footer(); ?>
