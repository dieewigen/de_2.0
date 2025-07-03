<?php
include 'inc/lang/'.$sv_server_lang.'_resline.lang.php';

//transparenz setzen, wenn sie von 100 abweicht
if($ums_transparency!=100){
echo '<style type="text/css">
td,c,cl,cc,ccg,ccr,ccy,cr,tl,tc,tr,cell,cell1{filter:alpha(opacity='.$ums_transparency.');-moz-opacity: 0.'.$ums_transparency.';}
</style>';
}

//tickzeiten laden
include "cache/lasttick.tmp";
include "cache/lastmtick.tmp";

$mes1='';
$mes2='';
if($newtrans==1){
	$mes1='<a href="hyperfunk.php?l=new">'.$resline_lang['hyperfunk'].'</a>';
}
if($newnews==1){
	$mes2='<a href="sysnews.php">'.$resline_lang['nachricht'].'</a>';
}


if($_SESSION['ums_vote']=="1"){

	echo '<br><div class="info_box"><span class="text3">'.$resline_lang['vote'].'</span></div><br>';

	include("vote.php");
	exit();
}

$flotten=mysql_query("SELECT aktion, fleetsize FROM de_user_fleet WHERE zielsec = '$sector' AND zielsys = '$system' AND (aktion = 1 OR aktion = 2) AND entdeckt > 0",$db);
$fa = mysql_num_rows($flotten);
$gea=0;
$gev=0;
$geaflag=0;
$gevflag=0;

for ($i=0; $i<$fa; $i++)
{
  $akt=mysql_result($flotten, $i, "aktion");

  if ($akt==1)//angreifer
  {
    $erg=mysql_result($flotten, $i, "fleetsize");
    $gea=$gea+$erg;
    $geaflag=1;
  }
  elseif ($akt==2)//verteidiger
  {
    $erg=mysql_result($flotten, $i, "fleetsize");
    $gev=$gev+$erg;
    $gevflag=1;
  }
  //einheiten z&auml;hlen

}

//Flottenzahen formatieren
if($geaflag==0){
	$gea_desktop_long='';
	$gea_desktop_short='&nbsp;';
	$gea='&nbsp;';
}else{
	$gea_desktop_long=$gea;
	$gea_desktop_short=formatMasseinheit($gea);
	$gea=number_format($gea, 0,"",".");
}

if($gevflag==0){
	$gev_desktop_long='';
	$gev_desktop_short='&nbsp;';
	$gev='&nbsp;';
}else{
	$gev_desktop_long=$gev;
	$gev_desktop_short=formatMasseinheit($gev);	
	$gev=number_format($gev, 0,"",".");
}


if(!isset($flag_ang_big_iframe)){
	$flag_ang_big_iframe=false;
}

if(!$flag_ang_big_iframe){
	echo '<div align="center">';
}



///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
// die resline darstellen
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

