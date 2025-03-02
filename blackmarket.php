<?php
include('inc/header.inc.php');
include('lib/transaction.lib.php');
include('functions.php');
include('inc/lang/'.$sv_server_lang.'_userartefact.inc.lang.php');
include('inc/userartefact.inc.php');
include('inc/lang/'.$sv_server_lang.'_trade.blackmarketinc.lang.php');
include_once('lib/religion.lib.php');

$pt = loadPlayerTechs($_SESSION['ums_user_id']);
$pd = loadPlayerData($_SESSION['ums_user_id']);
$row = $pd;
$restyp01 = $row['restyp01'];
$restyp02 = $row['restyp02'];
$restyp03 = $row['restyp03'];
$restyp04 = $row['restyp04'];
$restyp05 = $row['restyp05'];
$punkte = $row['score'];
$newtrans = $row['newtrans'];
$newnews = $row['newnews'];
$sector = $row['sector'];
$system = $row['system'];
$gr01 = $restyp01;
$gr02 = $restyp02;
$gr03 = $restyp03;
$gr04 = $restyp04;
$gr05 = $restyp05;

$credits    = $row['credits'];
$tick       = $row['tick'];
$sm_rboost  = $row['sm_rboost'];
$sm_col     = $row['sm_col'];
$sm_kartefakt = $row['sm_kartefakt'];
$sm_tronic  = $row['sm_tronic'];
$palaufzeit = $row['patime'];
$col        = $row['col'];

//den maximalen tick auslesen
$result = mysql_query("SELECT MAX(tick) AS tick FROM de_user_data", $db);
//$result  = mysql_query("SELECT wt AS tick FROM de_system LIMIT 1",$db);
$row = mysql_fetch_array($result);
$maxtick = $row['tick'];


if ($sv_ewige_runde == 1 || $sv_hardcore == 1) {
    $maxtick = $tick;
}

$rtick = $maxtick;

