<?php
// ============================================================
// includes/layout.php
// Shared layout helpers
// ============================================================

function layout_head($pageTitle) {
  if (!$pageTitle) $pageTitle = 'SchoolConnect';
  $root = layout_root();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle); ?> &mdash; Hope College</title>
  <!-- ✅ FAVICON — place your favicon file at: img/favicon.png -->
  <link rel="icon" type="image/png" href="<?php echo $root; ?>img/favicon.png"/>
  <link rel="shortcut icon" type="image/png" href="<?php echo $root; ?>img/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
  <meta http-equiv="Pragma" content="no-cache"/>
  <meta http-equiv="Expires" content="0"/>
  <link rel="stylesheet" href="<?php echo $root; ?>assets/css/style.css?v=2"/>
</head>
<body>
<?php
}

function layout_nav($activePage) {
  if (!$activePage) $activePage = 'home';
  $root = layout_root();
?>
<!-- Navigation -->
<nav>
  <a class="nav-brand" href="<?php echo $root; ?>index.php">
    <div class="nav-logo">
      <!-- ✅ SCHOOL LOGO — place your logo file at: img/logo.png -->
      <img src="<?php echo $root; ?>img/logo.png"
           alt="Hope College Logo"
           style="width:36px;height:36px;object-fit:contain;border-radius:8px;"/>
    </div>
    <div style="display:flex;flex-direction:column;line-height:1.15;">
      <span class="nav-brand-text">Hope<span> College</span></span>
      <!-- ✅ SCHOOL MOTTO -->
      <span style="font-size:9.5px;color:rgba(56,189,248,0.90);letter-spacing:.04em;font-family:var(--font-body);font-style:italic;">
        Character &bull; Scholarship &bull; Service &bull; Leadership
      </span>
    </div>
  </a>
  <div class="nav-links">
    <a href="<?php echo $root; ?>index.php"       <?php if ($activePage === 'home')        echo 'class="active"'; ?>>Home</a>
    <a href="<?php echo $root; ?>register.php"    <?php if ($activePage === 'register')    echo 'class="active"'; ?>>Register</a>
    <a href="<?php echo $root; ?>attendance.php"  <?php if ($activePage === 'attendance')  echo 'class="active"'; ?>>Attendance</a>
    <a href="<?php echo $root; ?>parent_portal.php" <?php if ($activePage === 'parent_portal') echo 'class="active"'; ?>>Parent Portal</a>
    <a href="<?php echo $root; ?>admin.php"         <?php if ($activePage === 'admin')         echo 'class="active"'; ?>>Admin</a>
    <a href="<?php echo $root; ?>domestic.php"    <?php if ($activePage === 'domestic')    echo 'class="active"'; ?>>Domestic</a>
    <a href="<?php echo $root; ?>houseparent.php" <?php if ($activePage === 'houseparent') echo 'class="active"'; ?>>House Parent</a>
  </div>
  <button class="hamburger" onclick="toggleMobile()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu">
  <a href="<?php echo $root; ?>index.php">Home</a>
  <a href="<?php echo $root; ?>register.php">Register</a>
  <a href="<?php echo $root; ?>attendance.php">Attendance / Visitation</a>
  <a href="<?php echo $root; ?>parent_portal.php">Parent Portal</a>
  <a href="<?php echo $root; ?>admin.php">Admin Dashboard</a>
  <a href="<?php echo $root; ?>domestic.php">Domestic Affairs</a>
  <a href="<?php echo $root; ?>houseparent.php">House Parent Portal</a>
</div>
<?php
}

function layout_footer() {
  $root = layout_root();
?>

<!-- Toast notification -->
<div class="toast" id="toast"><span id="toastMsg"></span></div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="<?php echo $root; ?>assets/js/app.js"></script>
</body>
</html>
<?php
}

function layout_root() {
  return '';
}
