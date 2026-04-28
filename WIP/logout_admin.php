<?php
require 'db_connect.php';
# kick out if not logged in
if (!$_SESSION['admin_logged_in']) {
    header('Location: login_admin.php');
    exit;
}
# log out
$_SESSION['admin_logged_in'] = false;
header('Location: login_admin.php');
exit;
?>