//resline-daten laden
$db_datenresline=mysql_query("SELECT * FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$rowresline = mysql_fetch_array($db_datenresline);

$ehscore=$rowresline["ehscore"];
$tradescore=$rowresline["tradesystemscore"];
$pve_score=$rowresline["pve_score"];
$show_helper=$rowresline['helper'];
$helper_progress=$rowresline['helperprogress'];
//$helper_techs=$rowresline['techs'];
$helper_col=$rowresline['col'];
$resline_dailyallygift=$rowresline['dailyallygift'];
$show_trade_reminder=$rowresline['trade_reminder'];

/////////////////////////////////////////////////////////////
// menübutton für die mobile version
/////////////////////////////////////////////////////////////
$deactivate_touch_menu=0;
if($_SESSION['ums_mobi']==1){
	if(isset($_COOKIE['deactivate_swipe']) && $_COOKIE['deactivate_swipe']==1){
		echo '<a href="menu.php"><div class="mobilebtn" style="margin-bottom: 5px; width: 600px; margin-top: 5px;">Men&uuml;</div></a>';
	}else{
		echo '<a href="menu.php"><div class="mobilebtn" style="margin-bottom: 5px; width: 600px; margin-top: 5px;">Men&uuml;: links -> rechts wischen, oder ber&uuml;hren. Chat: rechts -> links wischen</div></a>';
	}
	

	if(isset($_COOKIE['deactivate_swipe']) && $_COOKIE['deactivate_swipe']==1){
		$deactivate_touch_menu=1;
	}

	//test ob Touch-Gesten generell deaktiviert sind
	if($deactivate_touch_menu!=1){
?>
<script>
function swipedetect(el, callback){
  
    var touchsurface = el,
    swipedir,
    startX,
    startY,
    distX,
    distY,
    threshold = 150, //required min distance traveled to be considered swipe
    restraint = 100, // maximum distance allowed at the same time in perpendicular direction
    allowedTime = 300, // maximum time allowed to travel that distance
    elapsedTime,
    startTime,
    handleswipe = callback || function(swipedir){}
  
    touchsurface.addEventListener('touchstart', function(e){
        var touchobj = e.changedTouches[0]
        swipedir = 'none'
        dist = 0
        startX = touchobj.pageX
        startY = touchobj.pageY
        startTime = new Date().getTime() // record time when finger first makes contact with surface
        //e.preventDefault()
    }, false)
  
    touchsurface.addEventListener('touchmove', function(e){
        //e.preventDefault() // prevent scrolling when inside DIV
    }, false)
  
    touchsurface.addEventListener('touchend', function(e){
        var touchobj = e.changedTouches[0]
        distX = touchobj.pageX - startX // get horizontal dist traveled by finger while in contact with surface
        distY = touchobj.pageY - startY // get vertical dist traveled by finger while in contact with surface
        elapsedTime = new Date().getTime() - startTime // get time elapsed
        if (elapsedTime <= allowedTime){ // first condition for awipe met
            if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint){ // 2nd condition for horizontal swipe met
                swipedir = (distX < 0)? 'left' : 'right' // if dist traveled is negative, it indicates left swipe
            }
            else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint){ // 2nd condition for vertical swipe met
                swipedir = (distY < 0)? 'up' : 'down' // if dist traveled is negative, it indicates up swipe
            }
        }
        handleswipe(swipedir)
        //e.preventDefault()
    }, false)
}
  
document.addEventListener('DOMContentLoaded', function() {
	var el = document;//getElementById('document.body')
	swipedetect(el, function(swipedir){
		//swipedir contains either "none", "left", "right", "top", or "down"
		if (swipedir =='right'){
			document.location.href='menu.php';
		}
		
		if (swipedir =='left'){
			document.location.href='chat.php';
		}	
	});
}, false);
</script>
<?php	
	}
}

if(!isset($_SESSION['de_frameset'])){
	$_SESSION['de_frameset']=0;
}

