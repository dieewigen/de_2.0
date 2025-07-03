<?php
include "inc/header.inc.php";
include "lib/kampfbericht.lib.php";
include 'inc/lang/'.$sv_server_lang.'_sysnews.lang.php';
include "functions.php";
include "tickler/kt_einheitendaten.php";

$db_daten = mysql_query("SELECT restyp01, restyp02, restyp03, restyp04, restyp05, score, sector, `system`, newtrans, newnews FROM de_user_data WHERE user_id='$ums_user_id'", $db);
$row = mysql_fetch_array($db_daten);
$restyp01 = $row[0];
$restyp02 = $row[1];
$restyp03 = $row[2];
$restyp04 = $row[3];
$restyp05 = $row[4];
$punkte = $row["score"];
$newtrans = $row["newtrans"];
$newnews = $row["newnews"];
$sector = $row["sector"];
$system = $row["system"];

if ($newnews == 1) { //wenn einen neue nachricht vorlag, den indikator wieder auf 0 setzen
    mysql_query("UPDATE de_user_data SET newnews = 0 WHERE user_id='$ums_user_id'", $db);
}
$newnews = 0;

//Schiffspunkte f�r die Kampfbericht-lib
for ($rasse = 1;$rasse <= 5;$rasse++) {
    $schiffspunkte[$rasse - 1][0] = $unit[$rasse - 1][0][4];//j�ger
    $schiffspunkte[$rasse - 1][1] = $unit[$rasse - 1][1][4];//jagdboot
    $schiffspunkte[$rasse - 1][2] = $unit[$rasse - 1][2][4];//zerst�rer
    $schiffspunkte[$rasse - 1][3] = $unit[$rasse - 1][3][4];//kreuzer
    $schiffspunkte[$rasse - 1][4] = $unit[$rasse - 1][4][4];//schlachtschiff
    $schiffspunkte[$rasse - 1][5] = $unit[$rasse - 1][5][4];//bomber
    $schiffspunkte[$rasse - 1][6] = $unit[$rasse - 1][6][4];//transmitterschiff
    $schiffspunkte[$rasse - 1][7] = $unit[$rasse - 1][7][4];//tr�gerschiff
    $schiffspunkte[$rasse - 1][8] = $unit[$rasse - 1][8][4];//frachter
    $schiffspunkte[$rasse - 1][9] = $unit[$rasse - 1][9][4];//titan
    //t�rme
    $schiffspunkte[$rasse - 1][10] = $unit[$rasse - 1][10][4];
    $schiffspunkte[$rasse - 1][11] = $unit[$rasse - 1][11][4];
    $schiffspunkte[$rasse - 1][12] = $unit[$rasse - 1][12][4];
    $schiffspunkte[$rasse - 1][13] = $unit[$rasse - 1][13][4];
    $schiffspunkte[$rasse - 1][14] = $unit[$rasse - 1][14][4];
}

//echo serialize($schiffspunkte);

?>
<!DOCTYPE HTML>
<html>
<head>
<?php
echo '<title>'.$sn_lang['nachrichten'].'</title>';

include "cssinclude.php"; ?>
</head>
<body>

<?php
//stelle die ressourcenleiste dar
include "resline.php";

//wurde ein button gedrueckt??
if (isset($_GET["a"]) && $_GET["a"] == "d") {//alle nachricht l&ouml;schen
    mysql_query("DELETE FROM de_user_news WHERE user_id=$ums_user_id AND seen=1", $db);
    echo '<div class="info_box text3" style="margin-bottom: 5px; font-size: 14px;">'.$sn_lang["geloescht"].'</div>';
}


