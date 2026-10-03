<?php
//fix um den chat von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable = 1;
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_chat.lang.php';

//schauen ob es die variablen schon gibt
if (!isset($_SESSION["de_chat_inputchannel"])) {
    $_SESSION["de_chat_inputchannel"] = 0;
}

//Token für Schreiben/Channelwechsel, wird in de_ajaxrpc.php geprüft
if (empty($_SESSION['chat_token'])) {
    $_SESSION['chat_token'] = bin2hex(random_bytes(16));
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
				<input style="width:100%; height: {$chatHeight}px; font-size: {$chatchannelchangefontsize}px;" type="submit" name="send" id="chatsend" value="{$chat_lang['senden']}">
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

var chatToken = <?php echo json_encode($_SESSION['chat_token']); ?>;
//so viele Zeilen bleiben im Chatfenster, ältere werden entfernt
var chatMaxLines = 500;
var chatcounter = 100;

//Reihenfolge im Menü; typ wie channeltyp in de_chat_msg, die Farben stehen in gp/de-chat.scss
var chatChannels = [
	{typ: 3, name: 'Global'},
	{typ: 2, name: 'Server'},
	{typ: 0, name: <?php echo json_encode($chat_lang['sektor']); ?>},
	{typ: 1, name: <?php echo json_encode($chat_lang['allianz']); ?>}
];

function show_chatmenu(channeltyp){
	var menu = $('#chatchannelchanger').empty();
	$.each(chatChannels, function(i, ch){
		var item = $('<span class="chatchannel">').text(ch.name).on('click', function(){
			change_chatchannel(ch.typ);
		});
		if(ch.typ == channeltyp){
			item.addClass('active chat-ch'+ch.typ);
		}
		if(i > 0) menu.append('&nbsp;');
		menu.append(item);
	});

	//Eingabefeld und Senden-Knopf in der Farbe des Channels
	$('#chatinputfield, #chatsend').removeClass('chat-ch0 chat-ch1 chat-ch2 chat-ch3').addClass('chat-ch'+channeltyp);
}

function change_chatchannel(channeltyp){
	$.post('de_ajaxrpc.php', {changechatchannel: channeltyp + 1, token: chatToken}, function(data){
		show_chatmenu(data[0].newchatchannel);
		//einen eventuellen Hinweis gleich abholen
		chatcounter = 100;
	}, 'json');
}

if (window.Worker) {
	var worker = new Worker('js/de_chat.js?time=<?php echo time();?>');

	worker.addEventListener('message', function(e) {
		if(e.data.output){
			var chatcontent = $('#chatcontent');
			//anhängen statt neu aufbauen, so bleibt auch eine Textmarkierung erhalten
			chatcontent.append(e.data.output);

			var lines = chatcontent.children('.chatline');
			if(lines.length > chatMaxLines){
				lines.slice(0, lines.length - chatMaxLines).remove();
			}

			if($('#autoscroll').prop('checked')){
				chatcontent.scrollTop(chatcontent.prop('scrollHeight'));
			}
		}

		if(e.data.infocenter){
			$('#infocenter', parent.document).html(e.data.infocenter);
		}
	}, false);

	setInterval(get_chatdata, 1000);
}else{
	$('#chatcontent').html('<div class="chatline chat-error">Der Browser unterst&uuml;tzt keine Webworker, verwende bitte einen modernen Browser.</div>');
}

function get_chatdata(){
	if(chatcounter>=10){
		worker.postMessage('getchatdata');
		chatcounter=0;
	}
	else chatcounter++;
}

function chat_input(){
	var text = $('#chatinputfield').val();
	if (text === '') return false;
	$('#chatinputfield').val('');

	//abgelehnt oder nicht angekommen: Text zurück ins Eingabefeld, sofern dort nichts Neues steht
	function restore(){
		if($('#chatinputfield').val() === '') $('#chatinputfield').val(text);
	}

	$.post('de_ajaxrpc.php', {chatinsert: 1, insert: text, token: chatToken}, function(data){
		if(data[0].data == 1) $('#chatcontent').html('');
		if(data[0].data == 2) restore();
		chatcounter = 100;
	}, 'json').fail(restore);

	return false;
}

function setsize(){
	if('<?php echo $pageType; ?>'==='mobile') return; // Flex regelt mobil automatisch
	var height=document.getElementById('container').offsetHeight-document.getElementById('chatinput').offsetHeight;
	if(height<50) height=50;
	$('#chatcontent').css({height: height+'px', 'max-height': height+'px'});
}

show_chatmenu(<?php echo intval($_SESSION['de_chat_inputchannel']); ?>);
setsize();
</script>
</body>
</html>
