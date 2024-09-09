<html lang="en" style="scroll-behavior:smooth">

<head>
    <meta charset="UTF-8">
    <title>Bracket</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="x.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class=link onclick="location.href='index.php'" style="height:2.5%">Return to List of Games</div>
    <div class=column id=0 style="height:96%;border:1px solid #000"></div>
    <script>
        <?php
        require("getstats_tourney.php");
        ?>
        count = stats[0].length;
        depth = Math.floor(Math.log2(count - 1)) + 1;
        tot_depth = depth;
        col = document.getElementById(0).appendChild(document.createElement("div"));
        col.classList.add("column");
        col.style.width = '1%';
        col.style.height = "100%";
        height_basis = (98 - 5) / 2;
        LABELS = ["Champion", "Championship", "Semifinals", "Quarterfinals"];
        while (depth > 0) {
            col = document.getElementById(0).appendChild(document.createElement("div"));
            col.classList.add("column");
            col.style.width = (98 / (tot_depth + 1)) + '%';
            spacer = col.appendChild(document.createElement("div"));
            spacer.classList.add("roundtitle");
            if (depth > 3) {
                label = "Round of " + (1 << depth);
            } else {
                label = LABELS[depth];
            }
            spacer.innerText = label
            spacer.value = tot_depth - depth;
            spacer.onclick = function () { showodds(this.value); };
            depth--;
            height = (height_basis / (1 << depth)) + '%';
            for (i = 0; i < (1 << depth); i++) {
                spacer = col.appendChild(document.createElement("div"));
                spacer.classList.add("bracketsection");
                spacer.style.height = height;
                spacer.style.border = "none";
                spacer.value = tot_depth - depth - 1 + ' ' + i;
                spacer.onclick = function () { clicked(this.value) }
                section = col.appendChild(document.createElement("div"));
                section.classList.add("bracketsection");
                section.style.height = height;
                section.value = tot_depth - depth - 1 + ' ' + i;
                section.onclick = function () { clicked(this.value) }
            }
            col.childNodes[1].style.height = (height_basis / (2 << (depth)) + 1.25) + '%';
        }
        col = document.getElementById(0).appendChild(document.createElement("div"));
        col.classList.add("column");
        col.style.width = (98 / (tot_depth + 1)) + '%';
        spacer = col.appendChild(document.createElement("div"));
        spacer.classList.add("roundtitle");
        spacer.innerText = LABELS[0];
        spacer.onclick = function () { showodds(tot_depth); };
        section = col.appendChild(document.createElement("div"));
        section.classList.add("bracketsection");
        section.style.height = "47.5%";
        section.style.borderTop = "none";
        section.style.borderRight = "none";

        idx = [0];
        tot = 1;
        while (tot < count) {
            for (i = tot - 1; i >= 0; i--) {
                idx.splice(i + 1, 0, 2 * tot - idx[i] - 1);
            }
            tot <<= 1;
        }
        rev_idx = []
        for (i = 0; i < tot; i++) {
            rev_idx.push(0);
        }
        for (i = 0; i < tot; i++) {
            rev_idx[idx[i]] = i;
        }
        for (ii = 0; ii < stats.length; ii++) {
            for (i = 0; i < stats[ii].length; i++) {
                if (stats[ii][i] != "") {
                    stat = stats[ii][i];
                    stat = stat.split('_');
                    btext = document.getElementById(0).childNodes[1 + ii].childNodes[1 + i].appendChild(document.createElement("div"));
                    btext.classList.add("brackettext");
                    btext.innerHTML = stat[0] + "<div class=winrate></div>";
                    if (stat.length > 1) {
                        bscore = document.getElementById(0).childNodes[1 + ii].childNodes[1 + i].appendChild(document.createElement("div"));
                        bscore.classList.add("bracketscore");
                        bscore.innerHTML = stat[1] + "<div class=winrate>" + stat[2] + " drinks" + (stat[3] != 0 ? ", " + stat[3] + " sink" + (stat[3] == 1 ? "" : "s") : "") + "</div>";
                    }
                }
            }
        }

        function clicked(value) {
            v = value.split(' ').map(x => parseInt(x));
            if (stats[v[0]][2 * v[1]] != "" && stats[v[0]][2 * v[1] + 1] != "" && stats[v[0] + 1][v[1]] == "") {
                query = stats[v[0]][2 * v[1]] + "_" + stats[v[0]][2 * v[1] + 1] + "_" + v[0] + "_" + v[1];
                window.location.href = "report_score.php?m=" + query;
            }
        }

        function win_prob(player1, player2) {
            if (player1 == "") {
                if (player2 == "") {
                    return [0.5, 0.5];
                }
                return [0, 1];
            }
            if (player2 == "") {
                return [1, 0];
            }
            player1 = player1.split("_")[0];
            player2 = player2.split("_")[0];
            power = powers[player1] - powers[player2];
            power /= 2;
            return [1 / (1 + Math.exp(-power)), 1 / (1 + Math.exp(power))];
        }

        tot_size = (1 << tot_depth);

        odds = [Array(tot_size).fill(1)];
        for (depth = 0; depth < tot_depth; depth++) {
            odds.push(Array(tot_size).fill(0))
            for (j = 0; j < tot_size; j++) {
                if (stats[depth + 1][j >> (depth+1)] != "") {
                        odds[depth + 1][j] = (stats[depth + 1][j >> (depth + 1)].split("_")[0] == stats[0][j].split("_")[0]) ? 1 : 0;
                } else {
                    start = (((j >> depth) ^ 1) << depth);
                    for (opp = start; opp < start + (1 << depth); opp++) {
                        odds[depth + 1][j] += odds[depth][opp] * win_prob(stats[0][j], stats[0][opp])[0];
                    }
                    odds[depth + 1][j] *= odds[depth][j];
                }
            }
        }
        function showodds(round) {
            document.getElementsByClassName("roundtitle")[shown_round].style.backgroundColor = "#e8ffee";
            document.getElementsByClassName("roundtitle")[round].style.backgroundColor = "#f8d8f8";
            shown_round = round;
            for (i = 0; i < (1 << tot_depth); i++) {
                if (stats[0][i] == "") continue;
                btext = document.getElementById(0).childNodes[1].childNodes[1 + i].childNodes[0];
                if (btext) {
                    btext.childNodes[1].innerText = (100 * odds[round][i]).toFixed(1) + "%";
                }
            }
        }
        let shown_round = tot_depth;
        showodds(tot_depth);

    </script>
</body>

</html>