<?
include "det_userdata.inc.php";
/*
 * getLog.php - Loging Output for Die-Ewigen Support Tool
 *
 * Copyright (c) 2008 Rainer Zerbe <Corwin@die-ewigen.com>
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * $Version: 2008.02.12 v0.1
 *
 */




/*
  Setzen der Variablen
*/
 $g['files'] = '../cache/logs/'.$_GET['log'].'_getpost.txt';
 $g['lines'] = $_GET['lines'];
 $g['from'] = strtotime($_GET['timefrom']);
 $g['sites'] = $_POST;


	if(is_file($g['files']))
         {
/*
  Die Function searchStart geht bis annähernd der gesuchten Position im Logfile, ab da wird Zeilenweise gelesen, JSON in ein Array umgewandelt
  und geprüft ob dieser Eintrag ins ausgabearray ($data) gelegt werden soll.
  Am Ende wird $data in ein JSON umgewandelt und übergeben.
*/
                 $end = 0;
                 $fp = fopen ($g['files'], "r");

                 searchStart($fp,$g);   			// Suchen des ungefähren Starts des gesuchen Bereichs
                 $sb = ftell($fp);
                 $buffer = fgets($fp, 4096);             // damit der Datenzeiger auch wirklich am Anfang einer Zeiler steckt

                 while ( (!feof($fp)) && (!$end) )       // Durchlauf bis End Of File oder gesetzem Ende
                 {
                 	$buffer = fgets($fp, 4096);     		// einlesen der Zeile
                 	$t = json_decode($buffer,true);			// Umwandlung in ein Array
                         $t['TS'] = strtotime($t['Zeit']);		// Einfügen des Timestrings nach der Zeitangabe

                         if($t['TS']-$g['from'] >= 0 )                   // Wenn wir > Startzeitpunkt sind
                         	if(in_array($t['Datei'],$g['sites']))	// Wenn die Zeile eine Dateibeinhaltet die Angefordert ist
                                 {                                       // Umwandlung von Sonderzeichen in HTML Code
					foreach($t['Post'] as $k => $u) $t['Post'][$k] = htmlentities (urldecode($u),ENT_QUOTES,'ISO8859-1');
					foreach($t['Get']  as $k => $u) $t['Get'][$k]  = htmlentities (urldecode($u),ENT_QUOTES,'ISO8859-1');
                                 	$data[] = $t;			// Einfügem der Zeile ins ausgabe Array
                                 }

                         if(sizeof($data) >= $g['lines'])$end = 1;     	// Maxzeilen Erreicht = $end


                 }

                 $g['from'] = $data[sizeof($data)-1]['TS'];		// startzeitpunkt wird auf die Zeit des letzten Eintrags gesetzt

                 $out['info'] =						// zusätzliche Rückgabeinformmationen, zZ nur die Zeit
                 	array(
                         	'from'	=> date('Y-m-d H:i:s',$g['from'])
                         );
                 $out['data'] = $data;
                 echo json_encode($out);
		fclose ($fp);                                           // Ausgabe der Daten und Schliessen des Files
	}



function searchStart($fp,$g)
{
/*
  Das Logfile wird nach seiner grösse in 100 Teile aufgeteilt von denen je die erste Zeile eingelesen und der Timestamp geprüft wird.
  Sobald man über der angegebenen Zeit liegt springt man ein 100stel zurück und der Zeiger bleibt da stehen.
*/

         $step = filesize($g['files'])/100;

         if(feof($fp)) { fseek($fp, ftell($fp)-10000); $end = true; }

         $buffer = fgets($fp, 4096);
	$buffer = fgets($fp, 4096);
         $t = json_decode($buffer,true);

	if((strtotime($t['Zeit'])-$g['from'] <= 0 ) && ( ftell($fp)+$step < filesize($g['files']) ) )
         {

	    fseek($fp, ftell($fp)+$step);
	    $end = searchStart($fp,$g);
	}
         if((strtotime($t['Zeit'])-$g['from'] >= 0 ) && (!$end) )
         {
             fseek($fp, ftell($fp)-$step);
             return true;
         }
         else $end;
}
?>