<?php
//gegenstand benutzen
if($_GET["b"]==1)
{
  $id=intval($_GET["id"]);
  $usesid=intval($_GET["usesid"]);
  $itemmsg='';
  //schauen ob er den gegenstand auch im rucksack hat
  $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' AND uses='$usesid'", $eftadb);
  $num = mysql_num_rows($result);
  if($num>0)
  {
    //daten des items laden
    $filename='eftadata/items/'.$id.'.item';
    include($filename);
    //nur bestimmte items sind benutzbar
    if($item_useable==1)
    {
      //schauen ob er die voraussetzungen erfüllt
      if($item_level<=$level)
      {
        //schauen was für ein typ es ist
        switch($item_typ){
        case 21: //trank
          //heilen  
          //4843
          if($item_healhpmin>0 OR $item_healhpmax>0)
          {
            $hpold=$hp;
            $hp+=mt_rand($item_healhpmin,$item_healhpmax);
            if($hp>$hpmax)$hp=$hpmax;
            //item entfernen
            mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
            //cyborg updaten
            mysql_query("UPDATE de_cyborg_data SET hp='$hp' WHERE user_id='$efta_user_id'", $eftadb);
            //nachricht
            $errmsg.='Du kippst dir den Trank hinter die Binde.<br>';
            if($hpold<$hp)$errmsg.='Deine Lebensenergie steigt von '.$hpold.' auf '.$hp.' von maximal '.$hpmax.'.';
            $errmsg="<div id=\"meldunguk\"><br><b class=\"text2\">".$errmsg."</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldunguk').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          }
          //trank der lebensenergiefestigkeit
          if($id==4871)  
          {
            //den buff hinterlegen
            $time=time()+3600;
            mysql_query("UPDATE de_cyborg_data SET buff1='$time' WHERE user_id='$efta_user_id'", $eftadb);
          	
          	//nachricht
            $errmsg.='Du w&uuml;rgst das eklige Ges&ouml;ff herunter und sp&uuml;rst eine unbenennbare Ver&auml;nderung in dir.';
            $errmsg="<div id=\"meldunguk\"><br><b class=\"text2\">".$errmsg."</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldunguk').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          	//item entfernen
            mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
          }
          //trank der rast
          if($id==4890)  
          {
            //die aktionspunkte erhöhen
            $bewpunkte=$bewpunkte+2500;
            if($bewpunkte>$sv_max_efta_bew_punkte)$bewpunkte=$sv_max_efta_bew_punkte;
            
            mysql_query("UPDATE de_cyborg_data SET bewpunkte='$bewpunkte' WHERE user_id='$efta_user_id'", $eftadb);
          	
          	//nachricht
            $errmsg.='Du trinkst den Trank und f&uuml;hlst dich ausgeruht.';
            $errmsg="<div id=\"meldunguk\"><br><b class=\"text2\">".$errmsg."</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldunguk').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
          	//item entfernen
            mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
          }          
        break;
        case 25: //weiteres item
          if($id==4868)//dornenschlinge
          {
          	//die daten des feldes auslesen auf dem man steht
            $eftadb_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
            $num = mysql_num_rows($eftadb_daten);
            if($num==1)
  			{
  			  
  			  $row = mysql_fetch_array($eftadb_daten);
  			  //schauen ob das feld auf dem man steht frei ist
  			  if($row["groundtyp"]==1 AND $row["groundpicext"]==0 AND $row["bldg"]==0 AND $row["bldgpic"]==0)
  			  {
  			  	//die schlingpflanze setzen
  			  	mysql_query("UPDATE de_cyborg_map SET bldg=18, bldgpic=34 WHERE x='$x' AND y='$y' AND z='$map'", $eftadb);
  			  	
          	    //item entfernen
                mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
                
                //nachricht ausgeben
                $itemmsg.='Du pflanzt den Samen ein.';
  			  }else $itemmsg.='Hier ist kein Platz um den Samen einzpflanzen.';
  			}else $itemmsg.='Hier ist kein Platz um den Samen einzpflanzen.';
          }
          
          if($id==4869)//wegebau
          {
          	//die daten des feldes auslesen auf dem man steht
            $eftadb_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
            $num = mysql_num_rows($eftadb_daten);
            if($num==1)
  			{
  			  
  			  $row = mysql_fetch_array($eftadb_daten);
  			  //schauen ob das feld auf dem man steht frei ist
  			  if($row["groundtyp"]==1 AND $row["groundpicext"]==0 AND $row["bldg"]==0 AND $row["bldgpic"]==0)
  			  {
  			  	//den weg bauen
  			  	mysql_query("UPDATE de_cyborg_map SET bldg=19, fieldlevel=1 WHERE x='$x' AND y='$y' AND z='$map'", $eftadb);
  			  	
          	    //item entfernen
                mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
                
                //nachricht ausgeben
                $itemmsg.='Du sorgst dafür, dass ein Weg angelegt wird.';
  			  }else $itemmsg.='Hier ist kein Platz um einen Weg anzulegen.';
  			}else $itemmsg.='Hier ist kein Platz um einen Weg anzulegen.';
          }

          if($id==4870)//straßenbau
          {
          	//die daten des feldes auslesen auf dem man steht
            $eftadb_daten=mysql_query("SELECT * FROM de_cyborg_map WHERE x='$x' AND y='$y' AND z='$map'",$eftadb);
            $num = mysql_num_rows($eftadb_daten);
            if($num==1)
  			{
  			  
  			  $row = mysql_fetch_array($eftadb_daten);
  			  //schauen ob das feld auf dem man steht bereits ein weg ist
  			  if($row["groundtyp"]==1 AND $row["groundpicext"]==0 AND $row["bldg"]==19 AND $row["bldgpic"]==0)
  			  {
  			  	//den weg bauen
  			  	mysql_query("UPDATE de_cyborg_map SET bldg=19, fieldlevel=2 WHERE x='$x' AND y='$y' AND z='$map'", $eftadb);
  			  	
          	    //item entfernen
                mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
                
                //nachricht ausgeben
                $itemmsg.='Du sorgst dafür, dass ein Weg zur Stra&szlig;e ausgebaut wird.';
  			  }else $itemmsg.='Um eine Stra&szlig;e zu bauen mu&szlig; sich an dem Ort bereits ein Weg befinden.';
  			}else $itemmsg.='Um eine Stra&szlig;e zu bauen mu&szlig; sich an dem Ort bereits ein Weg befinden.';
          }
          //dornschlingenätherisierer zusammensetzen
          if($id==4872 OR $id==4873 OR $id==4874)
          {
          	//überprüfen ob er alle teile hat
          	$num=0;
            $eftadb_daten=mysql_query("SELECT id FROM de_cyborg_item WHERE user_id='$efta_user_id' AND id='4872' LIMIT 1",$eftadb);
            $num += mysql_num_rows($eftadb_daten);
			$eftadb_daten=mysql_query("SELECT id FROM de_cyborg_item WHERE user_id='$efta_user_id' AND id='4873' LIMIT 1",$eftadb);
            $num += mysql_num_rows($eftadb_daten);
            $eftadb_daten=mysql_query("SELECT id FROM de_cyborg_item WHERE user_id='$efta_user_id' AND id='4874' LIMIT 1",$eftadb);
            $num += mysql_num_rows($eftadb_daten);
            if($num==3)//man hat alle teile
            {
              //die einzelnen teile löschen
              mysql_query("DELETE FROM de_cyborg_item WHERE id='4872' AND user_id='$efta_user_id' LIMIT 1", $eftadb);
              mysql_query("DELETE FROM de_cyborg_item WHERE id='4873' AND user_id='$efta_user_id' LIMIT 1", $eftadb);
              mysql_query("DELETE FROM de_cyborg_item WHERE id='4874' AND user_id='$efta_user_id' LIMIT 1", $eftadb);
              
              //das neue item hinterlegen
              //daten des items laden
              $item_id=4875;
              $filename='eftadata/items/'.$item_id.'.item';
    		  include($filename);
        	  mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability, uses) VALUES ('$efta_user_id', '$item_id', '$item_typ', '1', '$item_durability', '$item_uses')",$eftadb);
        	  //nachricht ausgeben
    		  $itemmsg.='Du setzt den Gegestand zusammen.';
              
            }
            else //man hat nicht alle teile
            {
              $itemmsg.='Du verf&uuml;gst nicht &uuml;ber alle ben&ouml;tigten Teile';
            }
          }
          if($id==4875)//dornschliengenätherisierer
          {
          	$itemmsg.='Du setzt den Gegenstand ein.';
          	//alle dornschlingen im umfeld entfernen
          	$x1=$x-2;
          	$x2=$x+2;
			$y1=$y-2;
          	$y2=$y+2;          	
          	mysql_query("UPDATE de_cyborg_map SET bldg=0, bldgpic=0, fieldlevel=0 WHERE x>='$x1' AND x<='$x2' AND y>='$y1'  AND y<='$y2' AND z='$map' AND bldg=18", $eftadb);
          	$num=mysql_affected_rows();
          	//erfahrungspunkte für den einsatz
          	$expgew=$num*5;
          	give_exp($expgew);
          	
          	//item entfernen oder benutzungen verringern
          	if($usesid>1)
          	{
          	  mysql_query("UPDATE de_cyborg_item SET uses=uses-1 WHERE id='4875' AND uses='$usesid' AND user_id='$efta_user_id' LIMIT 1", $eftadb);
          	}
          	else 
          	{
          	  mysql_query("DELETE FROM de_cyborg_item WHERE id='4875' AND uses=1 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
          	  $itemmsg.=' Der Gegenstand wurde zerst&ouml;rt.';
          	}
          	$itemmsg.='<br>Erfahrungspunkte: '.$expgew;
          }
          //rucksack
          if($id==4876 OR $id==4877 OR $id==4878 OR $id==4879 OR $id==4880 OR $id==4881 OR $id==4882 OR $id==4883 OR $id==4884 OR $id==4885 OR $id==4886 OR $id==4887 OR $id==4888 OR $id==4889)
          {
          	//größen definieren
          	if($id==4876)$newbackpacksize=11;
          	elseif($id==4877)$newbackpacksize=12;
          	elseif($id==4878)$newbackpacksize=13;
          	elseif($id==4879)$newbackpacksize=14;
          	elseif($id==4880)$newbackpacksize=15;
          	elseif($id==4881)$newbackpacksize=16;
          	elseif($id==4882)$newbackpacksize=17;
          	elseif($id==4883)$newbackpacksize=18;
          	elseif($id==4884)$newbackpacksize=19;
          	elseif($id==4885)$newbackpacksize=20;
          	elseif($id==4886)$newbackpacksize=21;
          	elseif($id==4887)$newbackpacksize=22;
          	elseif($id==4888)$newbackpacksize=23;
          	elseif($id==4889)$newbackpacksize=24;
          	//überprüfen ob er größer ist als der alte
			if($newbackpacksize>$maxbackpack)
			{
			  //rucksackgröße updaten
			  mysql_query("UPDATE de_cyborg_data SET backpacksize='$newbackpacksize' WHERE user_id='$efta_user_id'", $eftadb);
			  $maxbackpack=$newbackpacksize;
			  
			  //item entfernen
			  mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND user_id='$efta_user_id' LIMIT 1", $eftadb);
			  
			  $itemmsg='Du tauschst deinen Rucksack gegen ein besseres Modell aus. Dein alter Rucksack ist leider nicht mehr zu gebrauchen.';
			}
			else $itemmsg.='Du kannst den Rucksack nicht verwenden, da er nicht gr&ouml;&szlig;er als dein aktueller Rucksack ist.';
          }
          
        break;//case 25
        }//switch $itme_typ
      } else $errmsg="<div id=\"meldunguk\"><br><b class=\"text6\">Du erf&uuml;llst noch nicht die n&ouml;tigen Voraussetzungen.</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldunguk').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";

    }
  }
}

//besondere items nutzen
if($_GET["b"]==2)
{
  $sitid=(int)$_GET["sitid"];
  switch($sitid)
  {
  	case 1:
  	  //überprüfen ob es schon möglich ist
  	  if($player_cooldown1<time())
  	  {
  	  //überprüfen ob genug credits vorhanden sind
  	  $hascredits=has_credits($ums_user_id);
  	  if($hascredits>=1 OR $ums_premium==1)
  	  {
  	  	//credits abziehen, wenn kein pa
  	  	if($ums_premium!=1)change_credits($ums_user_id, -1, 'EFTA - Heimatkristall von Waldmond');
 	  	//cyborg verschieben und cooldown setzen
  	  	$time=time()+3600;
  	  	mysql_query("UPDATE de_cyborg_data SET x=0, y=0, map=0, cooldown1='$time' WHERE user_id='$efta_user_id'", $eftadb);
  	  	echo '<script>lnk("");</script>';
  	  	exit;
  	  }
	  else $errmsg="<div id=\"meldunguk\"><br><b class=\"text6\">Du hast nicht genug Credits.</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldunguk').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
  	  }
  	  else $errmsg="<div id=\"meldunguk\"><br><b class=\"text6\">Das kannst du noch nicht verwenden.</b><br><br>
            &nbsp;<a class=\"close\" href=\"#\" onClick=\"javascript:document.getElementById('meldunguk').style.visibility='hidden';\">schlie&szlig;en</a>&nbsp;</div>";
  	break;
  	
  }

}
//gegenstände ablegen
if($_GET["a"]==1)
{
  $id=(int)$_GET["id"];
  mysql_query("UPDATE de_cyborg_item SET equip=0 WHERE id='$id' AND equip=1 AND user_id='$efta_user_id'", $eftadb);
}

//gegenstände anlegen
if($_GET["a"]==2)
{
  $id=intval($_GET["id"]);
  $did=intval($_GET["did"]);
  //schauen ob er den gegenstand auch im rucksack hat
  $result = mysql_query("SELECT id FROM de_cyborg_item WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id'", $eftadb);
  $num = mysql_num_rows($result);
  if($num>0)
  {
    //daten des items laden
    $filename='eftadata/items/'.$id.'.item';
    include($filename);
    //nur bestimmte typen sind anlegbar
    if($item_typ==3 OR $item_typ==5 OR $item_typ==11 OR $item_typ==12 OR $item_typ==7 OR $item_typ==2 OR $item_typ==1)
    {
      //schauen ob er die voraussetzungen erfüllt
      if($item_level<=$level)
      {
        //wenn er schon was an hat, das ablegen
        mysql_query("UPDATE de_cyborg_item SET equip=0 WHERE typ='$item_typ' AND equip=1 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
        //jetzt das item anlegen
        mysql_query("UPDATE de_cyborg_item SET equip=1 WHERE id='$id' AND durability='$did' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
      } else $errmsg.='<font color="#FF0000">Du erf&uuml;llst noch nicht die n&ouml;tigen Voraussetzungen.<br><br></font>';
    }
  }
}

//gegenstand wegwerfen
if($_GET["drop"]==1)
{
  $id=(int)$_GET["id"];
  mysql_query("DELETE FROM de_cyborg_item WHERE id='$id' AND equip=0 AND user_id='$efta_user_id' LIMIT 1", $eftadb);
}

//ausrüstung auslesen und tooltip generieren
if($player_cooldown1<time()){$str1='N&auml;chste Verwendung: sofort';}
else {$str1='N&auml;chste Verwendung: in '.round(($player_cooldown1-time())/60).' min';}
$sit[0]='&Dieser Kristall kann dich zur&uuml;ck zur Stadt Waldmond transportieren. Er ben&ouml;tigt nach seiner Benutzung 60 Minuten um sich wieder aufzuladen. <br><br>Verwendungkosten:<br><font color=00FF00>F&uuml;r Premium-Account-Nutzer kostenlos, ansonsten 1 Credit.</font><br><br>'.$str1;

//zuerst items laden, was man trägt
for($i=0;$i<=11;$i++)$body[$i][0]='-';
$result = mysql_query("SELECT id, durability, uses FROM de_cyborg_item WHERE equip=1 AND user_id='$efta_user_id'", $eftadb);
$cyborg_armor=0;
$cyborg_mindmg=0;
$cyborg_maxdmg=0;
while($row = mysql_fetch_array($result))
{
  //erstmal alles nullen
  $item_armor=0;
  $item_durability=0;
  $item_mindmg=0;
  $item_maxdmg=0;
  $item_level=0;
  $item_desc='';

  $itemid=$row["id"];
  $durability=$row["durability"];
  $filename='eftadata/items/'.$itemid.'.item';
  include($filename);
  //werte zusammenzählen
  /*$cyborg_armor+=$item_armor;
  $cyborg_mindmg+=$item_mindmg;
  $cyborg_maxdmg+=$item_maxdmg;*/

  //itemwerte in ein array packen
  /*
1 waffenhand
2 schildhand
3 kopf
4 hals
5 brust
6 rücken
7 hände
8 ring 1
9 ring 2
10 taille
11 beine
12 füße
*/
  if($item_typ==3)$id=2;
  if($item_typ==5)$id=4;
  if($item_typ==11)$id=10;
  if($item_typ==12)$id=11;
  if($item_typ==7)$id=6;
  if($item_typ==2)$id=1;
  if($item_typ==1)$id=0;
  //namen speichern
  $body[$id][0]=$item_name;
  //id speichern
  $body[$id][1]=$itemid;
 
  //tooltip bauen
  $ttext=$item_name.'&';

  if($durability=='-1')$durability=$item_durability;
  
  //werte aufgrund geringer haltbarkeit verringern
  if($item_durability==0)$item_durability=1;
  $hw=$durability/$item_durability;
  if($hw<0.5)
  {
    $hw=$hw*2;	
  }
  else $hw=1;
  
  $Text[$id]=make_item_tooltipstring($row["id"], $row["durability"], $row["uses"]);
  
  //$Text[$id]=make_item_tooltipstring($row["id"], $row["durability"], $row["uses"]);

  //werte zusammenzählen
  $cyborg_armor+=round($item_armor*$hw);
  $cyborg_mindmg+=round($item_mindmg*$hw);
  $cyborg_maxdmg+=round($item_maxdmg*$hw);
}

//restlichen items laden und tooltips generieren
$anzbp=0;
$itemsperpage=924;
$result = mysql_query("SELECT id FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id'", $eftadb);
$itemmenge = mysql_num_rows($result);

$sp=$_REQUEST["sp"];
if($sp<=1)$sp=1;
if($sp*$itemsperpage>$itemmenge)$sp=ceil($itemmenge/$itemsperpage);
$sp=(int)$sp;
if($sp<=1)$sp=1;
$showstart=(-1*$itemsperpage)+($sp*$itemsperpage);
$showmenge=$itemsperpage;
$result = mysql_query("SELECT id, durability, uses FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id' ORDER BY typ, id LIMIT $showstart,$showmenge", $eftadb);

//$result = mysql_query("SELECT id FROM de_cyborg_item WHERE equip=0 AND typ<>20 AND user_id='$efta_user_id' ORDER BY typ", $eftadb);
while($row = mysql_fetch_array($result))
{
  $BPText[$anzbp]=make_item_tooltipstring($row["id"], $row["durability"], $row["uses"]);
  //counter erhöhen
  $anzbp++;
}

//geld laden
$result = mysql_query("SELECT amount FROM de_cyborg_item WHERE equip=0 AND typ=20 AND id=1 AND user_id='$efta_user_id'", $eftadb);
$row = mysql_fetch_array($result);
$hasmoney=$row["amount"];

echo '<br><br>';
rahmen0_oben();

if($itemmsg!='')
{
  rahmen2_oben();
  echo '<div align="center">'.$itemmsg.'</div>';
  rahmen2_unten();
  echo '<br>';
}

//mit hilfe einer tabelle die seite einteilen
echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td width="50%" valign="top">';

rahmen1_oben('<div align="center"><b>Charakter</b></div>');

/*echo '<div id="charakter">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;Charakter&nbsp;</b></td></tr>';
echo '</table>';*/

echo '<table width="100%" cellpadding="1" cellspacing="1">';
echo '<tr align="center">';
echo '<td width="25%" class="cell1"><b>Rechte Hand</td>';
$id=0;if($body[$id][0]!='-')
echo '<td width="75%" class="cell1" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td width="75%" class="cell1"><b>'.$body[$id][0].'</td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td class="cell"><b>Linke Hand</td>';
$id=1;if($body[$id][0]!='-')
echo '<td class="cell" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td class="cell"><b>'.$body[$id][0].'</td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td class="cell1"><b>Kopf</td>';
$id=2;if($body[$id][0]!='-')
echo '<td class="cell1" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td class="cell1"><b>'.$body[$id][0].'</td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td class="cell"><b>Brust</td>';
$id=4;if($body[$id][0]!='-')
echo '<td class="cell" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td class="cell"><b>'.$body[$id][0].'</td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td class="cell1"><b>Beine</td>';
$id=10;if($body[$id][0]!='-')
echo '<td class="cell1" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td class="cell1"><b>'.$body[$id][0].'</td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td class="cell"><b>F&uuml;&szlig;e</td>';
$id=11;if($body[$id][0]!='-')
echo '<td class="cell" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td class="cell"><b>'.$body[$id][0].'</td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td class="cell1"><b>H&auml;nde</td>';
$id=6;if($body[$id][0]!='-')
echo '<td class="cell1" title="'.$Text[$id].'"><b>'.$body[$id][0].
' <a href="#" onClick="lnk(\'uk=1&a=1&id='.$body[$id][1].'\')" title="ablegen">[A]</a></td>';
else echo '<td class="cell1"><b>'.$body[$id][0].'</td>';
echo '</tr>';

echo '<tr align="center">';
echo '<td class="cell"><b><i>Gesamt</td>';
echo '<td class="cell" align="left"><b>R&uuml;stung: '.round($cyborg_armor).'<br>Schaden: '.round($cyborg_mindmg).' - '.round($cyborg_maxdmg).'</td>';
echo '</tr>';

echo '</table></div>';

rahmen1_unten();

//mit hilfe einer tabelle die seite einteilen
echo '</td><td width="50%" valign="top">';

//$cyborg_armor+=$item_armor;
//$cyborg_mindmg+=$item_mindmg;
//$cyborg_maxdmg+=$item_maxdmg;


//rucksack anzeigen
$kmalus=$anzbp;
$kmalus=$kmalus-$maxbackpack;
if($kmalus<0)$kmalus=0;
if($kmalus>9)$kmalus=9;
//in prozent umrechnen
$kmalus=$kmalus*10;
if($kmalus>0)$malusstr=' - '.$kmalus.'% Kampfmalus';else $malusstr='';

/*
echo '<div id="rucksack">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;.'&nbsp;</b></td></tr>';
echo '</table>';*/

rahmen1_oben('<div align="center"><b>Rucksack ('.$itemmenge.'/'.$maxbackpack.')'.$malusstr.'</b></div>');

//evtl. statusmeldungen ausgeben
if($errmsg!='')echo $errmsg;

  echo '<table width="100%" cellpadding="1" cellspacing="1">';
  //geldbörse anzeigen
  $geld=make_moneystring($hasmoney);

  echo '<tr align="left">';
  $c1=0;$bg='cell';
  echo '<td class="'.$bg.'"><b>Geldbeutel: '.$geld.'</td>';
  echo '</tr>';
  echo '<tr align="left">';
  $c1=1;$bg='cell1';
  echo '<td class="'.$bg.'" title="'.$sit[0].'"><b>Heimatkristall von Waldmond <a href="#" onClick="lnk(\'uk=1&b=2&sitid=1\')" title="benutzen">[B]</a></td>';
  echo '</tr>';
  for($i=0;$i<$anzbp;$i++)
  {
    if ($c1==0)
    {
      $c1=1;
      $bg='cell1';
    }
    else
    {
      $c1=0;
      $bg='cell';
    }
    echo '<tr align="left">';
    //tragbare sachen
    if(($bp[$i][2]==3 OR $bp[$i][2]==5 OR $bp[$i][2]==11 OR $bp[$i][2]==12 OR $bp[$i][2]==7 OR $bp[$i][2]==2 OR $bp[$i][2]==1))
    {
      //schauen ob man es anlegen kann
      if($bp[$i][3]<=$level)$str='<a href="#" onClick="lnk(\'uk=1&a=2&id='.$bp[$i][1].'&did='.$bp[$i][5].'\')" title="anlegen">[A]</a>'; else $str='';

      echo '<td nowrap class="'.$bg.'" title="'.$BPText[$i].'"><b><a href="#" onClick="lnk(\'uk=1&drop=1&id='.$bp[$i][1].'\')" title="wegwerfen">[W]</a>'.$str.' '.$bp[$i][0].' </td>';
    }
    //nicht tragbare sachen
    else
    {
      //schauen ob man es benutzen kann
      if ($bp[$i][6]==1)
      $str='<a href="#" onClick="lnk(\'uk=1&b=1&id='.$bp[$i][1].'&usesid='.$bp[$i][7].'\')" title="benutzen">[B]</a>'; else $str='';

      echo '<td nowrap class="'.$bg.'" title="'.$BPText[$i].'"><b><a href="#" onClick="lnk(\'uk=1&drop=1&id='.$bp[$i][1].'\')" title="wegwerfen">[W]</a>'.$str.' '.$bp[$i][0].' </td>';
    }
    echo '</tr>';
  }

    //evtl. untere leiste zum blättern anzeigen
    if($anzbp>0)
    {
    echo '<tr align="center"><td colspan="2">';
    echo '<table><tr>';
    //zurück
    if($sp>1 AND $itemmenge>0)echo '<td width="100" align="center"><a href="#" onClick="lnk(\'uk=1&sp='.($sp-1).'\')">zur&uuml;ck</a></td>';
    else echo '<td width="100" align="center">&nbsp;</td>';
    //itemzahl
    $bis=$showstart+$showmenge;
    if($bis>$itemmenge)$bis=$itemmenge;
    echo '<td width="100" align="center">'.($showstart+1).' - '.($bis).' ('.$itemmenge.')</td>';
    //weiter
    if(($bis<$itemmenge))
    echo '<td width="100" align="center"><a href="#" onClick="lnk(\'uk=1&sp='.($sp+1).'\')">weiter</a></td>';
    else echo '<td width="100" align="center">&nbsp;</td>';
    echo '</tr></table></td></tr>';
    }
  
  echo '</table></div>';
  
  rahmen1_unten();
  
  echo '</td></tr></table>';

rahmen0_unten();

//infoleiste anzeigen
show_infobar();

echo '</body></html>';

exit;
?>
