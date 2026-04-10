<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

session_destroy();

showSuccess("You have been logged out successfully.");
redirect('/E-comm/');
?>


