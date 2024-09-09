<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Season Initialization</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='admin.php'">Admin Control Panel</div>
    <form action="makeseason.php" method="post" id="form">

        <div class=link onclick="document.forms[0].submit()">Generate New Season?</div>
        <input type="text" placeholder="Enter Season Name Here" id="textbox" , name="name">
    </form>
<?php

session_start();
if (!(isset($_SESSION["admin"]) && $_SESSION["admin"] === true)) {
    header("location: login.php");
    die();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $active = trim($_POST["name"]); // the title/name of the ongoing game
    $active = preg_replace("/[^a-zA-Z0-9]+/", "", $active);
    $file_ = fopen("log_.txt", "a+");
    fwrite($file_, date("[Y-m-d H:i:s]:\t") . $_SERVER['REMOTE_ADDR'] . " created new season \"" . $active . "\".\n");
    fclose($file_);
    if (strlen($active) == 0) {
        echo "<div class=link>Please enter a name for the season (ex. Fall 2023).</div>";
    } elseif (file_exists("games/" . $active)) {
        echo "<div class=link>A season with this name already exists.</div>";
    } else {
        file_put_contents("active", $active);
        if (!file_exists("games")) {
            mkdir("games");
        }
        mkdir("games/" . $active);
        mkdir("games/" . $active . "/people");
        mkdir("games/" . $active . "/played");
        mkdir("games/" . $active . "/tourney");
        echo "<div class=link>Created season <b>" . $active . "</b>.</div>";
        file_put_contents("games/" . $active . "/updatestats", "");
        file_put_contents("games/" . $active . "/tourney/cachestale", "");
        $_SESSION["active"] = $active;
    }
}
?>

</body>

</html>