//////////////////////////////////////////////////
// Nachrichten per E-Mail versenden
//////////////////////////////////////////////////
if (isset($_REQUEST["mailnews"]) && $_REQUEST["mailnews"]) {
    $mailbody = $sn_lang["mailhallo"];
    $mailbody .= str_replace('&ouml;', 'ö', $sn_lang["mailende"]);

    //html dateiinhalt
    $allenachrichten = '
<html> 
<head>
<title>Die Ewigen - Mailservice</title>
<link rel="stylesheet" type="text/css" href="'.$ums_gpfad.'f'.$ums_rasse.'.css">
</head>
<body>
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" style="background-color: #000000;">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="560" class="ro" align="center">Die Ewigen - Mailservice</td>
<td width="13" class="ror">&nbsp;</td>
</tr>
<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="1">
<table width="560" border="0" cellpadding="0" cellspacing="1" width="100%">
';


    $query = mysql_query("SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' ORDER BY time DESC");
    $hrstr='';
    while ($row = mysql_fetch_array($query)) {
        $t = $row["time"];
        $n = $row["typ"];
        $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];

        switch ($n) {
            case 8:
                if ($n == 8) {
                    $n = 3;
                }
                $werte = explode(";", $row["text"]);
                $tronic = $werte[0];
                unset($na);
                include "inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";
                $nanr = mt_rand(0, count($na) - 1);

                $nachricht = $na[$werte[1]];

                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$nachricht.'<br><br></td>';
                $allenachrichten .= '</tr>';
                break;
            case 50:
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.showkampfberichtV0($row["text"], $ums_rasse, $ums_spielername, $sector, $system, $schiffspunkte).'</td>';
                $allenachrichten .= '</tr>';
                break;
            case 57:
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e50.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.showkampfberichtV1($row["text"], $ums_rasse, $ums_spielername, $sector, $system, $schiffspunkte).'</td>';
                $allenachrichten .= '</tr>';
                break;
            case 70: //Battleground
                $allenachrichten .= '<tr style="text-align: left;">';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e50.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr style="text-align: left;">';
                $allenachrichten .= '<td>'.showkampfberichtBG($row["text"]).'</td>';
                $allenachrichten .= '</tr>';
                break;
            default:
                //sektorkampfsymbol setzen, wenn n�tigt
                if ($n == 56) {
                    $n = 50;
                }
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
                $allenachrichten .= '</tr>';
                $allenachrichten .= '<tr>';
                $allenachrichten .= '<td>'.$row["text"].'<br><br></td>';
                $allenachrichten .= '</tr>';
                break;
        }
        $hrstr = '<hr>';
    }

    $allenachrichten .= '
</table>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table></div>
</body></html>;';

    // alles per email versenden
    $filename = 'news'.date('Ymd', time()).'.html';
    $db_mail = mysql_query("SELECT reg_mail FROM de_login WHERE user_id='$ums_user_id'", $db);
    $rowmail = mysql_fetch_array($db_mail);

    //jetzt die e-mail versenden

    sendmail_att($rowmail['reg_mail'], 'noreply@die-ewigen.com', $sn_lang["mailbetreff"], $mailbody, $filename, $allenachrichten);

    //die nachrichten nach dem versand löschen
    mysql_query("DELETE FROM de_user_news WHERE user_id='$ums_user_id' AND seen=1", $db);

}//mailnews ende


$th0 = '<a href="sysnews.php?option=0">['.$sn_lang["neue"].']</a>';
$th1 = '<a href="sysnews.php?option=1">['.$sn_lang["alle"].']</a>';
$th2 = '<a href="sysnews.php?option=2">['.$sn_lang["kampf"].']</a>';
$th3 = '<a href="sysnews.php?option=3">['.$sn_lang["handel"].']</a>';
$th4 = '<a href="sysnews.php?option=4">['.$sn_lang["gebaeude"].']</a>';
$th5 = '<a href="sysnews.php?option=5">['.$sn_lang["sonstige"].']</a>';
$th6 = '<a href="sysnews.php?option=6">['.$sn_lang["allianz"].']</a>';
$th7 = '<a href="sysnews.php?option=7">[BG]</a>';

if (!isset($_GET["option"])) {
    $_GET["option"] = 0;
}

$typ='';

