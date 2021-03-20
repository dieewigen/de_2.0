<?php
mt_srand((double)microtime()*10000);
//der gegnerdatensatz muß vom aufrufenden script bereits nach $enmrow geladen worden sein

//gegnerdaten auslesen
$enm_name=$enmrow["name"];
$enm_level=$enmrow["level"];
$enm_ship_diameter=$enmrow["shipdiameter"];
$enm_sp=$enmrow["sp"];
$enm_spmax=$enmrow["spmax"];
$enm_att=$enmrow["att"];
$enm_attmax=$enmrow["attmax"];
$enm_shield=$enmrow["shield"];
$enm_shieldmax=$enmrow["shieldmax"];
$enm_cardeck=$enmrow["carddeck"];
$enm_cardid[0]=$enmrow["enmcardid1"];
$enm_cardid[1]=$enmrow["enmcardid2"];
$enm_cardid[2]=$enmrow["enmcardid3"];
$enm_cardid[3]=$enmrow["enmcardid4"];
$enm_cardid[4]=$enmrow["enmcardid5"];

//spielerdaten auslesen, die nicht in der session stehen
$pc_sp=$enmrow["pcsp"];
$pc_spmax=$enmrow["pcspmax"];
$pc_att=$enmrow["pcatt"];
$pc_attmax=$enmrow["pcattmax"];
$pc_shield=$enmrow["pcshield"];
$pc_shieldmax=$enmrow["pcshieldmax"];

$pc_cardid[0]=$enmrow["pccardid1"];
$pc_cardid[1]=$enmrow["pccardid2"];
$pc_cardid[2]=$enmrow["pccardid3"];
$pc_cardid[3]=$enmrow["pccardid4"];
$pc_cardid[4]=$enmrow["pccardid5"];

