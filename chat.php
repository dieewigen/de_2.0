<?php
//fix um den chat von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable = 1;
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_chat.lang.php';

//farben definieren
$chat_sectorcolor = '#FFFFFF';
$chat_allycolor = '#00FF00';
$chat_allgemeincolor = '#4a91fc';

//schauen ob es die variablen schon gibt
if (!isset($_SESSION["de_chat_inputchannel"])) {
    $_SESSION["de_chat_inputchannel"] = 0;
}

//$_SESSION['ums_mobi']=0;

?>
<!DOCTYPE HTML>
<html>
<head>
<script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
<title>DE Chat</title>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="/gp/de-chat.css?<?php echo filemtime($_SERVER['DOCUMENT_ROOT'].'/gp/de-chat.css'); ?>">
<?php

$pageType='desktop';
if(isset($_SESSION['ums_mobi']) && $_SESSION['ums_mobi']==1){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
	$pageType='mobile';
}

echo '</head>';
echo '<body bgcolor="#000000" style="overflow:hidden;" class="theme-rasse'.$_SESSION['ums_rasse'].' '.$pageType.'">';

if($pageType==='mobile'){
	echo '<div class="chat-wrapper">';
	echo '<div id="chatheader"><a href="menu.php" style="color:#fff; text-decoration:none;">zum Menü</a></div>';
}

//container-div
echo '<div id="container" class="cellbg" style="'.($pageType==='mobile'
	? 'flex:1 1 auto; display:flex; flex-direction:column; width:100%; min-height:0;'
	: 'width:100%; height:100%; position:absolute;').'">';

// alter mobiler Menü-Block entfernt (Header jetzt außerhalb von chatcontent)

//ausgabe div
echo '<div id="chatcontent" style="'.($pageType==='mobile'
	? 'flex:1 1 auto; overflow:auto; -webkit-overflow-scrolling:touch; min-height:0;'
	: 'width:100%; height:100px; overflow:auto; position:relative;').'">';

echo '</div>';

//input div
if(isset($_SESSION['ums_mobi']) && $_SESSION['ums_mobi']==1){

    $chatchannelchangefontsize = 20;
    $chatinputheight = 40;
    $inputfontsize = 24;

    if (!isset($_COOKIE['deactivate_swipe'])) {
        $_COOKIE['deactivate_swipe'] = 0;
    }

    if ($_COOKIE['deactivate_swipe'] != 1) {
        ?>
<script>
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
    $chatinputheight = 16;
    $inputfontsize = 12;
}

if ($_SESSION['ums_mobi'] == 1) {
	$inputtags = ' autocomplete="on" autocorrect="on" spellcheck="on" ';
} else {
	$inputtags = '';
}
$chatHeight = $chatinputheight + 1;
$containerStyle = ($pageType==='mobile' ? 'position:relative; width:100%;' : 'bottom:0; position:relative; width:100%;');
$chatinput_html = <<<HTML
<div id="chatinput" style="$containerStyle">
	<form onsubmit="return chat_input()">
		<div style="display:flex;">
			<div style="flex-grow:1;">
				<span id="chatchannelchanger" style="font-size: {$chatchannelchangefontsize}px;"></span>&nbsp;
			</div>
			<div style="font-size:14px;">
				<span>Autoscroll</span> <input type="checkbox" id="autoscroll" checked>
			</div>
		</div>
		<div style="width:100%; display:flex; justify-content:center; align-items:center; height: {$chatHeight}px;">
			<div style="flex-grow:1;">
				<input $inputtags class="chatinput" style="width:100%; height: {$chatHeight}px; font-size: {$inputfontsize}px" type="text" name="chatinputfield" id="chatinputfield" maxlength="1000" value="" autocomplete="off">
			</div>
			<div style="width:100px; text-align:center; margin-left:2px;">
				<input style="width:100%; height: {$chatHeight}px; font-size: {$chatchannelchangefontsize}px;" type="submit" name="send" value="{$chat_lang['senden']}" onclick="chat_input()">
			</div>
		</div>
	</form>
</div>
HTML;
echo $chatinput_html;

echo '</div>'; // container

if($pageType==='mobile'){
	echo '</div>'; // chat-wrapper
}

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
			$('#infocenter', parent.document).html(e.data.infocenter);
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
	if('<?php echo $pageType; ?>'==='mobile') return; // Flex regelt mobil automatisch
	var height=document.getElementById('container').offsetHeight-document.getElementById('chatinput').offsetHeight;
	if(height<50) height=50;
	$('#chatcontent').css({height: height+'px', 'max-height': height+'px'});
}

show_chatmenu(<?php echo $_SESSION['de_chat_inputchannel']; ?>);
setsize();
</script>
</body>
</html>
