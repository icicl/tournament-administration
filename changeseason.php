<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Change Active Season (Local)</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='index.php'">Return to List of Games</div>

    <?php

    session_start();
    if (isset($_GET["g"])) {
        $g = $_GET["g"];
        if (file_exists("games/" . $g)) {
            $_SESSION["active"] = $g;
            header("location: index.php");
            die();
        }
    }

    if (isset($_SESSION["active"])) {
        $active = $_SESSION["active"];
    } else {
        $active = file_get_contents("active");
    }

    foreach (array_slice(scandir("games"), 2) as $season) {
        echo "<div class=link " . (($season == $active) ? "style=\"background-color:#fff\"" : "") . "onclick=\"location.href='changeseason.php?g=" . $season . "'\">" . $season . "</div>\n";
    }

    ?>
</body>

</html>