$fightinfo='';

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
// kartenzug berechnen
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
if($_REQUEST["slotid"]>0)
{
  //slot der gespielt wird auslesen
  $slotid=intval($_REQUEST["slotid"]);
  
  //aus dem slot die cardid auslesen
  $cardid=$pc_cardid[$slotid-1];
  
  //karte laden
  $db_daten=mysql_query("SELECT * FROM sou_cards WHERE id='$cardid' LIMIT 1",$soudb);
  $pc_card_data = mysql_fetch_array($db_daten);

  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  //zugwerte festlegen
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////

  $pc_att_turn=0;
  $pc_att_shield_turn=0;
  $pc_att_sp_turn=0;
  $pc_shiel_reg_turn=0;
  $pc_sp_reg_turn=0;
  
  //attpercentage
  $pc_att_turn+=round($pc_card_data["attpercentage"]/100*$pc_att);
  $pc_att_shield_turn+=round($pc_card_data["attshieldpercentage"]/100*$pc_att);
  $pc_att_sp_turn+=round($pc_card_data["attsppercentage"]/100*$pc_att);
  
  //attabsolute
  $pc_att_turn+=$pc_card_data["attabsolute"];
  $pc_att_shield_turn+=round($pc_card_data["attshieldabsolute"]);
  $pc_att_sp_turn+=round($pc_card_data["attspabsolute"]);
  
  //shieldregpercentage
  $pc_shiel_reg_turn+round($pc_card_data["shieldregpercentage "]/100*$pc_shieldmax);
  
  //shieldregabsolute
  $pc_shiel_reg_turn+=$pc_card_data["shieldregabsolute"];
  
  //spregpercentage
  $pc_sp_reg_turn+round($pc_card_data["spregpercentage "]/100*$pc_spmax);
  
  //spregabsolute
  $pc_sp_reg_turn+=$pc_card_data["spregabsolute"];
	
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  //spieler ist zuerst am zug	
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  
  //schildregeneration spieler
  if($pc_shiel_reg_turn>0)
  {
    $pc_shield+=$pc_shiel_reg_turn;
    if($pc_shield>$pc_shieldmax)$pc_shield=$pc_shieldmax;
    $fightinfo.='Deine Schildregeneration: '.$pc_shiel_reg_turn.' - ';
  }
  
  //hüllenregeneration spieler
  if($pc_sp_reg_turn>0)
  {
    $pc_sp+=$pc_shiel_reg_turn;
    if($pc_sp>$pc_spmax)$pc_sp=$pc_spmax;
    $fightinfo.='Deine H&uuml;llenregeneration: '.$pc_sp_reg_turn.' - ';
  }
  
  function fight_calc_att($target, $source)
  {
    if($target<=$source)
    {
  	  $change=$target;
  	  $source=$source-$target;
  	  $target=0;
    }
    else //schild hat mehr energie als der angriff
    {
  	  $change=$source;
  	  $target=$target-$source;
  	  $source=0;
    }
    
    return(array($target, $source, $change));
  }
  
  //variablen um die änderungen zu loggen
  $enm_info_shield_damage=0;
  $enm_info_sp_damage=0;
  
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  // angriff des spielers, waffenenergie die nur auf schilde geht
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  if($pc_att_shield_turn>0)
  {
    $re=fight_calc_att($enm_shield, $pc_att_shield_turn);
    $enm_shield=$re[0];
    $pc_att_shield_turn=$re[1];
    $enm_info_shield_damage+=$re[2];
  }

  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  // angriff des spielers, waffenenergie die nur auf die hülle geht
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  if($pc_att_sp_turn>0)
  {   
    $re=fight_calc_att($enm_sp, $pc_att_sp_turn);
    $enm_sp=$re[0];
    $pc_att_sp_turn=$re[1];
    $enm_info_sp_damage+=$re[2];
  }

  
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  // angriff des spielers, waffenenergie die zuerst auf schilde und dann auf die hülle geht
  //////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////
  
  //zuerst wird auf die schilde geschossen
  if($pc_att_turn>0)
  {
    $re=fight_calc_att($enm_shield, $pc_att_turn);
    $enm_shield=$re[0];
    $pc_att_turn=$re[1];
    $enm_info_shield_damage+=$re[2];    
  }
  
  //angriff auf die schiffshülle
  if($pc_att_turn>0)
  {    
    $re=fight_calc_att($enm_sp, $pc_att_turn);
    $enm_sp=$re[0];
    $pc_att_turn=$re[1];
    $enm_info_sp_damage+=$re[2];    
  }
   
  ///////////////////////////////
  ///////////////////////////////
  //gegner ist am zug
  ///////////////////////////////
  ///////////////////////////////
  //das schiff kämpft nur dann, wenn die hüllenstruktur > 0 ist
  if($enm_sp>0)
  {
    //zuerst wird auf die schilde geschossen
    if($pc_shield>0)
    {
      if($pc_shield<=$enm_att)
      {
  	    $fightinfo.='Dein Schildschaden: '.$pc_shield.' - ';
  	    $enm_att=$enm_att-$pc_shield;
  	    $pc_shield=0;
      }
      else //schild hat mehr energie als der angriff
      {
  	    $fightinfo.='Dein Schildschaden: '.$enm_att.' - ';
  	    $pc_shield=$pc_shield-$enm_att;
  	    $enm_att=0;
      }
    }
  
    if($enm_att>0)
    {
      //angriff auf die schiffshülle
      if($pc_sp<=$enm_att)
      {
  	    $fightinfo.='Dein H&uuml;llenschaden: '.$pc_sp.' - ';
  	    $enm_att=$enm_att-$pc_sp;
  	    $pc_sp=0;
      }
      else //schiffshülle hat mehr energie als der angriff
      {
  	    $fightinfo.='Dein H&uuml;llenschaden: '.$enm_att.'';
  	    $pc_sp=$pc_sp-$enm_att;
  	    $enm_att=0;
      }
    }
  }
  
  ////////////////////////////////////////
  ////////////////////////////////////////
  //überprüfen ob der kampf zu ende ist
  ////////////////////////////////////////
  ////////////////////////////////////////
  $endinfo='';
  //man hat gewonnen
  if($enm_sp<=0)
  {
  	//feindliches schiff aus der db werfen
  	$db_daten=mysql_query("DELETE FROM sou_user_enm WHERE user_id='$player_user_id'",$soudb);
  	
  	$endinfo='Du hast den Kampf gewonnen. Dunkle Materie erbeutet: '.$enm_level.' cm&sup3;';
  	
  	//der fraktion die dunkle materie gutschreiben
  	//mysql_query("UPDATE sou_system SET f".$player_fraction."res1=f".$player_fraction."res1+'$enm_level'",$soudb);
  	//dem spieler die dunkle materie gutschreiben
  	change_darkmatter($player_user_id, $enm_level);
  }
  elseif($pc_sp<=0) //der gegner hat gewonnen
  {
  	//feindliches schiff aus der db werfen
  	$db_daten=mysql_query("DELETE FROM sou_user_enm WHERE user_id='$player_user_id'",$soudb);

  	//reparaturzeit
  	$time=time()+600;
  	$chb++;
    mysql_query("UPDATE sou_user_data SET atimer1typ=5, atimer1time='$time' WHERE user_id='$player_user_id'",$soudb);
   	//header("Location: sou_main.php");
   	$endinfo='Du hast den Kampf verloren.';
  }

  //ggf sieg/verlustmeldung anzeigen
  if($endinfo!='')
  {
	echo '<br>';
	echo '<div align="center">';
	rahmen0_oben();  
	
	echo '<br>';
	echo $endinfo;
	echo '<br><br><a href="sou_main.php?action=systempage"><div class="b1" align="center">weiter</div></a><br><br>';

	rahmen0_unten();
	echo '<br>';
	die('</body></html>');
  }
  //die kampfdaten updaten
  mysql_query("UPDATE sou_user_enm SET shield='$enm_shield', sp='$enm_sp', pcshield='$pc_shield', pcsp='$pc_sp'  WHERE user_id='$player_user_id'",$soudb);  
  
  //die benutzte karte entfernen
  mysql_query("UPDATE sou_user_enm SET pccardid$slotid=0 WHERE user_id='$player_user_id'",$soudb);
  $pc_cardid[$slotid-1]=0;
  
  
  //fightinfo zusammenbauen
  $fightinfo.=' - Gegner Schildschaden: '.$enm_info_shield_damage.' - Gegner H&uuml;llenschaden: '.$enm_info_sp_damage;
  
  //fürs optische die waffenstärke wieder auf max setzen
  $enm_att=$enm_attmax;
  $pc_att=$pc_attmax;
}

