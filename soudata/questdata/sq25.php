<?php
//Datenfragment J4F8S5
include "soudata/questdata/questname".$quest_id.".php";

//questdaten laden
$db_daten=mysql_query("SELECT * FROM sou_frac_quests WHERE fraction='$player_fraction' AND id='$quest_id'",$soudb);
$rowquest = mysql_fetch_array($db_daten);
$questlevel=$rowquest["questlevel"];

//überprüfen ob die forschung 60011 abgeschlossen ist
$db_daten=mysql_query("SELECT f".$player_fraction."lvl AS wert FROM `sou_frac_techs` WHERE tech_id=60011",$soudb);

$row = mysql_fetch_array($db_daten);
$erforscht=$row["wert"];

if($erforscht>0)
{
	//es sind alle voraussetzungen erfüllt
	//questdatensatz locken
	$result = mysql_query("UPDATE sou_frac_quests SET aupdate=1 WHERE aupdate=0 AND id='$quest_id' AND fraction='$player_fraction'", $soudb);
	$num = mysql_affected_rows();
    if($num==1)
    {
		//sektor 0:0 sichtbar machen
        $time=time();
        mysql_query("INSERT INTO sou_map_known (fraction, x, y, expltime) VALUES ($player_fraction, '0', '0', '$time')",$soudb);
   	
        $done=1;
          
        $quest_text='Das Datenfragment wurde untersucht und in der Fraktionsdatenbank wurden Kartendaten f&uuml;r Sektor 0:0 hinterlegt.';
        $quest_text.=' Die Aufgabe wurde vollst&auml;ndg abgeschlossen.';
          
        $chat_text='Fraktion '.$player_fraction.' hat folgende Aufgabe abgeschlossen: '.$questname;
        $chat_text.=' In der Fraktionsdatenbank wurden Kartendaten f&uuml;r Sektor 0:0 hinterlegt.';
                  		
        //nachricht für den chat
      	$time=time();
        insert_chat_msg('Der Reporter', '<font color="#00FF00">'.$chat_text.'</font>', 0, 0);
		
        //datensatz wieder freigeben und updaten
        mysql_query("UPDATE sou_frac_quests SET questlevel=1, done='$done', aupdate=0 WHERE id='$quest_id' AND fraction='$player_fraction'", $soudb);
    }	  
	else $quest_text='<font color="FF0000">Die Koordinaten stimmen nicht &uuml;berein.';
}
else $quest_text='<font color="FF0000">Das Datenfragment wurde noch nicht erforscht.';

/*
INSERT INTO sou_frac_quests SET id=25, questlevel=0, fraction=1, x=0, y = 1500;
INSERT INTO sou_frac_quests SET id=25, questlevel=0, fraction=2, x= 1245, y = 825;
INSERT INTO sou_frac_quests SET id=25, questlevel=0, fraction=3, x=1245, y = -825;
INSERT INTO sou_frac_quests SET id=25, questlevel=0, fraction=4, x=0, y = -1500;
INSERT INTO sou_frac_quests SET id=25, questlevel=0, fraction=5, x= -1245, y = -825;
INSERT INTO sou_frac_quests SET id=25, questlevel=0, fraction=6, x= -1245, y = 825;
*/

?>