<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// rest of your code
?><?php
$output = array();
$return_value = 0;
exec('backup.sh', $output, $return_value);
echo "<pre>" . implode("\n", $output) . "</pre>";
echo "Return value: " . $return_value;
?>
