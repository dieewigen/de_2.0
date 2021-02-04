<?php
/*
if($ums_cooperation!=1){
  if(($ad_blocktime<time()-11000 OR time()-$ad_blocktime<30) AND !$_POST){
    //sponsorpay-werbung für spieler, die noch nie credits transferiert haben
    //geht nur auf einem freien server und der spieler sollte keinen PA haben, erstmal nur für deutsche server
    if($sv_payserver==0 AND $ums_premium==0 AND $ums_cooperation!=1 AND $sv_server_lang==1 AND $player_credittransfer==0 AND $_SESSION["ums_owner_id"]>0){
      echo '<a href="http://login.bgam.es/sponsorpay.php?uid='.$_SESSION["ums_owner_id"].'" target="_blank">
      <span style="position: relative; width:580px; height:45px; background-color: #FFFFFF; 
      border:1px solid #111111; display:inline-block; overflow:hidden; text-align:left; color: #111111;">
      
        <span style="position: absolute; left: 5px; top: 8px;font-size:24px;">
          Hole Dir kostenlose Credits &uuml;ber
          </span>
      
          <span style="position: absolute; left: 380px; top: 6px; width:200px; height:36px; display:inline-block; overflow:hidden; 
          background-image:url(https://login.bgam.es/img/sponsorpay_logo.png); background-repeat:no-repeat; text-align:left;">
          </span>
      
        </span>
        </a>
        <br>';
    }

    //votehinweis einbauen
    //platzdaten auslesen
    $votefilename="../div_server_data/votecron/votecron_lastday.txt";
    $fpvote = fopen($votefilename, "r");
    $vote_lastday=intval(trim(fgets($fpvote, 1024)));
    fclose($fpvote);
    
    $votefilename="../div_server_data/votecron/votecron_lasthour.txt";
    $fpvote = fopen($votefilename, "r");
    $vote_lasthour=intval(trim(fgets($fpvote, 1024)));
    fclose($fpvote);
    
    //tendenz bestimmen
    $vote_tendenz='gleichbleibend';
    if($vote_lastday<$vote_lasthour)$vote_tendenz='fallend';
    if($vote_lastday>$vote_lasthour)$vote_tendenz='steigend';
    
    //farben definieren
    if(date("n")%2==0)
    {
      $vote_color_border='#f3601b';
      $vote_color_background='#9c2f04';
      $vote_color_text='#ff7d14';  
    }
    else 
    {
      $vote_color_border='#5a2772';
      $vote_color_background='#3c0954';
      $vote_color_text='#ae6dd3';
    }
    
    echo '<span style="position: relative; width:580px; height:45px; background: '.$vote_color_background.'; border:1px solid '.$vote_color_border.'; display:inline-block; overflow:hidden; text-align:left; color: '.$vote_color_text.';">
    
    <span style="position: absolute; left: 5px; top: 3px;font-size:10px;">
    Platz heute: '.$vote_lasthour.'<br>
    Platz gestern: '.$vote_lastday.'<br>
    Tendenz: '.$vote_tendenz.'
    </span>
    
    <span style="position: absolute; left: 256px; top: 6px;">

    <span style="width:88px; height:31px; display:inline-block; overflow:hidden; background-image:url(https://www.browsergames.info/images/bgbutton.png); background-repeat:no-repeat; text-align:left;"><a href="https://www.browsergames.info" target="_blank" style="width:87px; height:27px; display:inline-block; margin: 2px 0 0 0; font-family:Arial,sans-serif; font-size:11px; font-weight:bold; letter-spacing:0px; color:#ffffff; text-decoration:none; text-align:center;">browsergames</a></span>    


    </span>
    
    <span style="position: absolute; left: 420px; top: 8px;font-size:10px;">
    Bitte stimme t&auml;glich ab, damit wir dadurch neue Spieler gewinnen.
    </span>
    
    </span><br><br>';
    
    echo '&nbsp;<a href="http://www.galaxy-news.de/?page=charts&op=vote&game_id=108" target="_blank"><img src="https://grafik-de.bgam.es/b/gn_vote.gif" border=0></a>';
    //echo '&nbsp;<a href="http://www.gamingfacts.de/charts.php?was=abstimmen2&spielstimme=75" target="_blank"><img src="http://grafik-de.bgam.es/b/gamingfacts_charts.gif" border="0"></a>';

    echo '<br><br>';
    
    
    $md_sec=$ad_blocktime+30-time();
    if($md_sec<0)$md_sec=30;
    if($ad_blocktime<time()-30)mysql_query("UPDATE de_login SET blocktime=".time()." WHERE user_id='$ums_user_id'", $db);

  }
}
*/
?>
