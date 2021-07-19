<?
/*
 * getFullLog.php - Full Log Output for Die-Ewigen Support Tool
 *
 * Copyright (c) 2008 Rainer Zerbe <Corwin@die-ewigen.com>
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * $Version: 2008.04.30 v0.1
 *
 */

 initGzip();
 $uid=(int)$uid;

/////////////////////////////////////////////////////////// 
///////////////////////////////////////////////////////////
// alle daten in einen puffer einlesen
///////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////// 



/*
 
 //schauen ob es die datei gibt
$filename='../cache/logs/'.$uid.'_getpost.txt';

if(is_file($filename))
 {
	header('Content-type: application/txt');
    header('Content-Disposition: attachment; filename="'.$uid.'_getpost.txt"');
	$fp = fopen ($filename, "r");

         while(!feof($fp))
         {
	         $buffer = fgets($fp, 4096);
	         $t = json_decode($buffer,true);
                  echo 'Zeit: '	.$t['Zeit']."\r\n";
                  echo 'IP: '	.$t['IP']."\r\n";
                  echo 'Datei: '	.$t['Datei']."\r\n";
                  echo 'Get: '	.showArray($t['Get'])."\r\n";
                  echo 'Post: '	.showArray($t['Post'])."\r\n";
                  echo "-------------------- \r\n";
         }


	fclose ($fp);



 }
 else echo "Keine Daten gefunden";
*/


doGzip();

/**
 * Make Array's readable
 */
function showArray($a)
{
	$o = '';
 	if(is_array($a)) foreach($a as $k => $u) $o .= $k .' => '.urldecode($u)."\r\n";
         else return $a;
         return $o;
}

/**
 * Initialise GZIP
 */
function initGzip() {
    global $do_gzip_compress;
    $do_gzip_compress = FALSE;
    if (true) {
        $phpver     = phpversion();
        $useragent     = $_SERVER['HTTP_USER_AGENT'];
        $canZip     = $_SERVER['HTTP_ACCEPT_ENCODING'];

        $gzip_check     = 0;
        $zlib_check     = 0;
        $gz_check        = 0;
        $zlibO_check    = 0;
        $sid_check        = 0;
        if ( strpos( $canZip, 'gzip' ) !== false) {
            $gzip_check = 1;
        }
        if ( extension_loaded( 'zlib' ) ) {
            $zlib_check = 1;
        }
        if ( function_exists('ob_gzhandler') ) {
            $gz_check = 1;
        }
        if ( ini_get('zlib.output_compression') ) {
            $zlibO_check = 1;
        }
        if ( ini_get('session.use_trans_sid') ) {
            $sid_check = 1;
        }

        if ( $phpver >= '4.0.4pl1' && ( strpos($useragent,'compatible') !== false || strpos($useragent,'Gecko')    !== false ) ) {
            // Check for gzip header or northon internet securities or session.use_trans_sid
            if ( ( $gzip_check || isset( $_SERVER['---------------']) ) && $zlib_check && $gz_check && !$zlibO_check && !$sid_check ) {
                // You cannot specify additional output handlers if
                // zlib.output_compression is activated here
                ob_start( 'ob_gzhandler' );
                return;
            }
        } else if ( $phpver > '4.0' ) {
            if ( $gzip_check ) {
                if ( $zlib_check ) {
                    $do_gzip_compress = TRUE;
                    ob_start();
                    ob_implicit_flush(0);

                    header( 'Content-Encoding: gzip' );
                    return;
                }
            }
        }
    }
    ob_start();
}

/**
* Perform GZIP
*/
function doGzip() {
    global $do_gzip_compress;
    if ( $do_gzip_compress ) {
        /**
        *Borrowed from php.net!
        */
        $gzip_contents = ob_get_contents();
        ob_end_clean();

        $gzip_size = strlen($gzip_contents);
        $gzip_crc = crc32($gzip_contents);

        $gzip_contents = gzcompress($gzip_contents, 9);
        $gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        echo $gzip_contents;
        echo pack('V', $gzip_crc);
        echo pack('V', $gzip_size);
    } else {
        ob_end_flush();
    }
}



?>