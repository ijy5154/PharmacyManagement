<?php
session_start();

$_SESSION = array();

echo '<script type="text/javascript">';
echo 'location.href="/";';
echo '</script>';
?>
