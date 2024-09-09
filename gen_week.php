<html>

<head>
    <meta charset="UTF-8">
    <title>Generate Matchups</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href=x.css>
</head>

<body>
    <div class=link onclick="location.href='admin.php'">Admin Control Panel</div>
    <form action="gen_week.php" method="post" id="form">
        <div class=link onclick="document.forms[0].submit()">Generate New Matchup?</div>
    </form>


    <?php

    session_start();
    if (!(isset($_SESSION["admin"]) && $_SESSION["admin"] === true)) {
        header("location: login.php");
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

    if (file_exists($gamepath . "SZNLOCK")) {
        header("location: admin.php");
        die();
    }

    function mstring($n1, $n2)
    {
        return min($n1, $n2) . "_" . max($n1, $n2);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $file_ = fopen("log_.txt", "a+");
        fwrite($file_, date("[Y-m-d H:i:s]:\t") . $_SERVER['REMOTE_ADDR'] . " generated new matchups.\n");
        fclose($file_);
        $week = file_exists($gamepath . "week") ? (int) file_get_contents($gamepath . "week") : 1;
        $limit = 256;
        while ($limit > 0) {
            $valid = true;
            $people = array_slice(scandir($gamepath . "people"), 2);
            if (count($people) & 1 == 1) {
                array_push($people, "^");
            }
            shuffle($people);
            $matching = array();
            while (count($people) > 0) {
                $max_prev = -1;
                $max_pers = "";
                foreach ($people as $person) {
                    $prev = 0;
                    foreach ($people as $opp) {
                        if (file_exists($gamepath . "played/" . mstring($person, $opp))) {
                            $prev++;
                        }
                    }
                    if ($prev > $max_prev) {
                        $max_prev = $prev;
                        $max_pers = $person;
                    }
                }
                $person = $max_pers;
                $opponent = "";
                foreach ($people as $opp) {
                    if (!($opp == $person || file_exists($gamepath . "played/" . mstring($person, $opp)))) {
                        $opponent = $opp;
                        break;
                    }
                }
                if ($opponent == "") {
                    $valid = false;
                    break;
                } else {
                    array_push($matching, $person, $opp);
                    unset($people[array_search($person, $people)]);
                    unset($people[array_search($opp, $people)]);
                }
            }
            if (count($people) == 0) {
                break;
            }
            $limit--;
        }
        if ($valid) {
            if (!file_exists($gamepath . "week" . $week)) {
                mkdir($gamepath . "week" . $week);
            }
            $i = 0;
            while (count($matching) > $i) {
                $n1 = $matching[$i];
                $n2 = $matching[$i + 1];
                $i += 2;
                file_put_contents($gamepath . "week" . $week . "/" . mstring($n1, $n2), "");
                file_put_contents($gamepath . "played/" . mstring($n1, $n2), "");
            }
            echo "<div class=link>Successfully generated new matchups.</div>";
            file_put_contents($gamepath . "week", $week + 1);
            file_put_contents($gamepath . "updatestats", "");
            file_put_contents($tourneypath . "cachestale", "");
        } else {
            echo "<div class=link>Could not generate matchups after 256 attempts. Likely no permutation for non-duplicates.</div>";
        }
        /* OLD GEN        shuffle($people);
        $i = count($people) - 1;
        $count = 1 << 16;
        while ($count > 0) {
            if ($i <= 0) {
                break;
            }
            $n1 = $people[$i];
            $n2 = $people[$i - 1];
            $i -= 2;
            if (file_exists($gamepath . "played/" . mstring($n1, $n2))) {
                $i = count($people) - 1;
                shuffle($people);
                $count--;
            }
        }
        if ($count == 0) {
            echo "<div class=link>Could not generate matchups after testing 65536 permutations. Likely no permutation for non-duplicates.</div>";
        } else {
            echo "<div class=link>Successfully generated new matchups.</div>";
            mkdir($gamepath . "week" . $week);
            $i = count($people) - 1;
            while ($i > 0) {
                $n1 = $people[$i];
                $n2 = $people[$i - 1];
                $i -= 2;
                file_put_contents($gamepath . "week" . $week . "/" . mstring($n1, $n2), "");
                file_put_contents($gamepath . "played/" . mstring($n1, $n2), "");
            }
            file_put_contents($gamepath . "week", $week + 1);
            file_put_contents($gamepath . "updatestats", "");
        } */
    }
    ?>
</body>

</html>