<head>
    <meta charset="UTF-8">
    <title>Add Players</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='admin.php'">Admin Control Panel</div>
    <form action="addplayer.php" method="post" id="form">

        <div class=link onclick="document.forms[0].submit()">Add Player</div>
        <input type="text" placeholder="Player's Name" id="textbox" , name="name">
    </form>
    <script>
        document.getElementById("textbox").focus()
    </script>
    <?php


    session_start();
    if (!(isset($_SESSION["admin"]) && $_SESSION["admin"] === true)) {
        header("location: login.php");
        die();
    }
    if (!file_exists("active")) {
        header("location: makeseason.php");
        die();
    }

    if (isset($_SESSION["active"])) {
        $active = $_SESSION["active"];
    } else {
        $active = file_get_contents("active");
        $_SESSION["active"] = $active;
    }
    $gamepath = "games/" . $active . "/";

    if (file_exists($gamepath . "week") || file_exists($gamepath . "SZNLOCK")) {
        header("location: admin.php");
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST["name"]); // the title/name of the ongoing game
        $name = preg_replace("/[^a-zA-Z0-9]+/", "", $name);
        $file_ = fopen("log_.txt", "a+");
        fwrite($file_, date("[Y-m-d H:i:s]:\t") . $_SERVER['REMOTE_ADDR'] . " added player \"" . $name . "\".\n");
        fclose($file_);
        if (strlen($name) == 0 || file_exists($gamepath . "people/" . $name)) {
            echo "<div class=link>" . $name . " already added to current season.</div>";
        } else if (strpos($name, "_") !== false) {
            echo "<div class=link>" . $name . " contains disallowed character _</div>";
        } else {
            file_put_contents($gamepath . "people/" . $name, '');
        }
    }
    ?>
    <html>

    <body>
        <br>
        <div style='width:20%;margin-left:40%' class=link>Current Players</div>
        <?php foreach (array_slice(scandir($gamepath . "people"), 2) as $player) {
            echo "<div style='width:20%;margin-left:40%' class=link>" . $player . "</div>";
        } ?>
    </body>

    </html>