<?php
// index.php – SchoolConnect main entry point
// QR code redirects here with ?page=attendance
$startPage = in_array($_GET['page'] ?? '', ['home','register','attendance','admin']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>SchoolConnect — Parent & Student Registry</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0B1F3A;--navy-mid:#1A3558;--navy-light:#2B4F7E;
  --gold:#D4993A;--gold-light:#EDB95A;
  --cream:#FAF7F2;--cream-dark:#F0EAE0;
  --text:#0B1F3A;--text-muted:#5A6E87;--white:#FFFFFF;
  --success:#1B7E5A;--danger:#C0392B;
  --border:rgba(11,31,58,0.12);--shadow:0 4px 24px rgba(11,31,58,0.10);
  --radius:12px;--radius-sm:8px;
  --font-display:'DM Serif Display',Georgia,serif;
  --font-body:'DM Sans',sans-serif;
}
html{scroll-behavior:smooth}
body{font-family:var(--font-body);background:var(--cream);color:var(--text);min-height:100vh;font-size:16px;line-height:1.6}

/* NAV */
nav{background:var(--navy);position:sticky;top:0;z-index:100;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(1rem,5vw,2.5rem);height:64px;box-shadow:0 2px 16px rgba(0,0,0,0.18)}
.nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;cursor:pointer}
.nav-logo{width:36px;height:36px;border-radius:8px;background:var(--gold);display:flex;align-items:center;justify-content:center}
.nav-brand-text{font-family:var(--font-display);font-size:18px;color:var(--white);letter-spacing:.01em}
.nav-brand-text span{color:var(--gold-light)}
.nav-links{display:flex;align-items:center;gap:4px}
.nav-links a{color:rgba(255,255,255,.75);text-decoration:none;font-size:14px;font-weight:400;padding:6px 14px;border-radius:6px;transition:all .2s;cursor:pointer}
.nav-links a:hover,.nav-links a.active{color:var(--white);background:rgba(255,255,255,.1)}
.nav-links a.active{color:var(--gold-light)}
.nav-btn{background:var(--gold);color:var(--navy);font-weight:600;font-size:13px;padding:8px 18px;border-radius:6px;text-decoration:none;transition:background .2s;margin-left:8px;cursor:pointer;border:none}
.nav-btn:hover{background:var(--gold-light)}
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;background:none;border:none;padding:6px}
.hamburger span{display:block;width:22px;height:2px;background:var(--white);border-radius:2px;transition:all .3s}
.mobile-menu{display:none;position:fixed;top:64px;left:0;right:0;background:var(--navy);padding:1rem clamp(1rem,5vw,2rem) 1.5rem;border-bottom:2px solid var(--gold);z-index:99;flex-direction:column;gap:4px}
.mobile-menu.open{display:flex}
.mobile-menu a{color:rgba(255,255,255,.8);text-decoration:none;font-size:15px;padding:10px 14px;border-radius:8px;transition:background .2s;cursor:pointer}
.mobile-menu a:hover{background:rgba(255,255,255,.1);color:var(--white)}
.mobile-menu .nav-btn{align-self:flex-start;margin-left:14px;margin-top:8px}

/* PAGES */
.page{display:none}.page.active{display:block}

/* HERO */
.hero{background:var(--navy);color:var(--white);padding:clamp(3rem,8vw,6rem) clamp(1rem,5vw,2.5rem);text-align:center;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 100%,rgba(212,153,58,.18) 0%,transparent 70%);pointer-events:none}
.hero-kicker{display:inline-block;background:rgba(212,153,58,.18);color:var(--gold-light);font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:5px 14px;border-radius:20px;border:1px solid rgba(212,153,58,.3);margin-bottom:1.25rem}
.hero h1{font-family:var(--font-display);font-size:clamp(2rem,6vw,3.5rem);line-height:1.15;margin-bottom:1rem;max-width:700px;margin-left:auto;margin-right:auto}
.hero h1 em{color:var(--gold-light);font-style:italic}
.hero p{font-size:clamp(.95rem,2.5vw,1.1rem);color:rgba(255,255,255,.72);max-width:560px;margin:0 auto 2rem}
.hero-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn-primary{background:var(--gold);color:var(--navy);font-weight:600;font-size:15px;padding:12px 28px;border-radius:8px;border:none;cursor:pointer;text-decoration:none;transition:all .2s;display:inline-block}
.btn-primary:hover{background:var(--gold-light);transform:translateY(-1px)}
.btn-outline{background:transparent;color:var(--white);font-weight:500;font-size:15px;padding:12px 28px;border-radius:8px;border:1.5px solid rgba(255,255,255,.3);cursor:pointer;text-decoration:none;transition:all .2s;display:inline-block}
.btn-outline:hover{border-color:rgba(255,255,255,.6);background:rgba(255,255,255,.06)}
.btn-danger{background:var(--danger);color:var(--white);border:none;padding:8px 18px;border-radius:6px;font-weight:600;cursor:pointer;font-size:14px;transition:opacity .2s}
.btn-danger:hover{opacity:.85}
.btn-success{background:var(--success);color:var(--white);border:none;padding:8px 18px;border-radius:6px;font-weight:600;cursor:pointer;font-size:14px;transition:opacity .2s}
.btn-success:hover{opacity:.85}

/* EVENTS STRIP */
.events-strip{background:var(--cream-dark);border-bottom:1px solid var(--border);padding:.75rem clamp(1rem,5vw,2.5rem)}
.events-strip-inner{max-width:1100px;margin:0 auto;display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
.events-strip-label{font-size:12px;font-weight:600;color:var(--gold);text-transform:uppercase;letter-spacing:.08em;white-space:nowrap}
.event-pill{background:var(--white);border:1px solid var(--border);border-radius:20px;padding:5px 14px;font-size:13px;color:var(--navy-mid);white-space:nowrap}
.event-pill strong{color:var(--navy)}

/* SECTION */
.section{padding:clamp(2.5rem,7vw,5rem) clamp(1rem,5vw,2.5rem);max-width:1100px;margin:0 auto}
.section-header{text-align:center;margin-bottom:clamp(2rem,5vw,3.5rem)}
.section-kicker{font-size:12px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:.75rem}
.section-title{font-family:var(--font-display);font-size:clamp(1.6rem,4vw,2.4rem);color:var(--navy);line-height:1.2}
.section-sub{font-size:1rem;color:var(--text-muted);margin-top:.6rem;max-width:500px;margin-left:auto;margin-right:auto}

.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px}
.feat-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;transition:box-shadow .2s,transform .2s;cursor:pointer}
.feat-card:hover{box-shadow:var(--shadow);transform:translateY(-2px)}
.feat-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;font-size:22px}
.feat-icon.navy{background:rgba(11,31,58,.08)}
.feat-icon.gold{background:rgba(212,153,58,.12)}
.feat-icon.green{background:rgba(27,126,90,.1)}
.feat-icon.blue{background:rgba(43,79,126,.1)}
.feat-title{font-weight:600;font-size:1rem;margin-bottom:.4rem;color:var(--navy)}
.feat-desc{font-size:.875rem;color:var(--text-muted);line-height:1.55}

/* UPCOMING EVENTS CARDS */
.events-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px}
.ev-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem 1.5rem;position:relative;overflow:hidden}
.ev-card::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:var(--gold)}
.ev-type-badge{display:inline-block;font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;padding:3px 10px;border-radius:20px;background:rgba(212,153,58,.12);color:var(--gold);margin-bottom:.6rem}
.ev-name{font-family:var(--font-display);font-size:1.15rem;color:var(--navy);margin-bottom:.35rem}
.ev-meta{font-size:13px;color:var(--text-muted);display:flex;flex-direction:column;gap:4px}
.ev-meta span{display:flex;align-items:center;gap:6px}

