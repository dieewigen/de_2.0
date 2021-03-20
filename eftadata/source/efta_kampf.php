<?php
//das eigentliche kampfscript f�r efta
/*
//trefferwahrscheinlichkeiten nach leveln
$trefferwahrscheinlichkeitliste = array(50,55,60,62,64,67,70,72,74,75,76,77,78,79,80,81,82,83,84,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85
,85,85,85,85,85,85,85,85,85,85,85,85,85,85,85);
$trefferwahrscheinlichkeit=$trefferwahrscheinlichkeitliste[$level-1];
//schadensmodifikator
//$schadensmodifikatorliste = array(0,1,2,2,3,4, 4,5,6,6,7,8, 8,9,10,10,11, 12,12,13,14,14);
//$schadensmodifikator=$schadensmodifikatorliste[$str-10];
//ausweichmodifikator
//$ausweichmodifikatorliste = array(0,1,1,2,2,3, 4,4,5,5,6,7, 7,8,8,9,10,10,11,12,12,13);
//$ausweichmodifikator=$ausweichmodifikatorliste[$dex-10];*/

//grundschaden + schadensmodifikator + Waffe - ausweichmodifikator - R�stung

echo '<br><br>';
//daten des gegners laden
$db_daten=mysql_query("SELECT * FROM de_cyborg_enm WHERE user_id='$efta_user_id'",$eftadb);
$row = mysql_fetch_array($db_daten);

$enm_id=$row["enm_id"];
$enm_level=$row["level"];
$enm_name=umlaut($row["name"]);
$enm_hpakt=$row["hpakt"];
$enm_hpmax=$row["hpmax"];
$enm_attmin=$row["attmin"];
$enm_attmax=$row["attmax"];
$enm_armor=$row["armor"];
$enm_lootid=$row["lootid"];
$enm_x=$row["x"];
$enm_y=$row["y"];
$enm_z=$row["z"];
$enm_spell1=$row["spell1"];
$enm_questid=$row["questid"];
$enm_flagid=$row["flagid"];

  //cyborg r�stung auslesen
  $result = mysql_query("SELECT id, durability FROM de_cyborg_item WHERE equip=1 AND user_id='$efta_user_id'", $eftadb);
  $cyborg_armor=0;
  $cyborg_mindmg=0;
  $cyborg_maxdmg=0;
  while($row = mysql_fetch_array($result))
  {
    //erstmal alles nullen
    $item_armor=0;
    $item_mindmg=0;
    $item_maxdmg=0;
    $item_durability=1;

    $itemid=$row["id"];
    $durability=$row["durability"];
    $filename='eftadata/items/'.$itemid.'.item';
    include($filename);
    //werte aufgrund geringer haltbarkeit verringern
    $hw=$durability/$item_durability;
    if($hw<0.5)
    {
      $hw=$hw*2;	
    }
    else $hw=1;
    
    //werte zusammenz�hlen
    $cyborg_armor+=$item_armor*$hw;
    $cyborg_mindmg+=$item_mindmg*$hw;
    $cyborg_maxdmg+=$item_maxdmg*$hw;
  }
  
//zuerst schauen wie der standardr�stungswert ist, den der gegner erwartet
$standardruestwert=$enm_level*50*2;
$enm_standardruestwert=$level*50*2;
//die eigene/fremde r�stung in relation dazu setzen
//spieler
$schadenssenkung=$cyborg_armor*100/$standardruestwert;
if($enm_level>$level)$schadenssenkung=$schadenssenkung-(($enm_level-$level)*10);
if($schadenssenkung<0)$schadenssenkung=0;
if($schadenssenkung>100)$schadenssenkung=100;

//bei level 1 gegnern kann die schadenssenkung nicht unter 40 fallen
//bei level 1 gegnern kann der schaden nicht unter x - y fallen
if($enm_level==1)
{
  if($schadenssenkung<35)$schadenssenkung=35;
  if($cyborg_mindmg<30)$cyborg_mindmg=30;
  if($cyborg_maxdmg<35)$cyborg_maxdmg=35;
}    
//gegner
$enm_schadenssenkung=$enm_armor*100/$enm_standardruestwert;  
if($level>$enm_level)$enm_schadenssenkung=$enm_schadenssenkung-(($level-$enm_level)*10);
if($enm_schadenssenkung<0)$enm_schadenssenkung=0;
if($enm_schadenssenkung>100)$enm_schadenssenkung=100;


