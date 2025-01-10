<?php require_once("mysql/mysql-connect.php"); ?>

<?php

  $gameId = $_GET['gameId'];

  $Query = "SELECT game.id, game.date FROM watchmyteam.avalanche as game WHERE game.date >= CURDATE() ORDER BY game.date ASC LIMIT 0,1";
  if(isset($gameId)) {
    $Query = "SELECT game.id, game.date FROM watchmyteam.avalanche as game WHERE game.idint = $gameId ORDER BY game.date ASC LIMIT 0,1";
  }

  $Res = mysqli_query(dbcConnectAsViewer(), $Query);
  $Data = mysqli_fetch_assoc($Res);

  $gameId = $Data['id'];
  $date = $Data["date"];
  $formattedDate = date('l, F jS, Y \@ h:i a', strtotime($date));

  // -----

  $Query = "SELECT game.avalanche_tv_id, league.channel_logo as avalanche_channel_logo from watchmyteam.avalanche as game LEFT JOIN watchmyteam.league as league ON game.avalanche_tv_id = league.id WHERE game.id = '$gameId'";

  $Res = mysqli_query(dbcConnectAsViewer(), $Query);
  $Data = mysqli_fetch_assoc($Res);

  $avalancheChannelLogo = $Data['avalanche_channel_logo'];
  $avalancheTvId = $Data['avalanche_tv_id'];

  // Denver Local TV ID's
  $localTvArray = array('66670b42-ad07-426a-b686-5b0a362723e9','89e5ff58-64ec-498b-9b10-7eef291e0550'); // KTVD, KUSA
  $isLocalTv = in_array($avalancheTvId, $localTvArray);

  // -----

  $Query = "SELECT game.opponent_team_id, game.opponent_tv_id, league.channel_logo as opponent_channel_logo, team.name, logo.logo as opponent_logo from watchmyteam.avalanche as game LEFT JOIN watchmyteam.league as league ON game.opponent_tv_id = league.id LEFT JOIN teams.teams as team ON game.opponent_team_id = team.id LEFT JOIN logos.logos as logo ON game.opponent_team_id = logo.team_id WHERE game.id = '$gameId' AND logo.active = 1";

  $Res = mysqli_query(dbcConnectAsViewer(), $Query);
  $Data = mysqli_fetch_assoc($Res);

  $opponentTeamId = $Data['opponent_team_id'];
  $opponentTeamName = $Data['name'];
  $opponentLogo = $Data['opponent_logo'];
  $opponentTvId = $Data['opponent_tv_id'];
  $opponentChannelLogo = $Data['opponent_channel_logo'];

  // -----

  $Query = "SELECT game.national, game.national_tv_id, league.channel_logo as national_channel_logo from watchmyteam.avalanche as game LEFT JOIN watchmyteam.league as league ON game.national_tv_id = league.id WHERE game.id = '$gameId'";

  $Res = mysqli_query(dbcConnectAsViewer(), $Query);
  $Data = mysqli_fetch_assoc($Res);

  $nationalTvType = $Data['national'];
  $nationalChannelLogo = $Data['national_channel_logo'];

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>How to Watch The Colorado Avalanche on TV</title>
    <link rel="icon" type="image/x-icon" href="_img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="_css/style.css" />
  </head>

  <body>
    <div class="header-logo">
      <img src="_img/logo.png" class="tv-logo" />
    </div>
    <div class="container no-color">
      <h4>Your guide to what channel the Avs game is on, while watching from the United States.</h4>
    </div>
    <div class="container">
      <h3>Next game: <?php echo $formattedDate; ?> MT.</h3>
      <div class="container text-center">
        <div class="row">
          <div class="col">
            <img src="https://i.logocdn.com/nhl/1999/colorado-avalanche.svg" width="100"/>
          </div>
          <div class="col">
            <img src="https://i.logocdn.com<?php echo$opponentLogo; ?>" width="100"/>
          </div>
        </div>
      </div>
      <?php
        if($nationalTvType == 'exclusive') {
      ?>
        <div class="container text-center">
          <div class="row">
            <div class="col">
              <p>This game is an exclusive National Broadcast. It can only be seen on:</p>
              <img src="<?php echo $nationalChannelLogo; ?>" width="200"/>
            </div>
          </div>
        </div>
      <?php
        } else {
      ?>
        <div class="container text-center">
          <div class="row">
            <div class="col">
              <p>If you live in the Avalanche broadcast region:</p>
              <img src="<?php echo $avalancheChannelLogo; ?>" width="200"/>
            </div>
            <div class="col">
              <p>If you live in the <?php echo $opponentTeamName; ?> broadcast region:</p>
              <img src="<?php echo $opponentChannelLogo; ?>" width="200"/>
            </div>
          </div>
        </div>
        <div class="container text-center">
          <div class="row">
            <div class="col">
              <?php
                 if($nationalTvType == 'simulcast') {
              ?>
                <p>This game is a National Broadcast, with local simulcasts. It is not available on ESPN+, and will only be on:</p>
                <img src="<?php echo $nationalChannelLogo; ?>" width="200"/>
              <?php
                } else {
              ?>
                <p>If you live outside either of these broadcast regions*:</p>
                <img src="<?php echo $nationalChannelLogo; ?>" width="200"/>
                <p><em>*Some areas that fall in multiple team regions might be blacked out on ESPN+</em></p>
              <?php
                }
              ?>
            </div>
          </div>
        </div>
      <?php
        }
      ?>
    </div>
    <div>
      <p style="text-align: center">
        <a style="color:#fff"
          href="https:&#x2F;&#x2F;www.canva.com&#x2F;design&#x2F;DAGbuh4pGgE&#x2F;WZvFUq714VoemHwdf1snww&#x2F;view?utm_content=DAGbuh4pGgE&amp;utm_campaign=designshare&amp;utm_medium=embeds&amp;utm_source=link"
          target="_blank"
          rel="noopener"
          >Click here for a zoomable version of the following flowchart</a
        >
      </p>
    </div>
    <div
      style="
        position: relative;
        width: 100%;
        height: 0;
        padding-top: 100%;
        padding-bottom: 0;
        box-shadow: 0 2px 8px 0 rgba(63, 69, 81, 0.16);
        margin-top: 1.6em;
        margin-bottom: 0.9em;
        overflow: hidden;
        border-radius: 8px;
        will-change: transform;
      "
    >
      <iframe
        loading="lazy"
        style="
          position: absolute;
          width: 100%;
          height: 100%;
          top: 0;
          left: 0;
          border: none;
          padding: 0;
          margin: 0;
        "
        src="https://www.canva.com/design/DAGbuh4pGgE/WZvFUq714VoemHwdf1snww/view?embed"
        allowfullscreen="allowfullscreen"
        allow="fullscreen"
      >
      </iframe>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
