<head>
    <meta charset="UTF-8">
    <title>Remove Player</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='admin.php'">Admin Control Panel</div>
    <form action="removeplayer.php" method="post" id="form">

        <div class=link onclick="document.forms[0].submit()">Remove Player</div>
        <input type="text" placeholder="Player's Name" id="textbox" , name="name">
    </form>
    <script>
        document.getElementById("textbox").focus()
    </script>
    <?php

    function mstring($n1, $n2)
    {
        return min($n1, $n2) . "_" . max($n1, $n2);
    }

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
    $tourneypath = $gamepath . "tourney/";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST["name"]); // the title/name of the ongoing game
        $name = preg_replace("/[^a-zA-Z0-9]+/", "", $name);
        $file_ = fopen("log_.txt", "a+");
        fwrite($file_, date("[Y-m-d H:i:s]:\t") . $_SERVER['REMOTE_ADDR'] . " removed player \"" . $name . "\".\n");
        fclose($file_);
        if (strlen($name) != 0 && file_exists($gamepath . "people/" . $name)) {
            file_put_contents($gamepath . "updatestats", "");
            file_put_contents($tourneypath . "cachestale", "");
            unlink($gamepath . "people/" . $name);
            $week = (int) file_get_contents($gamepath . "week");
            for ($i = 1; $i < $week; $i++) {
                foreach (array_slice(scandir($gamepath . "week" . $i), 2) as $match) {
                    $p = explode("_", $match);
                    if ($p[0] == $name || $p[1] == $name) {
                        if (file_get_contents($gamepath . "played/" . $match) == "") {
                            unlink($gamepath . "played/" . $match);
                            unlink($gamepath . "week" . $i . "/" . $match);
                            if ($p[0] != "^" && $p[0] != $name) {
                                file_put_contents($gamepath . "played/" . mstring("^", $p[0]), "");
                                file_put_contents($gamepath . "week" . $i . "/" . mstring("^", $p[0]), "");
                            }
                            if ($p[1] != "^" && $p[1] != $name) {
                                file_put_contents($gamepath . "played/" . mstring("^", $p[1]), "");
                                file_put_contents($gamepath . "week" . $i . "/" . mstring("^", $p[1]), "");
                            }
                        }
                        break;
                    }
                }
            }
            echo "<div class=link>" . $name . " removed from current season.</div>";
        } else {
            echo "<div class=link>" . $name . " not added to current season.</div>";
        }
    }
    ?>
</body>

</html>