<?php
include "topban.php";

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

$credits=$rowresline["credits"];
$ehscore=$rowresline["ehscore"];
$tradescore=$rowresline["tradesystemscore"];
$pve_score=$rowresline["pve_score"];
$player_credittransfer=$rowresline['credittransfer'];
$show_helper=$rowresline['helper'];
$helper_progress=$rowresline['helperprogress'];
//$helper_techs=$rowresline['techs'];
$helper_col=$rowresline['col'];
$resline_dailyallygift=$rowresline['dailyallygift'];
$show_trade_reminder=$rowresline['trade_reminder'];

//altes mouseoversystem
/*
echo '<SCRIPT language="JavaScript1.2" src="efta_ttip.js" type="text/javascript""></SCRIPT>';
echo '<DIV id="tiplayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100px;"></DIV>';
echo '<SCRIPT language="JavaScript1.2">';
echo 'Style[0]=["","","","","",,"#FFFFFF","#222222","","","",,400,,2,"#111111",2,24,0.5,0,2,"gray",,2,,13];';
// these are global settings
echo 'var TipId="tiplayer";'; // should be the same as <div> tag's id
echo 'var FiltersEnabled = 0;'; // should be the set as to 1 if your going to use visual effects if not set to 0
echo 'mig_clay();';
echo '</SCRIPT>';
*/

