<?php
//variable für die bot-detektion, damit man es im efta-design ausgeben kann
$thisisefta=1;
$eftacss=1;
include "eftadata/source/efta_functions.php";
include "inc/header.inc.php";
include "eftadata/quests/queststart.php";
include "eftadata/lib/efta_dbconnect.php";

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Die Ewigen - Efta</title>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
//$eftacss=1;
include "cssinclude.php";
echo '<script type="text/javascript" src="js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="js/jquery-migrate.min.js"></script>';
echo '<script type="text/javascript" src="js/jquery.dimensions.min.js"></script>';
echo '<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>';
echo '<script type="text/javascript" src="js/jquery-ui-1.8.9.custom.min.js"></script>';
echo '</head>';
echo '<body bgcolor="#000000" style="overflow: auto">';

//ajax-loader-symbol
//echo '<div id="ajaxloader" style="position:absolute; overflow:hidden; visibility: hidden; left: 48%; top: 40px; border: solid 1px #666666; background-color: #FFFFFF; width: 31px; height: 31px;  z-index:10;"><img src="'.$gpfad.'progress.gif" width="100%" height="100%"></div>';

//echo '<div id="debug" style="position: absolute; right: 0px; top: 20px; z-index: 1000;"></div>';

echo '<div id="maincontent" align="center" style="padding-top: 0px; position: absolute; width: 100%; height: 100%; z-index:1;">';
echo '</div>';

//include "fooban.php";

?>
<script language="JavaScript">

self.focus();
window.onresize = setsize;

function setsize()
{
	  var top =((parseInt($('#mapcontent').css('height'))/2)*-1)+(parseInt($('#maincontent').css('height'))/2);
	  var left=((parseInt($('#mapcontent').css('width'))/2)*-1) +(parseInt($('#maincontent').css('width'))/2);

	  top=parseInt(top);
	  left=parseInt(left);
	  
	  $('#mapcontent').css('top', top+'px');
	  $('#mapcontent').css('left', left+'px');
}

lnk("");

function lnk(parameter)
{

$.ajax({
	  type: "POST",
	  url: "efta_ajaxrpc.php?"+parameter,
	  dataType: "html"
	}).done(function( data ) {
		$('#maincontent').html(data);
	});
}

function TLosglsn (Ereignis)
{
	if(!disablekeys)
	{
		if (!Ereignis)Ereignis = window.event;
		if (Ereignis.which) {
			Tastencode = Ereignis.which;
		} 
		else if (Ereignis.keyCode) 
		{
			Tastencode = Ereignis.keyCode;
		}
   
		if(Tastencode==87) lnk("w=1");//hoch
		if(Tastencode==83) lnk("w=3");//runter
		if(Tastencode==65) lnk("w=4");//links
		if(Tastencode==68) lnk("w=2");//rechts   
		if(Tastencode==81) lnk("q=1");//quest
		if(Tastencode==82) lnk("r=1");//rastplatz
		if(Tastencode==77) lnk("");//map
		if(Tastencode==66) lnk("uk=1");//uk
		if(Tastencode==49) lnk("kampf=1");//kampf angriff
		if(Tastencode==48) lnk("kampf=2");//kampf flucht
		if(Tastencode==50) lnk("kampf=3");//kampf feuerstoss
	}
}

document.onkeyup = TLosglsn;

function showmap(maxrows, maxcols, picheight, picwidth, layer, gpfad, data)
{
  var ident;
  var datenstring="";
  var layertop;
  var layerleft;
  var ac=0;
  for(row=1; row <= maxrows; row++)
  {
    for(col=1; col <= maxcols; col++)
    {
      if(data[ac]!='b' && data[ac]!='1')
      {
        ident=row+'_'+col+'_'+layer;
        layertop = 0 + (row * picheight) - picheight;
        layerleft = (col * picwidth) - picwidth;

        datenstring=datenstring+"<div style='position: absolute;top: " + layertop + "px; left: " + layerleft + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible; z-index: " + layer + ";'><img src='"+ gpfad + data[ac] +".gif' width='"+picwidth +"px' height='"+picheight+"px' border='0'></div>";
      }
      ac=ac+1;
    }
  }
  return(datenstring);
}

function showcybandstuff(maxrows, maxcols, picheight, picwidth, layer, gpfad)
{

  var datenstring="";
  var ident=100000;
  var layertop=0+(Math.round(maxrows/2)-1)*picheight;
  var layerleft=(Math.round(maxcols/2)-1)*picwidth;

  //player
  //datenstring=datenstring+"<div id='map" + ident + "' style='position: absolute;top: " + layertop + "px; left: " + layerleft + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible; z-index: " + ident + ";'><img src=' "+ gpfad + "c1.gif' width='"+picwidth +"' height='"+picheight +"' border='0'></div>";

  //norden
  ident++;
  datenstring=datenstring+"<div style='position: absolute;top: " + (layertop-picheight) + "px; left: " + layerleft + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible;z-index: " + ident + ";'><img src=' "+ gpfad + "p1.gif' width='"+picwidth +"' height='"+picheight +"' border='0' onClick='lnk(\"w=1\")'></div>";
  
  //osten
  ident++;
  datenstring=datenstring+"<div style='position: absolute;top: " + (layertop) + "px; left: " + (layerleft+picwidth) + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible;z-index: " + ident + ";'><img src=' "+ gpfad + "p2.gif' width='"+picwidth +"' height='"+picheight +"' border='0' onClick='lnk(\"w=2\")''></div>";
   
  //sueden
  ident++;
  datenstring=datenstring+"<div style='position: absolute;top: " + (layertop+picheight) + "px; left: " + layerleft + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible;z-index: " + ident + ";'><img src=' "+ gpfad + "p3.gif' width='"+picwidth +"' height='"+picheight +"' border='0' onClick='lnk(\"w=3\")''></div>";
  
   //westen
  ident++;
  datenstring=datenstring+"<div style='position: absolute;top: " + (layertop) + "px; left: " + (layerleft-picwidth) + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible;z-index: " + ident + ";'><img src=' "+ gpfad + "p4.gif' width='"+picwidth +"' height='"+picheight +"' border='0' onClick='lnk(\"w=4\")''></div>";
  
  return(datenstring);
}

</script>
</body>
</html>