<?php
// ============================================================
// attendance.php  —  GPS-gated Attendance Sign-In
// URL: /attendance.php
// ============================================================
require_once 'includes/layout.php';
layout_head('Attendance');
layout_nav('attendance');
?>

<div class="sign-page">

  <!-- ── Heading ─────────────────────────────────────────── -->
  <div style="text-align:center;margin-bottom:2rem">
    <div class="section-kicker">Sign In</div>
    <h2 class="section-title" style="color:var(--navy)">Attendance &amp; Visitation</h2>
    <p class="section-sub">You must be on school premises to sign attendance.</p>
  </div>

  <div class="sign-card">

    <!-- ── GPS status banner ─────────────────────────────── -->
    <div class="gps-banner checking" id="gpsBanner">
      <span class="gps-icon">📡</span>
      <div><b>Checking your location…</b> Please allow location access when prompted.</div>
    </div>

    <!-- ── Step 1: Phone lookup ───────────────────────────── -->
    <div class="sign-step active" id="signStep1">

      <div id="gpsBlockedMsg" style="display:none;text-align:center;padding:1rem 0">
        <div style="font-size:3rem;margin-bottom:.75rem">🚫</div>
        <h3 style="font-family:var(--font-display);color:var(--danger);margin-bottom:.5rem">Not On Campus</h3>
        <p style="color:var(--text-muted);font-size:14px">You must be physically on school premises to sign attendance.</p>
      </div>

      <div id="gpsAllowedForm" style="display:none">
        <div class="form-group" style="margin-bottom:1rem">
          <label>Your Registered Phone Number <span class="req">*</span></label>
          <input type="tel" id="sign_phone" placeholder="e.g. 0244 000 000"/>
        </div>
        <button class="btn-primary" style="width:100%" onclick="lookupParent()">Find My Record →</button>
        <p style="text-align:center;margin-top:1rem;font-size:13px;color:var(--text-muted)">
          Not registered?
          <a href="register.php" style="color:var(--gold)">Register here</a>
        </p>
      </div>
    </div><!-- /signStep1 -->

    <!-- ── Step 2: Confirm identity ──────────────────────── -->
    <div class="sign-step" id="signStep2">
      <div class="found-parent" id="foundParentBox"></div>

      <div class="form-group" style="margin-bottom:1rem">
        <label>Select Event <span class="req">*</span></label>
        <select id="sign_event"></select>
      </div>

      <div class="form-group" style="margin-bottom:1rem">
        <label>Visit Type</label>
        <select id="sign_vtype">
          <option value="Event Attendance">Event Attendance</option>
          <option value="Visitation">Visitation</option>
          <option value="Walk-in">Walk-in</option>
        </select>
      </div>

      <div class="form-group" style="margin-bottom:1.25rem">
        <label>Notes (optional)</label>
        <textarea id="sign_notes" placeholder="e.g. Visited ward in classroom 3A…"></textarea>
      </div>

      <div style="display:flex;gap:10px">
        <button class="btn-outline" style="color:var(--navy);border-color:var(--border);flex:1"
          onclick="resetSignIn()">← Back</button>
        <button class="btn-primary" style="flex:2" onclick="submitAttendance()">Sign In ✓</button>
      </div>
    </div><!-- /signStep2 -->

    <!-- ── Step 3: Confirmed ──────────────────────────────── -->
    <div class="sign-step" id="signStep3" style="text-align:center;padding:1rem 0">
      <div style="font-size:3rem;margin-bottom:.75rem">✅</div>
      <h3 style="font-family:var(--font-display);color:var(--navy);margin-bottom:.5rem">Signed In!</h3>
      <p id="signedInMsg" style="color:var(--text-muted);margin-bottom:1.5rem;font-size:14px"></p>
      <button class="btn-primary" onclick="resetSignIn()">Sign In Another Parent</button>
    </div><!-- /signStep3 -->

  </div><!-- /sign-card -->
</div><!-- /sign-page -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  loadAttendanceEvents();
  checkGPS();
});
</script>

<?php layout_footer(); ?>
