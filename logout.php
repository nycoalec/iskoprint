<?php
require_once 'auth.php';

$auth = new Auth();
$result = $auth->logout();

// Redirect to main page
header('Location: index.php');
exit();
?>
