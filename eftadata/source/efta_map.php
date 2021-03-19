<?php
//infoleiste anzeigen
show_infobar();

//feststellen welches bild angezeigt werden soll
$picid=intval($_REQUEST["picid"]);
if($picid<1)$picid=1;

echo '<div style="position: absolute; top: 26px; left: 2px; z-index: 11; background-color: #111111; border: solid 1px #666666;" align="center"><b>Karte</b> (X: <span id="stratx">0</span> Y: <span id="straty">0</span>)</div>';

echo '<div id="stratmap" style="position:absolute; top: 0px; left: 0px; width:100%; height:100%; overflow:hidden; background-color: #3352c5; z-index:0;">';
echo '<div id="stratmapimg" style="position:absolute; overflow:hidden; left: 0px; top: 0px; width: 2000px; height: 2000px;"  z-index:1;>';
echo '<img ismap src="showeftapic.php?eftapic=1" border="0">';
echo '</div>';
echo '</div>';

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
// minimap
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
echo '<div style="position: absolute; z-index: 10; right: 0px; top: 26px;">';
rahmen1_oben('<div align="center"><b>Minimap</b></div>');
echo '<img src="showeftapic.php?eftapic=1" border="0" width="300" height="300" id="minimapimg">';
echo '<br>(Gew&uuml;nschte Position anklicken) ';

//$x=(1000+$sv_sou_startposition[$player_fraction-1][0])*-1;
//$y=(1000-$sv_sou_startposition[$player_fraction-1][1])*-1;

$x=(1000+$x)*-1;
$y=(1000-$y)*-1;

?>
<script type="text/javascript">
//startpos

posX = <?php echo $x;?>+($("#stratmap").width()/2);
posY = <?php echo $y;?>+($("#stratmap").height()/2);


document.getElementById("stratmapimg").style.left = posX+"px";
document.getElementById("stratmapimg").style.top = posY+"px";


var inmove=0;
$(function() {
	$("#stratmapimg").draggable();
});

$("#stratmapimg").bind( "dragstart", function(event, ui) {
  inmove=1;
});

$("#stratmapimg").bind( "dragstop", function(event, ui) {
  inmove=0;
});

$("#stratmapimg").mousemove(function(e){
  var position = $("#stratmapimg").position();
  document.getElementById('stratx').innerHTML = parseInt(e.clientX-position.left-1000);
  document.getElementById('straty').innerHTML = parseInt(1000-(e.clientY-position.top));
}
);

$("#minimapimg").click(function(evt){
var evt = evt || window.event;
posX = parseInt((getMouseX(evt)*-6.66666666)+($("#stratmap").width()/2));
posY = parseInt((getMouseY(evt)*-6.66666666)+($("#stratmap").height()/2));

document.getElementById("stratmapimg").style.left = posX+"px";
document.getElementById("stratmapimg").style.top = posY+"px";
}
);

function getMouseX(evt) {
if (evt && evt.offsetY) {
return evt.offsetX;
} else {
var obj = getEventObject( evt );
if ( !obj.pageLeft )
setPageTopLeft( obj );
return (evt.clientX - obj.pageLeft - document.body.scrollLeft);
}
}

function getMouseY(evt) {
if (evt && evt.offsetY) {
return evt.offsetY;
} else {
var obj = getEventObject( evt );
if ( !obj.pageTop )
setPageTopLeft( obj );
return (evt.clientY - obj.pageTop - document.body.scrollTop);
}
}

function setPageTopLeft( o )
{
var top = 0,
left = 0,
obj = (typeof o == "string") ? document.getElementById(o) : o,
body = document.getElementsByTagName('body')[0];

while ( o.offsetParent && o != body) 
{
left += o.offsetLeft ;
top += o.offsetTop ;
o = o.offsetParent ;
};

obj.pageTop = top;
obj.pageLeft = left;
}

function getEventObject( e )
{
return e.target || e.srcElement;
};


</script>
<?php
rahmen1_unten();

?>
<script language="javascript">
  $('#tt1, #tt2').tooltip({ 
      track: true, 
      delay: 0, 
      showURL: false, 
      showBody: "&",
      extraClass: "design1", 
      fixPNG: true,
      opacity: 0.15,
      left: 0
	  });  
</script>
<?php
die('</body></html>');

?>