//echo $enm_schadenssenkung;

if($kampf==1 OR $kampf==3)
{
  //angriff des gegners berechnen
  //zuerst schauen ob der gegner einen trifft
  //h�ngt erstmal vom levelunterschied ab und geht 60% chance zu treffen bei gleichem level aus, pro unterschiedlichen level x% malus/bonus
  $levelunterschied=($enm_level-$level)*5;
  if (60+$levelunterschied> mt_rand(0, 100))
  {
    //schaden=grundschaden + schadensmodifikator + Waffe - ausweichmodifikator - R�stung
    //$schaden=mt_rand($enm_attmin, $enm_attmax)-$ausweichmodifikator;
    //$schaden=round(mt_rand($enm_attmin, $enm_attmax)-$cyborg_armor);
    
    //den schaden berechnen, den der cyborg nimmt
    $schaden=round(mt_rand($enm_attmin, $enm_attmax));
    //den schaden je nach r�stungsgr��e verringern
    $schaden=round($schaden*(100-$schadenssenkung)/100);
    //echo $standardruestwert.' - '.$cyborg_armor.' - '.$schadenssenkung;
    
    if ($schaden<0)$schaden=0;
    $hp=$hp-$schaden;
    if($schaden==0)$gegner_kmsg='schl&auml;gt zu und du weichst aus';
    else $gegner_kmsg='schl&auml;gt zu und verursacht '.$schaden.' Schaden';
    //�berpr�fen, ob ausr�stung besch�digt wird
    if($schaden>0)
    {
      //feststellen welches item besch�digt wird
      $w=mt_rand (1, 10000);
      if ($w<=1000) $id=7;
      elseif ($w<=2000) $id=12;
      elseif ($w<=3500) $id=3;
      elseif ($w<=5000) $id=11;
      elseif ($w<=6500) $id=2;
      elseif ($w<=10000) $id=5;
	  //die haltbarkeit abziehen
	  mysql_query("UPDATE de_cyborg_item SET durability = durability - 1 WHERE durability >1 AND typ='$id' AND equip=1 AND user_id='$efta_user_id' LIMIT 1",$eftadb);
    }
  }
  else $gegner_kmsg='schl&auml;gt zu und verfehlt';
  
  if ($hp<=0) //man ist tot
  {
    //gegner l�schen
    mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$efta_user_id'",$eftadb);
    //cyborg stirbt
    cyborg_die($efta_user_id, 1, 'Du wurdest besiegt und wirst von den Replikationsanlagen bei der n&auml;chstgelegen Stadt wiederbelebt.');
  }
  else mysql_query("UPDATE de_cyborg_data SET hp='$hp' WHERE user_id='$efta_user_id'",$eftadb);

  //angriff des cyborgs berechnen
  //schauen ob er eine psikraft einsetzt
  if($kampf==3 AND $mp>0 AND $enm_spell1==0)
  {
    //gegner mit spell belegen
    $enm_spell1=1;
    mysql_query("UPDATE de_cyborg_enm SET spell1=1 WHERE user_id='$efta_user_id'",$eftadb);
    //psienergie abziehen
    $mp=$mp-1;
    mysql_query("UPDATE de_cyborg_data SET mp=mp-1 WHERE user_id='$efta_user_id'",$eftadb);
  }

  //h�ngt erstmal vom levelunterschied ab und geht 60% chance zu treffen bei gleichem level aus, pro unterschiedlichen level x% malus/bonus
  //zus�tzlich gibt es einen bonus je geschickter man ist
  $levelunterschied=($level-$enm_level)*5;
  if (60+$levelunterschied*(($dex-10)/2.5)> mt_rand(0, 100))
  {
    //alt: schaden=grundschaden + schadensmodifikator + Waffe - ausweichmodifikator - R�stung
    //neu: schaden=grundschaden + schaden aus st�rke + waffe
    //$schaden=round(mt_rand(1, 2)+$str+mt_rand($cyborg_mindmg, $cyborg_maxdmg));
    $schaden=round(($str*1.5)+mt_rand($cyborg_mindmg, $cyborg_maxdmg));
    //echo 'A: '.$schaden;
    //den schaden je nach r�stungsgr��e verringern
    $schaden=round($schaden*(100-$enm_schadenssenkung)/100);
    //echo 'B: '.$schaden;
    //psikraft 1
    $psischaden[0]=0;
    if($enm_spell1>0)
    {
      $psischaden[0]=round($schaden*0.1);
      $schaden=$schaden+$psischaden[0];
    }
    //echo 'C: '.$schaden;
    //schauen wieviel gep�ck er hat um malus zu berechnen
    $kmalus=mysql_query("SELECT count(*) FROM de_cyborg_item WHERE equip=0 AND user_id='$efta_user_id'", $eftadb);
    $kmalus=mysql_result($kmalus,0,0)-1;
    $kmalus=$kmalus-$maxbackpack;
    if($kmalus<0)$kmalus=0;
    if($kmalus>9)$kmalus=9;
    //in prozent umrechnen
    $kmalus=$kmalus/10;
    //schadensmalus einrechnen
    $schaden=$schaden-($schaden*$kmalus);
    //echo 'D: '.$schaden;
    $psischaden[0]=round($psischaden[0]-($psischaden[0]*$kmalus));

    //schaden runden, damit ganze werte entstehen
    $schaden=floor($schaden);
        
    if ($schaden<0)$schaden=0;
    $enm_hpakt=$enm_hpakt-$schaden;
    if($schaden==0)$cyb_kmsg='schl&auml;gt zu und der Gegner weicht aus';
    else $cyb_kmsg='schl&auml;gt zu und verursacht '.$schaden.' Schaden';
    if($psischaden[0]>0)$cyb_kmsg.=' (davon '.$psischaden[0].' durch Feuersto&szlig;)';

    if($schaden>0)
    {
      //die waffe nimmt schaden
      $id=1;
      //die haltbarkeit abziehen
      if(mt_rand(1, 100)<=25)
	  mysql_query("UPDATE de_cyborg_item SET durability = durability - 1 WHERE durability >1 AND typ='$id' AND equip=1 AND user_id='$efta_user_id' LIMIT 1",$eftadb);
    }
    
    
  }
  else $cyb_kmsg='schl&auml;gt zu und verfehlt';

  if ($enm_hpakt<=0)//gegner ist tot
  {
  	//gegner aus der db l�schen
    mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$efta_user_id'",$eftadb);
    
    mysql_query("DELETE FROM de_cyborg_enm_map WHERE x='$enm_x' AND y='$enm_y' AND z='$enm_z' LIMIT 1",$eftadb);
    
    //evtl. quest updaten
    if($enm_questid>0)
    {
      mysql_query("UPDATE de_cyborg_quest SET flag$enm_flagid=flag$enm_flagid+1 WHERE user_id='$efta_user_id' AND typ='$enm_questid'",$eftadb);
    }

    
    //die("DELETE FROM de_cyborg_enm_map WHERE x='$enm_x' AND y='$enm_y' AND z='$enm_z' LIMIT 0,1");
    //erfahrungspunktegewinn
    //berechnung nach stufe und stufenunterschied zwischen gegner und cyborg
    $levelunterschied=$enm_level-$level;
    if($levelunterschied>5)$levelunterschied=5;
    $expgew=10+(round($levelunterschied*2));
    if($expgew<0)$expgew=0;
    //ab dem maximallevel gibt es keine erfahrungspunkte mehr durch den kampf
    if($level>=$maxplayerlevel)$expgew=0;
        
    $ostr='<b>Der Gegner wurde besiegt und es gab folgenden Ertrag:</b><br>'.$expgew.' Erfahrungspunkte<br>';
    $exp=$exp+$expgew;
    if($enm_level>=$level-1)$killcounter=1; else $killcounter=0;
    $player_killcounter+=$killcounter;
    
    //gegner auslesen
    $res01=0;$res02=0;$res03=0;$res04=0;$res05=0;$res06=0;
    $filename='eftadata/loottable/'.$enm_lootid.'.lt';
    if (file_exists($filename)==1)
    {
      $fenm    = fopen ($filename, 'r');
      //datei bis zum ende durchlaufen und schauen ob man etwas bekommt
      while (!feof($fenm))
      {
        $lootwerte = explode (";",trim(fgets($fenm, 1024)));
        //schauen was es f�r ein loot ist
        switch($lootwerte[0])
        {
          /* 
          case 'res1':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             $res01=mt_rand($lootwerte[1], $lootwerte[2]);
             if ($res01>0) $ostr.= $res01.' Multiplex<br>';
             //nur res geben, wenn efta aktiv
             if($ums_useefta==0)$res01=0;
           }
           break;
           case 'res2':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             $res02=mt_rand($lootwerte[1], $lootwerte[2]);
             if ($res02>0) $ostr.= $res02.' Dyharra<br>';
             //nur res geben, wenn efta aktiv
             if($ums_useefta==0)$res02=0;
           }
           break;
           case 'res3':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             $res03=mt_rand($lootwerte[1], $lootwerte[2]);
             if ($res03>0) $ostr.= $res03.' Iradium<br>';
             //nur res geben, wenn efta aktiv
             if($ums_useefta==0)$res03=0;
           }
           break;
           case 'res4':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             $res04=mt_rand($lootwerte[1], $lootwerte[2]);
             if ($res04>0) $ostr.= $res04.' Eternium<br>';
             //nur res geben, wenn efta aktiv
             if($ums_useefta==0)$res04=0;
           }
           break;
           case 'res5':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             $res05=mt_rand($lootwerte[1], $lootwerte[2]);
             if ($res05>0) $ostr.= $res05.' Tronic<br>';
             //nur res geben, wenn efta aktiv
             if($ums_useefta==0)$res05=0;
           }
           break;
           */
           case 'res6':
           //palenium wird nur dann verteilt, wenn efta in de integriert ist
           if($sv_efta_in_de==1)
           {
             //man bekommt es nur bei einem gegner der passenden gr��e
             if($killcounter==1)
             {
               if($has_palenium==1)
               {
               	 $res06=1;//round(mt_rand($lootwerte[1], $lootwerte[2])*sqrt($level));
                 //testen ob man nicht �ber die grenze kommt
                 if($palenium_anz+$res06>$sv_max_palenium)$res06=$sv_max_palenium-$palenium_anz;
               }
               else $res06=0;
               if ($res06>0) $ostr.= $res06.' Palenium<br>';
             }
           }
           break;
           case 'ua'://spielerartefakt
           //wird nur dann verteilt, wenn efta in de integriert ist
           /*
           if($sv_efta_in_de==1)
           {
             //schauen ob man es bekommt
             if($has_artbldg==1)
             //if(mt_rand(1,10000)/100 <= $lootwerte[3])
             if($player_killcounter % 100 ==0 AND $killcounter==1 AND $player_killcounter>0)
             {
               //info um zu testen ob jemand artefakte farmt
               //@mail($GLOBALS['env_admin_email'],"$sv_server_tag - $ums_spielername - Level: $level - Gegnerlevel: $enm_level", 'FROM: '.$GLOBALS['env_admin_email']);
           	   //noch schauen ob es platz f�r das artefakt gibt
               $db_datenx=mysql_query("SELECT user_id FROM de_user_artefact WHERE user_id='$ums_user_id'",$db);
               $numx = mysql_num_rows($db_datenx);
               if($numx<$artbldglevel) //es gibt noch platz
               {
                 include "inc/userartefact.inc.php";
                 //f�r ein einfacheres system werden die artefakte beim finden direkt festegelegt
                 $lootwerte[1]=mt_rand(1, 15);//artefakt-id
                 $lootwerte[2]=1;//level
                 mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$lootwerte[1]', '$lootwerte[2]')",$db);
                 $ostr.= '1 '.$ua_name[$lootwerte[1]-1].' Artefakt<br>';
                 //counter setzen, wann man das letzte artefakt erhalten hat
                 mysql_query("UPDATE de_user_data SET eftagetlastartefact=tick WHERE user_id='$ums_user_id'",$db);
               }
               else $ostr.= 'Du findest ein Artefakt, hast aber keinen Platz mehr um es unterzubringen.<br>';
               //aktionspunktetrank hinterlegen
               $ostr.= 'Du findest einen Trank der Rast.<br>';
               add_item(4890, 1);
             }
           }*/
           break;
           case 'item':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             $item_durability=0;
           	 $filename='eftadata/items/'.$lootwerte[1].'.item';
             include($filename);
             mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$efta_user_id', '$lootwerte[1]', '$item_typ', '$lootwerte[2]', '$item_durability')",$eftadb);
             $ostr.= $lootwerte[2].' '.$item_name.'<br>';
           }
           break;
           case 'money':
           //schauen ob man es bekommt
           if(mt_rand(1,10000)/100 <= $lootwerte[3])
           {
             //schauen wieviel man bekommt
             $money=mt_rand($lootwerte[1],$lootwerte[2]);
             //db updaten
             mysql_query("UPDATE de_cyborg_item SET amount=amount+'$money' WHERE user_id='$efta_user_id' AND id=1 AND typ=20",$eftadb);

             //bildschirmausgabe
             //$ostr.= $money.' Kupferm&uuml;nzen<br>';
             $ostr.= make_moneystring($money).'<br>';
           }
           break;
           default:
           break;
        }
      }
      //sachen unter bestimmten bedingungen vergeben, wie z.b. items der server quest
      
      //trank der rast
      if($player_killcounter % 100 ==0 AND $killcounter==1 AND $player_killcounter>0)
      {
        $ostr.= 'Du findest einen Trank der Rast.<br>';
        add_item(4890, 1);     
      }
      
      
      //hafenquest auf 2:9 -> bei x% der gegner den fetisch fallenlassen
      //schauen ob es schon genug fetische gibt
      $result = mysql_query("SELECT * FROM de_cyborg_struct WHERE x = 2 AND y= 9 AND z=0;",$eftadb);
      $row = mysql_fetch_array($result);
      if($row["flag1"]<2500 AND $enm_level>=6 AND mt_rand(1,100)<=10)
      {
        $item_id=4865;
        $filename='eftadata/items/'.$item_id.'.item';
        include($filename);
        mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$efta_user_id', '$item_id', '$item_typ', '1', '$item_durability')",$eftadb);
        $ostr.= '1 '.$item_name.'<br>';
        
      }//hafen-ende
      
      //man kann bei jedem gegner einen dornschlingensamen bekommen
      if(mt_rand(1,1000)<=10)
      {
        $item_id=4868;
        $filename='eftadata/items/'.$item_id.'.item';
        include($filename);
        mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$efta_user_id', '$item_id', '$item_typ', '1', '$item_durability')",$eftadb);
        $ostr.= '1 '.$item_name.'<br>';
      }

      //man kann bei jedem gegner einen teil eines dornschlingen-zerstörer bekommen
      if(mt_rand(1,1000)<=20)
      {
        $item_ids = array (4872, 4873, 4874);
      	$item_id=$item_ids[mt_rand(0,2)];
        $filename='eftadata/items/'.$item_id.'.item';
        include($filename);
        mysql_query("INSERT INTO de_cyborg_item (user_id, id, typ, amount, durability) VALUES ('$efta_user_id', '$item_id', '$item_typ', '1', '$item_durability')",$eftadb);
        $ostr.= '1 '.$item_name.'<br>';
      }
      
      
      fclose($fenm);
    }else die("Datei nicht gefunden");
    
    //rohstoffe updaten, wenn efta in de integriert ist

    if($sv_efta_in_de==1 && $sv_server_tag='123')
    {
      $sql="UPDATE de_user_data set restyp01=restyp01+'$res01', restyp02=restyp02+'$res02', restyp03=restyp03+'$res03',restyp04=restyp04+'$res04', restyp05=restyp05+'$res05', palenium=palenium+'$res06' WHERE user_id='$ums_user_id'";
      mysql_query($sql, $db);
    }

    //cyborg updaten
    //anzeigemessage erstellen
    $showmsg='';
    if($gegner_kmsg!='' OR $cyb_kmsg!='')
    {
      $showmsg.='<br>'.$enm_name.': '.$gegner_kmsg.'<br>';
      $showmsg.=$efta_spielername.': '.$cyb_kmsg.'<br>';
    }
    $showmsg.='<br>'.$ostr.'<br>';
	
    //spieler updaten
    mysql_query("UPDATE de_cyborg_data set exp=exp+'$expgew', killcounter=killcounter+'$killcounter', showmsg2='$showmsg' WHERE user_id='$efta_user_id'",$eftadb); 
    

    //seite refreshen
    echo '<script>lnk("");</script>';
    exit;
  }
  else mysql_query("UPDATE de_cyborg_enm set hpakt='$enm_hpakt' WHERE user_id='$efta_user_id'",$eftadb);
}
elseif($kampf==2)//flucht
{
  if ($bewpunkte>=100)
  {
    mysql_query("UPDATE de_cyborg_data set bewpunkte = bewpunkte - 100, x=oldx, y=oldy WHERE user_id='$efta_user_id'",$eftadb);
    mysql_query("DELETE FROM de_cyborg_enm WHERE user_id='$efta_user_id'",$eftadb);
   
    if($hp<$hpmax) 
    echo '<script>lnk("r=2");</script>';
    else
    echo '<script>lnk("");</script>';
    exit;
  }
}
//daten der k�mpfer
/*echo '<div id="ct_city">';
echo '<table class="ct_title" width="100%" border="0" cellpadding="0" cellspacing="0">';
echo '<tr><td align="center"><b class="ueber">&nbsp;Kampf&nbsp;</b></td></tr>';
echo '</table><br>';*/

