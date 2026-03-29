<?php
session_start();


require_once ('DBconnect/connection.php');

$errorMessage = "";
if (!isset($_SESSION['userID'])) {
    $errorMessage = "Unauthorized Access! Please log in.";
}

// Fetch Girls candidates grouped by position
$girlsResult = mysqli_query($connect, "SELECT * FROM candidatedetails WHERE gender IN ('F', 'female', 'girl', 'Female', 'Girl') AND LOWER(TRIM(position)) != 'head prefect' ORDER BY position, candidateid");

$positions = [];
while ($row = mysqli_fetch_assoc($girlsResult)) {
    $position = $row['position'];
    if (!isset($positions[$position])) $positions[$position] = [];
    $positions[$position][] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Girls Voting Page | Sonrise Christian High School</title>
<link rel="icon" href="img/sonrise-favicon.png" type="image/jpg">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="styles/boys.css">
<style>
.candidate-image {
  width: 100%; height: 350px !important; background-color: #f8f9fa;
  overflow: hidden; position: relative; border-bottom: 3px solid #e0e0e0;
}
.candidate-image img {
  width: 100%; height: 100%; object-fit: contain !important;
  object-position: center; padding: 10px;
}
.candidate-container {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px; margin-bottom: 20px;
}
.candidate-card {
  background: white; border-radius: 12px; border: 2px solid #e0e0e0;
  overflow: hidden; transition: all 0.3s ease; position: relative;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column;
}
.candidate-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.15); border-color: #8A2BE2; }
.candidate-card.selected { border: 3px solid #8A2BE2; box-shadow: 0 0 20px rgba(138,43,226,0.4); transform: translateY(-5px); }
.candidate-card.yes-selected { border: 3px solid #28a745; box-shadow: 0 0 20px rgba(40,167,69,0.4); transform: translateY(-5px); }
.candidate-card.no-selected { border: 3px solid #dc3545; box-shadow: 0 0 20px rgba(220,53,69,0.4); transform: translateY(-5px); }
.candidate-info { padding: 20px; text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
.candidate-info h3 { margin: 0 0 10px 0; color: #333; font-size: 1.3rem; font-weight: 600; }
.candidate-gender { color: #1976d2; margin-bottom: 15px; font-size: 1rem; font-weight: 500; }
.candidate-gender i { font-size: 1.2rem; margin-right: 5px; }
.select-btn {
  display: block; width: 100%; background: linear-gradient(135deg, #e3f2fd, #bbdefb);
  color: #1976d2; border: 2px solid #1976d2; padding: 12px; border-radius: 8px;
  cursor: pointer; transition: all 0.3s; font-weight: 600; font-size: 1rem; margin-top: 10px;
}
.select-btn:hover { background: linear-gradient(135deg, #bbdefb, #90caf9); transform: scale(1.02); }
.custom-radio:checked + .select-btn { background: linear-gradient(135deg, #7209b7, #8A2BE2); color: white; border-color: #7209b7; }
.custom-radio:checked + .select-btn:after { content: " ✓"; font-size: 1.2rem; }
.yes-no-group { display: flex; gap: 10px; margin-top: 10px; }
.yes-btn, .no-btn { flex: 1; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 1rem; border: 2px solid; transition: all 0.3s; }
.yes-btn { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); color: #2e7d32; border-color: #2e7d32; }
.yes-btn:hover { background: linear-gradient(135deg, #c8e6c9, #a5d6a7); transform: scale(1.03); }
.no-btn { background: linear-gradient(135deg, #ffebee, #ffcdd2); color: #c62828; border-color: #c62828; }
.no-btn:hover { background: linear-gradient(135deg, #ffcdd2, #ef9a9a); transform: scale(1.03); }
.yes-no-radio { display: none; }
.yes-no-radio.yes:checked + .yes-btn { background: linear-gradient(135deg, #2e7d32, #388e3c); color: white; border-color: #1b5e20; }
.yes-no-radio.yes:checked + .yes-btn:after { content: " ✓"; }
.yes-no-radio.no:checked + .no-btn { background: linear-gradient(135deg, #c62828, #d32f2f); color: white; border-color: #b71c1c; }
.yes-no-radio.no:checked + .no-btn:after { content: " ✓"; }
.single-candidate-badge {
  display: inline-block; background: #fff3e0; color: #e65100;
  border: 1px solid #ffcc80; padding: 3px 10px; border-radius: 12px;
  font-size: 0.78rem; font-weight: 600; margin-bottom: 10px;
}
@media screen and (max-width: 992px) { .candidate-container { grid-template-columns: repeat(2, 1fr); } .candidate-image { height: 300px !important; } .main { padding: 20px; } }
@media screen and (max-width: 768px) {
  .sidenav { width: 70px; } .sidenav a { padding: 15px 10px; text-align: center; }
  .sidenav a i { margin-right: 0; font-size: 20px; } .sidenav a span, .brand-section h4 { display: none; }
  .main { margin-left: 70px; padding: 15px; } .footer { left: 70px; width: calc(100% - 70px); padding: 10px; }
  .toggle-btn { left: 70px; } .header h1 { font-size: 1.8rem; }
  .candidate-container { grid-template-columns: 1fr; } .candidate-image { height: 350px !important; }
}
@media screen and (max-width: 576px) { .header { padding: 20px 15px; } .main { padding: 10px; } .voting-section { padding: 15px; } .candidate-image { height: 320px !important; } }
@media screen and (max-width: 400px) { .sidenav { width: 60px; } .main { margin-left: 60px; } .footer { left: 60px; width: calc(100% - 60px); } .toggle-btn { left: 60px; width: 35px; height: 35px; } .candidate-image { height: 280px !important; } }
@media print { .sidenav, .toggle-btn, .footer, .vote-btn { display: none; } .main { margin-left: 0; padding: 20px; } .candidate-card { break-inside: avoid; } }
</style>
</head>
<body>
<?php if ($errorMessage): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Authentication Error</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="text-center mb-4"><i class="fas fa-user-lock text-danger" style="font-size:64px;"></i></div>
            <p class="text-center"><?php echo $errorMessage; ?></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="window.location.href='index.php';"><i class="fas fa-sign-in-alt mr-2"></i>Go to Login</button>
          </div>
        </div>
      </div>
    </div>
    <script>$(document).ready(function(){ $('#errorModal').modal({backdrop:'static',keyboard:false}); $('#errorModal').modal('show'); });</script>
<?php else: ?>
<button class="toggle-btn" id="toggleSidebar"><i class="fas fa-bars"></i></button>
<div class="sidenav">
  <div class="brand-section">
    <img src="img/sonrise-favicon.png" alt="Sonrise Logo" onerror="this.src='https://via.placeholder.com/80'">
    <h4>Girls Voting</h4>
  </div>
</div>

<div class="main">
  <div class="header">
    <h1>Sonrise Christian High School</h1>
    <p>Girls' Electronic Voting System</p>
  </div>

  <?php if (empty($positions)): ?>
    <div class="alert alert-info text-center"><i class="fas fa-info-circle mr-2"></i><strong>No female candidates available at this time.</strong></div>
  <?php else: ?>
    <form action="submit_girls_vote.php" method="POST" id="votingForm">
      <input type="hidden" name="gender" value="F">

      <?php foreach ($positions as $position => $candidates):
        $isSingle = (count($candidates) === 1);
      ?>
        <div class="voting-section">
          <div class="position-heading">
            <i class="fas fa-award"></i>
            <?php echo strtoupper(htmlspecialchars($position)); ?>
          </div>
          <div class="candidate-container">
            <?php foreach ($candidates as $candidate): ?>
              <div class="candidate-card" data-position="<?php echo htmlspecialchars($position); ?>">
                <div class="candidate-image">
                  <img src="uploads/<?php echo htmlspecialchars($candidate['image']); ?>"
                       alt="<?php echo htmlspecialchars($candidate['fname']); ?>"
                       onerror="this.src='https://via.placeholder.com/300x350?text=No+Image'">
                </div>
                <div class="candidate-info">
                  <div>
                    <h3><?php echo htmlspecialchars($candidate['fname']); ?></h3>
                    <p class="candidate-gender"><i class="fas fa-venus"></i> Female Candidate</p>
                    <?php if ($isSingle): ?>
                      <span class="single-candidate-badge"><i class="fas fa-user"></i> Sole Candidate — Vote Yes or No</span>
                    <?php endif; ?>
                  </div>

                  <?php if ($isSingle): ?>
                    <div>
                      <input type="hidden" name="candidate[<?php echo htmlspecialchars($position); ?>]" value="<?php echo $candidate['candidateid']; ?>">
                      <div class="yes-no-group">
                        <input type="radio" class="yes-no-radio yes"
                               name="yesno[<?php echo htmlspecialchars($position); ?>]"
                               value="1"
                               id="yes_<?php echo $candidate['candidateid']; ?>">
                        <label for="yes_<?php echo $candidate['candidateid']; ?>" class="yes-btn">
                          <i class="fas fa-thumbs-up"></i> YES
                        </label>
                        <input type="radio" class="yes-no-radio no"
                               name="yesno[<?php echo htmlspecialchars($position); ?>]"
                               value="0"
                               id="no_<?php echo $candidate['candidateid']; ?>">
                        <label for="no_<?php echo $candidate['candidateid']; ?>" class="no-btn">
                          <i class="fas fa-thumbs-down"></i> NO
                        </label>
                      </div>
                    </div>
                  <?php else: ?>
                    <div>
                      <input type="radio"
                             class="custom-radio"
                             name="vote[<?php echo htmlspecialchars($position); ?>]"
                             value="<?php echo $candidate['candidateid']; ?>"
                             id="candidate_<?php echo $candidate['candidateid']; ?>">
                      <label for="candidate_<?php echo $candidate['candidateid']; ?>" class="select-btn">
                        Select Candidate
                      </label>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php if ($position !== array_key_last($positions)): ?><div class="separator"></div><?php endif; ?>
      <?php endforeach; ?>

      <div class="text-center mt-4">
        <button type="submit" class="vote-btn"><i class="fas fa-check-circle"></i> Submit Your Votes</button>
        <p class="text-muted mt-3">
          <small><i class="fas fa-info-circle"></i>
            For positions with one candidate, click <strong>YES</strong> to support or <strong>NO</strong> to oppose.
            For positions with multiple candidates, click <strong>Select Candidate</strong> to choose.
            You may skip any position.
          </small>
        </p>
      </div>
    </form>
  <?php endif; ?>
</div>

<div class="footer">
  <p>&copy; <?php echo date('Y'); ?> Sonrise Christian High School</p>
  <p>Electronic Voting System - Girls' Section</p>
</div>

<script>
$(document).ready(function() {
  $("#toggleSidebar").click(function() {
    $(".sidenav").toggleClass("collapsed");
    if ($(".sidenav").hasClass("collapsed")) {
      $(".sidenav").css("width","70px"); $(".main").css("margin-left","70px");
      $(".footer").css({"width":"calc(100% - 70px)","left":"70px"}); $(".toggle-btn").css("left","70px");
      $(".sidenav a span, .brand-section h4").hide();
      $(".sidenav a").css({"text-align":"center","padding":"15px 10px"}); $(".sidenav a i").css("margin-right","0");
    } else {
      $(".sidenav").css("width","220px"); $(".main").css("margin-left","220px");
      $(".footer").css({"width":"calc(100% - 220px)","left":"220px"}); $(".toggle-btn").css("left","220px");
      $(".sidenav a span, .brand-section h4").show();
      $(".sidenav a").css({"text-align":"left","padding":"15px 20px"}); $(".sidenav a i").css("margin-right","10px");
    }
  });
  var lastChecked = {};
  $(".custom-radio").click(function() {
    var radioName = $(this).attr('name');
    var position = $(this).closest('.candidate-card').data('position');
    if (lastChecked[radioName] === this) {
      $(this).prop('checked', false); lastChecked[radioName] = null;
      $('.candidate-card[data-position="' + position + '"]').removeClass('selected');
    } else {
      lastChecked[radioName] = this;
      $('.candidate-card[data-position="' + position + '"]').removeClass('selected');
      $(this).closest('.candidate-card').addClass('selected');
    }
  });
  $(".yes-no-radio.yes").change(function() {
    $(this).closest('.candidate-card').removeClass('no-selected').addClass('yes-selected');
  });
  $(".yes-no-radio.no").change(function() {
    $(this).closest('.candidate-card').removeClass('yes-selected').addClass('no-selected');
  });
  $("#votingForm").submit(function(e) {
    if (!confirm("Are you sure you want to submit your votes? This action cannot be undone.")) { e.preventDefault(); return false; }
  });
  $(".candidate-card").hover(
    function() { if (!$(this).hasClass('selected') && !$(this).hasClass('yes-selected') && !$(this).hasClass('no-selected')) $(this).css('transform','translateY(-8px)'); },
    function() { if (!$(this).hasClass('selected') && !$(this).hasClass('yes-selected') && !$(this).hasClass('no-selected')) $(this).css('transform','translateY(0)'); }
  );
});
</script>
<?php endif; ?>
</body>
</html>