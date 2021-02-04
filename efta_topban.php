<?php
//fix um die werbung von der botabfrage unabhängig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;
include 'inc/header.inc.php';
?>
<html>
<head>
<title>AFrame</title>
<?php
$eftacss=1;
include 'cssinclude.php';
?>
</head>
<body>
<?php
/*
//votebutton
$topban_votebutton[0]='&nbsp;<a href="http://www.galaxy-news.de/?page=charts&op=vote&game_id=108" target="_blank"><img src="http://grafik-de.bgam.es/b/gn_vote.gif" border=0></a>';
$topban_votebutton[1]='&nbsp;<a href="http://www.gamingfacts.de/charts.php?was=abstimmen2&spielstimme=75" target="_blank"><img src="http://grafik-de.bgam.es/b/gamingfacts_charts.gif" border="0"></a>';
//$topban_votebutton[2]='&nbsp;<a href="http://www.rawnews.de/index.php?pg=charts&at=vote&game_id=30" target="_blank"><img src="http://www.rawnews.de/vote.php?img=vote&game_id=30" border="0"></a>';
echo '<font color="#FFFFFF" size="3">Bitte t&auml;glich voten.</font>'.$topban_votebutton[rand(0,count($topban_votebutton)-1)];

//werbung
if($ums_premium!=1 AND $ums_cooperation==0)
{
$format[0]="468x60"; $size[0][0]=468; $size[0][1]=60;
$welches_format=0;
?>
<iframe id="babyea2" name="babyea2" id='a50927b4' name='a50927b4' src="http://diener<?=rand(1,6)?>.garathor.com/unverdaechtig.php?format=<? echo $format[$welches_format]; ?>&ref=http://<?=$_SERVER[SERVER_NAME]?><?=$_SERVER[SCRIPT_NAME]?>&usid=<?=$REMOTE_ADDR?>"
framespacing='0' frameborder='no' scrolling='no' width='<? echo $size[$welches_format][0]; ?>'
height='<? echo $size[$welches_format][1]; ?>'></iframe>

<?php
}
*/
?>
</body>
</html>