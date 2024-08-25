<?php
include('config.php');

// Assuming $userData array is defined and contains the necessary data
$userDataType = isset($userData['Type']) ? $userData['Type'] : '';
$pageName = basename($_SERVER['PHP_SELF']);
?>
            
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="./"><img src="<?php echo $logo;?>" alt="Company Logo" height="45"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
<?php 
include("lib/logfile.php");

 if($userData['Type']!=='limited' && $userData['Type']!=='suspended'){echo'
<li class="nav-item">
    <a class="nav-link" href="add.php"><img src="lib/img/plus.png" width="20"> New Record</a>
</li> ';}
    if($userData['Type']==='admin'){echo'
<li class="nav-item">
    <a class="nav-link" href="log-view.php?file=logfile"><img src="lib/img/log.png" width="20">Log console</a>
</li>';}
    if($userData['Type']==='advanced' or $userData['Type']==='admin'){echo'
<li class="nav-item">
    <a class="nav-link" href="statistics.php"><img src="lib/img/stats.png" width="20">Stats</a>
</li> ';}
?>

                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><img src="lib/img/profile.png" width="20"> Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="terms.php"><img src="lib/img/legal.png" width="20"> Terms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php?logout"><img src="lib/img/logout.png" width="20"> Logout</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<div id="popupContainer" class="popup-container" style="display: none;">
  <div class="popup">
    <span class="close-btn">&times;</span>
    <form id="inputForm" action="create_new.php" method="POST">
        
<input type="text" id="inputField" name="newSession" placeholder="Create new record" maxlength="100" pattern="^[^\s.]+(\s+[^\s.]+)*$" title="Please enter text without dots and without leading or trailing spaces" required>      <button type="submit">Create new</button>
    </form>
  </div>
</div>   