if ($_GET["option"] == "7") {
    $nachrichten = array(70);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' and (".$typ.") ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th7 = '<a href="sysnews.php?option=7"><font color="#00DF00">[BG]</font></a>';
} elseif ($_GET["option"] == "6") {
    $nachrichten = array(6);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' and (".$typ.") ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th6 = '<a href="sysnews.php?option=6"><font color="#00DF00">['.$sn_lang["allianz"].']</font></a>';
} elseif ($_GET["option"] == "5") {
    $nachrichten = array(3,7,60);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' and (".$typ.") ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th5 = '<a href="sysnews.php?option=5"><font color="#00DF00">['.$sn_lang["sonstige"].']</font></a>';
} elseif ($_GET["option"] == "4") {
    $nachrichten = array(1,2);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' and (".$typ.") ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th4 = '<a href="sysnews.php?option=4"><font color="#00DF00">['.$sn_lang["gebaeude"].']</font></a>';
} elseif ($_GET["option"] == "3") {
    $nachrichten = array(10,11,12);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' and (".$typ.") ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th3 = '<a href="sysnews.php?option=3"><font color="#00DF00">['.$sn_lang["handel"].']</font></a>';
} elseif ($_GET["option"] == "2") {
    $nachrichten = array(4,5,50,51,52,53,54,55,56,57);
    for ($i = 0;$i < count($nachrichten);$i++) {
        if ($i == 0) {
            $typ = $typ."typ='$nachrichten[$i]'";
        } else {
            $typ = $typ." or typ='$nachrichten[$i]'";
        }
    }

    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' and (".$typ.") ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th2 = '<a href="sysnews.php?option=2"><font color="#00DF00">['.$sn_lang["kampf"].']</font></u></a>';
} elseif ($_GET["option"] == "1") {
    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th1 = '<a href="sysnews.php?option=1"><font color="#00DF00">['.$sn_lang["alle"].']</font></a>';
} elseif (empty($_GET["option"])) {
    $query = "SELECT time, typ, text FROM de_user_news WHERE user_id='$ums_user_id' AND seen=0 ORDER BY time DESC";
    $db_daten = mysql_query($query, $db);
    $th0 = '<a href="sysnews.php?option=0"><font color="#00DF00">['.$sn_lang["neue"].']</font></a>';
    mysql_query("UPDATE de_user_news set seen=1 WHERE user_id='$ums_user_id' AND seen=0", $db);
}



?>
<br>
<form action="sysnews.php" method="POST">
<table border="0" cellpadding="0" cellspacing="0">
<tr height="37">
<td width="13" height="37" class="rol">&nbsp;</td>
<td width="560" class="ro" align="center" nowrap><div class="cell"><?php echo $th0.$th1.$th4.$th2.$th3.$th6.$th5.$th7;?></div></td>
<td width="13" class="ror">&nbsp;</td>
</tr>



<tr>
<td width="13" class="rl">&nbsp;</td>
<td colspan="1">
<div class="cell">
<table width="560" border="0" cellpadding="0" cellspacing="1" width="100%">
<?php
$hrstr = '';
while ($row = mysql_fetch_array($db_daten)) { //jeder gefundene datensatz wird ausgegeben
    $t = $row["time"];
    $n = $row["typ"];
    $time = $t[6].$t[7].'.'.$t[4].$t[5].'.'.$t[0].$t[1].$t[2].$t[3].' - '.$t[8].$t[9].':'.$t[10].$t[11].':'.$t[12].$t[13];
    //if ($n==0) $typ='';
    //if ($n==1) $typ='Geb&auml;ude';
    //if ($n==2) $typ='Forschung';
    //if ($n==3) $typ='Ereignis';
    //if ($n==4) $typ='Sonde';
    //if ($n==5) $typ='Agent';
    //if ($n==6) $typ='Allianz';
    //if ($n==7) $typ='Sektorspende';
    //if ($n==8) $typ='Tronic';
    //if ($n==9) $typ='Zufallsereignis';

    //if ($n==10) $typ='eingekauft';
    //if ($n==11) $typ='verkauft';
    //if ($n==12) $typ='zurückgebucht';

    //if ($n==50) $typ='Kampfbericht V0';
    //if ($n==51) $typ='Angriff';
    //if ($n==52) $typ='Angriff zieht ab';
    //if ($n==53) $typ='Deff';
    //if ($n==54) $typ='Deff zieht ab';
    //if ($n==55) $typ='Recycling';
    //if ($n==56) $typ='Sektorkampf';
    //if ($n==57) $typ='Kampfbericht V1';
    //if ($n==60) $typ='Großes Ereignis';
    //if ($n==70) $typ='Battleground KB';

    //echo '<table border="0" cellpadding="0" cellspacing="1" width="500" bgcolor="#000000">';
    switch ($n) {
        case 8:
            if ($n == 8) {
                $n = 3;
            }
            $werte = explode(";", $row["text"]);
            $tronic = $werte[0];
            unset($na);
            include "inc/lang/".$sv_server_lang."_wt_tronicmsg.lang.php";
            $nanr = mt_rand(0, count($na) - 1);

            $nachricht = $na[$werte[1]];

            echo '<tr style="text-align: left;">';
            echo '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
            echo '</tr>';
            echo '<tr style="text-align: left;">';
            echo '<td>'.$nachricht.'<br><br></td>';
            echo '</tr>';
            break;
        case 50:
            echo '<tr style="text-align: left;">';
            echo '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
            echo '</tr>';
            echo '<tr style="text-align: left;">';
            echo '<td>'.showkampfberichtV0($row["text"], $ums_rasse, $ums_spielername, $sector, $system, $schiffspunkte).'</td>';
            echo '</tr>';
            break;
        case 57: //Kampfbericht V1
            echo '<tr style="text-align: left;">';
            echo '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e50.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
            echo '</tr>';
            echo '<tr style="text-align: left;">';
            echo '<td>'.showkampfberichtV1($row["text"], $ums_rasse, $ums_spielername, $sector, $system, $schiffspunkte).'</td>';
            echo '</tr>';
            break;
        case 70: //Battleground
            echo '<tr style="text-align: left;">';
            echo '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e50.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
            echo '</tr>';
            echo '<tr style="text-align: left;">';
            echo '<td>'.showkampfberichtBG($row["text"]).'</td>';
            echo '</tr>';
            break;
        default:
            //sektorkampfsymbol setzen, wenn nötigt
            if ($n == 56) {
                $n = 50;
            }
            echo '<tr style="text-align: left;">';
            echo '<td>'.$hrstr.'<br><img src="'.$ums_gpfad.'g/'.$ums_rasse.'_e'.$n.'.gif" border="0" align="left" hspace="20"><br><b> '.$time.'</b></td>';
            echo '</tr>';
            echo '<tr style="text-align: left;">';
            echo '<td>'.$row["text"].'<br><br></td>';
            echo '</tr>';
            break;
    }
    $hrstr = '<hr>';
}

