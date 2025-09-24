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

//update für Sektor 666
/*
UPDATE de_user_data SET spielername='DX*Buguser', nrspielername='DX*Buguser', rasse=1 WHERE sector=666 AND `system`=1;
UPDATE de_user_data SET spielername='DX*Cheater', nrspielername='DX*Cheater', rasse=1 WHERE sector=666 AND `system`=2;
UPDATE de_user_data SET spielername='DX*Flamer', nrspielername='DX*Flamer', rasse=2 WHERE sector=666 AND `system`=3;
UPDATE de_user_data SET spielername='DX*Hacker', nrspielername='DX*Hacker', rasse=2 WHERE sector=666 AND `system`=4;
UPDATE de_user_data SET spielername='DX*Multi', nrspielername='DX*Multi', rasse=3 WHERE sector=666 AND `system`=5;
UPDATE de_user_data SET spielername='DX*PW-Sharer', nrspielername='DX*PW-Sharer', rasse=3 WHERE sector=666 AND `system`=6;
UPDATE de_user_data SET spielername='DX*Scripter', nrspielername='DX*Scripter', rasse=4 WHERE sector=666 AND `system`=7;
UPDATE de_user_data SET spielername='DX*Sekhopper', nrspielername='DX*Sekhopper', rasse=4 WHERE sector=666 AND `system`=8;
UPDATE de_user_data SET spielername='DX*Spamer', nrspielername='DX*Spamer', rasse=5 WHERE sector=666 AND `system`=9;
UPDATE de_user_data SET spielername='DX*ExecutorKarlath', nrspielername='DX*ExecutorKarlath', rasse=5 WHERE sector=666 AND `system`=10;


Buguser
Cheater
Flamer
Hacker
Multi
PW-Sharer
Scripter
Sekhopper
Spamer
ExecutorKarlath
*/

function generierespielername()
{
    //namen zusammenbauen
    //struktur: 1-4 silben bindestrich 1-5 silben

    $silben = array('ar','xa','xo','na','an','ra','ox','ax','yn','ny','za','az',
    'zy','yz','ka','ak','as','sa','co','oc','ac','ca','te','et','tz','zt','it','ti',
    'tx','xt','lo','ol','yl','ly','ay','ya','ry','yr');

    $anzsilben = count($silben);

    $name = 'DX*';
    //1. teil
    $csilben = rand(1, 4);
    for ($i = 1; $i <= $csilben; $i++) {
        $suchsilbe = $silben[rand(0, $anzsilben - 1)];
        if ($i == 1) {
            $suchsilbe = ucfirst($suchsilbe);
        }
        $name .= $suchsilbe;
    }
    $name .= '-';
    $csilben = rand(1, 5);
    for ($i = 1; $i <= $csilben; $i++) {
        $suchsilbe = $silben[rand(0, $anzsilben - 1)];
        if ($i == 1) {
            $suchsilbe = ucfirst($suchsilbe);
        }
        $name .= $suchsilbe;
    }

    //Spielernamen auf 20 Zeichen kürzen
    $name = substr($name, 0, 20);    

    return $name;
}

$anzahl = intval($_REQUEST["anzahl"] ?? 0);

if ($anzahl > 0) {
    $npc = intval($_REQUEST["npc"] ?? 1);
    //accounts in die db einfügen
    for ($j = 0; $j < $anzahl; $j++) {

        //spielername erzeugen und schauen ob er schon vergeben ist
        $ok = 0;
        while ($ok == 0) {
            $spielername = generierespielername();
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE spielername=?", [$spielername]);
            if (mysqli_num_rows($result) == 0) {
                $ok = 1;
            }
            echo $spielername.'<br>';
        }

        //loginnamen erzeugen und schauen ob er schon vergeben ist
        $ok = 0;
        while ($ok == 0) {
            $zz = rand(100000000, 900000000);
            $loginname = 'ki'.$zz;
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE nic=?", [$loginname]);
            if (mysqli_num_rows($result) == 0) {
                $ok = 1;
            }
            echo $loginname.'<br>';
        }

        //e-mail-addy erzeugen und schauen ob sie schon vergeben ist
        $ok = 0;
        while ($ok == 0) {
            $reg_mail = 'ki'.$zz.'@example.com';
            $result = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_login WHERE reg_mail=?", [$reg_mail]);
            if (mysqli_num_rows($result) == 0) {
                $ok = 1;
            }
            $zz = rand(1000000000, 4000000000);
            echo $reg_mail.'<br>';
        }

        //account einfügen
        //de_login
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_login (nic, reg_mail, pass, register, last_login, status, last_ip)
      VALUES (?, ?, PASSWORD(?), NOW(), NOW(), 1, '127.0.0.1')", [$loginname, $reg_mail, $reg_mail]);
        $user_id = mysqli_insert_id($GLOBALS['dbi']);

        //de_user_data
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_data (user_id, spielername, restyp01, restyp02, techs,
      ekey, sector, `system`, rasse, npc, nrrasse, nrspielername)
      VALUES (?, ?, 10000, 5000,
      's0000000000000000000000000000000000000001000010000000001000010000000000000000000000000000000000000000000000000',
      '100;0;0;0', 0, 0, 5, ?, 5, ?)", 
      [$user_id, $spielername, $npc, $spielername]);

        //de_user_fleet
        $fleet_id = $user_id.'-0';
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);

        $fleet_id = $user_id.'-1';
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);
        $fleet_id = $user_id.'-2';
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);

        $fleet_id = $user_id.'-3';
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_fleet (user_id) VALUES (?)", [$fleet_id]);

        //de_user_info
        $sql = "INSERT INTO de_user_info (user_id, vorname, nachname, strasse, plz, ort, land, telefon, tag, monat, jahr, geschlecht)
      VALUES (?, '', '', '', 0, '', '', '', 0, 0, 0, 0)";
        mysqli_execute_query($GLOBALS['dbi'], $sql, [$user_id]);

    }
    echo("Done. Es wurden ".$anzahl." Accounts erstellt.<br><br>");
}
echo '<form method="post">';

//select für npc
echo 'NPC: ';
echo '<select name="npc">';
echo '<option value="1">NPC 1</option>';
echo '<option value="2">NPC 2</option>';
echo '</select>';

echo '<br>Wieviel neue Accounts anlegen? ';
echo '<input type="text" name="anzahl" size="5" maxlength="5" value="0">';
echo '<br><input type="Submit" name="create" value="Anlegen">';
echo '<form>';
?>
</body>
</html>
