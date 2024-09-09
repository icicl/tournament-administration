<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Snappa Leaderboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="x.css">
</head>

<?php
        session_start();
        if (isset($_SESSION["active"])) {
            $active = $_SESSION["active"];
        } else {
            $active = file_get_contents("active");
            $_SESSION["active"] = $active;
        }
        $gamepath = "games/" . $active . "/";
?>

<body>
    <div class=link onclick="location.href='index.php'">Return to List of Games</div>
    <br>
    <div id=0 class=link onclick="statmode(0)">View Unnormalized Stats</div>
    <div id=1 class=link onclick="statmode(1)">Viewing Stats Normalized by Number of Games Played</div>
    <div id=2 class=link onclick="statmode(2)">View Stats Normalized by Number of Games Assigned</div>
    <br>
    <div style="margin:2px">Current Season: <?php echo $active;?></div>
    <div class="board"></div>
    <script>
        <?php require("getstats.php"); ?>
        people_raw.splice(0, 0, [-1])
        stat_mode = 1;
        let people;
        people_unnorn = [];
        people_norm_played = [];
        people_norm_asigned = []; 
        for (person of people_raw) {
            people_unnorn.push(person.slice(0, 8));
            denom = (person[1] + person[2]);
            people_norm_played.push(person.slice(0, 8).map((x) => !Number.isInteger(x) ? x : (x / denom).toFixed(2)));
            denom = (person[8]);
            arr = person.slice(0, 8).map((x) => !Number.isInteger(x) ? x : (x / denom).toFixed(2));
            arr[3] = ((person[3] - 7*(person[8] - person[1] - person[2]))/person[8]).toFixed(2);
            people_norm_asigned.push(arr);
        }
        boardelement = document.getElementsByClassName("board")[0];
        boardelement.style.height = (32 * people_raw.length + 2) + "px";
        boardelement.style.margin = 1 + "%";

        board = document.getElementsByClassName("board")[0];
        function show() {
            board.innerHTML = "";
            people.splice(0, 0, ["Name", "Wins", "Losses", "Pt. Diff", "Points", "Op.Pts.", "Sinks", "Brews"]);
            for (person of people) {
                row = board.appendChild(document.createElement("div"));
                row.className = "row";
                row.style.height = '0px';
                pname = row.appendChild(document.createElement("div"));
                pname.className = "name";
                pname.innerHTML = person[0];
                pname.onclick = function () {location.href="player.php?p=" + this.innerHTML;};
                i = 1
                for (stat of person.slice(1)) {
                    stat_el = row.appendChild(document.createElement("div"));
                    stat_el.className = "stat";
                    stat_el.innerHTML = stat;
                    stat_el.value = i;
                    i++;
                    stat_el.onclick = function () { sort(this.value) };
                }
            }
        }
        function sort(index) {
            function sfunc(a, b) {
                a = a.map((x) => parseFloat(x));
                b = b.map((x) => parseFloat(x));
                if (isNaN(b[1])) return -1;
                if (isNaN(a[1])) return +1;
                return (b[index] - a[index]) * 100000 + (b[1] - a[1]) * 10000 + (b[3] - a[3]) * 100 + (b[6] - a[6]);
            }
            people = people.slice(1);
            people = people.sort(sfunc)
            show(people);
        }

        function statmode(mode) {
            if (mode == stat_mode) {
                return;
            }
            document.getElementById(stat_mode).innerHTML = "View" + document.getElementById(stat_mode).innerHTML.slice(7)
            document.getElementById(stat_mode).style.backgroundColor = "#eee";
            document.getElementById(mode).innerHTML = "Viewing" + document.getElementById(mode).innerHTML.slice(4)
            document.getElementById(mode).style.backgroundColor = "#fff";
            stat_mode = mode;
            switch (mode) {
                case 0:
                    people = people_unnorn;
                    break;
                case 1:
                    people = people_norm_played;
                    break;
                case 2:
                    people = people_norm_asigned;
                    break;
            }
            sort(1);
        }
        statmode(0);
        sort(1)
    </script>
    


    <div class=link onclick="location.href='bracket.php'">View Current Bracket</div>



    
</body>

</html>