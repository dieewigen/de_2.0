<?php

echo 'Sektorartefakte berechnen:<br>';

//größter tick
//$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT MAX(tick) AS tick FROM de_user_data",$db);
$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT wt AS tick FROM de_system LIMIT 1", []);
$row     = mysqli_fetch_array($result);
$maxtick = $row["tick"];

//ID=1 Die Schale von Sabrulia
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=1", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);

while ($row = mysqli_fetch_array($res)) {
    //rohstoffe - anfang
    $uid   = $row["user_id"];
    $col   = $row["col"];
    $ekey  = $row["ekey"];

    $pt = loadPlayerTechs($uid);

    //ekey aufsplitten
    $hv = explode(";", $ekey);
    $keym = $hv[0];
    $keyd = $hv[1];
    $keyi = $hv[2];
    $keye = $hv[3];

    //gesamtenergie pro tick, energieausbeute
    $ea = $col * $sv_artefakt[0][0]; //die 5% energie f�r das artefakt

    //energieinput pro rohstoff
    $em = (int)$ea / 100 * $keym;
    $ed = (int)$ea / 100 * $keyd;
    $ei = (int)$ea / 100 * $keyi;
    $ee = (int)$ea / 100 * $keye;

    //energie->materie verhaeltnis
    if (hasTech($pt, 18)) {
        $emvm = 1;
    } else {
        $emvm = 2;
    }
    if (hasTech($pt, 19)) {
        $emvd = 2;
    } else {
        $emvd = 4;
    }
    if (hasTech($pt, 20)) {
        $emvi = 3;
    } else {
        $emvi = 6;
    }
    if (hasTech($pt, 21)) {
        $emve = 4;
    } else {
        $emve = 8;
    }

    //rohstoffoutput
    $rm = $em / $emvm;
    $rd = $ed / $emvd;
    $ri = $ei / $emvi;
    $re = $ee / $emve;
    $rm = intval($rm);
    $rd = intval($rd);
    $ri = intval($ri);
    $re = intval($re);

    //falls es keine materieumwandler gibt, erhält man keine res
    /*
      if(!hasTech($pt,14)){
        $rm=0;
        $rd=0;
        $ri=0;
        $re=0;
    }
    */


    //rohstoffe - ende
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 + ?, restyp02 = restyp02 + ?,
	  restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE user_id=?", [$rm, $rd, $ri, $re, $uid]);
}

//ID=2 Der Spiegel von Calderan
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=2", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.col>0 AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    $uid   = $row["user_id"];

    $r = rand(1, 100);
    if ($r <= $sv_artefakt[1][5]) { //10% W f�r kooliegewinn
        mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set col = col + 1 WHERE user_id=?", [$uid]);

        $time = date("YmdHis");
        $nachricht = $wt_lang['kollektorspiegelcalderan'];
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$uid, $time, $nachricht]);
        //echo 'KOLLIE<br>';
    }

}

//ID=3 Der Spiegel von Coltassa
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=3", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.col>0 AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    $uid   = $row["user_id"];

    $r = rand(1, 100);
    if ($r <= $sv_artefakt[2][5]) { //15% W f�r kooliegewinn
        mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set col = col + 1 WHERE user_id=?", [$uid]);

        $time = date("YmdHis");
        $nachricht = $wt_lang['kollektorspiegelcoltassa'];
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 3, ?, ?)", [$uid, $time, $nachricht]);
        mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = ?", [$uid]);
        //echo 'KOLLIE<br>';
    }
}

//ID=4 Die Schale von Kesh-Ha
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=4", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    //rohstoffe - anfang
    $uid   = $row["user_id"];
    $col   = $row["col"];
    $ekey  = $row["ekey"];

    $pt = loadPlayerTechs($uid);

    //ekey aufsplitten
    $hv = explode(";", $ekey);
    $keym = $hv[0];
    $keyd = $hv[1];
    $keyi = $hv[2];
    $keye = $hv[3];

    //gesamtenergie pro tick, energieausbeute
    $ea = $col * $sv_artefakt[3][0]; //die 3% energie f�r das artefakt

    //energieinput pro rohstoff
    $em = (int)$ea / 100 * $keym;
    $ed = (int)$ea / 100 * $keyd;
    $ei = (int)$ea / 100 * $keyi;
    $ee = (int)$ea / 100 * $keye;

    //energie->materie verhaeltnis
    if (hasTech($pt, 18)) {
        $emvm = 1;
    } else {
        $emvm = 2;
    }
    if (hasTech($pt, 19)) {
        $emvd = 2;
    } else {
        $emvd = 4;
    }
    if (hasTech($pt, 20)) {
        $emvi = 3;
    } else {
        $emvi = 6;
    }
    if (hasTech($pt, 21)) {
        $emve = 4;
    } else {
        $emve = 8;
    }

    //rohstoffoutput
    $rm = $em / $emvm;
    $rd = $ed / $emvd;
    $ri = $ei / $emvi;
    $re = $ee / $emve;
    $rm = intval($rm);
    $rd = intval($rd);
    $ri = intval($ri);
    $re = intval($re);

    //falls es keine materieumwandler gibt, erh�lt man keine res
    /*
    if (!hasTech($pt,14)) {
      $rm=0;
      $rd=0;
      $ri=0;
      $re=0;
    }
    */

    //rohstoffe - ende
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 + ?, restyp02 = restyp02 + ?,
    restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE user_id=?", [$rm, $rd, $ri, $re, $uid]);
}

