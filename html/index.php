<?php require_once("mysql/mysql-connect.php"); ?>

<?php

  $gameId = $_GET['gameId'];

  define('TIMEZONE', 'America/Denver');
  date_default_timezone_set(TIMEZONE);

  $queryDate = date("Y-m-d");

  $Query = "SELECT game.id, game.date FROM watchmyteam.avalanche as game WHERE game.date >= '$queryDate' ORDER BY game.date ASC LIMIT 0,1";
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
              <?php
                if($isLocalTv) {
              ?>
                <p>If you live in the Avalanche broadcast region:</p>
                <p>If you live in the Denver Media Market:</p>
                <img src="<?php echo $avalancheChannelLogo; ?>" width="200"/>
                <p>If you live outside the Denver Media Market:</p>
                <img src="https://watchtheavs.com/_img/league/colorado-avalanche/altitude-sports.png" width="200"/>
              <?php
                } else {
              ?>
                <p>If you live in the Avalanche broadcast region:</p>
                <img src="<?php echo $avalancheChannelLogo; ?>" width="200"/>
              <?php
                }
              ?>
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
                <p><em>*Some areas that fall in multiple team regions might be blacked out</em></p>
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

    <div class="kofi">
      <iframe id='kofiframe' src='https://ko-fi.com/kieckhafer/?hidefeed=true&widget=true&embed=true&preview=true' style='border:none;width:100%;padding:4px;background:#6F263D;' height='712' title='kieckhafer'></iframe>
    </div>

    <div class="kofi">
    <div id="mc_embed_shell">
      <link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css">
        <style type="text/css">
              #mc_embed_signup{background:#fff; false;clear:left; font:14px Helvetica,Arial,sans-serif; width: 600px;}
              /* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
                 We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
         </style>
        <div id="mc_embed_signup">
          <form action="https://watchtheavs.us20.list-manage.com/subscribe/post?u=5eceb0cbd182dcbec1fe66bbf&amp;id=8f8582cc1a&amp;f_id=00c2c2e1f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
              <div id="mc_embed_signup_scroll"><h2>Subscribe to receive email notifications about the next games viewing options</h2>
                  <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                  <div class="mc-field-group"><label for="mce-EMAIL">Email Address <span class="asterisk">*</span></label><input type="email" name="EMAIL" class="required email" id="mce-EMAIL" required="" value=""></div><div class="mc-field-group"><label for="mce-FNAME">First Name </label><input type="text" name="FNAME" class=" text" id="mce-FNAME" value=""></div><div class="mc-field-group"><label for="mce-LNAME">Last Name </label><input type="text" name="LNAME" class=" text" id="mce-LNAME" value=""></div>
      <div hidden=""><input type="hidden" name="tags" value="38"></div>
              <div id="mce-responses" class="clear foot">
                  <div class="response" id="mce-error-response" style="display: none;"></div>
                  <div class="response" id="mce-success-response" style="display: none;"></div>
              </div>
          <div aria-hidden="true" style="position: absolute; left: -5000px;">
              /* real people should not fill this in and expect good things - do not remove this or risk form bot signups */
              <input type="text" name="b_5eceb0cbd182dcbec1fe66bbf_8f8582cc1a" tabindex="-1" value="">
          </div>
              <div class="optionalParent">
                  <div class="clear foot">
                      <input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button" value="Subscribe">
                      <p style="margin: 0px auto;"><a href="http://eepurl.com/i8An8I" title="Mailchimp - email marketing made easy and fun"><span style="display: inline-block; background-color: transparent; border-radius: 4px;"><img class="refferal_badge" src="https://digitalasset.intuit.com/render/content/dam/intuit/mc-fe/en_us/images/intuit-mc-rewards-text-dark.svg" alt="Intuit Mailchimp" style="width: 220px; height: 40px; display: flex; padding: 2px 0px; justify-content: center; align-items: center;"></span></a></p>
                  </div>
              </div>
          </div>
      </form>
      </div>
      <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday';fnames[6]='COMPANY';ftypes[6]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script></div>


    <div class="container">
      <p>WatchTheAvs.com is an entertainment website, and should not be used as an official source for anything related to any of the leagues or teams represented.</p>
      <p>All names, logos, and likenesses are property of their respective owners and leages.</p>
      <p>&copy; <?php echo date("Y"); ?> <a href="https://codeandsons.com" target="_blank">Code & Sons</a>. All rights reserved.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></>
  </body>
</html>
