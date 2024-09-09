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
$tourneypath = $gamepath . "tourney/";
if (file_exists($tourneypath . "cachestale")) {
    $people = [];
    foreach (array_slice(scandir($gamepath . "people"), 2) as $person) {
        $people[$person] = [0, 0, 0, 0, 0]; // Wins, Pt. Diff, Sinks, Games Assigned, Games Played.
    }
    foreach (array_slice(scandir($gamepath . "played"), 2) as $f) {
        if (str_contains($f, "^")) {
            continue;
        }
        $g = file_get_contents($gamepath . "played/" . $f);
        $p = explode("_", $f);
        if ($g !== "") {
            $people[$p[0]][4]++;
            $people[$p[1]][4]++;
            $e = explode(" ", $g);
            if ($e[0] > $e[3]) {
                $people[$p[0]][0]++;
            } else {
                $people[$p[1]][0]++;
            }
            $people[$p[0]][1] += $e[0] - $e[3];
            $people[$p[1]][1] += $e[3] - $e[0];
            $people[$p[0]][2] += $e[2];
            $people[$p[1]][2] += $e[5];
        }
        $people[$p[0]][3]++;
        $people[$p[1]][3]++;
    }
    function cmp($a, $b)
    {
        for ($i = 0; $i < 3; $i++) {
            if ($a[$i] * $b[3] == $b[$i] * $a[3]) {
                continue;
            }
            return $a[$i] * $b[3] > $b[$i] * $a[3] ? -1 : 1;
        }
    }
    uasort($people, "cmp");
    $idx = [0];
    $tot_depth = floor(log(count($people) - 1, 2)) + 1;
    $tot = 1;
    while ($tot < (1 << $tot_depth)) {
        for ($i = $tot - 1; $i >= 0; $i--) {
            array_splice($idx, $i + 1, 0, array(2 * $tot - $idx[$i] - 1));
        }
        $tot <<= 1;
    }
    $positions = array_fill(0, (1 << $tot_depth), "");
    for ($i = 0; $i < (1 << $tot_depth); $i++) {
        if ($idx[$i] < count($people)) {
            $positions[$i] = array_keys($people)[$idx[$i]];
        }
    }
    $stats = [$positions];
    for ($i = $tot_depth - 1; $i >= 0; $i--) {
        array_push($stats, array_fill(0, (1 << $i), ""));
    }
    for ($i = 0; $i < 1 << ($tot_depth - 1); $i++) {
        if ($positions[2 * $i + 1] == "") {
            $stats[1][$i] = $positions[2 * $i];
        }
    }
    if (file_exists($gamepath . "SZNLOCK")) {
        for ($i = 0; $i < count($positions); $i += 2) {
            if ($positions[$i + 1] != "") {
                file_put_contents($tourneypath . "awaiting/" . $positions[$i] . "_" . $positions[$i + 1] . "_0_" . ($i >> 1), "");
            }
        }
        for ($i = 0; $i < count($positions); $i += 4) {
            if ($positions[$i + 1] == "" && $positions[$i + 3] == "") {
                file_put_contents($tourneypath . "awaiting/" . $positions[$i] . "_" . $positions[$i + 2] . "_1_" . ($i >> 2), "");
            }
        }
    }
    function power($player)
    {
        return $player[4] == 0 ? 0 : ($player[0] * 2 + $player[1] / 2 + $player[2]) / $player[4];
    }
    array_splice($stats, 0, 0, [[]]);
    foreach ($people as $person => $ignore) {
        $stats[0][$person] = power($people[$person]);
    }
    file_put_contents($tourneypath . "cache", json_encode($stats));
    unlink($tourneypath . "cachestale");
} else {
    $stats = (array) json_decode(file_get_contents($tourneypath . "cache"));
}
$powers = array_shift($stats);
echo "powers = {};\n";
foreach ($powers as $player => $value) {
    echo "powers[\"" . $player . "\"] = " . $value . ";\n";
}
echo "stats = [];\n";
foreach ($stats as $round) {
    echo "stats.push([\"" . join("\", \"", $round) . "\"]);\n";
}
?>