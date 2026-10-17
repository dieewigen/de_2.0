var chatid=0;
var chatidallg=0;
//läuft gerade eine Abfrage? Eine zweite mit derselben chatid würde Nachrichten doppelt liefern.
var busy=false;
//die Fehlermeldung nur einmal zeigen, bis der Chat wieder erreichbar ist
var errorShown=false;

//art 'verbindung': der Server antwortet nicht (Netz, Zeitüberschreitung, Serverfehler)
//art 'sitzung': der Server antwortet, aber nicht mit Chat-Daten, sondern mit der Seite "Nicht eingeloggt" oder dem
//Botschutz; die Texte dazu stehen in chat.php (chat_fehler)
function postError(art){
	if(errorShown) return;
	errorShown=true;
	self.postMessage({error: art});
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
			postError(xmlhttp.status == 200 ? 'sitzung' : 'verbindung');
		}
	};
	xmlhttp.onerror = function() { postError('verbindung'); };
	xmlhttp.ontimeout = function() { postError('verbindung'); };
	xmlhttp.onloadend = function() {
		busy=false;
	};

	xmlhttp.open("GET", "/de_ajaxrpc.php?managechat=1&chatid="+chatid+"&chatidallg="+chatidallg, true);
	xmlhttp.send();
}, false);
