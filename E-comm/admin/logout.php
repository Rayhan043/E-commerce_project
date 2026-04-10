<?php


include_once '../config/db.php';
include_once '../includes/functions.php';

unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);

header("Location: /E-comm/admin/");
exit;
?>