//$_SESSION['ums_mobi']=0;

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// menübutton für die mobile version
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if($_SESSION['ums_mobi']==1){
	if($_COOKIE['deactivate_swipe']==1){
		echo '<a href="menu.php"><div class="mobilebtn" style="margin-bottom: 5px; width: 600px; margin-top: 5px;">Men&uuml;</div></a>';
	}else{
		echo '<a href="menu.php"><div class="mobilebtn" style="margin-bottom: 5px; width: 600px; margin-top: 5px;">Men&uuml;: links -> rechts wischen, oder ber&uuml;hren. Chat: rechts -> links wischen</div></a>';
	}
	

	if($_COOKIE['deactivate_swipe']==1){
		$deactivate_touch_menu=1;
	}

	//test ob Touch-Gesten generell deaktiviert sind
	if($deactivate_touch_menu!=1){
?>
<script type="text/javascript">
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
	echo '<div id="resclock1" rel="tooltip" title="'.$resline_lang['restipservertime'].'<br>'.$resline_lang['restipservertimedesc'].'">'.date("H:m").'</div>';
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

	//credits
	echo '<div id="rescredits" rel="tooltip" title="'.$resline_lang['restipcredits'].'<br>'.$resline_lang['restipcreditsdesc'].'">'.number_format($credits, 0,"",".").'</div>';

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


	
	/*
	echo '$("#tb_res1", parent.document).html("'.number_format(floor($restyp01), 0,"",".").'");';
	echo '$("#tb_res2", parent.document).html("'.number_format(floor($restyp02), 0,"",".").'");';
	echo '$("#tb_res3", parent.document).html("'.number_format(floor($restyp03), 0,"",".").'");';
	echo '$("#tb_res4", parent.document).html("'.number_format(floor($restyp04), 0,"",".").'");';
	echo '$("#tb_res5", parent.document).html("'.number_format(floor($restyp05), 0,"",".").'");';
	*/

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

	//Credits
	echo '$("#tb_credits", parent.document).html("'.formatMasseinheit($credits).'");';	
	echo '$("#tb_credits", parent.document).attr("title", "'.number_format(floor($credits), 0,"",".").'");';
	
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
	echo '$("#tb_time1", parent.document).html("'.strftime("%H:%M").'");';
	echo '$("#tb_time2", parent.document).html("'.$lasttick.'");';
	echo '$("#tb_time3", parent.document).html("'.$lastmtick.'");';
	
	/*
	echo '$("#tb_res1", parent.document).attr("title","'.number_format(floor($restyp01)).'");';	
	echo '$("#tb_res2", parent.document).attr("title","'.number_format(floor($restyp02)).'");';	
	echo '$("#tb_res3", parent.document).attr("title","'.number_format(floor($restyp03)).'");';	
	echo '$("#tb_res4", parent.document).attr("title","'.number_format(floor($restyp04)).'");';	
	echo '$("#tb_res5", parent.document).attr("title","'.number_format(floor($restyp05)).'");';	
	*/
	
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
/////////////////////////////////////////////////////////////
// check für ungelesene sektorforumthreads
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/*
$likestr='s__________';
$likestr[$system]=0;
$db_daten=mysql_query("SELECT id FROM de_sectorforum_threads WHERE sector='$sector' AND gelesen LIKE '$likestr'");
$num = mysql_num_rows($db_daten);
if($num>0)
{
	echo '<div class="info_box text1" style="margin-bottom: 5px; font-size: 14px;">Es liegen ungelesene Beitr�ge im <a href="secforum.php">Sektorforum</a> vor.</div>';
}
*/

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// helper
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if($show_helper==1){
	include 'lib/helper.inc.php';
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// info über schwarzmarktcredit
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if(isset($show_activetime_msg) && $show_activetime_msg==1){
  $nexttime=time()+$sv_activetime;
  echo '<div class="info_box"><span class="text3">'.$resline_lang['getcredit'].'';
  echo  ' '.$resline_lang['getnextcredit'].date("G:i:s d.m.Y", $nexttime);
  
  echo '</span></div><br>';
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// schwarzmarktreminder
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

include "inc/sm_reminder.inc.php";

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// Mission-Reminder
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
if($show_trade_reminder==1 && !$flag_ang_big_iframe){
	include "lib/trade_reminder.inc.php";
}

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
// bannerunterbrechung
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

include "bannerunterbrechung.php";

/*
if($ums_one_way_bot_protection==1)
echo '<script language="JavaScript">
<!--
alert(unescape"'.$resline_lang[botlogout].'"));
//-->
</script>';
*/

//temporärer hinweis, dass man seinen account, mit dem hauptaccount verknüpfen soll
/*
$db_daten=mysql_query("SELECT user_id FROM de_login WHERE owner_id=0 AND user_id='$ums_user_id'",$db);
if(mysql_num_rows($db_daten)>0)
echo '<table width=590><tr><td class="ccg">
Der Account ist noch nicht mit der zentralen Accountverwaltung unter <a href="http://login.die-ewigen.com" target="_blank">http://login.die-ewigen.com
</a> verbunden. Siehe auch die Information im <a href="http://forum.die-ewigen.com/thread.php?threadid=17325" target="_blank">Forum</a>. Ben�tigt wird 
nur ein Hauptaccount<br><br><br><br>Wenn bis zum 15.06.2007 der Account nicht mit der zentralen Accountverwaltung verbunden ist, kann nicht garantiert werden,
dass der Zugriff auf den Account weiterhin m�glich sein wird.
</td></tr></table><br><br><br><br>';*/

//seite deaktivieren, wenn es ein payserver ist und man keinen pa hat
if($sv_payserver==1)
{
  $posps = strpos($PHP_SELF, "options.php");
  if($posps === false)
  {
    //laufzeit auslesen
    $db_datenps=mysql_query("SELECT patime FROM de_user_data WHERE user_id='$ums_user_id'",$db);	
    $rowps = mysql_fetch_array($db_datenps);
    if($rowps["patime"]<time())
    {
      echo '<table width="590px"><tr><td class="ccg">'.$resline_lang['payserver'].'</td></tr></table>';
      die('</body></html>');
    }
  }
}

//echo '<br><div class="info_box text2">Executor Karlath hat den DX61a23 den Krieg erkl&auml;rt.</div><br>';

//Seite schließen/Seitenname ANG
if($GLOBALS['sv_ang']==1 && $_SESSION['ums_mobi']!=1 && $_SESSION['de_frameset']!=1){
	if(!$flag_ang_big_iframe){

		echo '<div style="height: 10px;"></div>';

		/*
		echo '
		<div id="page_topbar" style="position: relative; width: 590px; margin-top: 6px; font-size: 16px; margin-bottom: 8px; border-radius: 10px; height: 20px; border: 1px solid #CCCCCC; color: #FFFFFF;" class="cell">
			<span id="page_topbar_text">Seitentitel</span>
			<img onclick="closeIframeMain();" src="g/close_icon.png" style="position: absolute; right: 1px; height: 26px; margin-top: -3px; width: auto;">
		</div>';
	
		echo '
		<script type="text/javascript">
		function closeIframeMain(){
			$("#iframe_main_container", parent.document).css("display", "none");
		}

		$( document ).ready(function() {
			$("#iframe_main_container", parent.document).css("display", "");
			$("#page_topbar_text").html(document.title);
		});

		</script>
		';

		*/
	}
}

////////////////////////////////////////////////////////////////////////
// User-Counter
////////////////////////////////////////////////////////////////////////
//if($_SESSION['ums_user_id']==1){
/*
	echo '
	<div style="width: 500px; background-color: #333333; color: #EEEEEE; padding: 5px; border: 1px solid #999999; font-size: 20px; margin-top: 10px; margin-bottom: 10px;">
		<div style="display: flex">
			<div style="flex-grow: 1;">135,57 von 145 Euro (2021)</div>
			<div style="flex-grow: 1;"><a style="font-size: 20px;" href="https://paypal.me/pools/c/8vL9wuicz9" target="_blank">Spendenseite</a></div>
			<div style="flex-grow: 1; cursor: pointer;" onclick="$(\'#sf_desc\').show();">?</div>
		</div>
		<div style="font-size: 10pt;">Du meinst Du kannst DE helfen? Melde Dich einfach per <a href="mailto:'.$GLOBALS['env_admin_email'].'">E-Mail</a> oder im <a href="https://discord.gg/qBpCPx4" target="_blank">Discord</a>.</div>
		<div id="sf_desc" style="display: none; margin-top: 20px; font-size: 16px;">
			Die Ewigen kostet ca. 145 Euro pro Jahr f&uuml;r Domains/Hosting/Server. Eine Beteiligung der Spieler an den Kosten w&auml;re begr&uuml;&szlig;enswert. Aktuell wird f&uuml;r das Jahr 2021 gesammelt.
		</div>
	</div>';
*/

	/*
	$db_daten_uc=mysqli_query($GLOBALS['dbi'], "SELECT anzahl FROM loginsystem.ls_user_count ORDER BY datum DESC LIMIT 1;");
	$row_uc = mysqli_fetch_array($db_daten_uc);
	$anzahl_uc=$row_uc["anzahl"];
	$anzahl_db=$anzahl_uc;
	//01.12.2019: 1547251200

	if($anzahl_uc<100){
		$anzahl_uc=100;
	}

	$target_time=1575158400+(86400*3*($anzahl_uc-100));

	$db_daten_forum=mysqli_query($GLOBALS['dbi'], "SELECT * FROM deforum_de.bb1_posts WHERE threadid=23437 ORDER BY postid DESC LIMIT 1;");
	$row_forum = mysqli_fetch_array($db_daten_forum);
	$forum_output='<div><a href="https://forum.bgam.es/thread.php?goto=lastpost&threadid=23437" target="_blank">Diskussion dazu im Forum - letzter Beitrag: '.date("d.m.Y H:i:s", $row_forum['posttime']).': '.$row_forum['username'].'</a></div>';

	echo '
	<div style="width: 500px; background-color: #333333; color: #EEEEEE; padding: 5px; border: 1px solid #999999; font-size: 20px; margin-top: 10px; margin-bottom: 10px;">
		<div style="display: flex">
			<div style="flex-grow: 1;">Spieler: '.$anzahl_db.'</div>
			<div style="flex-grow: 1;">'.date('d.m.Y', $target_time).'</div>
			<div style="width: 230px;" id="sfj12"></div>
			<div style="flex-grow: 1; cursor: pointer;" onclick="$(\'#sf_desc\').show();">?</div>
		</div>
		'.$forum_output.'
		<div style="font-size: 10pt;">Du meinst Du kannst DE helfen? Melde Dich einfach per <a href="mailto:'.$GLOBALS['env_admin_email'].'">E-Mail</a> oder im <a href="https://discord.gg/qBpCPx4" target="_blank">Discord</a>.</div>
		<div id="sf_desc" style="display: none; margin-top: 20px; font-size: 16px;">
			Der Counter l&auml;uft mindestens bis zum 01.12.2019. Für jeden Spieler über 100 verl&auml;ngert sich der Counter um 3 Tage. Sollten die Spielerzahlen stark steigen, kann der Counter wieder entfernt werden.
		</div>
	</div>

	<script>';

	echo '
	var countDownDate = new Date("'.date('c', $target_time) .'").getTime();
	
	var x = setInterval(function() {
	
	  var now = new Date().getTime();
		
	  var distance = countDownDate - now;
		
	  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		
	  document.getElementById("sfj12").innerHTML = days + "T " + hours + "Std "
	  + minutes + "Min " + seconds + "Sek ";
		
	  if (distance < 0) {
		clearInterval(x);
		document.getElementById("sfj12").innerHTML = "ENDE";
	  }
	}, 1000);
	</script>	

	';
	*/
//}
?>