/* FORMS */
.form-page{max-width:720px;margin:0 auto;padding:clamp(2rem,6vw,4rem) clamp(1rem,5vw,2.5rem)}
.form-card{background:var(--white);border:1px solid var(--border);border-radius:16px;padding:clamp(1.5rem,4vw,2.5rem);box-shadow:var(--shadow)}
.form-section-title{font-family:var(--font-display);font-size:1.3rem;color:var(--navy);margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.form-row.single{grid-template-columns:1fr}
.form-group{display:flex;flex-direction:column;gap:6px}
label{font-size:13px;font-weight:500;color:var(--navy-mid)}
label span.req{color:var(--danger)}
input,select,textarea{font-family:var(--font-body);font-size:14px;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;background:var(--cream);color:var(--text);transition:border .2s,box-shadow .2s;outline:none;width:100%}
input:focus,select:focus,textarea:focus{border-color:var(--navy-light);box-shadow:0 0 0 3px rgba(43,79,126,.1)}
textarea{resize:vertical;min-height:80px}
.form-divider{height:1px;background:var(--border);margin:1.5rem 0}
.photo-upload-label{display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed var(--border);border-radius:10px;padding:1.25rem;cursor:pointer;transition:border .2s;gap:8px;background:var(--cream)}
.photo-upload-label:hover{border-color:var(--gold)}
.photo-upload-label span{font-size:13px;color:var(--text-muted)}
.photo-preview{width:72px;height:72px;border-radius:50%;object-fit:cover;display:none;border:3px solid var(--gold)}
.submit-row{margin-top:1.5rem;display:flex;gap:12px;justify-content:flex-end;flex-wrap:wrap}

/* ADMIN */
.admin-layout{display:grid;grid-template-columns:240px 1fr;min-height:calc(100vh - 64px)}
.admin-sidebar{background:var(--navy);color:var(--white);padding:2rem 0}
.admin-sidebar-section{padding:0 1.25rem;margin-bottom:1.5rem}
.admin-sidebar h2{font-family:var(--font-display);font-size:1.1rem;color:var(--gold-light);margin-bottom:1rem;padding:0 1rem}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:9px 1rem;border-radius:8px;cursor:pointer;font-size:14px;color:rgba(255,255,255,.72);transition:all .2s;margin-bottom:2px}
.sidebar-link:hover,.sidebar-link.active{background:rgba(255,255,255,.1);color:var(--white)}
.sidebar-link.active{color:var(--gold-light)}
.sidebar-link .icon{font-size:18px;width:22px;text-align:center}
.admin-main{background:var(--cream);padding:2rem clamp(1rem,3vw,2rem)}
.admin-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.admin-header h1{font-family:var(--font-display);font-size:1.8rem;color:var(--navy)}
.admin-sub{display:none}.admin-sub.active{display:block}

/* STATS CARDS */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:2rem}
.stat-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem}
.stat-card .num{font-family:var(--font-display);font-size:2rem;color:var(--navy);display:block}
.stat-card .lbl{font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em}

