<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: ./dashboard");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    require_once $_SERVER["DOCUMENT_ROOT"] . "/php/info_send_log.php";

    if ($username === str_replace("@", "", $login_telegram) && $password === $telegram_id) {
        $_SESSION['username'] = $username;
        $_SESSION['language'] = 'ru';
        
        header("Location: ./dashboard");
        exit();
    } else {
        $error_message = "Wrong name or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CG Private area | Entering your mom</title>
    <link rel="stylesheet" href="./static/css/auth.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
</head>

<body>
    <form class="window" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <p class="toptext">The bots are gay, and this is the entrance</p>
        <div class="logdiv">
            <label class="labeltext">Login</label>
            <input class="input" name="username" type="text" placeholder="Your login">
        </div>
        <div class="passdiv">
            <label class="labeltext">Pas</label>
            <input class="input" name="password" type="password" placeholder="Your password">
        </div>
        <button type="submit" class="sign_in_but">Sign</button>
    </form>
</body>

</html>