<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Admin Control Panel</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='index.php'">Return to List of Games</div>
    <?php
     if (file_exists("active")) {
        if (isset($_SESSION["active"])) {
            $active = $_SESSION["active"];
        } else {
            $active = file_get_contents("active");
            $_SESSION["active"] = $active;
        }
        $gamepath = "games/" . $active . "/";
        if (!file_exists($gamepath . "LOCKSZN")) {
        echo '
        <div class=link onclick="location.href=\'gen_week.php\'">Generate New Weekly Matchups</div>
        <div class=link onclick="location.href=\'addplayer.php\'">Add Players</div>
        <div class=link onclick="location.href=\'altermatchups.php\'">Manually Assign Matchups</div>
        <div class=link onclick="location.href=\'removeplayer.php\'">Remove Players</div>
        <div class=link onclick="location.href=\'lockseason.php\'">Lock Season</div>';
    }
    }?>
    <div class=link onclick="location.href='makeseason.php'">Start a new Season</div>
</body>

</html>