/* TABLE */
.table-wrap{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.table-toolbar{display:flex;align-items:center;gap:12px;padding:1rem 1.25rem;border-bottom:1px solid var(--border);flex-wrap:wrap}
.table-toolbar input,.table-toolbar select{max-width:200px;padding:8px 12px;font-size:13px}
table{width:100%;border-collapse:collapse}
thead th{background:var(--navy);color:var(--white);font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;padding:10px 14px;text-align:left}
tbody tr{border-bottom:1px solid var(--border);transition:background .15s}
tbody tr:hover{background:var(--cream)}
tbody td{padding:10px 14px;font-size:13px;color:var(--text)}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-father{background:rgba(43,79,126,.12);color:var(--navy-light)}
.badge-mother{background:rgba(212,153,58,.14);color:#8a6010}
.badge-guardian{background:rgba(27,126,90,.12);color:var(--success)}
.badge-other{background:rgba(90,110,135,.12);color:var(--text-muted)}
.avatar-sm{width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid var(--border);background:var(--navy-light);color:var(--white);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;flex-shrink:0}
.avatar-sm img{width:100%;height:100%;border-radius:50%;object-fit:cover}
.parent-cell{display:flex;align-items:center;gap:10px}

/* ATTENDANCE */
.att-layout{display:grid;grid-template-columns:300px 1fr;gap:1.5rem;align-items:start}
.event-list{display:flex;flex-direction:column;gap:10px;max-height:70vh;overflow-y:auto}
.event-list-item{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:1rem;cursor:pointer;transition:all .2s}
.event-list-item:hover{border-color:var(--gold)}
.event-list-item.active{border-color:var(--gold);background:rgba(212,153,58,.06)}
.event-list-item .ev-n{font-weight:600;font-size:.9rem;color:var(--navy)}
.event-list-item .ev-d{font-size:12px;color:var(--text-muted)}
.event-list-item .ev-progress{height:4px;background:var(--cream-dark);border-radius:2px;margin-top:.5rem}
.event-list-item .ev-fill{height:100%;background:var(--gold);border-radius:2px;transition:width .4s}
.checkin-list{display:flex;flex-direction:column;gap:8px;max-height:60vh;overflow-y:auto}
.checkin-item{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:.75rem 1rem;display:flex;align-items:center;gap:12px}
.checkin-item .info{flex:1;min-width:0}
.checkin-item .name{font-weight:600;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.checkin-item .sub{font-size:12px;color:var(--text-muted)}
.toggle-btn{width:38px;height:22px;border-radius:11px;background:var(--cream-dark);border:none;cursor:pointer;position:relative;flex-shrink:0;transition:background .2s}
.toggle-btn::after{content:'';position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:var(--white);transition:left .2s;box-shadow:0 1px 4px rgba(0,0,0,.2)}
.toggle-btn.on{background:var(--success)}
.toggle-btn.on::after{left:19px}

/* ATTENDANCE SELF-SIGN PAGE */
.sign-page{max-width:500px;margin:0 auto;padding:clamp(2rem,6vw,4rem) clamp(1rem,5vw,2.5rem)}
.sign-card{background:var(--white);border:1px solid var(--border);border-radius:16px;padding:2rem;box-shadow:var(--shadow)}
.sign-step{display:none}.sign-step.active{display:block}
.found-parent{background:var(--cream);border-radius:10px;padding:1rem;display:flex;align-items:center;gap:12px;margin-bottom:1rem}
.found-parent .big-av{width:52px;height:52px;border-radius:50%;object-fit:cover;background:var(--navy-light);display:flex;align-items:center;justify-content:center;color:var(--white);font-weight:700;font-size:1.1rem}
.found-parent .big-av img{width:100%;height:100%;border-radius:50%;object-fit:cover}

/* MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(11,31,58,.55);z-index:200;align-items:center;justify-content:center;padding:1rem}
.modal-overlay.open{display:flex}
.modal{background:var(--white);border-radius:16px;padding:clamp(1.5rem,4vw,2rem);max-width:480px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25);position:relative}
.modal h3{font-family:var(--font-display);font-size:1.3rem;color:var(--navy);margin-bottom:1.25rem}
.modal-close{position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted);line-height:1}
.modal-close:hover{color:var(--danger)}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:1.25rem;flex-wrap:wrap}

/* LOGIN overlay */
.login-overlay{position:fixed;inset:0;background:rgba(11,31,58,.7);z-index:300;display:flex;align-items:center;justify-content:center;padding:1rem}
.login-box{background:var(--white);border-radius:16px;padding:2.5rem;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.login-box h2{font-family:var(--font-display);font-size:1.6rem;color:var(--navy);margin-bottom:.4rem}
.login-box p{font-size:13px;color:var(--text-muted);margin-bottom:1.5rem}

/* TOAST */
.toast{position:fixed;bottom:1.5rem;right:1.5rem;background:var(--navy);color:var(--white);padding:.75rem 1.25rem;border-radius:10px;font-size:14px;opacity:0;transform:translateY(10px);transition:all .3s;z-index:400;pointer-events:none;max-width:300px}
.toast.show{opacity:1;transform:translateY(0)}
.toast.error{background:var(--danger)}
.toast.success-toast{background:var(--success)}

/* DETAIL MODAL */
.parent-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;font-size:14px;margin:1rem 0}
.parent-detail-grid dt{font-weight:600;color:var(--text-muted);font-size:12px;text-transform:uppercase;letter-spacing:.06em}
.parent-detail-grid dd{color:var(--navy)}

/* QR */
#qrTarget{display:flex;align-items:center;justify-content:center;padding:1rem}

/* WARD COUNT */
.ward-count-btn{width:36px;height:36px;border-radius:50%;border:2px solid var(--gold);background:var(--white);color:var(--gold);font-size:1.3rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;line-height:1}
.ward-count-btn:hover{background:var(--gold);color:var(--white)}
.ward-block{background:var(--cream);border:1.5px solid var(--border);border-radius:12px;padding:1.25rem;margin-bottom:1rem;position:relative}
.ward-block-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem}
.ward-block-title{font-weight:600;color:var(--navy);font-size:.95rem;display:flex;align-items:center;gap:8px}
.ward-block-num{width:26px;height:26px;border-radius:50%;background:var(--navy);color:var(--white);font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center}

/* RESPONSIVE */
@media(max-width:900px){
  .admin-layout{grid-template-columns:1fr}
  .admin-sidebar{display:none}
  .att-layout{grid-template-columns:1fr}
  .form-row{grid-template-columns:1fr}
}
@media(max-width:640px){
  .nav-links,.nav-btn{display:none}
  .hamburger{display:flex}
  .stats-grid{grid-template-columns:1fr 1fr}
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-brand" onclick="showPage('home')">
    <div class="nav-logo">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#0B1F3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <span class="nav-brand-text">School<span>Connect</span></span>
  </div>
  <div class="nav-links">
    <a onclick="showPage('home')"       id="nl-home">Home</a>
    <a onclick="showPage('register')"   id="nl-register">Register</a>
    <a onclick="showPage('attendance')" id="nl-attendance">Attendance</a>
    <a onclick="requireAdmin()"         id="nl-admin">Admin</a>
  </div>
  <button class="hamburger" onclick="toggleMobile()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a onclick="showPage('home');toggleMobile()">Home</a>
  <a onclick="showPage('register');toggleMobile()">Register</a>
  <a onclick="showPage('attendance');toggleMobile()">Attendance / Visitation</a>
  <a onclick="requireAdmin();toggleMobile()">Admin Dashboard</a>
</div>

<!-- ══════════════ HOME PAGE ══════════════ -->
<div class="page active" id="page-home">

  <!-- Upcoming events strip -->
  <div class="events-strip">
    <div class="events-strip-inner">
      <span class="events-strip-label">📅 Upcoming</span>
      <div id="stripEvents"><span class="event-pill">Loading events…</span></div>
    </div>
  </div>

  <div class="hero">
    <div class="hero-kicker">Parent & Student Registry</div>
    <h1>Welcome to <em>SchoolConnect</em></h1>
    <p>A seamless platform connecting parents, students, and school administration — from registration to attendance.</p>
    <div class="hero-btns">
      <button class="btn-primary" onclick="showPage('register')">Register Now</button>
      <button class="btn-outline" onclick="showPage('attendance')">Sign Attendance</button>
    </div>
    <div class="stats-row">
      <div class="stat"><span class="stat-num" id="statParents">—</span><span class="stat-label">Parents</span></div>
      <div class="stat"><span class="stat-num" id="statStudents">—</span><span class="stat-label">Students</span></div>
      <div class="stat"><span class="stat-num" id="statEvents">—</span><span class="stat-label">Events</span></div>
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <div class="section-kicker">What you can do</div>
      <h2 class="section-title">Everything in one place</h2>
      <p class="section-sub">From registration to QR attendance — SchoolConnect keeps parents and administration in sync.</p>
    </div>
    <div class="features-grid">
      <div class="feat-card" onclick="showPage('register')">
        <div class="feat-icon gold">👨‍👩‍👧</div>
        <div class="feat-title">Parent & Student Registration</div>
        <div class="feat-desc">Register your child with full parent and student details, including photos.</div>
      </div>
      <div class="feat-card" onclick="showPage('attendance')">
        <div class="feat-icon green">✅</div>
        <div class="feat-title">Attendance & Visitation</div>
        <div class="feat-desc">Sign attendance for PTA meetings, visitation days, and walk-in visits.</div>
      </div>
      <div class="feat-card" onclick="requireAdmin()">
        <div class="feat-icon navy">🛡️</div>
        <div class="feat-title">Admin Dashboard</div>
        <div class="feat-desc">View all parents, wards, event attendance history, and schedule new events.</div>
      </div>
      <div class="feat-card" onclick="openQR()">
        <div class="feat-icon blue">📲</div>
        <div class="feat-title">QR Code Access</div>
        <div class="feat-desc">Generate a QR code that parents can scan to jump straight to attendance signing.</div>
      </div>
    </div>
  </div>

  <!-- Upcoming events full section -->
  <div style="background:var(--navy);padding:3rem clamp(1rem,5vw,2.5rem)">
    <div style="max-width:1100px;margin:0 auto">
      <div style="text-align:center;margin-bottom:2rem">
        <div class="section-kicker" style="color:var(--gold-light)">Calendar</div>
        <h2 class="section-title" style="color:var(--white)">Upcoming Events</h2>
      </div>
      <div class="events-grid" id="homeEvents"><p style="color:rgba(255,255,255,.6);text-align:center">Loading…</p></div>
    </div>
  </div>

</div><!-- /page-home -->

<!-- ══════════════ REGISTER PAGE ══════════════ -->
<div class="page" id="page-register">
  <div class="form-page" style="max-width:780px">
    <div style="margin-bottom:2rem;text-align:center">
      <div class="section-kicker">Parent / Student Space</div>
      <h2 class="section-title" style="color:var(--navy)">Register Parent & Wards</h2>
      <p class="section-sub">Fill in parent details and add all wards in school. Fields marked <span style="color:var(--danger)">*</span> are required.</p>
    </div>
    <div class="form-card">

      <!-- PARENT SECTION -->
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
          <input type="file" id="reg_pphoto" accept="image/*" style="display:none" onchange="previewPhoto(this,'reg_pphoto_preview')"/>
        </div>
      </div>

      <div class="form-divider"></div>

      <!-- WARDS COUNT SELECTOR -->
      <div class="form-section-title">🎒 Ward(s) Details</div>
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

      <!-- DYNAMIC WARD FORMS -->
      <div id="wardFormsContainer"></div>

      <div class="submit-row">
        <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="showPage('home')">Cancel</button>
        <button class="btn-primary" onclick="submitRegistration()">Register →</button>
      </div>
    </div>
  </div>
</div><!-- /page-register -->

<!-- ══════════════ ATTENDANCE / VISITATION PAGE ══════════════ -->
<div class="page" id="page-attendance">
  <div class="sign-page">
    <div style="text-align:center;margin-bottom:2rem">
      <div class="section-kicker">Sign In</div>
      <h2 class="section-title" style="color:var(--navy)">Attendance & Visitation</h2>
      <p class="section-sub">Enter your registered phone number to find your record, then sign in for today's event.</p>
    </div>
    <div class="sign-card">
      <!-- Step 1: look up by phone -->
      <div class="sign-step active" id="signStep1">
        <div class="form-group" style="margin-bottom:1rem">
          <label>Your Registered Phone Number <span class="req">*</span></label>
          <input type="tel" id="sign_phone" placeholder="e.g. 0244 000 000"/>
        </div>
        <button class="btn-primary" style="width:100%" onclick="lookupParent()">Find My Record →</button>
        <p style="text-align:center;margin-top:1rem;font-size:13px;color:var(--text-muted)">Not registered? <a href="#" onclick="showPage('register')" style="color:var(--gold)">Register here</a></p>
      </div>

      <!-- Step 2: confirm identity -->
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
          <button class="btn-outline" style="color:var(--navy);border-color:var(--border);flex:1" onclick="resetSignIn()">← Back</button>
          <button class="btn-primary" style="flex:2" onclick="submitAttendance()">Sign In ✓</button>
        </div>
      </div>

      <!-- Step 3: confirmed -->
      <div class="sign-step" id="signStep3" style="text-align:center;padding:1rem 0">
        <div style="font-size:3rem;margin-bottom:.75rem">✅</div>
        <h3 style="font-family:var(--font-display);color:var(--navy);margin-bottom:.5rem">Signed In!</h3>
        <p id="signedInMsg" style="color:var(--text-muted);margin-bottom:1.5rem;font-size:14px"></p>
        <button class="btn-primary" onclick="resetSignIn()">Sign In Another Parent</button>
      </div>
    </div>
  </div>
</div><!-- /page-attendance -->

<!-- ══════════════ ADMIN PAGE ══════════════ -->
<div class="page" id="page-admin">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <h2 style="padding:0 1.5rem;margin-bottom:1.25rem">Admin Panel</h2>
      <div class="admin-sidebar-section">
        <div class="sidebar-link active" id="sb-overview"  onclick="adminTab('overview')"><span class="icon">📊</span> Overview</div>
        <div class="sidebar-link"         id="sb-parents"   onclick="adminTab('parents')"><span class="icon">👥</span> Parents & Wards</div>
        <div class="sidebar-link"         id="sb-events"    onclick="adminTab('events')"><span class="icon">📅</span> Events</div>
        <div class="sidebar-link"         id="sb-checkin"   onclick="adminTab('checkin')"><span class="icon">✅</span> Attendance</div>
        <div class="sidebar-link"         id="sb-qr"        onclick="openQR()"><span class="icon">📲</span> QR Code</div>
      </div>
      <div style="padding:0 1.5rem;margin-top:auto;padding-top:2rem">
        <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:.5rem" id="adminNameLabel"></div>
        <button class="btn-danger" style="width:100%" onclick="adminLogout()">Logout</button>
      </div>
    </aside>

    <main class="admin-main">
      <!-- Overview -->
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

      <!-- Parents -->
      <div class="admin-sub" id="admin-parents">
        <div class="admin-header">
          <h1>Parents & Wards</h1>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px" onclick="exportCSV()">⬇ CSV</button>
          </div>
        </div>
        <div class="table-wrap">
          <div class="table-toolbar">
            <input type="text" id="searchParents" placeholder="Search name or phone…" oninput="renderParentsTable()" style="flex:1;min-width:160px"/>
            <select id="filterClass" onchange="renderParentsTable()" style="min-width:130px">
              <option value="">All Classes</option>
            </select>
            <select id="filterRel" onchange="renderParentsTable()">
              <option value="">All Relations</option>
              <option>Father</option><option>Mother</option><option>Guardian</option><option>Other</option>
            </select>
          </div>
          <div style="overflow-x:auto">
            <table>
              <thead><tr>
                <th>Parent</th><th>Phone</th><th>Relation</th><th>Student</th><th>Class</th><th>Registered</th><th></th>
              </tr></thead>
              <tbody id="parentsTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Events -->
      <div class="admin-sub" id="admin-events">
        <div class="admin-header">
          <h1>Events</h1>
          <button class="btn-primary" onclick="openAddEvent()">+ Schedule Event</button>
        </div>
        <div class="events-grid" id="adminEventCards"></div>
      </div>

      <!-- Attendance/Check-in -->
      <div class="admin-sub" id="admin-checkin">
        <div class="admin-header">
          <h1>Attendance</h1>
          <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <select id="adminEventSelect" onchange="loadAdminAttendance(this.value)" style="padding:8px 12px;font-size:14px;border-radius:6px;border:1.5px solid var(--border)"></select>
            <button class="btn-outline" style="color:var(--navy);border-color:var(--border);font-size:13px;padding:8px 14px" onclick="exportAttendance()">⬇ Export</button>
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
    </main>
  </div>
</div><!-- /page-admin -->

<!-- ══ MODALS ══ -->

<!-- Add Event -->
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
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="closeModal('addEventModal')">Cancel</button>
      <button class="btn-primary" onclick="saveEvent()">Create Event</button>
    </div>
  </div>
</div>

<!-- Parent Detail -->
<div class="modal-overlay" id="parentDetailModal" onclick="closeModalOnBackdrop(event,'parentDetailModal')">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('parentDetailModal')">✕</button>
    <h3>Parent Details</h3>
    <div id="parentDetailContent"></div>
    <div class="modal-actions"><button class="btn-primary" onclick="closeModal('parentDetailModal')">Close</button></div>
  </div>
</div>

<!-- QR -->
<div class="modal-overlay" id="qrModal" onclick="closeModalOnBackdrop(event,'qrModal')">
  <div class="modal" style="text-align:center">
    <button class="modal-close" onclick="closeModal('qrModal')">✕</button>
    <h3>Attendance QR Code</h3>
    <p style="font-size:13px;color:var(--text-muted);margin-bottom:1rem">Parents scan this to go directly to the Attendance / Visitation page.</p>
    <div id="qrTarget"></div>
    <p id="qrUrl" style="font-size:11px;color:var(--text-muted);margin-top:.5rem;word-break:break-all"></p>
    <div class="modal-actions" style="justify-content:center">
      <button class="btn-outline" style="color:var(--navy);border-color:var(--border)" onclick="downloadQR()">⬇ Download PNG</button>
      <button class="btn-primary" onclick="closeModal('qrModal')">Done</button>
    </div>
  </div>
</div>

<!-- Admin Login -->
<div class="login-overlay" id="adminLoginOverlay" style="display:none">
  <div class="login-box">
    <h2>Admin Login</h2>
    <p>Enter your credentials to access the dashboard.</p>
    <div class="form-group" style="margin-bottom:12px">
      <label>Username</label>
      <input type="text" id="loginUser" placeholder="admin" autocomplete="username"/>
    </div>
    <div class="form-group" style="margin-bottom:1.25rem">
      <label>Password</label>
      <input type="password" id="loginPass" placeholder="••••••••" autocomplete="current-password" onkeydown="if(event.key==='Enter')doLogin()"/>
    </div>
    <button class="btn-primary" style="width:100%" onclick="doLogin()">Login →</button>
    <button class="btn-outline" style="width:100%;margin-top:8px;color:var(--navy);border-color:var(--border)" onclick="document.getElementById('adminLoginOverlay').style.display='none'">Cancel</button>
    <p style="font-size:12px;color:var(--text-muted);margin-top:1rem;text-align:center">Default: admin / Admin@1234</p>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"><span id="toastMsg"></span></div>

<!-- ══ SCRIPTS ══ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// ── STATE ────────────────────────────────────────────────────
let allParents   = [];
let allEvents    = [];
let adminEvtId   = null;
let foundParent  = null;
let adminLoggedIn = false;

const START_PAGE = <?= json_encode($startPage) ?>;

// ── INIT ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  loadPublicData();
  renderWardForms();   // initialise with 1 ward form
  showPage(START_PAGE);
});

function loadPublicData() {
  // Stats
  apiFetch('actions/fetch.php?action=stats').then(r => {
    if (r.success) {
      setText('statParents',  r.data.totalParents);
      setText('statStudents', r.data.totalStudents);
      setText('statEvents',   r.data.totalEvents);
    }
  });
  // Events
  apiFetch('actions/fetch.php?action=events').then(r => {
    if (r.success) {
      allEvents = r.data;
      renderEventStrip();
      renderHomeEvents();
      populateEventDropdown('sign_event', allEvents);
    }
  });
}

// ── NAV ──────────────────────────────────────────────────────
function showPage(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
  const el = document.getElementById('page-' + page);
  if (el) el.classList.add('active');
  const nl = document.getElementById('nl-' + page);
  if (nl) nl.classList.add('active');
  window.scrollTo(0, 0);
  if (page === 'admin' && adminLoggedIn) loadAdminData();
  if (page === 'attendance') resetSignIn();
}

function toggleMobile() {
  document.getElementById('mobileMenu').classList.toggle('open');
}

// ── EVENTS ───────────────────────────────────────────────────
function renderEventStrip() {
  const upcoming = allEvents.filter(e => e.event_date >= today()).slice(0, 4);
  const strip = document.getElementById('stripEvents');
  if (!upcoming.length) { strip.innerHTML = '<span class="event-pill">No upcoming events</span>'; return; }
  strip.innerHTML = upcoming.map(e =>
    `<span class="event-pill"><strong>${e.name}</strong> · ${fmtDate(e.event_date)}</span>`
  ).join('');
}

function renderHomeEvents() {
  const upcoming = allEvents.filter(e => e.event_date >= today());
  const el = document.getElementById('homeEvents');
  if (!upcoming.length) { el.innerHTML = '<p style="color:rgba(255,255,255,.5);text-align:center;grid-column:1/-1">No upcoming events scheduled.</p>'; return; }
  el.innerHTML = upcoming.map(e => `
    <div class="ev-card">
      <div class="ev-type-badge">${e.event_type}</div>
      <div class="ev-name">${e.name}</div>
      <div class="ev-meta">
        <span>📅 ${fmtDate(e.event_date)}${e.event_time ? ' at ' + fmtTime(e.event_time) : ''}</span>
        ${e.venue ? `<span>📍 ${e.venue}</span>` : ''}
        ${e.description ? `<span style="color:rgba(255,255,255,.55)">${e.description}</span>` : ''}
      </div>
    </div>`).join('');
}

// ── REGISTRATION – MULTI-WARD ────────────────────────────────
let wardCount = 1;

function changeWardCount(delta) {
  wardCount = Math.max(1, Math.min(10, wardCount + delta));
  document.getElementById('wardCountDisplay').textContent = wardCount;
  renderWardForms();
}

function renderWardForms() {
  const container = document.getElementById('wardFormsContainer');
  // preserve any values already typed
  const existing = [];
  document.querySelectorAll('.ward-block').forEach((blk, i) => {
    existing[i] = {
      first:  blk.querySelector(`[data-field="first"]`)?.value  || '',
      last:   blk.querySelector(`[data-field="last"]`)?.value   || '',
      cls:    blk.querySelector(`[data-field="class"]`)?.value  || '',
      idno:   blk.querySelector(`[data-field="idno"]`)?.value   || '',
      dob:    blk.querySelector(`[data-field="dob"]`)?.value    || '',
      gender: blk.querySelector(`[data-field="gender"]`)?.value || '',
    };
  });

  container.innerHTML = '';
  for (let i = 0; i < wardCount; i++) {
    const prev = existing[i] || {};
    const label = wardCount === 1 ? 'Ward Details' : `Ward ${i + 1} of ${wardCount}`;
    container.insertAdjacentHTML('beforeend', `
      <div class="ward-block" id="ward-block-${i}">
        <div class="ward-block-header">
          <div class="ward-block-title">
            <div class="ward-block-num">${i + 1}</div>
            ${label}
          </div>
          ${wardCount > 1 ? `<button type="button" onclick="removeWard(${i})" style="background:none;border:none;color:var(--danger);font-size:18px;cursor:pointer;line-height:1" title="Remove this ward">✕</button>` : ''}
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>First Name <span class="req">*</span></label>
            <input type="text" data-ward="${i}" data-field="first" placeholder="e.g. Ama" value="${prev.first}"/>
          </div>
          <div class="form-group">
            <label>Last Name <span class="req">*</span></label>
            <input type="text" data-ward="${i}" data-field="last" placeholder="e.g. Mensah" value="${prev.last}"/>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Class <span class="req">*</span></label>
            <input type="text" data-ward="${i}" data-field="class" placeholder="e.g. JHS 2B" value="${prev.cls}"/>
          </div>
          <div class="form-group">
            <label>Student ID No.</label>
            <input type="text" data-ward="${i}" data-field="idno" placeholder="Optional" value="${prev.idno}"/>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" data-ward="${i}" data-field="dob" value="${prev.dob}"/>
          </div>
          <div class="form-group">
            <label>Gender</label>
            <select data-ward="${i}" data-field="gender">
              <option value="">— Select —</option>
              <option ${prev.gender==='Male'?'selected':''}>Male</option>
              <option ${prev.gender==='Female'?'selected':''}>Female</option>
              <option ${prev.gender==='Other'?'selected':''}>Other</option>
            </select>
          </div>
        </div>
        <div class="form-row single">
          <div class="form-group">
            <label>Student Photo (optional)</label>
            <label class="photo-upload-label" for="ward_photo_${i}">
              <img id="ward_photo_preview_${i}" class="photo-preview" alt=""/>
              <span>📷 Click to upload photo</span>
              <span style="font-size:11px">JPG / PNG / WEBP · max 5 MB</span>
            </label>
            <input type="file" id="ward_photo_${i}" accept="image/*" style="display:none"
              onchange="previewPhoto(this,'ward_photo_preview_${i}')"/>
          </div>
        </div>
      </div>`);
  }
}

function removeWard(index) {
  // collect current values, remove that ward, re-render
  const blocks = document.querySelectorAll('.ward-block');
  const all = [];
  blocks.forEach((blk, i) => {
    if (i !== index) all.push({
      first:  blk.querySelector(`[data-field="first"]`)?.value  || '',
      last:   blk.querySelector(`[data-field="last"]`)?.value   || '',
      cls:    blk.querySelector(`[data-field="class"]`)?.value  || '',
      idno:   blk.querySelector(`[data-field="idno"]`)?.value   || '',
      dob:    blk.querySelector(`[data-field="dob"]`)?.value    || '',
      gender: blk.querySelector(`[data-field="gender"]`)?.value || '',
    });
  });
  wardCount = Math.max(1, wardCount - 1);
  document.getElementById('wardCountDisplay').textContent = wardCount;
  // re-render with preserved values
  const container = document.getElementById('wardFormsContainer');
  container.innerHTML = '';
  for (let i = 0; i < wardCount; i++) {
    const prev = all[i] || {};
    const label = wardCount === 1 ? 'Ward Details' : `Ward ${i + 1} of ${wardCount}`;
    container.insertAdjacentHTML('beforeend', `
      <div class="ward-block" id="ward-block-${i}">
        <div class="ward-block-header">
          <div class="ward-block-title"><div class="ward-block-num">${i+1}</div>${label}</div>
          ${wardCount > 1 ? `<button type="button" onclick="removeWard(${i})" style="background:none;border:none;color:var(--danger);font-size:18px;cursor:pointer;line-height:1">✕</button>` : ''}
        </div>
        <div class="form-row">
          <div class="form-group"><label>First Name <span class="req">*</span></label>
            <input type="text" data-ward="${i}" data-field="first" placeholder="e.g. Ama" value="${prev.first}"/></div>
          <div class="form-group"><label>Last Name <span class="req">*</span></label>
            <input type="text" data-ward="${i}" data-field="last" placeholder="e.g. Mensah" value="${prev.last}"/></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Class <span class="req">*</span></label>
            <input type="text" data-ward="${i}" data-field="class" placeholder="e.g. JHS 2B" value="${prev.cls}"/></div>
          <div class="form-group"><label>Student ID No.</label>
            <input type="text" data-ward="${i}" data-field="idno" placeholder="Optional" value="${prev.idno}"/></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Date of Birth</label>
            <input type="date" data-ward="${i}" data-field="dob" value="${prev.dob}"/></div>
          <div class="form-group"><label>Gender</label>
            <select data-ward="${i}" data-field="gender">
              <option value="">— Select —</option>
              <option ${prev.gender==='Male'?'selected':''}>Male</option>
              <option ${prev.gender==='Female'?'selected':''}>Female</option>
              <option ${prev.gender==='Other'?'selected':''}>Other</option>
            </select></div>
        </div>
        <div class="form-row single">
          <div class="form-group"><label>Student Photo (optional)</label>
            <label class="photo-upload-label" for="ward_photo_${i}">
              <img id="ward_photo_preview_${i}" class="photo-preview" alt=""/>
              <span>📷 Click to upload photo</span>
              <span style="font-size:11px">JPG / PNG / WEBP · max 5 MB</span>
            </label>
            <input type="file" id="ward_photo_${i}" accept="image/*" style="display:none"
              onchange="previewPhoto(this,'ward_photo_preview_${i}')"/>
          </div>
        </div>
      </div>`);
  }
}

function submitRegistration() {
  const pfirst = val('reg_pfirst');
  const plast  = val('reg_plast');
  const phone  = val('reg_phone');
  if (!pfirst || !plast || !phone) {
    showToast('Please fill in all parent fields.', 'error'); return;
  }

  // collect ward data
  const wards = [];
  for (let i = 0; i < wardCount; i++) {
    const first = document.querySelector(`[data-ward="${i}"][data-field="first"]`)?.value?.trim() || '';
    const last  = document.querySelector(`[data-ward="${i}"][data-field="last"]`)?.value?.trim()  || '';
    const cls   = document.querySelector(`[data-ward="${i}"][data-field="class"]`)?.value?.trim() || '';
    if (!first || !last || !cls) {
      showToast(`Please complete all required fields for Ward ${i+1}.`, 'error'); return;
    }
    wards.push({
      first, last, cls,
      dob:    document.querySelector(`[data-ward="${i}"][data-field="dob"]`)?.value    || '',
      gender: document.querySelector(`[data-ward="${i}"][data-field="gender"]`)?.value || '',
      idno:   document.querySelector(`[data-ward="${i}"][data-field="idno"]`)?.value   || '',
      photo:  document.getElementById(`ward_photo_${i}`)?.files[0] || null,
    });
  }

  const fd = new FormData();
  fd.append('action',       'register_parent');
  fd.append('first_name',   pfirst);
  fd.append('last_name',    plast);
  fd.append('phone',        phone);
  fd.append('email',        val('reg_email'));
  fd.append('address',      val('reg_address'));
  fd.append('relationship', val('reg_rel'));
  fd.append('ward_count',   wardCount);

  wards.forEach((w, i) => {
    fd.append(`students[${i}][first]`,  w.first);
    fd.append(`students[${i}][last]`,   w.last);
    fd.append(`students[${i}][class]`,  w.cls);
    fd.append(`students[${i}][dob]`,    w.dob);
    fd.append(`students[${i}][gender]`, w.gender);
    fd.append(`students[${i}][idno]`,   w.idno);
    if (w.photo) fd.append(`student_photo_${i}`, w.photo);
  });

  const pFile = document.getElementById('reg_pphoto').files[0];
  if (pFile) fd.append('parent_photo', pFile);

  apiPost('actions/insert.php', fd).then(r => {
    if (r.success) {
      const msg = wardCount > 1
        ? `Registration successful! ${wardCount} wards registered. 🎉`
        : 'Registration successful! 🎉';
      showToast(msg, 'success');
      clearRegForm();
      loadPublicData();
    } else {
      showToast(r.message, 'error');
    }
  });
}

function clearRegForm() {
  ['reg_pfirst','reg_plast','reg_phone','reg_email','reg_address'].forEach(id => {
    const el = document.getElementById(id); if (el) el.value = '';
  });
  document.getElementById('reg_rel').value = 'Guardian';
  const pp = document.getElementById('reg_pphoto_preview');
  if (pp) { pp.style.display = 'none'; pp.src = ''; }
  wardCount = 1;
  document.getElementById('wardCountDisplay').textContent = '1';
  renderWardForms();
}

function previewPhoto(input, previewId) {
  const prev = document.getElementById(previewId);
  if (!prev) return;
  const file = input.files[0];
  if (!file) { prev.style.display = 'none'; return; }
  const reader = new FileReader();
  reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
  reader.readAsDataURL(file);
}

// ── ATTENDANCE SELF-SIGN ──────────────────────────────────────
function lookupParent() {
  const phone = val('sign_phone');
  if (!phone) { showToast('Please enter your phone number.', 'error'); return; }

  apiFetch(`actions/fetch.php?action=parent_by_phone&phone=${encodeURIComponent(phone)}`).then(r => {
    if (!r.success) { showToast(r.message, 'error'); return; }
    foundParent = r.data;
    // Build found-parent box
    const av = foundParent.photo_path
      ? `<div class="big-av"><img src="${foundParent.photo_path}" alt=""/></div>`
      : `<div class="big-av" style="background:var(--navy-light)">${initials(foundParent.first_name, foundParent.last_name)}</div>`;
    document.getElementById('foundParentBox').innerHTML = `
      ${av}
      <div>
        <div style="font-weight:600">${foundParent.first_name} ${foundParent.last_name}</div>
        <div style="font-size:13px;color:var(--text-muted)">${foundParent.relationship} of ${foundParent.s_first||''} ${foundParent.s_last||''} (${foundParent.student_class||''})</div>
      </div>`;
    showSignStep('signStep2');
  });
}

function submitAttendance() {
  if (!foundParent) return;
  const eventId   = val('sign_event');
  const visitType = val('sign_vtype');
  const notes     = val('sign_notes');
  if (!eventId) { showToast('Please select an event.', 'error'); return; }

  const fd = new FormData();
  fd.append('action',     'sign_attendance');
  fd.append('event_id',   eventId);
  fd.append('parent_id',  foundParent.id);
  fd.append('visit_type', visitType);
  fd.append('notes',      notes);

  apiPost('actions/insert.php', fd).then(r => {
    if (r.success) {
      const evName = allEvents.find(e => e.id == eventId)?.name || 'the event';
      document.getElementById('signedInMsg').textContent =
        `${foundParent.first_name} ${foundParent.last_name} has been signed in for "${evName}".`;
      showSignStep('signStep3');
    } else {
      showToast(r.message, 'error');
    }
  });
}

function resetSignIn() {
  foundParent = null;
  document.getElementById('sign_phone').value = '';
  document.getElementById('sign_notes').value = '';
  showSignStep('signStep1');
}

function showSignStep(id) {
  document.querySelectorAll('.sign-step').forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

// ── ADMIN ─────────────────────────────────────────────────────
function requireAdmin() {
  if (adminLoggedIn) { showPage('admin'); }
  else { document.getElementById('adminLoginOverlay').style.display = 'flex'; }
}

function doLogin() {
  const fd = new FormData();
  fd.append('action',   'admin_login');
  fd.append('username', val('loginUser'));
  fd.append('password', val('loginPass'));
  apiPost('actions/insert.php', fd).then(r => {
    if (r.success) {
      adminLoggedIn = true;
      document.getElementById('adminLoginOverlay').style.display = 'none';
      document.getElementById('adminNameLabel').textContent = r.name;
      showPage('admin');
    } else {
      showToast(r.message, 'error');
    }
  });
}

function adminLogout() {
  const fd = new FormData(); fd.append('action', 'admin_logout');
  apiPost('actions/insert.php', fd).then(() => {
    adminLoggedIn = false;
    showToast('Logged out.');
    showPage('home');
  });
}

function adminTab(tab) {
  document.querySelectorAll('.admin-sub').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
  document.getElementById('admin-' + tab).classList.add('active');
  const sb = document.getElementById('sb-' + tab);
  if (sb) sb.classList.add('active');
  if (tab === 'parents') loadAdminParents();
  if (tab === 'events')  loadAdminEvents();
  if (tab === 'checkin') loadAdminCheckin();
}

function loadAdminData() {
  // Stats
  apiFetch('actions/fetch.php?action=stats').then(r => {
    if (!r.success) return;
    setText('a-totalParents',  r.data.totalParents);
    setText('a-totalStudents', r.data.totalStudents);
    setText('a-totalEvents',   r.data.totalEvents);
    setText('a-upcoming',      r.data.upcomingCount);
  });
  // Events preview
  apiFetch('actions/fetch.php?action=events').then(r => {
    if (!r.success) return;
    allEvents = r.data;
    renderAdminEventsPreview();
  });
}

// PARENTS TABLE
function loadAdminParents() {
  apiFetch('actions/fetch.php?action=parents').then(r => {
    if (!r.success) return;
    allParents = r.data;
    buildClassFilter();
    renderParentsTable();
  });
}

function buildClassFilter() {
  const classes = [...new Set(allParents.map(p => p.student_class).filter(Boolean))];
  const sel = document.getElementById('filterClass');
  const cur = sel.value;
  sel.innerHTML = '<option value="">All Classes</option>' + classes.map(c =>
    `<option ${c === cur ? 'selected' : ''}>${c}</option>`).join('');
}

function renderParentsTable() {
  const q   = (document.getElementById('searchParents')?.value || '').toLowerCase();
  const cls = document.getElementById('filterClass')?.value || '';
  const rel = document.getElementById('filterRel')?.value   || '';
  const filtered = allParents.filter(p =>
    (!q   || `${p.first_name} ${p.last_name} ${p.phone}`.toLowerCase().includes(q)) &&
    (!cls || p.student_class === cls) &&
    (!rel || p.relationship  === rel)
  );
  const tbody = document.getElementById('parentsTableBody');
  if (!filtered.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">No records found.</td></tr>`;
    return;
  }
  tbody.innerHTML = filtered.map(p => {
    const av = p.photo_path
      ? `<div class="avatar-sm"><img src="${p.photo_path}" alt=""/></div>`
      : `<div class="avatar-sm">${initials(p.first_name, p.last_name)}</div>`;
    const wardCount = p.wards?.length || 0;
    const wardLabel = wardCount > 1
      ? `<span style="font-size:11px;background:rgba(212,153,58,.15);color:var(--gold);padding:1px 7px;border-radius:10px;font-weight:600;margin-left:6px">${wardCount} wards</span>`
      : (p.s_first ? `${p.s_first} ${p.s_last}` : '—');
    return `<tr>
      <td><div class="parent-cell">${av}<span>${p.first_name} ${p.last_name}</span></div></td>
      <td>${p.phone}</td>
      <td><span class="badge badge-${p.relationship.toLowerCase()}">${p.relationship}</span></td>
      <td>${wardCount > 1 ? wardLabel : (p.s_first ? `${p.s_first} ${p.s_last}` : '—')}</td>
      <td>${wardCount > 1 ? p.wards.map(w=>w.student_class).join(', ') : (p.student_class||'—')}</td>
      <td>${fmtDate(p.registered_at?.split(' ')[0])}</td>
      <td><button class="btn-outline" style="font-size:12px;padding:5px 12px;color:var(--navy);border-color:var(--border)" onclick="viewParent(${JSON.stringify(p).replace(/"/g,'&quot;')})">View</button></td>
    </tr>`;
  }).join('');
}

function viewParent(p) {
  const wardsHtml = (p.wards && p.wards.length > 0)
    ? p.wards.map((w, i) => `
        <div style="background:var(--cream);border-radius:8px;padding:.75rem 1rem;margin-bottom:.5rem;display:flex;align-items:center;gap:10px">
          ${w.photo_path ? `<img src="${w.photo_path}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--gold)"/>` : `<div style="width:36px;height:36px;border-radius:50%;background:var(--navy-light);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">${(w.first_name[0]||'')+(w.last_name[0]||'')}</div>`}
          <div>
            <div style="font-weight:600;font-size:.9rem">${w.first_name} ${w.last_name}</div>
            <div style="font-size:12px;color:var(--text-muted)">${w.student_class}${w.student_id_no ? ' · ID: '+w.student_id_no : ''}</div>
          </div>
          ${p.wards.length > 1 ? `<span style="margin-left:auto;font-size:11px;color:var(--text-muted)">Ward ${i+1}</span>` : ''}
        </div>`)
      .join('')
    : '<p style="color:var(--text-muted);font-size:13px">No wards recorded.</p>';

  document.getElementById('parentDetailContent').innerHTML = `
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:1rem">
      ${p.photo_path ? `<img src="${p.photo_path}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--gold)"/>` : `<div style="width:64px;height:64px;border-radius:50%;background:var(--navy-light);color:white;display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:700">${initials(p.first_name, p.last_name)}</div>`}
      <div>
        <strong style="font-size:1.05rem">${p.first_name} ${p.last_name}</strong><br/>
        <span style="font-size:13px;color:var(--text-muted)">${p.relationship}</span>
        ${p.wards && p.wards.length > 1 ? `<span style="margin-left:8px;background:rgba(212,153,58,.15);color:var(--gold);font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px">${p.wards.length} wards</span>` : ''}
      </div>
    </div>
    <dl class="parent-detail-grid">
      <dt>Phone</dt><dd>${p.phone}</dd>
      <dt>Email</dt><dd>${p.email||'—'}</dd>
      <dt>Address</dt><dd>${p.address||'—'}</dd>
      <dt>Registered</dt><dd>${fmtDate(p.registered_at?.split(' ')[0])}</dd>
    </dl>
    <div style="margin-top:1rem">
      <div style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text-muted);letter-spacing:.07em;margin-bottom:.6rem">
        Wards in School (${p.wards?.length || 0})
      </div>
      ${wardsHtml}
    </div>`;
  openModal('parentDetailModal');
}

function exportCSV() {
  if (!allParents.length) { showToast('No data to export.'); return; }
  const h = 'First Name,Last Name,Phone,Email,Relationship,Student,Class,Registered\n';
  const r = allParents.map(p =>
    `"${p.first_name}","${p.last_name}","${p.phone}","${p.email||''}","${p.relationship}","${p.s_first||''} ${p.s_last||''}","${p.student_class||''}","${p.registered_at||''}"`
  ).join('\n');
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([h + r], {type:'text/csv'}));
  a.download = 'parents.csv'; a.click();
  showToast('CSV downloaded!', 'success');
}

// ADMIN EVENTS
function loadAdminEvents() {
  apiFetch('actions/fetch.php?action=events').then(r => {
    if (!r.success) return;
    allEvents = r.data;
    const el = document.getElementById('adminEventCards');
    if (!allEvents.length) { el.innerHTML = '<p style="color:var(--text-muted)">No events yet.</p>'; return; }
    el.innerHTML = allEvents.map(e => `
      <div class="ev-card">
        <div class="ev-type-badge">${e.event_type}</div>
        <div class="ev-name">${e.name}</div>
        <div class="ev-meta">
          <span>📅 ${fmtDate(e.event_date)}${e.event_time ? ' at '+fmtTime(e.event_time):''}</span>
          <span>📍 ${e.venue||'TBD'}</span>
          ${e.description ? `<span style="color:var(--text-muted)">${e.description}</span>`:''}
        </div>
      </div>`).join('');
  });
}

function renderAdminEventsPreview() {
  const upcoming = allEvents.filter(e => e.event_date >= today()).slice(0,4);
  const el = document.getElementById('adminEventsPreview');
  if (!upcoming.length) { el.innerHTML = '<p style="color:var(--text-muted)">No upcoming events.</p>'; return; }
  el.innerHTML = upcoming.map(e => `
    <div class="ev-card">
      <div class="ev-type-badge">${e.event_type}</div>
      <div class="ev-name">${e.name}</div>
      <div class="ev-meta">
        <span>📅 ${fmtDate(e.event_date)}</span>
        <span>📍 ${e.venue||'TBD'}</span>
      </div>
    </div>`).join('');
}

function openAddEvent() {
  document.getElementById('evDate').value = new Date().toISOString().split('T')[0];
  document.getElementById('evName').value  = '';
  document.getElementById('evVenue').value = '';
  document.getElementById('evDesc').value  = '';
  document.getElementById('evType').value  = 'PTA Meeting';
  document.getElementById('evTime').value  = '10:00';
  openModal('addEventModal');
}

function saveEvent() {
  const fd = new FormData();
  fd.append('action',      'create_event');
  fd.append('name',        val('evName'));
  fd.append('event_type',  val('evType'));
  fd.append('event_date',  val('evDate'));
  fd.append('event_time',  val('evTime'));
  fd.append('venue',       val('evVenue'));
  fd.append('description', val('evDesc'));
  if (!val('evName') || !val('evDate')) { showToast('Name and date required.','error'); return; }
  apiPost('actions/insert.php', fd).then(r => {
    if (r.success) {
      closeModal('addEventModal');
      showToast('Event created!', 'success');
      loadAdminData();
      loadAdminEvents();
      apiFetch('actions/fetch.php?action=events').then(r2 => {
        if (r2.success) { allEvents = r2.data; renderEventStrip(); renderHomeEvents(); }
      });
    } else { showToast(r.message, 'error'); }
  });
}

// ADMIN CHECKIN
function loadAdminCheckin() {
  apiFetch('actions/fetch.php?action=events').then(r => {
    if (!r.success) return;
    allEvents = r.data;
    const sel = document.getElementById('adminEventSelect');
    sel.innerHTML = allEvents.map(e => `<option value="${e.id}">${e.name} (${fmtDate(e.event_date)})</option>`).join('');
    if (allEvents.length) {
      adminEvtId = allEvents[0].id;
      renderAdminEventList();
      loadAdminAttendance(adminEvtId);
    }
  });
}

function renderAdminEventList() {
  const el = document.getElementById('adminEventList');
  el.innerHTML = allEvents.map(e => `
    <div class="event-list-item ${e.id == adminEvtId ? 'active':''}" onclick="loadAdminAttendance(${e.id})">
      <div class="ev-n">${e.name}</div>
      <div class="ev-d">${fmtDate(e.event_date)}</div>
      <div class="ev-progress"><div class="ev-fill" id="fill-${e.id}" style="width:0%"></div></div>
    </div>`).join('');
}

function loadAdminAttendance(evtId) {
  adminEvtId = parseInt(evtId);
  renderAdminEventList();
  document.getElementById('adminEventSelect').value = adminEvtId;
  apiFetch(`actions/fetch.php?action=attendance&event_id=${adminEvtId}`).then(r => {
    setText('adminAttCount', r.data?.length || 0);
    const el = document.getElementById('adminCheckinList');
    if (!r.success || !r.data.length) {
      el.innerHTML = '<p style="color:var(--text-muted);padding:1rem">No one has signed in for this event yet.</p>';
      return;
    }
    el.innerHTML = r.data.map(a => {
      const av = a.photo_path
        ? `<div class="avatar-sm"><img src="${a.photo_path}" alt=""/></div>`
        : `<div class="avatar-sm">${initials(a.first_name, a.last_name)}</div>`;
      return `<div class="checkin-item">
        ${av}
        <div class="info">
          <div class="name">${a.first_name} ${a.last_name}</div>
          <div class="sub">${a.s_first||''} ${a.s_last||''} · ${a.student_class||''} · ${a.visit_type}</div>
          <div class="sub" style="font-size:11px">${fmtDatetime(a.signed_at)}</div>
        </div>
        <span class="badge badge-guardian">✓</span>
      </div>`;
    }).join('');
  });
}

function exportAttendance() {
  if (!adminEvtId) return;
  apiFetch(`actions/fetch.php?action=attendance&event_id=${adminEvtId}`).then(r => {
    if (!r.success || !r.data.length) { showToast('No data to export.'); return; }
    const h = 'Parent,Phone,Student,Class,Visit Type,Signed At\n';
    const rows = r.data.map(a =>
      `"${a.first_name} ${a.last_name}","${a.phone}","${a.s_first||''} ${a.s_last||''}","${a.student_class||''}","${a.visit_type}","${a.signed_at}"`
    ).join('\n');
    const ev = allEvents.find(e => e.id == adminEvtId);
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([h+rows],{type:'text/csv'}));
    a.download = `attendance-${(ev?.name||'event').replace(/\s+/g,'-')}.csv`;
    a.click();
    showToast('Exported!', 'success');
  });
}