//ID=5 Die Schale von Kesh-Na
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=5", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);

while ($row = mysqli_fetch_array($res)) {
    //rohstoffe - anfang
    $uid   = $row["user_id"];
    $col   = $row["col"];
    $ekey  = $row["ekey"];

    $pt = loadPlayerTechs($uid);

    //ekey aufsplitten
    $hv = explode(";", $ekey);
    $keym = $hv[0];
    $keyd = $hv[1];
    $keyi = $hv[2];
    $keye = $hv[3];

    //gesamtenergie pro tick, energieausbeute
    $ea = $col * $sv_artefakt[4][0]; //die 3% energie f�r das artefakt

    //energieinput pro rohstoff
    $em = (int)$ea / 100 * $keym;
    $ed = (int)$ea / 100 * $keyd;
    $ei = (int)$ea / 100 * $keyi;
    $ee = (int)$ea / 100 * $keye;

    //energie->materie verhaeltnis
    if (hasTech($pt, 18)) {
        $emvm = 1;
    } else {
        $emvm = 2;
    }
    if (hasTech($pt, 19)) {
        $emvd = 2;
    } else {
        $emvd = 4;
    }
    if (hasTech($pt, 20)) {
        $emvi = 3;
    } else {
        $emvi = 6;
    }
    if (hasTech($pt, 21)) {
        $emve = 4;
    } else {
        $emve = 8;
    }

    //rohstoffoutput
    $rm = $em / $emvm;
    $rd = $ed / $emvd;
    $ri = $ei / $emvi;
    $re = $ee / $emve;
    $rm = intval($rm);
    $rd = intval($rd);
    $ri = intval($ri);
    $re = intval($re);

    //falls es keine materieumwandler gibt, erh�lt man keine res
    /*
    if (!hasTech($pt,14))	{
      $rm=0;
      $rd=0;
      $ri=0;
      $re=0;
    }
    */

    //rohstoffe - ende
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 + ?, restyp02 = restyp02 + ?,
    restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE user_id=?", [$rm, $rd, $ri, $re, $uid]);
}
//ID=6 Die Schale von Kesh-Za
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=6", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    //rohstoffe - anfang
    $uid   = $row["user_id"];
    $col   = $row["col"];
    $ekey  = $row["ekey"];

    $pt = loadPlayerTechs($uid);

    //ekey aufsplitten
    $hv = explode(";", $ekey);
    $keym = $hv[0];
    $keyd = $hv[1];
    $keyi = $hv[2];
    $keye = $hv[3];

    //gesamtenergie pro tick, energieausbeute
    $ea = $col * $sv_artefakt[5][0]; //die 3% energie f�r das artefakt

    //energieinput pro rohstoff
    $em = (int)$ea / 100 * $keym;
    $ed = (int)$ea / 100 * $keyd;
    $ei = (int)$ea / 100 * $keyi;
    $ee = (int)$ea / 100 * $keye;

    //energie->materie verhaeltnis
    if (hasTech($pt, 18)) {
        $emvm = 1;
    } else {
        $emvm = 2;
    }
    if (hasTech($pt, 19)) {
        $emvd = 2;
    } else {
        $emvd = 4;
    }
    if (hasTech($pt, 20)) {
        $emvi = 3;
    } else {
        $emvi = 6;
    }
    if (hasTech($pt, 21)) {
        $emve = 4;
    } else {
        $emve = 8;
    }

    //rohstoffoutput
    $rm = $em / $emvm;
    $rd = $ed / $emvd;
    $ri = $ei / $emvi;
    $re = $ee / $emve;
    $rm = intval($rm);
    $rd = intval($rd);
    $ri = intval($ri);
    $re = intval($re);

    //falls es keine materieumwandler gibt, erh�lt man keine res
    /*
    if (!hasTech($pt,14))
    {
      $rm=0;
      $rd=0;
      $ri=0;
      $re=0;
    }
    */

    //rohstoffe - ende
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 + ?, restyp02 = restyp02 + ?,
    restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE user_id=?", [$rm, $rd, $ri, $re, $uid]);
}

