<?php
include "soudata/defs/colors.inc.php";

if(isset($_REQUEST["secpic"]))
{
  $picid=intval($_REQUEST["secpic"]);
  $im = imagecreatefrompng("soudata/cache/stratmap".$picid.".png");	
  header("Content-type: image/png");
  imagePng($im);
  die();
}

// Standard inclusions      
include("lib/pData.class");   
include("lib/pChart.class");   

//übergebene daten bearbeiten
$data = $_GET["data"];
$data = explode('*',$data);
 
 
// Dataset definition    
$DataSet = new pData;   
$DataSet->AddPoint($data,"Serie1");   
//$DataSet->AddPoint(array("Jan","Feb","Mar","Apr","May"),"Serie2");   
$DataSet->AddAllSeries();   
//$DataSet->SetAbsciseLabelSerie("Serie2");   
  
// Initialise the graph   
$Test = new pChart(300,200);   

// define colors
$Test->setColorPalette(0,hexdec($colors_text[0][0].$colors_text[0][1]),hexdec($colors_text[0][2].$colors_text[0][3]),hexdec($colors_text[0][4].$colors_text[0][5]));
$Test->setColorPalette(1,hexdec($colors_text[1][0].$colors_text[1][1]),hexdec($colors_text[1][2].$colors_text[1][3]),hexdec($colors_text[1][4].$colors_text[2][5]));
$Test->setColorPalette(2,hexdec($colors_text[2][0].$colors_text[2][1]),hexdec($colors_text[2][2].$colors_text[2][3]),hexdec($colors_text[2][4].$colors_text[3][5]));
$Test->setColorPalette(3,hexdec($colors_text[3][0].$colors_text[3][1]),hexdec($colors_text[3][2].$colors_text[3][3]),hexdec($colors_text[3][4].$colors_text[4][5]));
$Test->setColorPalette(4,hexdec($colors_text[4][0].$colors_text[4][1]),hexdec($colors_text[4][2].$colors_text[4][3]),hexdec($colors_text[4][4].$colors_text[5][5]));
$Test->setColorPalette(5,hexdec($colors_text[5][0].$colors_text[5][1]),hexdec($colors_text[5][2].$colors_text[5][3]),hexdec($colors_text[5][4].$colors_text[6][5]));

 
$Test->drawFilledRoundedRectangle(7,7,293,193,5,240,240,240);   
//$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
  
// Draw the pie chart   
$Test->setFontProperties("fonts/font0.ttf",8);   
$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);   
//$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
  
$Test->Stroke();   
 
exit;

/*
header('Content-Type: image/jpeg');

//faktor für die bildgrößenänderung
$percent = 0.5;   // verkleinerung zu originalgröße (1) ;-)

$width = 600;
$height = $width/2;

//$text_color = '000000';
//$colors = array('CCD6E0', '006600','F7EFC6', 'eb0924', 'C6BE8C', 'CC6600','990000','520000','BFBFC1','808080','7F99B2');

$shadow_height = 64;
$shadow_dark = true;

//übergebene daten vorbereiten
$data = $_GET["data"];
$data = explode('*',$data);

//farbarray bauen
for ($i = 0; $i < count($data); $i++)
{
  if($data[$i]>0) $colors[]=$colors_text[$i];
}

$img = ImageCreateTrueColor($width+$xtra_width,
$height+ceil($shadow_height)+$xtra_height);

ImageFill($img, 0, 0, colorHex($img, $background_color));

foreach ($colors as $colorkode)
{
   $fill_color[] = colorHex($img, $colorkode);
   $shadow_color[] = colorHexshadow($img, $colorkode, $shadow_dark);
}

//$label_place = 5;

$centerX = round($width/2);
$centerY = round($height/2);
$diameterX = $width-4;
$diameterY = $height-4;

$data_sum = array_sum($data);

$start = 270;

for ($i = 0; $i < count($data); $i++)
{
   //nur duchführen, wenn der wert größer null ist
   if($data[$i]>0)
   {
     $value += $data[$i];
     $end = ceil(($value/$data_sum)*360) + 270;
     $slice[] = array($start, $end, $shadow_color[$value_counter %
count($shadow_color)], $fill_color[$value_counter % count($fill_color)]);
     $start = $end;
     $value_counter++;
   }
}

for ($i=$centerY+$shadow_height; $i>$centerY; $i--)
{
   for ($j = 0; $j < count($slice); $j++)
   {
       ImageFilledArc($img, $centerX, $i, $diameterX, $diameterY,
$slice[$j][0], $slice[$j][1], $slice[$j][2], IMG_ARC_PIE);
   }
}

for ($j = 0; $j < count($slice); $j++)
{
   ImageFilledArc($img, $centerX, $centerY, $diameterX, $diameterY,
$slice[$j][0], $slice[$j][1], $slice[$j][3], IMG_ARC_PIE);
}
 


function colorHex($img, $HexColorString)
{
       $R = hexdec(substr($HexColorString, 0, 2));
       $G = hexdec(substr($HexColorString, 2, 2));
       $B = hexdec(substr($HexColorString, 4, 2));
       return ImageColorAllocate($img, $R, $G, $B);
}

function colorHexshadow($img, $HexColorString, $mork)
{
   $R = hexdec(substr($HexColorString, 0, 2));
   $G = hexdec(substr($HexColorString, 2, 2));
   $B = hexdec(substr($HexColorString, 4, 2));

   if ($mork)
   {
       ($R > 99) ? $R -= 100 : $R = 0;
       ($G > 99) ? $G -= 100 : $G = 0;
       ($B > 99) ? $B -= 100 : $B = 0;
   }
   else
   {
       ($R < 220) ? $R += 35 : $R = 255;
       ($G < 220) ? $G += 35 : $G = 255;
       ($B < 220) ? $B += 35 : $B = 255;
   }

   return ImageColorAllocate($img, $R, $G, $B);
   
}

//die größe des bildes nach dem verkleinern
$new_width = imagesx($img) * $percent;
$new_height = imagesy($img) * $percent;

// Resample
$image_p = imagecreatetruecolor($new_width, $new_height);
//$image = imagecreatefromjpeg($filename);
imagecopyresampled($image_p, $img, 0, 0, 0, 0, $new_width, $new_height, imagesx($img), imagesy($img));

// Output
imagejpeg($image_p, null, 95);
//imagejpeg($img, null, 100);

//speicher freigeben
ImageDestroy($img);
ImageDestroy($image_p);
*/
?>