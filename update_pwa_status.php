<?php
if (isset(_GET['pwa']) && _GET['pwa'] === 'true') {
    // Update the user's status to indicate they are using a PWA
    // You can store this information in a session variable, database, or wherever you prefer
    _SESSION['pwa_user'] = true;
}
?>