//owner id auslesen
$db_daten = mysql_query("SELECT owner_id FROM de_login WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$owner_id = intval($row['owner_id']);

//preise für die spielerartefakte
$artefaktpreis = array(50, 25, 25, 25, 50, 50, 50, 25, 25, 25, 25, 25, 25, 25, 25, 50, 50, 50, 50, 50);

//spielerartefakte die im angebot sind
$artefaktangebot = array(100, 104, 105, 106);

//in der br die preise stark senken
if ($maxtick >= 2500000) {
    if ($sv_comserver != 1) {
        $artefaktpreis = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
        $sv_sm_preisliste = array(1, 1, 10, 300, 1, 1, 1);
    }
}

//creditpreise ggf. aufgrund des religi�sen ranges verringern
$relrang = get_religion_level($owner_id);

//$relcreditmod=array(0,3,6,9,12,15,18,21,24,27,30);
$relcreditmod = array(0,5,10,15,20,25,35,45,60,61,62);

for ($i = 0;$i < count($artefaktpreis);$i++) {
    $artefaktpreis[$i] = $artefaktpreis[$i] - floor($artefaktpreis[$i] / 100 * $relcreditmod[$relrang]);
    if ($artefaktpreis[$i] == 0) {
        $artefaktpreis[$i] = 1;
    }
}

for ($i = 0;$i < count($sv_sm_preisliste);$i++) {
    $sv_sm_preisliste[$i] = $sv_sm_preisliste[$i] - floor($sv_sm_preisliste[$i] / 100 * $relcreditmod[$relrang]);
}

//lieferzeiten definieren
$sm_col_lz = 550;
$sm_kartefakt_lz = 66;
$sm_tronic_lz = 240;
$artefaktlz = array(1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200);


?>
<!DOCTYPE HTML>
<html>
<head>
<title>Schwarzmarkt</title>
<?php include "cssinclude.php"; ?>
</head>
<body>
<?php
//schwarzmarkt deaktiviert?
if (isset($sv_deactivate_blackmarket) && $sv_deactivate_blackmarket == 1) {
    include "resline.php";
    echo '<br><div class="info_box text2">Auf diesem Server ist der Schwarzmarkt deaktiviert.</div>';

    die('</body></html>');
}

$errmsg='';

$submit2 = $_POST['submit2'] ?? false;
$submit3 = $_POST['submit3'] ?? false;
$submit7=$_POST['submit7'] ?? false;
/*
$submit1=$_POST['submit1'];
$submit4=$_POST['submit4'];
$submit100=$_POST['submit100'];
$submit101=$_POST['submit101'];
$submit102=$_POST['submit102'];
$submit103=$_POST['submit103'];
$submit104=$_POST['submit104'];
$submit105=$_POST['submit105'];
$submit106=$_POST['submit106'];
$submit107 = $_POST['submit107'];
$submit108=$_POST['submit108'];
$submit109=$_POST['submit109'];
$submit110=$_POST['submit110'];
$submit111=$_POST['submit111'];
$submit112=$_POST['submit112'];
$submit113=$_POST['submit113'];
$submit114=$_POST['submit114'];
*/

$remsubmit2 = $_GET['remsubmit2'] ?? false;
$remsubmit3 = $_GET['remsubmit3'] ?? false;
$remsubmit107 = $_GET['remsubmit107'] ?? false;

/*
$remsubmit1=$_GET['remsubmit1'];
$remsubmit4=$_GET['remsubmit4'];
$remsubmit100=$_GET['remsubmit100'];
$remsubmit101=$_GET['remsubmit101'];
$remsubmit102=$_GET['remsubmit102'];
$remsubmit103=$_GET['remsubmit103'];
$remsubmit104=$_GET['remsubmit104'];
$remsubmit105=$_GET['remsubmit105'];
$remsubmit106=$_GET['remsubmit106'];
$remsubmit108=$_GET['remsubmit108'];
$remsubmit109=$_GET['remsubmit109'];
$remsubmit110=$_GET['remsubmit110'];
$remsubmit111=$_GET['remsubmit111'];
$remsubmit112=$_GET['remsubmit112'];
$remsubmit113=$_GET['remsubmit113'];
$remsubmit114=$_GET['remsubmit114'];
*/

//reminder de-/aktivieren
if ($remsubmit2 || $remsubmit3 || $remsubmit107) {
    //artefakte
    $artid = -1;
    if ($remsubmit100) {
        $artid = 0;
    }
    if ($remsubmit101) {
        $artid = 1;
    }
    if ($remsubmit102) {
        $artid = 2;
    }
    if ($remsubmit103) {
        $artid = 3;
    }
    if ($remsubmit104) {
        $artid = 4;
    }
    if ($remsubmit105) {
        $artid = 5;
    }
    if ($remsubmit106) {
        $artid = 6;
    }
    if ($remsubmit107) {
        $artid = 7;
    }
    if ($remsubmit108) {
        $artid = 8;
    }
    if ($remsubmit109) {
        $artid = 9;
    }
    if ($remsubmit110) {
        $artid = 10;
    }
    if ($remsubmit111) {
        $artid = 11;
    }
    if ($remsubmit112) {
        $artid = 12;
    }
    if ($remsubmit113) {
        $artid = 13;
    }
    if ($remsubmit114) {
        $artid = 14;
    }
    if ($artid != '-1') {
        //schauen wie der status ist und dann �ndern
        $artid++;
        $result = mysql_query("SELECT sm_art".$artid."rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($result);
        if ($row['sm_art".$artid."rem'] == 1) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        //db updaten
        mysql_query("UPDATE de_user_data SET sm_art".$artid."rem='$flag' WHERE user_id = '$ums_user_id'", $db);
    }
    //rohstofflieferung
    if ($remsubmit1) {
        //schauen wie der status ist und dann �ndern
        $result = mysql_query("SELECT sm_rboost_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($result);
        if ($row['sm_rboost_rem'] == 1) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        //db updaten
        mysql_query("UPDATE de_user_data SET sm_rboost_rem='$flag' WHERE user_id = '$ums_user_id'", $db);
    }
    //troniclieferung
    if ($remsubmit2) {
        //schauen wie der status ist und dann �ndern
        $result = mysql_query("SELECT sm_tronic_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($result);
        if ($row['sm_tronic_rem'] == 1) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        //db updaten
        mysql_query("UPDATE de_user_data SET sm_tronic_rem='$flag' WHERE user_id = '$ums_user_id'", $db);
    }
    //kriegsartefakt
    if ($remsubmit3) {
        //schauen wie der status ist und dann �ndern
        $result = mysql_query("SELECT sm_kartefakt_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($result);
        if ($row['sm_kartefakt_rem'] == 1) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        //db updaten
        mysql_query("UPDATE de_user_data SET sm_kartefakt_rem='$flag' WHERE user_id = '$ums_user_id'", $db);
    }

    //kollektor
    if ($remsubmit4) {
        //schauen wie der status ist und dann �ndern
        $result = mysql_query("SELECT sm_col_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($result);
        if ($row['sm_col_rem'] == 1) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        //db updaten
        mysql_query("UPDATE de_user_data SET sm_col_rem='$flag' WHERE user_id = '$ums_user_id'", $db);
    }
}


//käufe tätigen
/*
if($submit1){//kollektor
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //schauen ob er genug credits hat
    $db_daten=mysql_query("SELECT credits, sm_col FROM de_user_data WHERE user_id='$ums_user_id'",$db);
    $row = mysql_fetch_array($db_daten);
    $credits=$row[0];$sm_col=$row[1];

    if($sm_col < floor($maxtick/$sm_col_lz))
    {
      if($credits>=$sv_sm_preisliste[0])
      {
        $errmsg.='<font color="#00FF00">'.$tradeblackmarketinc_lang[msg_1].'</font>';
        //kollektor gutschreiben und credits abziehen
        mysql_query("UPDATE de_user_data SET credits=credits-$sv_sm_preisliste[0], col=col+1, sm_col=sm_col+1 WHERE user_id = '$ums_user_id'",$db);
        $sm_col++;
        $credits=$credits-$sv_sm_preisliste[0];
        refererbonus($sv_sm_preisliste[0]);
        writetocreditlog($tradeblackmarketinc_lang[kolliegutschrift]);
        updatesmstat(1, $sv_sm_preisliste[0]);
      }
      else
      $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_2].'</font>';
    }
    else $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_3].'</font>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print("$tradeblackmarketinc_lang[msg_4_1] ".$ums_user_id." $tradeblackmarketinc_lang[msg_4_2]!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang[msg_5].'</font><br><br>';
}//ende submit1
*/

if ($submit2) {//kartefakt
    //transaktionsbeginn
    if (setLock($ums_user_id)) {
        //schauen ob er genug credits hat
        $db_daten = mysql_query("SELECT credits, sm_kartefakt FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($db_daten);
        $credits = $row[0];
        $sm_kartefakt = $row[1];

        if ($sm_kartefakt < floor($maxtick / $sm_kartefakt_lz)) {
            if ($credits >= $sv_sm_preisliste[1]) {
                $errmsg .= '<font color="#00FF00">'.$tradeblackmarketinc_lang['msg_1'].'</font>';
                //kriegsartefakt gutschreiben und credits abziehen
                mysql_query("UPDATE de_user_data SET credits=credits-$sv_sm_preisliste[1], kartefakt=kartefakt+1, sm_kartefakt=sm_kartefakt+1 WHERE user_id = '$ums_user_id'", $db);
                $sm_kartefakt++;
                $credits = $credits - $sv_sm_preisliste[1];
                refererbonus($sv_sm_preisliste[1]);

                writetocreditlog($tradeblackmarketinc_lang['kartigutschrift']);
                updatesmstat(2, $sv_sm_preisliste[1]);
            } else {
                $errmsg .= '<font color="#FF0000">'.$tradeblackmarketinc_lang['msg_2'].'</font>';
            }
        } else {
            $errmsg .= '<font color="#FF0000">'.$tradeblackmarketinc_lang['msg_3'].'</font>';
        }

        //transaktionsende
        $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($tradeblackmarketinc_lang['msg_4_1'].' '.$ums_user_id.' '.$tradeblackmarketinc_lang['msg_4_2'].'!<br><br><br>');
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang['msg_5'].'</font><br><br>';
    }
}//ende submit2


//rohstofflieferung alle 1000 wochen
if ($submit3) {
    //transaktionsbeginn
    if (setLock($ums_user_id)) {
        //schauen ob er genug credits hat
        $db_daten = mysql_query("SELECT credits, sm_rboost FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($db_daten);
        $credits = $row[0];
        $sm_rboost = $row[1];

        if ($tick > $sm_rboost + 1000) {
            if ($credits >= $sv_sm_preisliste[2]) {
                $errmsg .= '<font color="#00FF00">'.$tradeblackmarketinc_lang['msg_1'].'</font>';
                //rohstoffe gutschreiben und credits abziehen

                $res[0] = $rtick * 500;
                $res[1] = $rtick * 250;
                $res[2] = $rtick * 60;
                $res[3] = $rtick * 37;
                mysql_query("UPDATE de_user_data SET credits=credits-$sv_sm_preisliste[2],
        restyp01=restyp01+$res[0], restyp02=restyp02+$res[1], restyp03=restyp03+$res[2], restyp04=restyp04+$res[3], sm_rboost=tick
        WHERE user_id = '$ums_user_id'", $db);
                $credits = $credits - $sv_sm_preisliste[2];
                refererbonus($sv_sm_preisliste[2]);
                $sm_rboost = $tick;
                writetocreditlog($tradeblackmarketinc_lang['rohstoffgutschrift']);
                updatesmstat(3, $sv_sm_preisliste[2]);
            } else {
                $errmsg .= '<font color="#FF0000">'.$tradeblackmarketinc_lang['msg_2'].'</font>';
            }
        } else {
            $errmsg .= '<font color="#FF0000">'.$tradeblackmarketinc_lang['msg_3'].'</font>';
        }

        //transaktionsende
        $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($tradeblackmarketinc_lang['msg_4_1'].' '.$ums_user_id.' '.$tradeblackmarketinc_lang['msg_4_2'].'!<br><br><br>');
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang['msg_5'].'</font><br><br>';
    }
}//ende submit3

/*
if($submit5 && $GLOBALS['sv_ang']!=1)//dartefakt
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //schauen ob er genug credits hat
    $db_daten=mysql_query("SELECT dartefakt, credits FROM de_user_data WHERE user_id='$ums_user_id'",$db);
    $row = mysql_fetch_array($db_daten);
    $dartefakt=$row['dartefakt'];
    $credits=$row['credits'];

    if($credits>=$sv_sm_preisliste[4])
    {
      if($dartefakt<$sv_max_dartefakt)
      {
        $errmsg.='<font color="#00FF00">'.$tradeblackmarketinc_lang[msg_1].'</font>';
        //kriegsartefakt gutschreiben und credits abziehen
        mysql_query("UPDATE de_user_data SET credits=credits-$sv_sm_preisliste[4], dartefakt=dartefakt+1 WHERE user_id = '$ums_user_id'",$db);
        $credits=$credits-$sv_sm_preisliste[4];
        refererbonus($sv_sm_preisliste[4]);
        writetocreditlog($tradeblackmarketinc_lang[diplogutschrift]);
        updatesmstat(5, $sv_sm_preisliste[4]);
      }
      else
      $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_6].'.</font>';
    }
    else
    $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_2].'</font>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print("$tradeblackmarketinc_lang[msg_4_1] ".$ums_user_id." $tradeblackmarketinc_lang[msg_4_2]!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang[msg_5].'</font><br><br>';
}//ende submit5
*/

/*
if($submit6)//palenium
{
  //transaktionsbeginn
  if (setLock($ums_user_id))
  {
    //schauen ob er genug credits hat
    $db_daten=mysql_query("SELECT palenium, credits FROM de_user_data WHERE user_id='$ums_user_id'",$db);
    $row = mysql_fetch_array($db_daten);
    $palenium=$row['palenium'];
    $credits=$row['credits'];

    //schaue ob er den palenium-verst�rker hat
    if($techs[27]==1)
    {
      if($credits>=$sv_sm_preisliste[5])
      {
        if($palenium<=$sv_max_palenium-100)
        {
          $errmsg.='<font color="#00FF00">'.$tradeblackmarketinc_lang[msg_1].'</font>';
          //kriegsartefakt gutschreiben und credits abziehen
          mysql_query("UPDATE de_user_data SET credits=credits-$sv_sm_preisliste[5], palenium=palenium+100 WHERE user_id = '$ums_user_id'",$db);
          $credits=$credits-$sv_sm_preisliste[5];
          refererbonus($sv_sm_preisliste[5]);
          writetocreditlog($tradeblackmarketinc_lang[paleniumgutschrift]);
          updatesmstat(6, $sv_sm_preisliste[5]);
        }
        else
        $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_7].'</font>';
      }
      else
      $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_2].'</font>';
    }
    else
    $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_8].'.</font>';

    //transaktionsende
    $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
    if ($erg)
    {
      //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
    }
    else
    {
      print("$tradeblackmarketinc_lang[msg_4_1] ".$ums_user_id." $tradeblackmarketinc_lang[msg_4_2]!<br><br><br>");
    }
  }// if setlock-ende
  else echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang[msg_5].'</font><br><br>';
}//ende submit6
*/

if ($submit7) {//tronic
    //transaktionsbeginn
    if (setLock($ums_user_id)) {
        //schauen ob er genug credits hat
        $db_daten = mysql_query("SELECT credits, sm_tronic FROM de_user_data WHERE user_id='$ums_user_id'", $db);
        $row = mysql_fetch_array($db_daten);
        $credits = $row[0];
        $sm_tronic = $row[1];

        if ($sm_tronic < floor($maxtick / $sm_tronic_lz)) {
            if ($credits >= $sv_sm_preisliste[6]) {
                $errmsg .= '<font color="#00FF00">'.$tradeblackmarketinc_lang['msg_1'].'</font>';
                //kollektor gutschreiben und credits abziehen
                mysql_query("UPDATE de_user_data SET credits=credits-$sv_sm_preisliste[6], restyp05=restyp05+25, sm_tronic=sm_tronic+1 WHERE user_id = '$ums_user_id'", $db);
                $sm_tronic++;
                $credits = $credits - $sv_sm_preisliste[6];
                refererbonus($sv_sm_preisliste[6]);
                writetocreditlog($tradeblackmarketinc_lang['tronicgutschrift']);
                updatesmstat(7, $sv_sm_preisliste[6]);
            } else {
                $errmsg .= '<font color="#FF0000">'.$tradeblackmarketinc_lang['msg_2'].'</font>';
            }
        } else {
            $errmsg .= '<font color="#FF0000">'.$tradeblackmarketinc_lang['msg_3'].'</font>';
        }

        //transaktionsende
        $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
        if ($erg) {
            //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        } else {
            print($tradeblackmarketinc_lang['msg_4_1'].' '.$ums_user_id.' '.$tradeblackmarketinc_lang['msg_4_2'].'!<br><br><br>');
        }
    }// if setlock-ende
    else {
        echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang['msg_5'].'</font><br><br>';
    }
}//ende submit7

//////////////////////////////////////////////
//////////////////////////////////////////////
//artefaktlieferung
//////////////////////////////////////////////
//////////////////////////////////////////////
//if($submit100 OR $submit101 OR $submit102 OR $submit103 OR $submit104 OR $submit105 OR $submit106 OR $submit107 OR $submit108  OR $submit109 OR $submit110 OR $submit111 OR $submit112 OR $submit113 OR $submit114)
/*
if($submit100 OR $submit104 OR $submit105 OR $submit106){

    //transaktionsbeginn
    if (setLock($ums_user_id)){

        //schauen welches artefakt es ist
        $artid=0;
        if($submit100)$artid=0;
        if($submit101)$artid=1;
        if($submit102)$artid=2;
        if($submit103)$artid=3;
        if($submit104)$artid=4;
        if($submit105)$artid=5;
        if($submit106)$artid=6;
        if($submit107)$artid=7;
        if($submit108)$artid=8;
        if($submit109)$artid=9;
        if($submit110)$artid=10;
        if($submit111)$artid=11;
        if($submit112)$artid=12;
        if($submit113)$artid=13;
        if($submit114)$artid=14;

        //schauen ob er genug credits hat und wie die lieferzeiten sind
        $result = mysql_query("SELECT credits, sm_art1, sm_art2, sm_art3, sm_art4, sm_art5, sm_art6, sm_art7, sm_art8, sm_art9, sm_art10, sm_art11, sm_art12, sm_art13, sm_art14, sm_art15, sm_art16, sm_art17, sm_art18, sm_art19 FROM de_user_data WHERE user_id = '$ums_user_id'", $db);
        $row = mysql_fetch_array($result);
        $credits=$row['credits'];

        $ai=$artid+1;
        if($row['sm_art$ai'] < floor($maxtick / $artefaktlz[$artid])){
            if($credits>=$artefaktpreis[$artid]){
                //schauen ob man das artefaktgeb�ude hat
                if (hasTech($pt, 28)){
                    //schauen ob man noch platz im artefaktgeb�ude hat
                    if(get_free_artefact_places($ums_user_id)>0){
                        $errmsg.='<font color="#00FF00">'.$tradeblackmarketinc_lang[msg_1].'</font>';
                        //credits abziehen
                        mysql_query("UPDATE de_user_data SET credits=credits-'$artefaktpreis[$artid]', sm_art$ai=sm_art$ai+1 WHERE user_id = '$ums_user_id'",$db);
                        //echo "UPDATE de_user_data SET credits=credits-'$artefaktpreis[$artid]', sm_art$ai=tick WHERE user_id = '$ums_user_id'";
                        $credits=$credits-$artefaktpreis[$artid];
                        refererbonus($artefaktpreis[$artid]);
                        $row['sm_art$ai']++;
                        writetocreditlog("$ua_name[$artid]-".$tradeblackmarketinc_lang[artefaktgutschrift]);
                        updatesmstat($artid+100, $artefaktpreis[$artid]);
                        $errmsg.='<font color="#00FF00">'.$tradeblackmarketinc_lang[msg_9].'<br><br></font>';
                        //artefakt zum spieler transferieren
                        mysql_query("INSERT INTO de_user_artefact (user_id, id, level) VALUES ('$ums_user_id', '$ai', '1')",$db);
                    }else $errmsg.='<table width=600><tr><td class="ccr">'.$tradeblackmarketinc_lang[msg_10].'.</td></tr></table>';
                }else $errmsg.='<table width=600><tr><td class="ccr">'.$tradeblackmarketinc_lang[msg_11].'</td></tr></table>';
            }
            else
            $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_2].'</font>';
        }
        else $errmsg.='<font color="#FF0000">'.$tradeblackmarketinc_lang[msg_3].'</font>';

        //transaktionsende
        $erg = releaseLock($ums_user_id); //L�sen des Locks und Ergebnisabfrage
        if ($erg)
        {
          //print("Datensatz Nr. 10 erfolgreich entsperrt<br><br><br>");
        }
        else
        {
          print("$tradeblackmarketinc_lang[msg_4_1] ".$ums_user_id." $tradeblackmarketinc_lang[msg_4_2]!<br><br><br>");
        }
    }// if setlock-ende
    else echo '<br><font color="#FF0000">'.$tradeblackmarketinc_lang[msg_5].'</font><br><br>';
}//ende submit7
*/

//sm_rboost lieferzeitpunkt berechnen
if ($tick > $sm_rboost + 1000) {
    //es ist lieferbar
    $lzp1 = '<i><font color="#00FF00">'.$tradeblackmarketinc_lang['msg_12'].'</font></i>';
} else {
    $lzp1 = '<i><font color="orange">'.$tradeblackmarketinc_lang['msg_13_1'].' '.(($tick - $sm_rboost - 1000) * (-1) + 1).' '.$tradeblackmarketinc_lang['msg_13_2'].'.</font></i>';
}

//berechnen bis wann der pa l�uft
if ($palaufzeit > time()) {
    //pa ist noch aktiv
    $palz = date("d.m.Y - G:i", $palaufzeit);
    $palz = $tradeblackmarketinc_lang['paruntime'].': '.$palz;
} else {
    //pa ist nicht aktiv
    $palz = $tradeblackmarketinc_lang['pana'];
}

include "resline.php";

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//orlandos avatar
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
/*
$title=$tradeblackmarketinc_lang['orlandodesc1'].'<br><font color=#FF0000>'.$tradeblackmarketinc_lang['orlandodesc2'].'</font>';
echo '<div class="cell" style="width:590px; height: 128px; position: relative; border: 1px solid #666666;">';
echo '<div style="background-color: #000000; width: 128px; height: 128px; left: 0px; position: relative; float: left">
      <img id="g200" src="'.$ums_gpfad.'g/orlando.png" border="0" width="100%" height="100%" title="'.$title.'">
      </div>';

echo '<div style="font-size:10pt; padding: 10px;">';
echo $tradeblackmarketinc_lang['ihrkontostand'].': <b>'.number_format($credits, 0,"",".").'</b> <img src="'.$ums_gpfad.'g/credits.gif" title="Credits"><br>';
echo ''.$tradeblackmarketinc_lang[msg_14_3].'';
echo '<br><a href="http://login.bgam.es/index.php?command=credits" target="_blank"><span class="text3">'.$tradeblackmarketinc_lang[creditangebot].'</span></a>';
echo '</div>';

echo '</div>';
*/
//echo '<div class="info_box text1" style="margin-top: 5px; font-size: 14px;">Preisnachl&auml;sse k&ouml;nnen durch <a href="religion.php?from=1">Bonuspunkte</a> errungen werden.</div>';


if ($errmsg != '') {
    echo '<br><div class="info_box">'.$errmsg.'</div>';
}

echo("<br>");
echo '<form action="blackmarket.php" method="POST">';

///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
// angebot darstellen
///////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////

//rahmen oben
rahmen_oben($tradeblackmarketinc_lang['angebote']);

echo '<table width="566px">';
echo '<tr class="cell1" align="center"><td colspan="2"><b>'.$tradeblackmarketinc_lang['artikel'].'</td><td><b>'.$tradeblackmarketinc_lang['genutzt'].'</td><td><b>'.$tradeblackmarketinc_lang['preis'].'</td></tr>';

///////////////////////////////////////////////////////////
//rohstofflieferung
///////////////////////////////////////////////////////////
//reminder button
$db_daten = mysql_query("SELECT sm_rboost_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
if ($row['sm_rboost_rem'] == 1) {
    $rembutton = '<a href="blackmarket.php?remsubmit1=1"><img id="rem1" src="'.$ums_gpfad.'g/symbol6.png" title="&'.$tradeblackmarketinc_lang['deacreminder'].'"></a>';
} else {
    $rembutton = '<a href="blackmarket.php?remsubmit1=1"><img id="rem1" src="'.$ums_gpfad.'g/symbol7.png" title="&'.$tradeblackmarketinc_lang['acreminder'].'"></a>';
}

echo '<tr class="cell" align="center">
  <td width="50px">
    <div style="background-color: #000000; width: 50px; height: 50px;">
      <img id="g3" src="'.$ums_gpfad.'g/symbol9.png" border="0" title="'.$tradeblackmarketinc_lang['msg_15_1'].'">
    </div>
  </td>
  <td align="left">'.$rembutton.' '.$tradeblackmarketinc_lang['msg_15_2a'].
'<br>'.$tradeblackmarketinc_lang['msg_15_2b'].' ('.$rtick.' '.$tradeblackmarketinc_lang['wochen'].') '.$tradeblackmarketinc_lang['msg_15_2c'].'<b>
  <br>'.number_format($rtick * 500, 0, "", ".").' '.$tradeblackmarketinc_lang['m'].'
  <br>'.number_format($rtick * 250, 0, "", ".").' '.$tradeblackmarketinc_lang['d'].'
  <br>'.number_format($rtick * 60, 0, "", ".").' '.$tradeblackmarketinc_lang['i'].'
  <br>'.number_format($rtick * 37, 0, "", ".").' '.$tradeblackmarketinc_lang['e'].'</b>
  <br><font color="#00FF00">'.$lzp1.'</font>
  <br><center></td>
  <td>&nbsp;</td>
  <td>'.$sv_sm_preisliste[2].' <img src="'.$ums_gpfad.'g/credits.gif" title="Credits">
  <input type="submit" name="submit3" value="'.$tradeblackmarketinc_lang['kaufen'].'"></td></tr>';

///////////////////////////////////////////////////////////
//palenium
///////////////////////////////////////////////////////////
/*
echo('<tr class="cell1" align="center"><td align="left"><u>'.$tradeblackmarketinc_lang[msg_16].'</i></td><td>'.$sv_sm_preisliste[5].'</td><td><input type="submit" name="submit6" value="'.$tradeblackmarketinc_lang[kaufen].'"></td></tr>');
*/

///////////////////////////////////////////////////////////
//troniclieferung
///////////////////////////////////////////////////////////
//reminder button
$db_daten = mysql_query("SELECT sm_tronic_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
if ($row['sm_tronic_rem'] == 1) {
    $rembutton = '<a href="blackmarket.php?remsubmit2=1"><img id="rem2" src="'.$ums_gpfad.'g/symbol6.png" title="&'.$tradeblackmarketinc_lang['deacreminder'].'"></a>';
} else {
    $rembutton = '<a href="blackmarket.php?remsubmit2=1"><img id="rem2" src="'.$ums_gpfad.'g/symbol7.png" title="&'.$tradeblackmarketinc_lang['acreminder'].'"></a>';
}

//beschreibung und m�gliche kaufanzahl
if ($sm_tronic < floor($maxtick / $sm_tronic_lz)) {
    $str1 = '<font color="#00FF00">';
    $str2 = '</font>';
} else {
    $str1 = '';
    $str2 = '';
}
$lzp4 = $str1.number_format($sm_tronic, 0, "", ".").'/'.number_format(floor($maxtick / $sm_tronic_lz), 0, "", ".").$str2;
//beschreibung
$c1=0;
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
echo '<tr class="'.$bg.'" align="center">
  <td>
    <div style="background-color: #000000; width: 50px; height: 50px;">
      <img id="g7" src="'.$ums_gpfad.'g/symbol8.png" border="0" title="Tronic">
    </div>
  </td>
  <td align="left">'.$rembutton.' '.$tradeblackmarketinc_lang['msg_17_1'].'.<br><i>'.
$tradeblackmarketinc_lang['msg_17_2'].'</i></td><td>'.$lzp4.
'<td>'.$sv_sm_preisliste[6].' <img src="'.$ums_gpfad.'g/credits.gif" title="Credits"><input type="submit" name="submit7" value="'.$tradeblackmarketinc_lang['kaufen'].'"></td></tr>';

///////////////////////////////////////////////////////////
//kriegsartefakt
///////////////////////////////////////////////////////////
//reminder button
$db_daten = mysql_query("SELECT sm_kartefakt_rem FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);

if ($row['sm_kartefakt_rem'] == 1) {
    $rembutton = '<a href="blackmarket.php?remsubmit3=1"><img id="rem3" src="'.$ums_gpfad.'g/symbol6.png" title="&'.$tradeblackmarketinc_lang['deacreminder'].'"></a>';
} else {
    $rembutton = '<a href="blackmarket.php?remsubmit3=1"><img id="rem3" src="'.$ums_gpfad.'g/symbol7.png" title="&'.$tradeblackmarketinc_lang['acreminder'].'"></a>';
}

//beschreibung und m�gliche kaufanzahl
if ($sm_kartefakt < floor($maxtick / $sm_kartefakt_lz)) {
    $str1 = '<font color="#00FF00">';
    $str2 = '</font>';
} else {
    $str1 = '';
    $str2 = '';
}
$lzp3 = $str1.number_format($sm_kartefakt, 0, "", ".").'/'.number_format(floor($maxtick / $sm_kartefakt_lz), 0, "", ".").$str2;
if ($c1 == 0) {
    $c1 = 1;
    $bg = 'cell1';
} else {
    $c1 = 0;
    $bg = 'cell';
}
//beschreibung
echo '<tr class="'.$bg.'" align="center">
  <td>
    <div style="background-color: #000000; width: 50px; height: 50px;">
      <img id="g3" src="'.$ums_gpfad.'g/symbol10.png" border="0" title="'.$tradeblackmarketinc_lang['kriegsartefakt'].'">
    </div>
  </td>
  <td align="left">'.$rembutton.' '.$tradeblackmarketinc_lang['msg_18_1'].'
  </td>
  <td>'.$lzp3.'</td>
  <td>'.$sv_sm_preisliste[1].' <img src="'.$ums_gpfad.'g/credits.gif" title="Credits"><input type="submit" name="submit2" value="'.$tradeblackmarketinc_lang['kaufen'].'"></td></tr>';


/*
///////////////////////////////////////////////////////////
//gebrauchter kollektor
///////////////////////////////////////////////////////////
//reminder button
$db_daten = mysql_query("SELECT sm_col_rem FROM de_user_data WHERE user_id='$ums_user_id'",$db);
$row = mysql_fetch_array($db_daten);

if($row['sm_col_rem']==1) $rembutton='<a href="blackmarket.php?remsubmit4=1"><img id="rem4" src="'.$ums_gpfad.'g/symbol6.png" title="&'.$tradeblackmarketinc_lang['deacreminder'].'"></a>';
else $rembutton='<a href="blackmarket.php?remsubmit4=1"><img id="rem4" src="'.$ums_gpfad.'g/symbol7.png" title="&'.$tradeblackmarketinc_lang['acreminder'].'"></a>';

//beschreibung und m�gliche kaufanzahl
if($sm_col < floor($maxtick/$sm_col_lz)){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
$lzp2=$str1.number_format($sm_col, 0,"",".").'/'.number_format(floor($maxtick/$sm_col_lz), 0,"",".").$str2;
if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
echo('<tr class="'.$bg.'" align="center">
<td>
  <div style="background-color: #000000; width: 50px; height: 50px;">
    <img id="g7" src="'.$ums_gpfad.'g/kollie.gif" width="50px" border="0" title="'.$tradeblackmarketinc_lang['gebrauchterkollektor'].'">
  </div>
</td>
<td align="left">'.$rembutton.' '.$tradeblackmarketinc_lang[msg_22_1].'
</td>
<td>'.$lzp2.'</td>
<td>'.$sv_sm_preisliste[0].' <img src="'.$ums_gpfad.'g/credits.gif" title="Credits"><input type="submit" name="submit1" value="'.$tradeblackmarketinc_lang[kaufen].'"></td></tr>');

///////////////////////////////////////////////////////////
//diplomatieartefakt
///////////////////////////////////////////////////////////
if($GLOBALS['sv_ang']!=1){
  //beschreibung und m�gliche kaufanzahl
  $db_daten=mysql_query("SELECT dartefakt FROM de_user_data WHERE user_id='$ums_user_id'",$db);
  $row = mysql_fetch_array($db_daten);
  $dartefakt=$row['dartefakt'];

  if($dartefakt < $sv_max_dartefakt){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
  $lzpdartefakt=$str1.number_format($dartefakt, 0,"",".").'/'.number_format($sv_max_dartefakt, 0,"",".").$str2;

  echo('<tr class="cell" align="center">
  <td>
    <div style="background-color: #000000; width: 50px; height: 50px;">
      <img id="g3" src="'.$ums_gpfad.'g/symbol11.png" border="0" title="'.$tradeblackmarketinc_lang['diplomatieartefakt'].'">
    </div>
  </td><td align="left">'.$tradeblackmarketinc_lang[msg_19_1].' '.
  $sv_max_dartefakt.' '.$tradeblackmarketinc_lang[msg_19_3].'</td>
  <td>'.$lzpdartefakt.'</td>
  <td>'.$sv_sm_preisliste[4].' <img src="'.$ums_gpfad.'g/credits.gif" title="Credits"><input type="submit" name="submit5" value="'.$tradeblackmarketinc_lang[kaufen].'"></td></tr>');
}

///////////////////////////////////////////////////////////
//spielerartefakte
///////////////////////////////////////////////////////////
//lieferzeiten auslesen
$result = mysql_query("SELECT sm_art1, sm_art2, sm_art3, sm_art4, sm_art5, sm_art6, sm_art7, sm_art8, sm_art9,  sm_art10,  sm_art11,
 sm_art12, sm_art13, sm_art14, sm_art15,
 sm_art1rem, sm_art2rem, sm_art3rem, sm_art4rem, sm_art5rem, sm_art6rem, sm_art7rem, sm_art8rem, sm_art9rem, sm_art10rem, sm_art11rem,
 sm_art12rem, sm_art13rem, sm_art14rem, sm_art15rem
 FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($result);

$submit=100;
for($i=0;$i<=$ua_index;$i++){
      $ai=$i+1;
      if(in_array($submit,$artefaktangebot)){
          if ($c1==0){$c1=1;$bg='cell1';}else{$c1=0;$bg='cell';}
          //beschreibung und m�gliche kaufanzahl
          if($row['sm_art$ai'] < floor($maxtick/$artefaktlz[$i])){$str1='<font color="#00FF00">';$str2='</font>';}else{$str1='';$str2='';}
          $lzart=$str1.number_format($row['sm_art$ai'], 0,"",".").'/'.number_format(floor($maxtick/$artefaktlz[$i]), 0,"",".").$str2;


          //reminder button
          if($row['sm_art".$ai."rem']==1) $rembutton='<a href="blackmarket.php?remsubmit'.$submit.'=1"><img id="rem'.$submit.'" src="'.$ums_gpfad.'g/symbol6.png" title="&'.$tradeblackmarketinc_lang['deacreminder'].'"></a>';
          else $rembutton='<a href="blackmarket.php?remsubmit'.$submit.'=1"><img id="rem'.$submit.'" src="'.$ums_gpfad.'g/symbol7.png" title="&'.$tradeblackmarketinc_lang['acreminder'].'"></a>';

          //bonuswert
          $bonus='';
          if($ua_werte[$i][0][0]>0)$bonus.=' '.$tradeblackmarketinc_lang['bonus'].': '.number_format($ua_werte[$i][0][0], 2,",",".").'%';

          echo('<tr class="'.$bg.'" align="center">
          <td align="left">
            <div style="background-color: #000000; width: 50px; height: 50px;">
            <img id="g'.$submit.'" src="'.$ums_gpfad.'g/arte'.$ai.'.gif" border="0" title="'.$ua_name[$i].'">
            </div>
          </td>
          <td align="left">
          </span>'.$rembutton.' <u><b>'.$ua_name[$i].'-'.$tradeblackmarketinc_lang[artilvleins].'</b></u><br>'.
          $ua_desc[$i].$bonus.'</span></td>

          <td>'.$lzart.'</td>
          <td>'.$artefaktpreis[$i].' <img src="'.$ums_gpfad.'g/credits.gif" title="Credits"><input type="submit" name="submit'.$submit.'" value="'.$tradeblackmarketinc_lang[kaufen].'"></td></tr>');
      }
      $submit++;
}
*/
echo('</table>');

//rahmen unten
rahmen_unten();

echo '</form>';

function refererbonus($credits)
{
    //bonus wurde entfernt
}

function writetocreditlog($clog)
{
    global $ums_user_id, $credits;
    $datum = date("Y-m-d H:i:s", time());
    $ip = getenv("REMOTE_ADDR");
    $clog = "Zeit: $datum\nIP: $ip\n".$clog."- Neuer Creditstand: $credits\n--------------------------------------\n";
    $fp = fopen("cache/creditlogs/$ums_user_id.txt", "a");
    fputs($fp, $clog);
    fclose($fp);
}

function updatesmstat($smid, $credits)
{
    global $db;
    mysql_query("UPDATE de_system SET smstat$smid=smstat$smid+'$credits'", $db);
}


echo '<script language="javascript">';
$data = array('a' => $artefacts);
echo 'var a = '.json_encode($artefacts).';';
?>
for(i=0;i<=200;i++)
$('#rem'+i+',#g'+i).tooltip({ 
      track: true, 
      delay: 0, 
      showURL: false, 
      showBody: "&",
      extraClass: "design1", 
      fixPNG: true,
      opacity: 0.15
	  });	  
	    
</script>
</body>
</html>

