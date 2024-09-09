<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Manually Assign Matchups</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='index.php'">Return to List of Games</div>
    <div class=link id=shake>Select two players to assign them to play.</div>
    <form action=<?php echo "altermatchups.php"; ?> method="post" id="form">
        <input type="hidden" id="p1" name="p1">
        <input type="hidden" id="p2" name="p2">
        <input type="hidden" id="w" name="w">
        <div class=link onclick="submit()">Submit</div>
        </div>
    </form>






    <div style="margin:2px">Current Season:
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
        echo $active; ?>
    </div>



    <script>
        let error = 0;

        <?php
        function mstring($n1, $n2)
        {
            return min($n1, $n2) . "_" . max($n1, $n2);
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") { // TODO prevent bad input
            $p1 = $_POST["p1"];
            $p2 = $_POST["p2"];
            $p1 = preg_replace("/[^a-zA-Z0-9]+/", "", $p1);
            $p2 = preg_replace("/[^a-zA-Z0-9]+/", "", $p2);
            $w = $_POST["w"];
            if (!ctype_digit($w) || $p1 == "" || $p2 == "") {
                echo "error = 1"; // bad week arg
            } else {
                $n1 = "";
                $n2 = "";
                foreach (array_slice(scandir($gamepath . "week" . $w), 2) as $f) {
                    $ms = explode("_", $f);
                    if ($ms[0] == $p1)
                        $n1 = $ms[1];
                    if ($ms[0] == $p2)
                        $n2 = $ms[1];
                    if ($ms[1] == $p1)
                        $n1 = $ms[0];
                    if ($ms[1] == $p2)
                        $n2 = $ms[0];
                }
                echo "//" . $p1 . $p2 . $n1 . $n2 . "\n";
                if ($n1 == "" || $n2 == "") {
                    echo "error = 3;";// could not calc new matches
                } elseif (file_exists($gamepath . "played/" . mstring($p1, $p2)) || file_exists($gamepath . "played/" . mstring($n1, $n2))) {
                    echo "error = 2;";// new matches already paired (any week)
                } elseif (file_get_contents($gamepath . "played/" . mstring($p1, $n1)) != "" || file_get_contents($gamepath . "played/" . mstring($p2, $n2)) != "") {
                    echo "error = 4;";// matches to remove already scored
                } else {
                    unlink($gamepath . "played/" . mstring($p1, $n1));
                    unlink($gamepath . "played/" . mstring($p2, $n2));
                    unlink($gamepath . "week" . $w . "/" . mstring($p1, $n1));
                    unlink($gamepath . "week" . $w . "/" . mstring($p2, $n2));
                    file_put_contents($gamepath . "played/" . mstring($p1, $p2), "");
                    file_put_contents($gamepath . "week" . $w . "/" . mstring($p1, $p2), "");
                    if ($n1 != "^" || $n2 != "^") {
                        file_put_contents($gamepath . "played/" . mstring($n1, $n2), "");
                        file_put_contents($gamepath . "week" . $w . "/" . mstring($n1, $n2), "");
                    }
                    file_put_contents($gamepath . "updatestats", "");
                }
            }
        }
        ?>

    </script>








    <div id="control"></div>
    <script>
        height = window.innerHeight;
        width = window.innerWidth;

        control = document.getElementById("control");
        control.style.border = 'none';
        week_num = <?php echo file_get_contents($gamepath . "week"); ?>;
        matchups = <?php
        sleep(0.5);
        $week = (int) file_get_contents($gamepath . "week");
        $s = "[";
        for ($i = 1; $i < $week; $i++) {
            $s .= "[";
            foreach (array_slice(scandir($gamepath . "week" . $i), 2) as $match) {
                if (str_contains($match, "^")) {
                    0;//  continue;
                }
                $s .= "[\"" . $match . "\",\"" . file_get_contents($gamepath . "played/" . $match) . "\"],";
            }
            $s = substr($s, 0, -1) . "],";
        }
        $s = substr($s, 0, -1) . "];";
        echo $s;
        ?>
        sz = 350;
        for (i = 1; i < week_num; i++) {
            week = control.appendChild(document.createElement("div"));
            week.classList.add("week");
            title = week.appendChild(document.createElement("div"));
            title.classList.add("title");
            title.innerHTML = "Week" + i;
            title.onclick = function () { window.location.href = "leaderboard.php" };
            byes = [];
            for (j of matchups[i - 1]) {
                if (j[0].includes("^")) {
                    byes.push(j[0]);
                    continue;
                }
                row = week.appendChild(document.createElement("div"));
                row.classList.add("row");
                row.value = j[0]
                //        row.onclick = function () { window.location.href = "report_score.php?m=" + this.value };
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
                    player.value = i;
                    player.onclick = function () { click(this) };
                    player = row.appendChild(document.createElement("div"));
                    player.classList.add("player");
                    player.innerHTML = j[0].split("_")[1];
                    player.value = i;
                    player.onclick = function () { click(this) };
                }
            }
            for (bye of byes) {
                bye = bye.replace("_", "").replace("^", "");
                row = week.appendChild(document.createElement("div"));
                row.classList.add("row");
                player = row.appendChild(document.createElement("div"));
                player.classList.add("player");
                player.innerHTML = bye;
                player.value = i;
                player.onclick = function () { click(this) };
                player = row.appendChild(document.createElement("div"));
                player.classList.add("player");
                player.innerHTML = "N/A";
                player.style.color = "#a66";
            }
        }

        swap = [];
        shake_el = document.getElementById("shake");
        function shake(err) {
            tm = 200;
            shake_el.innerHTML = err;
            for (i = 0; i < 4; i++) {
                setTimeout(() => { shake_el.style.backgroundColor = "#faa" }, tm * i);
                setTimeout(() => { shake_el.style.backgroundColor = "#eee" }, tm * i + tm / 2);
            }
        }

        function click(ele) {
            if (swap.includes(ele)) {
                swap.splice(swap.indexOf(ele), 1);
                ele.style.backgroundColor = "#eee";
            } else if (swap.length >= 2) {
                shake("Select only two players.");
            } else if (swap.length == 1 && swap[0].value != ele.value) {
                shake("Must select two player from the saem week.");
            } else {
                swap.push(ele);
                ele.style.backgroundColor = "#afa";
            }
        }
        function submit() {
            if (swap.length != 2) {
                shake("Must select two players.");
            } else {
                document.getElementById("w").value = swap[0].value;
                document.getElementById("p1").value = swap[0].innerHTML;
                document.getElementById("p2").value = swap[1].innerHTML;
                document.getElementById("form").submit();
            }
        }


        errs = ["", "Internal Error :(", "One of the matches that would result from this pairing has already been assigned.",
            "Internal Error ):", "Cannot change matchup after scores submitted."];
        if (error != 0) {
            shake(errs[error]);
        }

    </script>








    <script>

        errors = ["", "Values must be numeric and non-empty", "Invalid final score (first to seven, win by two)",
            "Invalid number of beers."];
        if (error > 0) {
            err_msg = errors[error];
            document.getElementById("h6").innerText = err_msg;
        }
        /* ERRORS:
          0: OK
          1: input data not numeric (or empty)
          2: scores invalid (must be to 7, win by two)
          3: beers entered not valid per league rules */

    </script>
</body>

</html>