?>
</table>
</div>
</td>
<td width="13" class="rr">&nbsp;</td>
</tr>
<tr height="20">
<td height="20" class="rul" width="13">&nbsp;</td>
<td class="ru">&nbsp;</td>
<td class="rur" width="13">&nbsp;</td>
</tr>
</table>

<?php
//////////////////////////////////////////////
//////////////////////////////////////////////
// alle nachrichten per e-mail versenden
//////////////////////////////////////////////
//////////////////////////////////////////////
if ($ums_cooperation == 0) {
    echo '
<br><br>
<form action="sysnews.php" method="post">
<table border="0" cellspacing="0" cellpadding="0" width="300">
<tr>
<td width="13" height="25" class="rol"></td>
<td align=center height="35" colspan="2" class="ro"><div class="cell">'.$sn_lang["mailservice"].'</div></td>
<td width="13" height="25" class="ror"></td>
</tr>

<tr>
<td width="13" height="25" class="rl">&nbsp;</td>
<td align=center height="45" colspan="2" class="cell"><input type="submit" name="mailnews" value="'.$sn_lang["mailbutton"].'"';

    $db_archiv = mysql_query("SELECT user_id FROM de_user_news WHERE user_id='$ums_user_id'", $db);
    $nummer = mysql_num_rows($db_archiv);
    if ($nummer == "0") {
        echo " disabled ";
    }

    echo '></td>
<td width="13" height="25" class="rr"></td>
</tr>
<tr>
 <td width="13" class="rul">&nbsp;</td>
 <td colspan="2" class="ru">&nbsp;</td>
 <td width="13" class="rur">&nbsp;</td>
</tr>
</table>
</form>';
}


echo '<br>';

echo "<a href=sysnews.php?a=d onclick=\"return confirm('".$sn_lang["deletewarning"]."')\"><div class=\"cell\" style=\"width:400px;\"><font color=red>".$sn_lang["deletenews"]."</font></div></a><br><br>";

?>
</div>
</form>
<?php include "fooban.php"; ?>
</body>
</html>
<?php

function sendmail_att($an, $from, $betreff, $text, $dateiname, $att_content)
{


    $email_subject = $betreff; // The Subject of the email

    require_once 'lib/phpmailer/class.phpmailer.php';
    require_once 'lib/phpmailer/class.smtp.php';

    $mail = new PHPMailer();

    $mail->IsHTML(true);
    $mail->isSMTP();
    $mail->Host = $GLOBALS['env_mail_server'];
    $mail->SMTPAuth = true;
    $mail->Username = $GLOBALS['env_mail_user'];
    $mail->Password = $GLOBALS['env_mail_password'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('noreply@die-ewigen.com', 'Die Ewigen');
    //Set an alternative reply-to address
    $mail->addReplyTo('noreply@die-ewigen.com', 'Die Ewigen');
    //Set who the message is to be sent to
    $mail->addAddress($an, '');
    //Set the subject line
    $mail->Subject = $email_subject;
    $mail->Body = $text;

    $mail->AddStringAttachment($att_content, $dateiname, 'base64', 'text/html');


    $mail->send();
}
?>