rahmen0_oben();
rahmen1_oben('<div align="center"><b>Kampf</b></div>');

//die kontrahenten sollen nebeneinander stehen, daf�r eine table bauen
echo '<table border="0" cellpadding="1" cellspacing="1">';
echo '<tr><td valign="top">';


//den spieler anzeigen

//farbe f�r die bewegungspunkte bestimmen
$bcolor='#00FF00';
if($bewpunkte<=$sv_max_efta_bew_punkte*0.75)$bcolor='yellow';
if($bewpunkte<=$sv_max_efta_bew_punkte*0.50)$bcolor='orange';
if($bewpunkte<=$sv_max_efta_bew_punkte*0.25)$bcolor='red';
//farbe f�r die lebensenergie bestimmen
$hpcolor='#00FF00';
if($hp<=$hpmax*0.75)$hpcolor='yellow';
if($hp<=$hpmax*0.50)$hpcolor='orange';
if($hp<=$hpmax*0.25)$hpcolor='red';
//farbe f�r die psienergie bestimmen
$mpcolor='#00FF00';
if($mp<=$mpmax*0.75)$mpcolor='yellow';
if($mp<=$mpmax*0.50)$mpcolor='orange';
if($mp<=$mpmax*0.25)$mpcolor='red';


echo '<table cellpadding="1" cellspacing="1">';
echo '<tr>';
echo '<td width="400" align="center" colspan="2"  class="tc"><b>'.$efta_spielername.'</b></td>';
echo '</tr>';
echo '<tr>';
echo '<td align="center" colspan="2"><img src="'.$gpfad.'c1.gif" width="50" height="50" border="0" alt="'.$efta_spielername.'"></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="200" class="cell">Stufe</td>';
echo '<td width="200" align="center" class="cell">'.$level.'</font></td>';
echo '</tr>';
echo '<tr>';
echo '<td width="200" class="cell1">Lebensenergie</td>';
echo '<td width="200" align="center" class="cell1"><font color="'.$hpcolor.'">'.$hp.'/'.$hpmax.'</font></td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cell">R&uuml;stungswert</td>';
echo '<td align="center" class="cell">'.round($cyborg_armor).' ('.number_format($schadenssenkung, "2",",",".").'%)</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cell1">Bewegungspunkte</td>';
echo '<td align="center" class="cell1"><font color="'.$bcolor.'">'.(floor($bewpunkte)).'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cell">Psienergie</td>';
echo '<td align="center" class="cell"><font color="'.$mpcolor.'">'.$mp.'/'.$mpmax.'</font></td>';
echo '</tr>';
echo '</table>';
//die kontrahenten sollen nebeneinander stehen, daf�r eine table bauen
echo '</td><td width="20"></td><td valign="top">';

