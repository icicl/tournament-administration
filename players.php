<head>
    <meta charset="UTF-8">
    <title>Players</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
    <script><?php require("getstats.php");?></script>
</head>


    <body>
    <div class=link onclick="location.href='index.php'" style="height:2.5%">Return to List of Games</div>
        <br>
        <?php foreach (array_slice(scandir($gamepath . "people"), 2) as $player) {
            $unplayed = $people[$player][7] - $people[$player][1] - $people[$player][0];
            $ucolor = ($unplayed < 3) ? "#aaa" : ($unplayed == 3 ? "#c77" : "#f00");
            echo "<div style='width:60%;margin-left:20%' class=link onclick=\"location.href='player.php?p=" .
            $player . "'\">" . $player . "<font color=#aaa> (" . $people[$player][0]. "-" . 
            $people[$player][1] . ")</font>" . ($unplayed == 0 ? "" : "<font color=" . $ucolor . "> (" . $unplayed . " unplayed)</font>") . "</div>";
        } ?>    </body>

    </html>