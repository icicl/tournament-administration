<!DOCTYPE html>
<html>
<?php
if (!file_exists("active")) {
    header("location: makeseason.php");
    die();
}
session_start();
if (isset($_SESSION["active"])) {
    $active = $_SESSION["active"];
} else {
    $active = file_get_contents("active");
    $_SESSION["active"] = $active;
}
$gamepath = "games/" . $active . "/";
if (isset($_GET["p"])) {
    $player = $_GET["p"];
    if (!file_exists($gamepath . "people/" . $player)) {
        header("location: leaderboard.php");
        die();
    }
} else {
    header("location: leaderboard.php");
    die();
}
?>

<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $player; ?>'s Stats
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>

</head>

<body>


    <div class=link onclick="location.href='index.php'">Return to List of Games</div>
    <div class=link onclick="location.href='leaderboard.php'">View Current Leaderboard / Stats</div>
    <div id="control"></div>
    <script>
        control = document.getElementById("control");
        control.style.border = 'none';
        matchups = <?php
        $week = (int) file_get_contents($gamepath . "week");
        $s = "[";
        for ($i = 1; $i < $week; $i++) {
            $s .= "[";
            foreach (array_slice(scandir($gamepath . "week" . $i), 2) as $match) {
                $m = explode("_", $match);
                if ($m[0] == $player || $m[1] == $player) {
                    $s .= "\"" . $match . "\",\"" . file_get_contents($gamepath . "played/" . $match) . "\",";
                }
            }
            $s = substr($s, 0, -1) . "],";
        }
        $s = substr($s, 0, -1) . "];\n";
        echo $s;
        ?>
        playername = "<?php echo $player; ?>";
        week = control.appendChild(document.createElement("div"));
        week.classList.add("week");
        title = week.appendChild(document.createElement("div"));
        title.classList.add("title");
        title.innerHTML = playername + "'s stats";
        bye = "";
        for (j of matchups) {
            if (j[0].includes("^")) {
                bye = j[0];
                continue;
            }
            row = week.appendChild(document.createElement("div"));
            row.classList.add("row");
            row.value = j[0];
            if (j[0].split('_')[1] == playername) {
                j[0] = j[0].split('_')[1] + '_' + j[0].split('_')[0];
                if (j[1]) {
                    tmp = j[1].split(' ');
                    j[1] = tmp[3] + ' ' + tmp[4] + ' ' + tmp[5] + ' ' + tmp[0] + ' ' + tmp[1] + ' ' + tmp[2];
                }
            }
            if (j[1]) {
                matchdata = j[1].split(' ');
                player = row.appendChild(document.createElement("div"));
                player.classList.add("playersmall");
                player.innerHTML = j[0].split("_")[0];
                subscore = player.appendChild(document.createElement("div"));
                subscore.classList.add("subscore");
                subscore.innerHTML = matchdata[1] + ' drink' + (matchdata[1] == '1' ? '' : 's') + (matchdata[2] != '0' ? ', ' + matchdata[2] + ' sink' + (matchdata[2] == '1' ? '' : 's') : '');
                score = row.appendChild(document.createElement("div"));
                score.classList.add('score');
                score.innerHTML = matchdata[0] + '-' + matchdata[3];
                player = row.appendChild(document.createElement("div"));
                player.classList.add("playersmall");
                player.innerHTML = j[0].split("_")[1];
                subscore = player.appendChild(document.createElement("div"));
                subscore.classList.add("subscore");
                subscore.innerHTML = matchdata[4] + ' drink' + (matchdata[4] == '1' ? '' : 's') + (matchdata[5] != '0' ? ', ' + matchdata[5] + ' sink' + (matchdata[5] == '1' ? '' : 's') : '');
            } else {
                player = row.appendChild(document.createElement("div"));
                player.classList.add("player");
                player.innerHTML = j[0].split("_")[0];
                player = row.appendChild(document.createElement("div"));
                player.classList.add("player");
                player.innerHTML = j[0].split("_")[1];
            }
        }
        if (bye != "") {
            bye = bye.replace("_", "").replace("^", "");
            row = week.appendChild(document.createElement("div"));
            row.classList.add("row");
            player = row.appendChild(document.createElement("div"));
            player.classList.add("player");
            player.innerHTML = bye;
            player = row.appendChild(document.createElement("div"));
            player.classList.add("player");
            player.innerHTML = "N/A";
            player.style.color = "#a66";
        }




    </script>


</body>

</html>