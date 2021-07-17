<?php
session_start();
header("Content-Type: image/png");
include "inc/sv.inc.php";
$givenocredit=1;
include "inccon.php";
include 'inc/lang/'.$sv_server_lang.'_imagegenerator.lang.php';


$ix=500;
$iy=125;
$realy=250;

$multicolor=0;
$zahlmulticolor=0;

/************************************************************
*                                                           *
*      Definition der Farbe und der Größe des Bildes        *
*                                                           *
*************************************************************/

//Bild erzeugen 
$image = imagecreate($ix, $iy); 

//hintergrundfarbe bestimmen
//transparenz ist gew�nscht
$backgroundcolor = imagecolorallocate($image, 0x00, 0x00, 0x00); 
//Hintergrundfarbe entfernen (transparent) 
imagecolortransparent($image, $backgroundcolor); 

mt_srand((double)microtime()*10000);

/************************************************************
*                                                           *
*           Array's mit Zahlen und Operatoren               *
*                                                           *
*************************************************************/
//$zahl wird über die sprachdatei eingebunden
/*
$randomzwang=0;
while($randomzwang<5)
{
   $zahleins = mt_rand(1,100);
   $randomzwang++;
}
*/
$zahleins = mt_rand(1,100);
$_SESSION['loginzahl']=md5('night'.$zahleins.'fall');

$zahleins = $zahl[$zahleins-1];
/************************************************************
*                                                           *
*                       Zahl                                *
*                                                           *
*************************************************************/

 $my = mt_rand(40,60);
 $mx = mt_rand(5,30);

 if(strlen($zahleins)<10)
 {
 $abstand=35;
 $mx+=80;
 }
 else
 $abstand=28;
 for($i=0;$i<strlen($zahleins);$i++)
 {
 $groesse = mt_rand(25,50);
 
 //winkel bestimmen, rechts links von x bis y� negativ oder positiv
 $winkel = mt_rand(5,25);
 if(mt_rand(1,2)==1)$winkel=$winkel*-1;
 $schriftart = mt_rand(0,9);
 $y = $my + mt_rand(1,40);

 //$fontcolor=mt_rand(150,255);
 //$demulticolor = ImageColorAllocate($image, $fontcolor, $fontcolor , $fontcolor);

 $decolor = ImageColorAllocate($image, 200, 200, 200);

 //$schriftart=getcwd().'/fonts/font'.$schriftart.'.ttf';
 $schriftart=getcwd().'/fonts/example_font.ttf';
 //echo '<br>A: '.$schriftart;
 
 imagettftext($image, $groesse, $winkel, $mx+($i*$abstand), $y, -$decolor, $schriftart , $zahleins[$i]);
 //imagettftext($image, $groesse, $winkel, $mx+($i*$abstand), $y+$iy, $demulticolor, $schriftart , $zahleins[$i]);

 }


/************************************************************
*                                                           *
*                       Rechteck                            *
*                                                           *
*************************************************************/

for ($k=0; $k<=4; $k++)
{
  $a = mt_rand(1,250);
  $b = mt_rand(1,30);
  $c = mt_rand(1,$ix);
  $d = mt_rand(1,$iy);


  //$demulticolor = ImageColorAllocate($image, mt_rand(1,255), mt_rand(1,255) , mt_rand(1,255));
  $decolor = ImageColorAllocate($image, 200, 200, 200);

  imagerectangle($image,$a,$b,$c,$d,$decolor);
  //imagerectangle($image,$a,$b+$iy,$c,$d+$iy,$demulticolor);
}
/************************************************************
*                                                           *
*                     Chaosgrafik                           *
*                                                           *
*************************************************************/
for ($q=0; $q<=2; $q++)
{
$e = mt_rand(1,$ix);
$f = mt_rand(1,$iy);
$g = mt_rand(1,$ix);
$h = mt_rand(1,$iy);
$i = mt_rand(1,$ix);
$j = mt_rand(1,$iy);
$k = mt_rand(1,$ix);
$l = mt_rand(1,$iy);
$m = mt_rand(1,$ix);
$n = mt_rand(1,$iy);
$o = mt_rand(1,$ix);
$p = mt_rand(1,$iy);

  //$demulticolor = ImageColorAllocate($image, mt_rand(1,255), mt_rand(1,255) , mt_rand(1,255));
  $decolor = ImageColorAllocate($image, 200, 200, 200);

$mess_p = array($e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p);
imagepolygon($image, $mess_p, 6 , $decolor);

//$mess_m = array($e,$f+$iy,$g,$h+$iy,$i,$j+$iy,$k,$l+$iy,$m,$n+$iy,$o,$p+$iy);
//imagepolygon($image, $mess_m, 6 , $demulticolor);
}
/************************************************************
*                                                           *
*                       Sterne                              *
*                                                           *
*************************************************************/

for ($k=0; $k<=9500; $k++)
{
  //sterne in der farbe der schrift
  $x = mt_rand(0,$ix);
  $y = mt_rand(0,$iy);
  ImageSetPixel($image, $x, $y, $decolor);
}

for ($k=0; $k<=9000; $k++)
{

  //transparente sterne zum durchsieben
  $x = mt_rand(0,$ix);
  $y = mt_rand(0,$iy);
  ImageSetPixel($image, $x, $y, $backgroundcolor);
}

/************************************************************
*                                                           *
*                       Rendering ^^                        *
*                                                           *
*************************************************************/
ImagePNG($image);


/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

