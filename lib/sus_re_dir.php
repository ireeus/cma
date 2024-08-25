<?php
// Check if userDataType is 'suspended' and pageName is not 'profile.php'
if ($userDataType === 'suspended' and $pageName !== 'profile.php') {
    echo'<script>window.location.href = "profile.php";</script>';
    exit; // Use exit instead of die for consistency
}
