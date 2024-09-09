<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Finish Season</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='admin.php'">Admin Control Panel</div>
    <form action="lockseason.php" method="post" id="form">

        <div class=link onclick="document.forms[0].submit()">End Season? (y/n)</div>
        <input type="text" placeholder="Enter Season Name Here" id="textbox" , name="confirm">
    </form>
    <?php

    session_start();
    if (!(isset($_SESSION["admin"]) && $_SESSION["admin"] === true)) {
        header("location: login.php");
        die();
    }

    if (isset($_SESSION["active"])) {
        $active = $_SESSION["active"];
    } else {
        $active = file_get_contents("active");
        $_SESSION["active"] = $active;
    }
    $gamepath = "games/" . $active . "/";
    if (file_exists($gamepath . 'SZNLOCK')) {
        header("location: admin.php");
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $confirm = trim($_POST["confirm"]);
        $confirm = strtolower($confirm);
        if (!($confirm == "y" || $confirm == "yes")) {
            echo "<div class=link>Season lock cancelled.</div>";
        } else {
            $file_ = fopen("log_.txt", "a+");
            fwrite($file_, date("[Y-m-d H:i:s]:\t") . $_SERVER['REMOTE_ADDR'] . " ended season \"" . $active . "\".\n");
            fclose($file_);
            file_put_contents($gamepath . "SZNLOCK", "");
            mkdir($gamepath . "tourney/awaiting");
            mkdir($gamepath . "tourney/played");
            file_put_contents($gamepath . "tourney/cachestale","");
            echo "<div class=link>Season locked.</div>";
        }
    }
    ?>

</body>

</html>