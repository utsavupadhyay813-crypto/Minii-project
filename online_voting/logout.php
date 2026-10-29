<?php require 'includes/init.php';
$_SESSION = []; session_destroy(); redirect('login.php');