///////////////////////////////////////////////////
///////////////////////////////////////////////////
// karten vergeben
///////////////////////////////////////////////////
///////////////////////////////////////////////////

//gegnerische karten


//spielerkarten
for($i=0;$i<5;$i++)
{
  //überprüfen ob er eine karte in dem slot hat
  if($pc_cardid[$i]==0)
  {
    //keine karte vorhanden, also eine vergeben
    $pc_cardid[$i]=mt_rand(1,6);
    $dbcardpos=$i+1;
    $dbcardid=$pc_cardid[$i];
    mysql_query("UPDATE sou_user_enm SET pccardid$dbcardpos='$dbcardid' WHERE user_id='$player_user_id'",$soudb);
  }
}



///////////////////////////////////////////////////
///////////////////////////////////////////////////
// kampfteilnehmer anzeigen anzeigen
///////////////////////////////////////////////////
///////////////////////////////////////////////////

?>
<style type="text/css"><!--

.cardmainenm1, .cardmainenm2, .cardmainenm3, .cardmainenm4, .cardmainenm5 {width:120px; height: 150px; background-color: #DDDDDD; color: #000000; font-size:10px; cursor: pointer; top:10px;}
.cardmainenm1 {position: absolute; right: 10px;}
.cardmainenm2 {position: absolute; right: 140px;}
.cardmainenm3 {position: absolute; right: 270px;}
.cardmainenm4 {position: absolute; right: 400px;}
.cardmainenm5 {position: absolute; right: 530px;}

.cg_shipenmmain {background-color: #720018; width:150px; height: 150px; position: absolute; left: 10px; top:10px}
.cg_shipenminfo {background-color: #720018; width:150px; height: 150px; position: absolute; left: 170px; top:10px}



.cardmain1, .cardmain2, .cardmain3, .cardmain4, .cardmain5 {width:120px; height: 150px; background-color: #DDDDDD; color: #000000; font-size:10px; cursor: pointer; bottom:10px;}
.cardmain1 {position: absolute; left: 10px;}
.cardmain2 {position: absolute; left: 140px;}
.cardmain3 {position: absolute; left: 270px;}
.cardmain4 {position: absolute; left: 400px;}
.cardmain5 {position: absolute; left: 530px;}

.cg_shipmain {background-color: #003a07; width:150px; height: 150px; position: absolute; right: 10px; bottom:10px}
.cg_shipinfo {background-color: #003a07; width:150px; height: 150px; position: absolute; right: 170px; bottom:10px}




.cg_infobox {background-color: #222222; width:960px; height: 60px; position: absolute; left: 10px; top: 170px}

--></style>
<?php

//spielbereich - anfang
echo '<br>';
echo '<div style="width: 980px; height: 400px; background-color: #333333; position: relative;">';

//gegnerisches schiff
echo '<div class="cg_shipenmmain">';
echo $enm_name;
echo '<br><img src="'.$gpfad.'v1.gif" width="100" height="100">';
echo '</div>';

//schiffsinfo
echo '<div class="cg_shipenminfo">';
echo 'Durchmesser: '.number_format($enm_ship_diameter, 0,"",".").'<br>H&uuml;llenstruktur: '.number_format($enm_sp, 0,"",".").'/'.number_format($enm_spmax, 0,"",".").'<br>Schildst&auml;rke: '.number_format($enm_shield, 0,"",".").'/'.number_format($enm_shieldmax, 0,"",".").'<br>Waffenst&auml;rke: '.number_format($enm_att, 0,"",".").'/'.number_format($enm_attmax, 0,"",".");
echo '</div>';

//gegnerische karten
for($i=1; $i<=5; $i++)
echo '<div class="cardmainenm'.$i.'">&nbsp;</div>';

//eigenes schiff
echo '<div class="cg_shipmain">';
echo $player_ship_name;
echo '<br><img src="'.$gpfad.'v1.gif" width="100" height="100">';
echo '</div>';

//schiffsinfo
echo '<div class="cg_shipinfo">';
echo 'Durchmesser: '.number_format($player_ship_diameter, 0,"",".").'<br>H&uuml;llenstruktur: '.number_format($pc_sp, 0,"",".").'/'.number_format($pc_spmax, 0,"",".").'<br>Schildst&auml;rke: '.number_format($pc_shield, 0,"",".").'/'.number_format($pc_shieldmax, 0,"",".").'<br>Waffenst&auml;rke: '.number_format($pc_att, 0,"",".").'/'.number_format($pc_attmax, 0,"",".");
echo '</div>';

//eigene karten
for($i=0; $i<5; $i++)
{
  //kartendaten auslesen
  $loadcardid=$pc_cardid[$i];
  
  $db_daten=mysql_query("SELECT * FROM sou_cards WHERE id='$loadcardid' LIMIT 1",$soudb);
  $pc_card_data = mysql_fetch_array($db_daten);
  
  echo '<div class="cardmain'.($i+1).'" onClick="javascript:location.href=\'sou_main.php?action=systempage&slotid='.($i+1).'\'">'.$pc_card_data["name"];
  //daten der karte ausgeben, damit man weiß was passiert

  $pc_att_turn=0;
  $pc_att_shield_turn=0;
  $pc_att_sp_turn=0;
  $pc_shiel_reg_turn=0;
  $pc_sp_reg_turn=0;
  
  //attpercentage
  $pc_att_turn+=round($pc_card_data["attpercentage"]/100*$pc_att);
  $pc_att_shield_turn+=round($pc_card_data["attshieldpercentage"]/100*$pc_att);
  $pc_att_sp_turn+=round($pc_card_data["attsppercentage"]/100*$pc_att);
  
  //attabsolute
  $pc_att_turn+=$pc_card_data["attabsolute"];
  $pc_att_shield_turn+=round($pc_card_data["attshieldabsolute"]);
  $pc_att_sp_turn+=round($pc_card_data["attspabsolute"]);
  
  //shieldregpercentage
  $pc_shiel_reg_turn+round($pc_card_data["shieldregpercentage "]/100*$pc_shieldmax);
  
  //shieldregabsolute
  $pc_shiel_reg_turn+=$pc_card_data["shieldregabsolute"];
  
  //spregpercentage
  $pc_sp_reg_turn+round($pc_card_data["spregpercentage "]/100*$pc_spmax);
  
  //spregabsolute
  $pc_sp_reg_turn+=$pc_card_data["spregabsolute"];
  
  //angriff
  if($pc_att_turn>0)echo '<br>Phasenschaden: '.$pc_att_turn;
  if($pc_att_shield_turn>0)echo '<br>Schildschaden: '.$pc_att_shield_turn;
  if($pc_att_sp_turn>0)echo '<br>H&uuml;llenschaden: '.$pc_att_sp_turn;
  //regeneration
  if($pc_shiel_reg_turn>0)echo '<br>Schildregeneration: '.$pc_shiel_reg_turn;
  if($pc_sp_reg_turn>0)echo '<br>H&uuml;llenregeneration: '.$pc_sp_reg_turn;
  
  //karte ende
  echo '</div>';
}

//infobox
echo '<div class="cg_infobox">'.$fightinfo.'</div>';



//spielbereich - ende
echo '</div>';
?>