// ── QR ───────────────────────────────────────────────────────
function openQR() {
  const url = window.location.origin + window.location.pathname + '?page=attendance';
  document.getElementById('qrUrl').textContent = url;
  openModal('qrModal');
  setTimeout(() => drawQR(url), 80);
}

function drawQR(url) {
  const t = document.getElementById('qrTarget');
  t.innerHTML = '';
  if (typeof QRCode !== 'undefined') {
    new QRCode(t, {text:url,width:200,height:200,colorDark:'#0B1F3A',colorLight:'#FFFFFF',correctLevel:QRCode.CorrectLevel.M});
  } else {
    t.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:2rem">QR library loading…</p>';
  }
}

function downloadQR() {
  const t = document.getElementById('qrTarget');
  const canvas = t.querySelector('canvas');
  const img    = t.querySelector('img');
  const src    = canvas ? canvas.toDataURL('image/png') : img?.src;
  if (!src) { showToast('QR not ready yet.'); return; }
  const a = document.createElement('a');
  a.download = 'schoolconnect-attendance-qr.png';
  a.href = src; a.click();
  showToast('QR downloaded!', 'success');
}

// ── MODAL UTILS ───────────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
function closeModalOnBackdrop(e, id) { if (e.target===document.getElementById(id)) closeModal(id); }