//ID=7 Der Strom von Kiz-Murat
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=7", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.col>0 AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    $uid  = $row["user_id"];
    $wert = $sv_artefakt[6][1];
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 + ? WHERE user_id=?", [$wert, $uid]);

    /*$time=date("YmdHis");
    $nachricht='Sie erhalten '.number_format($wert, 0,"",".").' M durch den Strom von Kiz-Murat.';
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 1,'$time','$nachricht')",$db);
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = $uid",$db);*/
}

//ID=8 Der Strom von Kiz-Joar
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=8", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    $uid  = $row["user_id"];

    $wert = $sv_artefakt[7][2];
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp02 = restyp02 + ? WHERE user_id=?", [$wert, $uid]);

    /*$time=date("YmdHis");
    $nachricht='Sie erhalten '.number_format($wert, 0,"",".").' D durch den Strom von Kiz-Joar.';
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 1,'$time','$nachricht')",$db);
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = $uid",$db);*/
}

//ID=9 Der Strom von Kiz-Benir
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=9", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    $uid  = $row["user_id"];

    $wert = $sv_artefakt[8][3];
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp03 = restyp03 + ? WHERE user_id=?", [$wert, $uid]);

    /*
    $time=date("YmdHis");
    $nachricht='Sie erhalten '.number_format($wert, 0,"",".").' I durch den Strom von Kiz-Benir.';
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 1,'$time','$nachricht')",$db);
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = $uid",$db);*/
}

//ID=10 Der Strom von Kiz-Vokl
$res = mysqli_execute_query($GLOBALS['dbi'], "select sector from de_artefakt where id=10", []);
$row = mysqli_fetch_array($res);
$artsec = $row["sector"];

