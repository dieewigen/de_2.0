<?php
use DieEwigen\DE2\Model\Chat\ChannelChoice;

//fix um den chat von der botabfrage unabh�ngig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable = 1;
include "inc/header.inc.php";
include 'inc/lang/'.$sv_server_lang.'_chat.lang.php';

include 'functions.php';

//Sektor, Allianz und zuletzt gewählter Kanal des Spielers
$chat_wahl = new ChannelChoice($GLOBALS['dbi']);
$chat_info = $chat_wahl->playerInfo((int)$_SESSION['ums_user_id']);

//beim ersten Öffnen in dieser Sitzung den zuletzt gewählten Kanal nehmen, sofern er noch offensteht
if (!isset($_SESSION["de_chat_inputchannel"])) {
    $_SESSION["de_chat_inputchannel"] = $chat_wahl->sanitize($chat_info, $chat_info['chatchannel'], ChannelChoice::SEKTOR);
}

//Reiter über dem Eingabefeld; typ wie channeltyp in de_chat_msg, die Farben stehen in gp/de-chat.scss
//aus: der Kanal steht dem Spieler gerade nicht offen (Reiter abgeblendet, der Server prüft beim Klick trotzdem)
$chat_kanaele = [
    ['typ' => 3, 'name' => 'Global', 'placeholder' => 'Nachricht an alle Server …', 'aus' => $chat_info['chatoffglobal'] == 1, 'hinweis' => 'In den Optionen abgeschaltet'],
    ['typ' => 2, 'name' => 'Server', 'placeholder' => 'Nachricht an den Server …', 'aus' => $chat_info['chatoffallg'] == 1, 'hinweis' => 'In den Optionen abgeschaltet'],
    ['typ' => 0, 'name' => $chat_lang['sektor'].' '.$chat_info['sector'], 'placeholder' => 'Nachricht an den Sektor …', 'aus' => false, 'hinweis' => ''],
    ['typ' => 1, 'name' => trim($chat_lang['allianz'].' '.$chat_info['allytag']), 'placeholder' => 'Nachricht an die Allianz …', 'aus' => $chat_info['allytag'] === '', 'hinweis' => 'Du bist in keiner Allianz'],
];

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

//container-div, das Layout (Nachrichten oben, Eingabe unten) kommt aus gp/de-chat.scss
echo '<div id="container" class="cellbg">';

//ausgabe div; der Knopf erscheint, wenn man hochgescrollt hat und neue Nachrichten kommen
echo '<div class="chat-scroll">';
echo '<div id="chatcontent"></div>';
echo '<button type="button" id="chatnewmsgs" class="chat-newmsgs" hidden>Neue Nachrichten &darr;</button>';
echo '</div>';

//input div
if(isset($_SESSION['ums_mobi']) && $_SESSION['ums_mobi']==1){

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
}

if ($_SESSION['ums_mobi'] == 1) {
	$inputtags = ' autocorrect="on" spellcheck="true"';
} else {
	$inputtags = '';
}
$chatinput_html = <<<HTML
<div id="chatinput">
	<form onsubmit="return chat_input()">
		<div id="chatchannelchanger"></div>
		<div class="chat-inputrow">
			<input$inputtags type="text" name="chatinputfield" id="chatinputfield" maxlength="1000" value="" autocomplete="off">
			<button type="submit" id="chatsend">{$chat_lang['senden']}</button>
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
var chatToken = <?php echo json_encode($_SESSION['chat_token']); ?>;
//so viele Zeilen bleiben im Chatfenster, ältere werden entfernt
var chatMaxLines = 500;
var chatcounter = 100;

//Reiter in dieser Reihenfolge, Inhalt siehe $chat_kanaele oben
var chatChannels = <?php echo json_encode($chat_kanaele, JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) ?: '[]'; ?>;

function show_chatmenu(channeltyp){
	var menu = $('#chatchannelchanger').empty();
	$.each(chatChannels, function(i, ch){
		var item = $('<span class="chatchannel chat-ch'+ch.typ+'">').text(ch.name).on('click', function(){
			change_chatchannel(ch.typ);
		});
		if(ch.aus){
			item.addClass('chat-aus').attr('title', ch.hinweis);
		}
		if(ch.typ == channeltyp){
			item.addClass('active');
			$('#chatinputfield').attr('placeholder', ch.placeholder);
		}
		menu.append(item);
	});

	//Eingabefeld und Senden-Knopf in der Farbe des Channels
	$('#chatinputfield, #chatsend').removeClass('chat-ch0 chat-ch1 chat-ch2 chat-ch3').addClass('chat-ch'+channeltyp);
}

//Trennlinie vor dem ersten Eintrag jedes Tages ("Heute", "Gestern", sonst Datum)
function chat_daydividers(){
	var chatcontent = $('#chatcontent');
	chatcontent.children('.chat-day').remove();

	var pad = function(n){ return (n < 10 ? '0' : '') + n; };
	var d = new Date();
	var today = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
	d.setDate(d.getDate() - 1);
	var yesterday = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

	var last = '';
	chatcontent.children('.chatline[data-day]').each(function(){
		var day = this.getAttribute('data-day');
		if(day !== last){
			var p = day.split('-');
			var label = day === today ? 'Heute' : (day === yesterday ? 'Gestern' : p[2] + '.' + p[1] + '.' + p[0]);
			$(this).before($('<div class="chat-day">').text(label));
			last = day;
		}
	});
}

//steht man (fast) ganz unten im Chat?
function chat_atbottom(){
	var cc = document.getElementById('chatcontent');
	return cc.scrollHeight - cc.scrollTop - cc.clientHeight < 30;
}

function chat_scrolldown(){
	var cc = document.getElementById('chatcontent');
	cc.scrollTop = cc.scrollHeight;
	$('#chatnewmsgs').prop('hidden', true);
}

$('#chatnewmsgs').on('click', chat_scrolldown);
$('#chatcontent').on('scroll', function(){
	if(chat_atbottom()) $('#chatnewmsgs').prop('hidden', true);
});

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
			//nur mitscrollen, wenn man unten war; wer hochgescrollt hat, bekommt stattdessen den Hinweisknopf
			var atBottom = chat_atbottom();

			//anhängen statt neu aufbauen, so bleibt auch eine Textmarkierung erhalten
			chatcontent.append(e.data.output);

			var lines = chatcontent.children('.chatline');
			if(lines.length > chatMaxLines){
				lines.slice(0, lines.length - chatMaxLines).remove();
			}

			chat_daydividers();

			if(atBottom){
				chat_scrolldown();
			}else{
				$('#chatnewmsgs').prop('hidden', false);
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
		//der Server hat den Channel umgestellt (z.B. keine Allianz mehr)
		if(data[0].newchatchannel !== undefined) show_chatmenu(data[0].newchatchannel);
		chatcounter = 100;
	}, 'json').fail(restore);

	return false;
}

show_chatmenu(<?php echo intval($_SESSION['de_chat_inputchannel']); ?>);
</script>
</body>
</html>