if($_SESSION['ums_mobi']==1 || $_SESSION['de_frameset']==1){
	//hauptgrafik laden
	echo '<div id="resmain" align="center">';
	//rohstoffe
	echo '<div id="restyp1" rel="tooltip" title="'.$resline_lang['restipres01'].'<br>'.$resline_lang['restipres01desc'].'">'.number_format(floor($restyp01), 0,"",".").'</div>';
	echo '<div id="restyp2" rel="tooltip" title="'.$resline_lang['restipres02'].'<br>'.$resline_lang['restipres02desc'].'">'.number_format(floor($restyp02), 0,"",".").'</div>';
	echo '<div id="restyp3" rel="tooltip" title="'.$resline_lang['restipres03'].'<br>'.$resline_lang['restipres03desc'].'">'.number_format(floor($restyp03), 0,"",".").'</div>';
	echo '<div id="restyp4" rel="tooltip" title="'.$resline_lang['restipres04'].'<br>'.$resline_lang['restipres04desc'].'">'.number_format(floor($restyp04), 0,"",".").'</div>';
	echo '<div id="restyp5" rel="tooltip" title="'.$resline_lang['restipres05'].'<br>'.$resline_lang['restipres05desc'].'">'.number_format(floor($restyp05), 0,"",".").'</div>';

	//uhr
	echo '<div id="resclock1" rel="tooltip" title="'.$resline_lang['restipservertime'].'<br>'.$resline_lang['restipservertimedesc'].'">'.date("H:i").'</div>';
	echo '<div id="resclock2" rel="tooltip" title="'.$resline_lang['restipserveretick'].'<br>'.$resline_lang['restipserveretickdesc'].'">'.$lasttick.'</div>';
	echo '<div id="resclock3" rel="tooltip" title="'.$resline_lang['restipservermtick'].'<br>'.$resline_lang['restipservermtickdesc'].'">'.$lastmtick.'</div>';

	//anzahl atter/deffer
	//$gevflag=1000000;$gev=1000000;$geaflag=1000000;$gea=1000000;

	if ($gevflag>0)//es gibt deffer
	{
	  //led
	  echo '<div id="resleddef"></div>';
	  //schiffe
	  echo '<div id="resdef" rel="tooltip" title="'.$resline_lang['restipdeffer'].'<br>'.$resline_lang['restipdefferdesc'].'">'.$gev.'</div>';
	}

	if ($geaflag>0)//es gibt deffer
	{
	  //led
	  echo '<div id="resledatt"></div>';
	  //schiffe
	  echo '<div id="resatt" rel="tooltip" title="'.$resline_lang['restipatter'].'<br>'.$resline_lang['restipatterdesc'].'">'.$gea.'</div>';
	}

	//punkte
	echo '<div id="resscore" rel="tooltip" title="'.$resline_lang['restipscore'].'<br>'.$resline_lang['restipscoredesc'].'<br>'.$resline_lang['restipehscore'].': '.number_format($ehscore, 0,"",".").'<br>Executor-Punkte: '.number_format($pve_score, 0,"",".").'">'.number_format($punkte, 0,"",".").'</div>';

	//hyperfunk
	//$newtrans=1;
	if ($newtrans==1){
		echo '<a href="hyperfunk.php?l=new" rel="tooltip" title="'.$resline_lang['restipnewhyper'].'<br>'.$resline_lang['restipnewhyperdesc'].'"><div id="reshyper"></div></a>';
	}

	//nachrichten
	//$newnews=1;
	if ($newnews==1){
		echo '<a href="sysnews.php" rel="tooltip" title="'.$resline_lang['restipnewnews'].'<br>'.$resline_lang['restipnewnewsdesc'].'"><div id="resnews"></div></a>';
	}

	//dailyallygif
	if($resline_dailyallygift==1){
		echo '<div id="resdailyallygift"><a href="ally_dailygift.php" title="'.$resline_lang['dailyallygift'].'<br>'.$resline_lang['dailyallygiftdesc'].'">
		<img src="'.$ums_gpfad.'g/symbol1.png" width="100%" height="100%"></a></div>';
	}
	echo '</div>';

	echo '<br>';
}else{

	//rohstoffe
	echo '<script type="text/javascript">';
	echo '$("#tb_res1", parent.document).html("'.formatMasseinheit(floor($restyp01)).'");';
	echo '$("#tb_res2", parent.document).html("'.formatMasseinheit(floor($restyp02)).'");';
	echo '$("#tb_res3", parent.document).html("'.formatMasseinheit(floor($restyp03)).'");';
	echo '$("#tb_res4", parent.document).html("'.formatMasseinheit(floor($restyp04)).'");';
	echo '$("#tb_res5", parent.document).html("'.formatMasseinheit(floor($restyp05)).'");';

	echo '$("#tb_res1", parent.document).attr("title", "'.number_format(floor($restyp01), 0,"",".").'");';
	echo '$("#tb_res2", parent.document).attr("title", "'.number_format(floor($restyp02), 0,"",".").'");';
	echo '$("#tb_res3", parent.document).attr("title", "'.number_format(floor($restyp03), 0,"",".").'");';
	echo '$("#tb_res4", parent.document).attr("title", "'.number_format(floor($restyp04), 0,"",".").'");';
	echo '$("#tb_res5", parent.document).attr("title", "'.number_format(floor($restyp05), 0,"",".").'");';

	//atter/deffer
	if ($gevflag>0){//es gibt deffer
		echo '$("#tb_deffer_img", parent.document).css("display","");';
		echo '$("#tb_deffer", parent.document).css("display","");';
		echo '$("#tb_deffer", parent.document).html("'.$gev_desktop_short.'");';
		echo '$("#tb_deffer", parent.document).attr("title", "'.number_format(floor($gev_desktop_long), 0,"",".").'");';

		echo '$("#tb_deffer_img_grey", parent.document).css("display","none");';

	}else{//es gibt keine Deffer
		echo '$("#tb_deffer_img_grey", parent.document).css("display","");';
		echo '$("#tb_deffer", parent.document).css("display","none");';
		echo '$("#tb_deffer_img", parent.document).css("display","none");';
	}


	if ($geaflag>0){//es gibt atter
		echo '$("#tb_atter_img", parent.document).css("display","");';
		echo '$("#tb_atter", parent.document).css("display","");';
		echo '$("#tb_atter", parent.document).html("'.$gea_desktop_short.'");';
		echo '$("#tb_atter", parent.document).attr("title", "'.number_format(floor($gea_desktop_long), 0,"",".").'");';
		
		echo '$("#tb_atter_img_grey", parent.document).css("display","none");';

	}else{//es gibt keine Atter
		echo '$("#tb_atter_img_grey", parent.document).css("display","");';
		echo '$("#tb_atter", parent.document).css("display","none");';
		
		echo '$("#tb_atter_img", parent.document).css("display","none");';
	}
	
	//Punkte
	echo '$("#tb_score_img", parent.document).attr("title","'.$resline_lang['restipscoredesc'].'<br>'.$resline_lang['restipehscore'].': '.number_format($ehscore, 0,"",".").'<br>Executor-Punkte: '.formatMasseinheit($pve_score).'");';
	echo '$("#tb_score", parent.document).html("'.formatMasseinheit($punkte).'");';
	echo '$("#tb_score", parent.document).attr("title", "'.number_format(floor($punkte), 0,"",".").'");';

	//hyperfunk
	//$newtrans=1;
	if ($newtrans==1){
		echo '$("#tb_hyper_img", parent.document).css("display","");';
	}else{
		echo '$("#tb_hyper_img", parent.document).css("display","none");';
	}

	//nachrichten
	//$newnews=1;
	if ($newnews==1){
		echo '$("#tb_news_img", parent.document).css("display","");';
	}else{
		echo '$("#tb_news_img", parent.document).css("display","none");';
	}

	//dailyallygif
	if($resline_dailyallygift==1){
		echo '$("#tb_daily_img", parent.document).css("display","");';
	}else{
		echo '$("#tb_daily_img", parent.document).css("display","none");';
	}
	
	
	//serverzeiten
	echo '$("#tb_time1", parent.document).html("'.date("H:i").'");';
	echo '$("#tb_time2", parent.document).html("'.$lasttick.'");';
	echo '$("#tb_time3", parent.document).html("'.$lastmtick.'");';
	
	echo '</script>';
}

