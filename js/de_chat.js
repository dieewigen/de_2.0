var chatid=0;
var chatidallg=0;
//läuft gerade eine Abfrage? Eine zweite mit derselben chatid würde Nachrichten doppelt liefern.
var busy=false;
//die Fehlermeldung nur einmal zeigen, bis der Chat wieder erreichbar ist
var errorShown=false;

function postError(){
	if(errorShown) return;
	errorShown=true;
	self.postMessage({output: '<div class="chatline chat-error">Auf den Chat konnte nicht zugegriffen werden. &Uuml;berpr&uuml;fe bitte Deine Internetverbindung.</div>', infocenter: ''});
}

self.addEventListener('message', function(e) {
	if(busy) return;
	busy=true;

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.timeout = 30000;

	xmlhttp.onload = function() {
		var data = null;
		if (xmlhttp.status == 200) {
			try {
				data = JSON.parse(xmlhttp.responseText);
			} catch (err) {
				data = null;
			}
		}

		if (data && data[0]) {
			errorShown=false;
			chatid=data[0].chatid;
			chatidallg=data[0].chatidallg;
			self.postMessage(data[0]);
		} else {
			postError();
		}
	};
	xmlhttp.onerror = postError;
	xmlhttp.ontimeout = postError;
	xmlhttp.onloadend = function() {
		busy=false;
	};

	xmlhttp.open("GET", "/de_ajaxrpc.php?managechat=1&chatid="+chatid+"&chatidallg="+chatidallg, true);
	xmlhttp.send();
}, false);
