<?php

include 'inc/lang/'.$sv_server_lang.'_format_sammlung.lang.php';

function db_aufbereitung($text)
{
    $text = str_replace('script', 'schkript', $text);
    $text = str_replace('Script', 'Schkript', $text);


    return $text;
}

function formatierte_anzeige($text, $ums_gpfad)
{
    $text = str_replace(":)", "<img src=\"" . $ums_gpfad . "g/smilies/sm1.gif\" alt=\"lustiger Smilie\">", $text);
    $text = str_replace(":D", "<img src=\"" . $ums_gpfad . "g/smilies/sm2.gif\" alt=\"lachernder Smilie\">", $text);
    $text = str_replace(";)", "<img src=\"" . $ums_gpfad . "g/smilies/sm3.gif\" alt=\"zwinkernder Smilie\">", $text);
    $text = str_replace(":x", "<img src=\"" . $ums_gpfad . "g/smilies/sm4.gif\" alt=\"flamender Smilie\">", $text);
    $text = str_replace(":(", "<img src=\"" . $ums_gpfad . "g/smilies/sm5.gif\" alt=\"trauriger Smilie\">", $text);
    $text = str_replace("x(", "<img src=\"" . $ums_gpfad . "g/smilies/sm6.gif\" alt=\"Smilie\">", $text);
    $text = str_replace(":p", "<img src=\"" . $ums_gpfad . "g/smilies/sm7.gif\" alt=\"Zunge rausstreck Smilie\">", $text);
    $text = str_replace("(?)", "<img src=\"" . $ums_gpfad . "g/smilies/sm8.gif\" alt=\"Fragezeichen\">", $text);
    $text = str_replace("(!)", "<img src=\"" . $ums_gpfad . "g/smilies/sm9.gif\" alt=\"Ausrufezeichen\">", $text);
    $text = str_replace(":{", "<img src=\"" . $ums_gpfad . "g/smilies/sm10.gif\" alt=\"Smilie\">", $text);
    $text = str_replace(":}", "<img src=\"" . $ums_gpfad . "g/smilies/sm11.gif\" alt=\"Smilie\">", $text);
    $text = str_replace(":L", "<img src=\"" . $ums_gpfad . "g/smilies/sm12.gif\" alt=\"rauchender Smilie\">", $text);
    $text = str_replace(":nene:", "<img src=\"" . $ums_gpfad . "g/smilies/sm13.gif\" alt=\"nene\">", $text);
    $text = str_replace(":eek:", "<img src=\"" . $ums_gpfad . "g/smilies/sm14.gif\" alt=\"eek\">", $text);
    $text = str_replace(":applaus:", "<img src=\"" . $ums_gpfad . "g/smilies/sm15.gif\" alt=\"applaus\">", $text);
    $text = str_replace(":cry:", "<img src=\"" . $ums_gpfad . "g/smilies/sm16.gif\" alt=\"cry\">", $text);
    $text = str_replace(":sleep:", "<img src=\"" . $ums_gpfad . "g/smilies/sm17.gif\" alt=\"sleep\">", $text);
    $text = str_replace(":rolleyes:", "<img src=\"" . $ums_gpfad . "g/smilies/sm18.gif\" alt=\"Rolleyes\">", $text);
    $text = str_replace(":wand:", "<img src=\"" . $ums_gpfad . "g/smilies/sm19.gif\" alt=\"Wand\">", $text);
    $text = str_replace(":dead:", "<img src=\"" . $ums_gpfad . "g/smilies/sm20.gif\" alt=\"Dead\">", $text);

    $text = preg_replace("/\[b\]/i", "<b>", $text);
    $text = preg_replace("/\[\/b\]/i", "</b>", $text);

    $text = preg_replace("/\[i\]/i", "<i>", $text);
    $text = preg_replace("/\[\/i]/i", "</i>", $text);

    $text = preg_replace("/\[u]/i", "<u>", $text);
    $text = preg_replace("/\[\/u]/i", "</u>", $text);

    $text = preg_replace("/\[center\]/i", "<center>", $text);
    $text = preg_replace("/\[\/center\]/i", "</center>", $text);

    $text = preg_replace("/\[pre]/i", "<pre>", $text);
    $text = preg_replace("/\[\/pre]/i", "</pre>", $text);

    $text = str_replace("[CGRUEN]", "<font color=\"#28FF50\">", $text);
    $text = str_replace("[CROT]", "<font color=\"#F10505\">", $text);
    $text = str_replace("[CW]", "<font color=\"#FFFFFF\">", $text);
    $text = str_replace("[CGELB]", "<font color=\"#FDFB59\">", $text);
    $text = str_replace("[CDE]", "<font color=\"#3399FF\">", $text);

    $text = preg_replace("/\[email\]([^[]*)\[\/email\]/", "<a href=\"mailto:\\1\">\\1</a>", $text);
    $text = preg_replace("/\[url\]([^[]*)\[\/url\]/i", '<a href="\\1" target="_blank">\\1</a>', $text);
    $text = preg_replace("/\[color=#([^[]+)\]([^[]*)\[\/color\]/", "<font color=\"#\\1\" >\\2</font>", $text);
    $text = preg_replace("/\[size=([^[]+)\]([^[]*)\[\/size\]/", "<font size=\"\\1\" >\\2</font>", $text);

    return $text;
}



