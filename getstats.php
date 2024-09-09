<?php

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();
if (isset($_SESSION["active"])) {
    $active = $_SESSION["active"];
} else {
    $active = file_get_contents("active");
    $_SESSION["active"] = $active;
}
$gamepath = "games/" . $active . "/";

if (file_exists($gamepath . "updatestats")) {

    $people = [];
    foreach (array_slice(scandir($gamepath . "people"), 2) as $person) {
        $people[$person] = [0, 0, 0, 0, 0, 0, 0, 0]; // Wins, Losses, Points Scored, Points Allowed, Sinks, Brews, Games Assigned.
    }
    foreach (array_slice(scandir($gamepath . "played"), 2) as $f) {
        if (str_contains($f,"^")) {
            continue;
        }
        $g = file_get_contents($gamepath . "played/" . $f);
        $p = explode("_", $f);
/*        if (!in_array($p[0], array_keys($people))) {
            continue;
        }
        if (!in_array($p[1], array_keys($people))) {
            continue;
        }*/
        if ($g !== "") {
            $e = explode(" ", $g);
            if (array_key_exists($p[0], $people)) {
                if ($e[0] > $e[3]) {
                    $people[$p[0]][0]++;
                } else {
                    $people[$p[0]][1]++;
                }
                $people[$p[0]][2] += $e[0] - $e[3];
                $people[$p[0]][3] += $e[0];
                $people[$p[0]][4] += $e[3];
                $people[$p[0]][5] += $e[2];
                $people[$p[0]][6] += $e[1];        
            }
            if (array_key_exists($p[1], $people)) {
                if ($e[0] > $e[3]) {
                    $people[$p[1]][1]++;
                } else {
                    $people[$p[1]][0]++;
                }
                $people[$p[1]][2] += $e[3] - $e[0];
                $people[$p[1]][3] += $e[3];
                $people[$p[1]][4] += $e[0];
                $people[$p[1]][5] += $e[5];
                $people[$p[1]][6] += $e[4];    
            }
        }
        if (array_key_exists($p[0], $people)) {
            $people[$p[0]][7]++;
        }
        if (array_key_exists($p[1], $people)) {
            $people[$p[1]][7]++;
        }
    }
    file_put_contents($gamepath . "stats", json_encode($people));
    unlink($gamepath . "updatestats");
} else {
    $people = (array) json_decode(file_get_contents($gamepath . "stats"));
}
echo "people_raw = [];";
foreach (array_keys($people) as $person) {
    if ($person != "^") {
        echo "people_raw.push([\"" . $person . "\", " . $people[$person][0] . "," . $people[$person][1] . "," . $people[$person][2] . "," . $people[$person][3] . "," . $people[$person][4] . "," . $people[$person][5] . "," . $people[$person][6] . "," . $people[$person][7] . "]);\n";
    }
}