//den gegner darstellen
//farbe f�r die lebensenergie bestimmen
$hpcolor='#00FF00';
if($enm_hpakt<=$enm_hpmax*0.75)$hpcolor='yellow';
if($enm_hpakt<=$enm_hpmax*0.50)$hpcolor='orange';
if($enm_hpakt<=$enm_hpmax*0.25)$hpcolor='red';

echo '<table border="0" cellpadding="1" cellspacing="1">';
echo '<tr>';
echo '<td width="400" align="center" colspan="2" class="text1"><b>'.$enm_name.'</b></td>';
echo '</tr>';
echo '<tr>';
echo '<td align="center" colspan="2"><img src="'.$gpfad.'e1.gif" width="50" height="50" border="0" alt="'.$enm_name.'"></td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cell">Stufe</td>';
echo '<td align="center" class="cell">'.$enm_level.'</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cell1" width="200">Lebensenergie</td>';
echo '<td align="center" class="cell1" width="200"><font color="'.$hpcolor.'">'.$enm_hpakt.'/'.$enm_hpmax.'</font></td>';
echo '</tr>';
echo '<tr>';
echo '<td class="cell">R&uuml;stungswert</td>';
echo '<td align="center" class="cell">'.number_format($enm_schadenssenkung, "2",",",".").'%</td>';
echo '</tr>';
//psiwirkung
$str='';
if($enm_spell1>0)$str='Feuersto&szlig;';
if($str=='')$str='keine';
echo '<tr>';
echo '<td class="cell1">Psiwirkung</td>';
echo '<td align="center" class="cell1">'.$str.'</td>';
echo '</tr>';
//echo '<tr>';
//echo '<td>Energievorrat</td>';
//echo '<td align="center">'.$gegner_mpakt.'/'.$gegner_mpmax.'</td>';
//echo '</tr>';
echo '</table><br>';