function oldsystem()
{
$ix=500;
$iy=125;
$realy=250;

$multicolor=0;
$zahlmulticolor=0;

/************************************************************
*                                                           *
*      Definition der Farbe und der Gr��e des Bildes        *
*                                                           *
*************************************************************/

//Bild erzeugen 
$image = imagecreate($ix, $realy); 

 //Lime Hintergrund setzen, f�r leichteres Entfernen 
$color['lime'] = imagecolorallocate($image, 0x00, 0x00, 0x00); 
//Hintergrundfarbe entfernen (transparent) 
imagecolortransparent($image, $color['lime']); 

//$image = ImageCreate($ix, $realy);

//$image = ImageCreateTrueColor($ix,$realy); 
//$color = imagecolorallocatealpha($image, 0, 0, 0, 127); 
//imagefill($image, 0, 0, $color); 

//imageSaveAlpha($image, true); 

//imagecolorallocate($image,0,0,0);

//$decolor = ImageColorAllocate($image, 200, 200, 200);

mt_srand((double)microtime()*10000);

/************************************************************
*                                                           *
*           Array's mit Zahlen und Operatoren               *
*                                                           *
*************************************************************/
//$zahl wird �ber die sprachdatei eingebunden
$randomzwang=0;
while($randomzwang<5)
{
   $zahleins = mt_rand(1,100);
   $randomzwang++;
}
$_SESSION['loginzahl']=md5('night'.$zahleins.'fall');

$zahleins = $zahl[$zahleins-1];
/************************************************************
*                                                           *
*                       Zahl                                *
*                                                           *
*************************************************************/

 $my = mt_rand(40,60);
 $mx = mt_rand(5,30);

 if(strlen($zahleins)<10)
 {
 $abstand=35;
 $mx+=80;
 }
 else
 $abstand=28;
 for($i=0;$i<strlen($zahleins);$i++)
 {
 $groesse = mt_rand(20,50);
 $winkel = mt_rand(-25,25);
 $schriftart = mt_rand(0,9);
 $y = $my + mt_rand(1,40);

 $fontcolor=mt_rand(150,255);
 $demulticolor = ImageColorAllocate($image, $fontcolor, $fontcolor , $fontcolor);

 $decolor = ImageColorAllocate($image, 200, 200, 200);

 #$schriftart='fonts/font'.$schriftart.'.ttf';
 $schriftart='fonts/example_font.ttf';
 imagettftext($image, $groesse, $winkel, $mx+($i*$abstand), $y, $decolor, $schriftart , $zahleins[$i]);
 imagettftext($image, $groesse, $winkel, $mx+($i*$abstand), $y+$iy, $demulticolor, $schriftart , $zahleins[$i]);

 }


/************************************************************
*                                                           *
*                       Rechteck                            *
*                                                           *
*************************************************************/

for ($k=0; $k<=4; $k++)
{
$a = mt_rand(1,250);
$b = mt_rand(1,30);
$c = mt_rand(1,$ix);
$d = mt_rand(1,$iy);


  $demulticolor = ImageColorAllocate($image, mt_rand(1,255), mt_rand(1,255) , mt_rand(1,255));
  $decolor = ImageColorAllocate($image, 200, 200, 200);

imagerectangle($image,$a,$b,$c,$d,$decolor);
imagerectangle($image,$a,$b+$iy,$c,$d+$iy,$demulticolor);
}
/************************************************************
*                                                           *
*                     Chaosgrafik                           *
*                                                           *
*************************************************************/
for ($q=0; $q<=2; $q++)
{
$e = mt_rand(1,$ix);
$f = mt_rand(1,$iy);
$g = mt_rand(1,$ix);
$h = mt_rand(1,$iy);
$i = mt_rand(1,$ix);
$j = mt_rand(1,$iy);
$k = mt_rand(1,$ix);
$l = mt_rand(1,$iy);
$m = mt_rand(1,$ix);
$n = mt_rand(1,$iy);
$o = mt_rand(1,$ix);
$p = mt_rand(1,$iy);

  $demulticolor = ImageColorAllocate($image, mt_rand(1,255), mt_rand(1,255) , mt_rand(1,255));
  $decolor = ImageColorAllocate($image, 200, 200, 200);

$mess_p = array($e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p);
imagepolygon($image, $mess_p, 6 , $decolor);

$mess_m = array($e,$f+$iy,$g,$h+$iy,$i,$j+$iy,$k,$l+$iy,$m,$n+$iy,$o,$p+$iy);
imagepolygon($image, $mess_m, 6 , $demulticolor);
}
/************************************************************
*                                                           *
*                       Sterne                              *
*                                                           *
*************************************************************/
for ($k=0; $k<=35; $k++)
{
  $demulticolor = ImageColorAllocate($image, mt_rand(1,255), mt_rand(1,255) , mt_rand(1,255));
  $decolor = ImageColorAllocate($image, 200, 200, 200);

  for ($i=0; $i<=250; $i++)
  {
     $x = mt_rand(0,$ix);
     $y = mt_rand(0,$iy);
     ImageSetPixel($image, $x, $y, $decolor);
  }
    for ($i=0; $i<=250; $i++)
  {
     $x = mt_rand(0,$ix);
     $y = mt_rand(0,$iy);
     ImageSetPixel($image, $x, $y+$iy, $demulticolor);
  }
}

/************************************************************
*                                                           *
*                       Rendering ^^                        *
*                                                           *
*************************************************************/
ImagePNG($image);

}
?>