function buttonpanel($ums_gpfad)
{
    global $buttonpanel_lang;
    echo '
   <tr>
    <td width="13" height="25" class="rl"></td>
              <td colspan="4" align="center" height=50 class="tl"><div align="center">
              <img src="'.$ums_gpfad.'g/smilies/sm1.gif" onclick="init(\'smile1\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm2.gif" onclick="init(\'smile2\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm3.gif" onclick="init(\'smile3\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm4.gif" onclick="init(\'smile4\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm5.gif" onclick="init(\'smile5\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm6.gif" onclick="init(\'smile6\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm7.gif" onclick="init(\'smile7\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm8.gif" onclick="init(\'smile8\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm9.gif" onclick="init(\'smile9\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm10.gif" onclick="init(\'smile10\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm11.gif" onclick="init(\'smile11\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm12.gif" onclick="init(\'smile12\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm13.gif" onclick="init(\'smile13\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm14.gif" onclick="init(\'smile14\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm15.gif" onclick="init(\'smile15\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm16.gif" onclick="init(\'smile16\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm17.gif" onclick="init(\'smile17\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm18.gif" onclick="init(\'smile18\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm19.gif" onclick="init(\'smile19\')" alt="Smilie">
              <img src="'.$ums_gpfad.'g/smilies/sm20.gif" onclick="init(\'smile20\')" alt="Smilie">
              <br>

              <input type="button" value="&nbsp;b&nbsp;"  onclick="init(\'fett\')">
              <input type="button" value="&nbsp;u&nbsp;"  onclick="init(\'under\')">
              <input type="button" value="&nbsp;i&nbsp;"  onclick="init(\'kursiv\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_1].'"  onclick="init(\'rot\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_2].'"  onclick="init(\'gelb\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_3].'"  onclick="init(\'gruen\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_4].'"  onclick="init(\'weiss\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_5].'"  onclick="init(\'blau\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_6].'"  onclick="init(\'farbe\')">
              <br>
              <input type="button" value="'.$buttonpanel_lang[eintrag_7].'"  onclick="init(\'size\')">
              <input type="button" value="center"  onclick="init(\'center\')">
              <input type="button" value="pre"  onclick="init(\'pre\')">
              <input type="button" value="Link"  onclick="init(\'www\')">
              <input type="button" value="@"  onclick="init(\'mail\')">
              <input type="button" value="'.$buttonpanel_lang[eintrag_8].'"  onclick="init(\'bild\')">
              <input type="button" value="&nbsp;?&nbsp;"  onclick="hilfe()">
              <input type="button" value="'.$buttonpanel_lang[eintrag_9].'"  onclick="leeren()">
              </div></td>
    <td width="13" height="25" class="rr"></td>
</tr>';
}

function nl2br_pre($string)
{

    $string = nl2br($string);

    $stat = preg_match("/\[pre[^\]]*?\](.|\n)*?\[\/pre\]/", $string, $ret);



    if ($stat != false) {

        $retr = preg_replace("/<br[^>]*?>/", "", $ret[0]);

        $retr = str_replace($ret[0], $retr, $string);


        return preg_replace("/\[\/pre\]<br[^>]*?>/", "[/pre]", $retr);
    } else {
        return $string;
    }
}
