<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Snappa Season Scoreboard</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="x.css?v=11">

</head>

<body>

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
  ?>

<div class=link onclick="location.href='leaderboard.php'">View Current Leaderboard / Stats</div>
<div class=link onclick="location.href='players.php'">View Roster</div>
  <div class=link onclick="location.href='changeseason.php'">View a Different Season</div>
  <div class=link onclick="location.href='admin.php'">Admin Tools</div>
  <div style="margin:2px">Current Season:
    <?php echo $active; ?>
  </div>
  <div id="control"></div>
  <script>
    height = window.innerHeight;
    width = window.innerWidth;

    control = document.getElementById("control");
    control.style.border = 'none';
    week_num = <?php echo file_get_contents($gamepath . "week"); ?>;
    matchups = <?php
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
        if (j[0].includes("^")){
          byes.push(j[0]);
          continue;
        }
        row = week.appendChild(document.createElement("div"));
        row.classList.add("row");
        row.value = j[0]
        row.onclick = function () { window.location.href = "report_score.php?m=" + this.value };
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
      for (bye of byes) {
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
    }




  </script>


</body>

</html>