if(!isset($GLOBALS['deactivate_old_design'])){
	$GLOBALS['deactivate_old_design']=false;
}

if(!$flag_ang_big_iframe){
	if($GLOBALS['deactivate_old_design']!==true){
		echo '
		<script>
		$(document).ready(function () {
		$("div, span, img, a, tr").tooltip({ 
			track: true, 
			delay: 0, 
			showURL: false, 
			showBody: "&",
			extraClass: "design1", 
			fixPNG: true,
			opacity: 1.00,
			left: 0
		});
		});
		</script>';
	}
}

/////////////////////////////////////////////////////////////
// helper
/////////////////////////////////////////////////////////////
if($show_helper==1){
	include 'lib/helper.inc.php';
}

/////////////////////////////////////////////////////////////
// Mission-Reminder
/////////////////////////////////////////////////////////////
if($show_trade_reminder==1 && !$flag_ang_big_iframe){
	include "lib/trade_reminder.inc.php";
}

//Seite schließen/Seitenname ANG
if($GLOBALS['sv_ang']==1 && $_SESSION['ums_mobi']!=1 && $_SESSION['de_frameset']!=1){
	if(!$flag_ang_big_iframe){

		echo '<div style="height: 10px;"></div>';
	}
}

/////////////////////////////////////////////////////////////
// temporärer Hinweis
/////////////////////////////////////////////////////////////
echo '
<div class="info_box text3" style="margin-bottom: 5px; font-size: 14px;">
Die nächste xDE/SDE-Runde startet am 04.07.2025 um ca. 19:00 Uhr.<br><br>

Da es zwischenzeitlich die Info gab, dass DE eingestellt worden ist, bitte ich jeden von euch die Rundenstartinfo möglichst an jeden weiterzuleiten.
</div>';
