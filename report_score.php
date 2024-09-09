<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Self-Report Scores</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <script>
        let error = 0;
        <?php
        session_start();
        if (isset($_SESSION["active"])) {
            $active = $_SESSION["active"];
        } else {
            $active = file_get_contents("active");
            $_SESSION["active"] = $active;
        }
        $gamepath = "games/" . $active . "/";
        $tourneypath = $gamepath . "tourney/";
        if (isset($_GET["m"])) {
            $m = $_GET["m"];
            $p = explode("_", $m);
            if (file_exists($gamepath . "SZNLOCK")) {
                if (file_exists($tourneypath . "awaiting/" . $m)) {
                    //TODO ??
                } else {
                    header("location: bracket.php");
                    die();
                }
            } else {
                if (file_exists($gamepath . "played/" . $m)) { //TODO add check for if matchup is valid
                } else {
                    if (count($p) == 4) {
                        header("location: bracket.php");
                    } else {
                        header("location: index.php");
                    }
                }
            }
            echo "";
        } else {
            header("location: index.php");
        }
        ?>
    </script>
    <div class=link style="width:360px;margin:4px" onclick="location.href='index.php'">Return to List of Games</div>
    <div class="week">
        <h2>Self-Report Score</h2>
        <h4 id="h6" style="color:#f00"></h4>
        <form action=<?php echo "report_score.php?m=" . $m; ?> method="post" id="form">
            <div class=player>
                <?php echo $p[0] . "'s statistics"; ?>
            </div>
            <div class=player>
                <?php echo $p[1] . "'s statistics"; ?>
            </div>
            <div class=scorereport>
                <input placeholder="Points" inputmode="numeric" id="p1" name="p1">
            </div>
            <div class=scorereport>
                <input placeholder="Points" inputmode="numeric" id="p2" name="p2">
            </div>
            <div class=scorereport>
                <input placeholder="Brews" inputmode="numeric" id="b1" name="b1">
            </div>
            <div class=scorereport>
                <input placeholder="Brews" inputmode="numeric" id="b2" name="b2">
            </div>
            <div class=scorereport>
                <input placeholder="Sinks" inputmode="numeric" id="s1" name="s1">
            </div>
            <div class=scorereport>
                <input placeholder="Sinks" inputmode="numeric" id="s2" name="s2">
            </div>
            <input style="width:98%;margin:1%" type="submit" value="Submit">
        </form>
    </div>
    <script>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { // TODO prevent bad input
                $p1 = $_POST["p1"];
                $b1 = $_POST["b1"];
                $s1 = $_POST["s1"];
                $p2 = $_POST["p2"];
                $b2 = $_POST["b2"];
                $s2 = $_POST["s2"];
                if ($s1 == "") {
                    $s1 = "0";
                }
                if ($s2 == "") {
                    $s2 = "0";
                }
                if (
                    ctype_digit($p1) && ctype_digit($b1) && ctype_digit($s1) &&
                    ctype_digit($p2) && ctype_digit($b2) && ctype_digit($s2)
                ) {
                    if (max($p1, $p2) < 7 || (max($p1, $p2) > 7 && abs($p1 - $p2) != 2) || abs($p1 - $p2) < 2) {
                        echo "error = 2;";
                    } elseif ($b1 < 1 + floor(($p2 - (int) ($p2 > $p1)) / 4) || $b2 < 1 + floor(($p1 - (int) ($p1 > $p2)) / 4)) {
                        echo "error = 3;";
                    } else {
                        if (!file_exists($gamepath . "SZNLOCK")) {
                            file_put_contents($gamepath . "played/" . $m, $p1 . " " . $b1 . " " . $s1 . " " . $p2 . " " . $b2 . " " . $s2);
                            header("location: index.php");
                        } else {
                            file_put_contents($tourneypath . "played/" . $m, $p1 . " " . $b1 . " " . $s1 . " " . $p2 . " " . $b2 . " " . $s2);
                            $stats = (array) json_decode(file_get_contents($tourneypath . "cache"));
                            $stats[$p[2] + 2][$p[3]] = $p1 > $p2 ? $p[0] : $p[1];
                            $stats[$p[2] + 1][2*$p[3]] .= "_" . $p1 . "_" . $b1 . "_" . $s1;
                            $stats[$p[2] + 1][2*$p[3] + 1] .= "_" . $p2 . "_" . $b2 . "_" . $s2;
                            file_put_contents($tourneypath . "cache", json_encode($stats));
                            unlink($tourneypath . "awaiting/" . $m);
                            if ($stats[$p[2] + 2][$p[3]^1] != "") {
                                file_put_contents($tourneypath . "awaiting/" . $stats[$p[2] + 2][$p[3] & 0xffffffe] . "_" . $stats[$p[2] + 2][$p[3] | 1] . "_" . ($p[2] + 1) . "_" . ($p[3] >> 1), "");
                            }
                            header("location: bracket.php");
                        }
                    }
                } else {
                    echo "error = 1;";
                }
                file_put_contents($gamepath . "updatestats", "");
            }
            ?>
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
        p1 = document.getElementById('p1');
        p2 = document.getElementById('p2');
        b1 = document.getElementById('b1');
        b2 = document.getElementById('b2');
        s1 = document.getElementById('s1');
        s2 = document.getElementById('s2');
        p1.focus();
        p1.onkeyup = function (e) {
            p2.focus();
        }
        p2.onkeyup = function (e) {
            b1.focus();
        }
        b1.onkeyup = function (e) {
            b2.focus();
        }
        b2.onkeyup = function (e) {
            s1.focus();
        }
        s1.onkeyup = function (e) {
            s2.focus();
        }
    </script>
</body>

</html>