<!-- logout logic -->
<?php
session_start();
if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['logged_in']);
    session_destroy();
}
print('<h2>Logged Out!</h2>');
header('Refresh: 1; URL = login.php');
?>