// ── API HELPERS ───────────────────────────────────────────────
async function apiFetch(url) {
  try { return await (await fetch(url)).json(); }
  catch(e) { showToast('Network error.','error'); return {success:false,message:'Network error'}; }
}

async function apiPost(url, fd) {
  try { return await (await fetch(url, {method:'POST',body:fd})).json(); }
  catch(e) { showToast('Network error.','error'); return {success:false,message:'Network error'}; }
}

// ── UTILS ─────────────────────────────────────────────────────
function val(id)       { return document.getElementById(id)?.value?.trim() || ''; }
function setText(id,v) { const el=document.getElementById(id); if(el) el.textContent=v; }
function initials(f,l) { return ((f||'')[0]||'').toUpperCase() + ((l||'')[0]||'').toUpperCase(); }
function today()       { return new Date().toISOString().split('T')[0]; }

function fmtDate(d) {
  if (!d) return '—';
  const dt = new Date(d + (d.includes('T')?'':'T00:00:00'));
  return dt.toLocaleDateString('en-GB', {day:'numeric',month:'short',year:'numeric'});
}
function fmtTime(t) {
  if (!t) return '';
  const [h,m] = t.split(':');
  const hour = parseInt(h);
  return `${hour%12||12}:${m} ${hour>=12?'PM':'AM'}`;
}
function fmtDatetime(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleString('en-GB', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
}

function populateEventDropdown(id, events) {
  const sel = document.getElementById(id);
  if (!sel) return;
  const upcoming = events.filter(e => e.event_date >= today());
  sel.innerHTML = upcoming.length
    ? upcoming.map(e => `<option value="${e.id}">${e.name} (${fmtDate(e.event_date)})</option>`).join('')
    : '<option value="">No upcoming events</option>';
}

function showToast(msg, type='') {
  const t = document.getElementById('toast');
  t.className = 'toast' + (type ? ' ' + (type==='error'?'error':'success-toast') : '');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
</script>
</body>
</html>
