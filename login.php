<?php
session_start();

if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    header("location: admin.php");
    die();
}
$valid = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $file_ = fopen("log_.txt", "a+");
    fwrite($file_, date("[Y-m-d H:i:s]:\t") . $_SERVER['REMOTE_ADDR'] . "attempted to login as user \"" . $username . "\".\n");
    fclose($file_);
    if ($username == "admin" && $password == "2647") { // TODO insecure - doesn't matter for this use case but if scaled fix
        $_SESSION["admin"] = true;
        header("location: admin.php");
    } else {
        $valid = false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='index.php'" style="height:2.5%">Return to List of Games</div>
    <br>
    <br>
    <div>Sign in to access the admin controls.</div>
    <br>
    <form action="login.php" method="post" id="form">
        <?php if (!$valid) {
            echo "<div style=\"color:#f00\">Invalid Credentials</div>";
        } ?>
        <input placeholder="username" type="text" , id="textbox" , name="username">
        <input placeholder="password" type="password" , id="passbox" , name="password">
        <input type="submit" value="Submit">
    </form>
</body>

</html>
