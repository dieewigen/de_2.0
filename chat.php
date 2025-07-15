<?php
//fix um den chat von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable = 1;
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_chat.lang.php';

//farben definieren
$chat_sectorcolor = '#FFFFFF';
$chat_allycolor = '#00FF00';
$chat_allgemeincolor = '#4a91fc';

//aus performancegr�nden den sektor nur alle x minuten auslesen
//schauen ob es die variablen schon gibt
if (!isset($_SESSION["de_chat_inputchannel"])) {
    $_SESSION["de_chat_inputchannel"] = 0;
}

//$_SESSION['de_chat_lastid']=0;

//include "functions.php";
?>
<!DOCTYPE HTML>
<html>
<head>
<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
<title>DE Chat</title>
<meta charset="UTF-8">
<?php

$loadcsschat = 1;
include "cssinclude.php";

//die Textgröße in der mobilen Version anpassen
if ($_SESSION['ums_mobi'] == 1) {
    echo '
<style type="text/css">
#chatcontent{font-size: 28px;}
A:link{font-size: 28px;}
A:visited{font-size: 28px;}
A:hover{font-size: 28px;}
</style>';

}

echo '</head>';
echo '<body bgcolor="#000000" style="overflow: hidden">';

//container-div
echo '<div id="container" class="cellbg" style="width: 100%; height: 100%; position: absolute;">';

//ausgabe div
echo '<div id="chatcontent" style="width: 100%; height: 100px; overflow: auto; position: relative;">';

if ($_SESSION['ums_mobi'] == 1) {
    //Menu in der mobilen Version
    echo '<a href="menu.php"><div style="
		margin-bottom: 5px; 
		width: 100%; 
		margin-top: 5px; 
		border: 1px solid #333333;
		background-color: #222222;
		color: #FFFFFF !important;
		font-size: 30px !important;
		padding: 3px;
		box-sizing: border-box;
		text-align: center;
		">zum Men&uuml;</div></a>';
}

echo '</div>';

//input div
if ($_SESSION['ums_mobi'] == 1) {

    $chatchannelchangefontsize = 26;
    $chatinputheight = 60;
    $inputfontsize = 50;

    if (!isset($_COOKIE['deactivate_swipe'])) {
        $_COOKIE['deactivate_swipe'] = 0;
    }

    if ($_COOKIE['deactivate_swipe'] != 1) {
        ?>
<script type="text/javascript">
function swipedetect(el, callback){
  
    var touchsurface = el,
    swipedir,
    startX,
    startY,
    distX,
    distY,
    threshold = 150, //required min distance traveled to be considered swipe
    restraint = 100, // maximum distance allowed at the same time in perpendicular direction
    allowedTime = 300, // maximum time allowed to travel that distance
    elapsedTime,
    startTime,
    handleswipe = callback || function(swipedir){}
  
    touchsurface.addEventListener('touchstart', function(e){
        var touchobj = e.changedTouches[0]
        swipedir = 'none'
        dist = 0
        startX = touchobj.pageX
        startY = touchobj.pageY
        startTime = new Date().getTime() // record time when finger first makes contact with surface
        //e.preventDefault()
    }, false)
  
    touchsurface.addEventListener('touchmove', function(e){
        //e.preventDefault() // prevent scrolling when inside DIV
    }, false)
  
    touchsurface.addEventListener('touchend', function(e){
        var touchobj = e.changedTouches[0]
        distX = touchobj.pageX - startX // get horizontal dist traveled by finger while in contact with surface
        distY = touchobj.pageY - startY // get vertical dist traveled by finger while in contact with surface
        elapsedTime = new Date().getTime() - startTime // get time elapsed
        if (elapsedTime <= allowedTime){ // first condition for awipe met
            if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint){ // 2nd condition for horizontal swipe met
                swipedir = (distX < 0)? 'left' : 'right' // if dist traveled is negative, it indicates left swipe
            }
            else if (Math.abs(distY) >= threshold && Math.abs(distX) <= restraint){ // 2nd condition for vertical swipe met
                swipedir = (distY < 0)? 'up' : 'down' // if dist traveled is negative, it indicates up swipe
            }
        }
        handleswipe(swipedir)
        //e.preventDefault()
    }, false)
}
  
document.addEventListener('DOMContentLoaded', function() {
	var el = document;//getElementById('document.body')
	swipedetect(el, function(swipedir){
		//swipedir contains either "none", "left", "right", "top", or "down"
		if (swipedir =='right'){
			document.location.href='menu.php';
		}
		
		if (swipedir =='left'){
			document.location.href='chat.php';
		}	
	});
}, false);

</script>
		
		<?php
    }
} else {
    $chatchannelchangefontsize = 10;
    $chatinputheight = 12;
    $inputfontsize = 12;
}

if ($_SESSION['ums_mobi'] == 1) {
    $inputtags = ' autocomplete="on" autocorrect="on" spellcheck="on" ';
} else {
    $inputtags = '';
}

echo '<div id="chatinput" style="bottom: 0px; position: relative; width: 100%;">
    <form OnSubmit="return chat_input()">';

echo '<table width="100%" border="0" cellpadding="0px" cellspacing="0px">
  		<tr>
    		<td colspan="2"><span id="chatchannelchanger" style="font-size: '.$chatchannelchangefontsize.'px;">
    		</span>&nbsp;Autoscroll <input type="checkbox" id="autoscroll" checked></td>
  		</tr>
  		<tr>
    		<td width="85%"><input '.$inputtags.' class="chatinput" style="width: 99%; height: '.($chatinputheight + 1).'px; font-size: '.$inputfontsize.'px" type="text" name="chatinputfield" id="chatinputfield" maxlength="1000" value="" autocomplete="off"></td>
    		<td width="15%"><input style="width: 99%; height: '.($chatinputheight + 4).'px; font-size: '.$chatchannelchangefontsize.'px" type="Submit" name="send" value="'.$chat_lang['senden'].'" onClick="chat_input()"></td>
  		</tr></table>';