//aktionen die man durchf�hren kann

//angriff

// Auff�llung mit &nbsp; da au�er im IE in allen anderen Browsern die Buttonl�nge nicht richtig dargestellt wird
// so sind wenigstens Button Angriff und Button Feuersto� gleich lang. Wichtig, da sie untereinander sind.

//die kontrahenten sollen nebeneinander stehen, daf�r eine table bauen
echo '</tr></table>';

echo '<table border="0" cellpadding="1" cellspacing="1">';
echo '<tr align="center"><td width="400">';
echo '<a href="#" onClick="lnk(\'kampf=1\')"><div class="b1">Angriff (0 AP)</a></div><br>
<a href="#" onClick="lnk(\'kampf=3\')"><div class="b1">Feuersto&szlig; (1 PE)</div></a>';
echo '</td><td width="20"></td>';
//flucht
echo '<td width="400">'; 
echo '<a href="#" onClick="lnk(\'kampf=2\')"><div class="b1">Flucht (100 AP)</div></a>';
echo '</td></tr>';
echo '</table>';

//kampfhistory anzeigen
if($gegner_kmsg!='' OR $cyb_kmsg!='')
{
  echo '<p class="text1">'.$enm_name.': '.$gegner_kmsg.'<br>';
  echo $efta_spielername.': '.$cyb_kmsg.'</p>';
}

rahmen1_unten();
rahmen0_unten();
//link zur�ck ins spiel
//echo '<a href="'.$PHP_SELF.'">Zur�ck</a><br><br>';
//infoleiste anzeigen
show_infobar();

echo '
<script language="JavaScript">
self.focus();
</script>
</body>
</html>';
exit;

?>