$res = mysqli_execute_query($GLOBALS['dbi'], "select de_user_data.user_id from de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);
while ($row = mysqli_fetch_array($res)) {
    $uid  = $row["user_id"];

    $wert = $sv_artefakt[9][4];
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp04 = restyp04 + ? WHERE user_id=?", [$wert, $uid]);

    /*$time=date("YmdHis");
    $nachricht='Sie erhalten '.number_format($wert, 0,"",".").' E durch den Strom von Kiz-Vokl.';
    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES ($uid, 1,'$time','$nachricht')",$db);
    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = $uid",$db);*/
}

//ID=11-20 Die Gabe der Reichen
for ($k = 11;$k <= 20;$k++) {
    $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE id=?", [$k]);
    $row = mysqli_fetch_array($res);
    $artsec = $row["sector"];

    $res = mysqli_execute_query($GLOBALS['dbi'], "SELECT de_user_data.user_id, de_user_data.col, de_user_data.techs, de_user_data.ekey FROM de_user_data, de_login where de_login.status=1 AND de_login.user_id = de_user_data.user_id AND de_user_data.sector=?", [$artsec]);
    while ($row = mysqli_fetch_array($res)) {
        //rohstoffe - anfang
        $uid   = $row["user_id"];
        $col   = $row["col"];
        $ekey  = $row["ekey"];

        $pt = loadPlayerTechs($uid);

        //ekey aufsplitten
        $hv = explode(";", $ekey);
        $keym = $hv[0];
        $keyd = $hv[1];
        $keyi = $hv[2];
        $keye = $hv[3];

        //bonusressourcen durch die gabe. $sv_kollieertrag_pa wird hier nicht mehr genutzt
        $ea = $col * $sv_kollieertrag / 100 * $sv_artefakt[$k - 1][0];

        //energieinput pro rohstoff
        $em = (int)$ea / 100 * $keym;
        $ed = (int)$ea / 100 * $keyd;
        $ei = (int)$ea / 100 * $keyi;
        $ee = (int)$ea / 100 * $keye;

        //energie->materie verhaeltnis

        if (hasTech($pt, 18)) {
            $emvm = 1;
        } else {
            $emvm = 2;
        }
        if (hasTech($pt, 19)) {
            $emvd = 2;
        } else {
            $emvd = 4;
        }
        if (hasTech($pt, 10)) {
            $emvi = 3;
        } else {
            $emvi = 6;
        }
        if (hasTech($pt, 21)) {
            $emve = 4;
        } else {
            $emve = 8;
        }

        //rohstoffoutput
        $rm = $em / $emvm;
        $rd = $ed / $emvd;
        $ri = $ei / $emvi;
        $re = $ee / $emve;
        $rm = intval($rm);
        $rd = intval($rd);
        $ri = intval($ri);
        $re = intval($re);

        //falls es keine materieumwandler gibt, erh�lt man keine res
        /*
        if (!hasTech($pt,14)){
            $rm=0;
            $rd=0;
            $ri=0;
            $re=0;
        }
        */

        //rohstoffe - ende
        mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set restyp01 = restyp01 + ?, restyp02 = restyp02 + ?, restyp03 = restyp03 + ?, restyp04 = restyp04 + ? WHERE user_id=?", [$rm, $rd, $ri, $re, $uid]);
    }
}

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//funktion, dass artefakte zuf�lligerweise transferiert werden  //
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

//zuerstmal schauen wieviel artefakte und sektorraumbasen es gibt
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(id) AS anzahl FROM de_artefakt WHERE id<'11' OR id>'20'", []);
$row = mysqli_fetch_array($db_daten);
$artefaktanzahl = $row["anzahl"];

//anzahl der bereits verteilten artefakte
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(id) AS anzahl FROM de_artefakt WHERE (id<'11' OR id>'20') AND sector > 1", []);
$row = mysqli_fetch_array($db_daten);
$artefaktanzahl_verteilt = $row["anzahl"];

//anzahl der sektorraumbasen
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT count(sec_id) AS anzahl FROM de_sector WHERE techs LIKE 's1%' AND npc=0", []);
$row = mysqli_fetch_array($db_daten);
$srbanzahl = $row["anzahl"];

$time = date("YmdHis");
//artefakte auslesen und verdichten
//$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT id, sector, artname, wm FROM de_artefakt ORDER BY sector ASC",$db);
$res = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector, count(id) as artanz, sum(wm) as wm FROM de_artefakt WHERE id<'11' OR id>'20' GROUP BY sector", []);

while ($row = mysqli_fetch_array($res)) {
    $artanzahl = $row["artanz"];
    $wm = $row["wm"];


    //wahrscheinlichkeit festellen, dass ein artefakt von alleine verschwindet
    if ($wm == 0) {
        $wm = 1;
    }

    //fix daf�r, dass pro sektor nicht mehr als 1 sektorartefakt sein sollte
    if ($artanzahl > 1) {
        $wm = $wm + 1000;
    }

    //wenn sektor -1 ist, dann ist die wahrscheinlichkeit höher, da die artefakte schneller ins spiel sollen
    if ($row["sector"] == -1) {
        //artefakte in sektor -1 sollen nur ins spiel kommen, wenn es genug raumbasen gibt
        $wm = 0;
        if (floor($srbanzahl / 2) > $artefaktanzahl_verteilt and $artefaktanzahl > 0) {
            $wm = $wm + 1000;
        }
        $artefaktanzahl_verteilt++;
    }


    //wenn sektor -2 ist, dann ist die wahrscheinlichkeit gleich null dass ein artefakt springt
    if ($row["sector"] == -2) {
        $wm = 0;
    }

    $w = sqrt($artanzahl) * 535 * $wm;
    $r = rand(1, 1000000);
    echo 'W: '.$w.'<br>';
    echo 'R: '.$r.'<br>';
    if ($r < $w) { //wenn r kleiner w, dann wechselt das artefakt seine position
        $artsec = $row["sector"]; //hier ist das artefakt jetzt
        //neuen sektor auslesen
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id, bk FROM de_sector WHERE techs LIKE 's1%' AND sec_id<>? AND npc=0 AND sec_id>1 ORDER BY RAND() LIMIT 0,1", [$artsec]);
        $rowx = mysqli_fetch_array($db_daten);
        $zielsec = $rowx["sec_id"] ?? -1;
        //bk des zielsektors
        $zielbk = getSKSystemBySecID($zielsec);

        //$zielbk = $rowx["bk"];

        //echo $artsec.' -> '.$zielsec.'<br>';
        //user_id vom bk des neuen sektors rausfinden
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? and `system`=?", [$zielsec, $zielbk]);
        $numbk = mysqli_num_rows($db_daten);
        if ($numbk == 1) {//nachricht an bk schicken
            $rowx = mysqli_fetch_array($db_daten);
            $uidz = $rowx["user_id"];

            $nachricht = $wt_lang['artefaktkommt'];
            mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 60, ?, ?)", [$uidz, $time, $nachricht]);
            mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_user_data set newnews = 1 where user_id = ?", [$uidz]);
        }

        //bk des jetzigen sektors rausfinden
        if ($artsec > 0) {

            //zuerst schauen welchen system in dem sektor der bk ist
            //$db_daten=mysqli_execute_query($GLOBALS['dbi'], "SELECT bk FROM de_sector WHERE sec_id='$artsec'",$db);
            //$numbk = mysqli_num_rows($db_daten);
            $herbk = getSKSystemBySecID($artsec);
            echo '<br>$herbk: '.$herbk;

            if ($herbk > 0) {//nachricht an sk schicken
                //$rowx = mysqli_fetch_array($db_daten);
                //bk des zielsektors
                //$herkbk = $rowx["bk"];

                //user_id vom sk rausfinden
                $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT user_id FROM de_user_data WHERE sector=? and `system`=?", [$artsec, $herbk]);
                $numbk = mysqli_num_rows($db_daten);
                echo '<br>$numbk: '.$numbk;

                if ($numbk == 1) {//nachricht an sk schicken
                    $rowx = mysqli_fetch_array($db_daten);
                    $uidh = $rowx["user_id"];

                    $nachricht = $wt_lang['artefaktgeht'];
                    mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_user_news (user_id, typ, time, text) VALUES (?, 60, ?, ?)", [$uidh, $time, $nachricht]);
                    mysqli_execute_query($GLOBALS['dbi'], "update de_user_data set newnews = 1 where user_id = ?", [$uidh]);
                }
            }
        }

        //schauen welches artefakt weg geht
        $db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT * FROM de_artefakt WHERE sector=? AND (id<11 OR id>20) ORDER BY RAND() LIMIT 1", [$artsec]);
        $rowx = mysqli_fetch_array($db_daten);
        $artid = $rowx["id"];

        //artefakt verschieben
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_artefakt set sector=? WHERE id=?", [$zielsec, $artid]);
                // Fallback falls keine Nutzer-IDs gefunden wurden (verhindert Undefined Variable Warnungen)
                if(!isset($uidh)) $uidh = 0;
                if(!isset($uidz)) $uidz = 0;
                echo 'Artefakttransfer: A-ID: '.$artid.', Herkunftssektor: '.$artsec.', Zielsektor: '.$zielsec.', 
            News SK (Herkunft): '.$uidh.', News SK (Ziel): '.$uidz.'<br>';

        //artefaktumzug in der db hinterlegen
        //typ: 0 = hypersturm, 1 = angriff
        $text = $artid.';'.$artsec.';'.$zielsec;
        mysqli_execute_query($GLOBALS['dbi'], "INSERT INTO de_news_server(wt, typ, text) VALUES (?, '0', ?);", [$maxtick, $text]);
        echo "INSERT INTO de_news_server(wt, typ, text) VALUES ('$maxtick', '0', '$text');";
        //echo 'Sektor: '.$artsec.'<br>';
    }

}
//artefakt 11-20 Die Gabe der Reichen wird immer in die kleinsten pc-sektor gepackt, aber erst nach x ticks und Anzahl der Sektorraumbasen
//auf Grund der wenigen Sektor werden jetzt nur noch 16-20 verteilt
if ($maxtick >= 2000 and $srbanzahl >= 11) {
    //schlechteste spielersektorn suchen
    //nach punkten
    //$result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id FROM `de_sector` WHERE npc=0 AND sec_id > 1 ORDER BY platz DESC LIMIT 10",$db);
    //nach kollektoren
    $result  = mysqli_execute_query($GLOBALS['dbi'], "SELECT sec_id FROM `de_sector` WHERE npc=0 AND sec_id > 1 AND platz>0 ORDER BY tempcol ASC LIMIT 5", []);

    for ($id = 16;$id <= 20;$id++) {
        $row = mysqli_fetch_array($result);
        $zielsec = $row ? $row['sec_id'] : -1;
        mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_artefakt set sector=? WHERE id=?", [$zielsec, $id]);
    }
}

//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// arthold hinterlgen, das ist die zeit in der sektoren ein sektorartefakt haben
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
$db_daten = mysqli_execute_query($GLOBALS['dbi'], "SELECT sector FROM de_artefakt WHERE sector>0", []);
while ($row = mysqli_fetch_array($db_daten)) {
    $sector = $row["sector"];
    mysqli_execute_query($GLOBALS['dbi'], "UPDATE de_sector SET arthold = arthold + 1 WHERE sec_id=?", [$sector]);
}
echo '<hr>';
?>
 