echo '</form>
    </div>';

echo '</div>';

?>
<script type="text/javascript">
window.onresize = setsize;

var chatcounter=100;

function show_chatmenu(channeltyp){
	var nm=''
	if(channeltyp==3){
		nm=nm+'<span onClick="change_chatchannel(4)" style="cursor: pointer; font-size: <?php echo ($chatchannelchangefontsize + 2)?>px; color: #ffad5d; border: 1px solid; padding: 1px;">Global</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(3)" style="cursor: pointer;">Server</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(1)" style="cursor: pointer;"><?php echo $chat_lang['sektor'];?></span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(2)" style="cursor: pointer;"><?php echo $chat_lang['allianz'];?></span>';
		$('input').css('color', '#ffad5d');
	}
	if(channeltyp==2){
		nm=nm+'<span onClick="change_chatchannel(4)" style="cursor: pointer;">Global</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(3)" style="cursor: pointer; font-size: <?php echo ($chatchannelchangefontsize + 2)?>px; color: #4a91fc; border: 1px solid; padding: 1px;">Server</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(1)" style="cursor: pointer;"><?php echo $chat_lang['sektor'];?></span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(2)" style="cursor: pointer;"><?php echo $chat_lang['allianz'];?></span>';
		$('input').css('color', '#4a91fc');
	}
	if(channeltyp==0){
		nm=nm+'<span onClick="change_chatchannel(4)" style="cursor: pointer;">Global</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(3)" style="cursor: pointer;">Server</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(1)" style="cursor: pointer; font-size: <?php echo ($chatchannelchangefontsize + 2)?>px; color: #FFFFFF; border: 1px solid; padding: 1px;"><?php echo $chat_lang['sektor'];?></span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(2)" style="cursor: pointer;"><?php echo $chat_lang['allianz'];?></span>';
		$('input').css('color', '#FFFFFF');
	}
	if(channeltyp==1){
		nm=nm+'<span onClick="change_chatchannel(4)" style="cursor: pointer;">Global</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(3)" style="cursor: pointer;">Server</span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(1)" style="cursor: pointer;"><?php echo $chat_lang['sektor'];?></span>';
		nm=nm+'&nbsp;<span onClick="change_chatchannel(2)" style="cursor: pointer; font-size: <?php echo ($chatchannelchangefontsize + 2)?>px; color: #00FF00; border: 1px solid; padding: 1px;"><?php echo $chat_lang['allianz'];?></span>';
		$('input').css('color', '#00FF00');
	}

	$('#chatchannelchanger').html(nm);
}

function change_chatchannel(channeltyp)
{
  $.getJSON("de_ajaxrpc.php?changechatchannel="+channeltyp,
	function(data)
	{
	  show_chatmenu(data[0].newchatchannel);	
	}
  );
} 

var chatid=0;
var chatcounter=100;

if (window.Worker) {
	var worker = new Worker('js/de_chat.js?time=<?php echo time();?>');
	
	worker.addEventListener('message', function(e) {
		if(e.data.output!=''){
			$('#chatcontent').html($('#chatcontent').html()+e.data.output);
			
			if($('#autoscroll').prop('checked')){
				$(window.opera?'html':'html, body, container, chatcontent').animate({ 
					  scrollTop: 100000}, 'slow' 
					);

				var objDiv = document.getElementById("chatcontent");
				objDiv.scrollTop = objDiv.scrollHeight;
			}
		}

		if(e.data.infocenter!=''){
			$('#ic', parent.document).html(e.data.infocenter);
		}

	  //alert('Worker said: '+e.data);
	}, false);
}

function get_chatdata(){
  if(chatcounter>=10){
	worker.postMessage('getchatdata'); // Send data to our worker.
    chatcounter=0;
  }
  else chatcounter++;
}

if (window.Worker) {
	var refreshID1 = setInterval(
	function()
	{
	  get_chatdata();
	},1000);
}else{
	$('#chatcontent').html("<font style='color: #FF0000;'>Der Browser unterst&uuml;tzt keine Webworker, verwende bitte einen modernen Browser.</font>");
}

function chat_input(){
  	let inputfield=$("#chatinputfield").val();
  	$("#chatinputfield").val('');
  
  	if (inputfield==='') return false;
  
	inputfield = encodeURIComponent(inputfield);
 
  	$.post("de_ajaxrpc.php?chatinsert=1&insert="+inputfield, function(data, textStatus) {
		if(data[0].data==1)$('#chatcontent').html('');
		chatcounter=100;
	}, "JSON");
  
  return false;
}

function setsize(){
  var height=document.getElementById("container").offsetHeight-document.getElementById("chatinput").offsetHeight-4;
  //var height=window.innerHeight-document.getElementById("chatinput").offsetHeight-4;
  $('#chatcontent').css('height', height+'px');
  $('#chatcontent').css('max-height', height+'px');
  <?php
  if ($ums_user_id == 1) {
      ?>
  	//$('#chatcontent').css('height', '300px');
  	//$('body').css('height', '300px');
  	//alert($('#chatcontent').css('height'));
	//$('#chatcontent').css('height', '200px');
    //$('#chatcontent').css('max-height', '200px');
    //alert(window.screen.height);
  	
  	<?php
  }
?>  
}

show_chatmenu(<?php echo $_SESSION['de_chat_inputchannel']; ?>);
setsize();
</script>
</body>
</html>
