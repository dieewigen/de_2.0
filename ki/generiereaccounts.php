<?php
include "../inccon.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Accountgenerator</title>
</head>
<body>
<?php



mt_srand((double)microtime()*10000);

function generierespielername(){
	//namen zusammenbauen
	//struktur: 1-4 silben bindestrich 1-5 silben

	$silben=array('ar','xa','xo','na','an','ra','ox','ax','yn','ny','za','az',
	'zy','yz','ka','ak','as','sa','co','oc','ac','ca','te','et','tz','zt','it','ti',
	'tx','xt','lo','ol','yl','ly','ay','ya','ry','yr');

	$anzsilben=count($silben);

	$name='';
	//1. teil
	$csilben=rand(1,4);
	for ($i=1; $i<=$csilben; $i++)
	{
	  $suchsilbe=$silben[rand(0,$anzsilben-1)];
	  if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
	  $name.=$suchsilbe;
	}
	$name.='-';
	$csilben=rand(1,5);
	for ($i=1; $i<=$csilben; $i++)
	{
	  $suchsilbe=$silben[rand(0,$anzsilben-1)];
	  if ($i==1)$suchsilbe = ucfirst ($suchsilbe);
	  $name.=$suchsilbe;
	}
	return $name;
}

if ($_REQUEST["anzahl"] AND $_REQUEST["create"])
{
  //accounts in die db einfügen
  for ($j=0; $j<=$_REQUEST["anzahl"]-1; $j++)
  {

    //spielername erzeugen und schauen ob er schon vergeben ist
    $ok=0;
    while ($ok==0)
    {
      $spielername=generierespielername();
      $result = mysql_query("SELECT user_id FROM de_user_data WHERE spielername='$spielername'",$db);
      if (mysql_num_rows($result)==0) $ok=1;
      echo $spielername.'<br>';
    }
    
    //loginnamen erzeugen und schauen ob er schon vergeben ist
    $ok=0;
    while ($ok==0)
    {
      $zz=rand(100000000, 900000000);
      $loginname='ki'.$zz;
      $result = mysql_query("SELECT user_id FROM de_login WHERE nic='$loginname'",$db);
      if (mysql_num_rows($result)==0) $ok=1;
      echo $loginname.'<br>';
    }
    
    //e-mail-addy erzeugen und schauen ob sie schon vergeben ist
    $ok=0;
    while ($ok==0)
    {
      $reg_mail='ki'.$zz.'@example.com';
      $result = mysql_query("SELECT user_id FROM de_login WHERE reg_mail='$reg_mail'",$db);
      if (mysql_num_rows($result)==0) $ok=1;
      $zz=rand(1000000000, 4000000000);
      echo $reg_mail.'<br>';
    }

    //account einfügen
    //de_login
    mysql_query("INSERT INTO de_login (nic, reg_mail, pass, register, last_login, status, last_ip)
      VALUES ('$loginname', '$reg_mail', PASSWORD('$reg_mail'), NOW(), NOW(), 1, '127.0.0.1')", $db);
    $user_id=mysql_insert_id();

    //de_user_data
    mysql_query("INSERT INTO de_user_data (user_id, spielername, restyp01, restyp02, techs,
      ekey, sector, system, rasse, npc, nrrasse, nrspielername)
      VALUES ($user_id , '$spielername' ,10000, 5000,
      's0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000',
      '100;0;0;0', 0, 0, 5, 1, 5, '$spielername')", $db);

    //de_user_fleet
    $fleet_id=$user_id.'-0';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);

    $fleet_id=$user_id.'-1';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);
    $fleet_id=$user_id.'-2';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);

    $fleet_id=$user_id.'-3';
    mysql_query("INSERT INTO de_user_fleet (user_id) VALUES ('$fleet_id')", $db);

    //de_user_info
    mysql_query("INSERT INTO de_user_info (user_id, vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht)
      VALUES ('$user_id', '$vorname', '$nachname', '$strasse', $plz, '$ort', '$land', '$telefon', $tag, $monat, $jahr, $geschl)", $db);

    
  }
  echo("Done. Es wurden ".$_REQUEST["anzahl"]." Accounts erstellt.<br><br>");
}
echo '<form method="post">';
echo 'Wieviel neue Accounts anlegen? ';
echo '<input type="text" name="anzahl" size="5" maxlength="5" value="0">';
echo '<br><input type="Submit" name="create" value="Anlegen">';
echo '<form>';
?>
</body>
</html>
