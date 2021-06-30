<?php
session_start();
ob_start();

//login logic
$msg = '';
if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] == 'gytis' && $_POST['password'] == '1234') {
        $_SESSION['logged_in'] = 'true';
        $_SESSION['timeout'] = time();
        $_SESSION['username'] = $_POST['username'];

        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'index.php';
        header("Location: http://$host$uri/$extra");
    } else {
        $msg = 'Wrong username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h2>Enter Username and Password</h2>
    <p><?php echo $msg ?></p>

    <div>
        <form action="./login.php" method="post">
            <input type="text" name="username" placeholder="username = gytis" required autofocus></br>
            <input type="password" name="password" placeholder="password = 1234" required>
            <button type="submit" name="login">Login</button>
        </form>

    </div>
</body>

</html>