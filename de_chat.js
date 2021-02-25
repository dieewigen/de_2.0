var chatid=0;
var chatidallg=0;

self.addEventListener('message', function(e) {
 
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var data = JSON.parse(xmlhttp.responseText);
			//self.postMessage(data[0].output);
			self.postMessage(data[0]);
			chatid=data[0].chatid;
			chatidallg=data[0].chatidallg;
		}else if (xmlhttp.readyState == 4 && xmlhttp.status != 200) {
			data[0].output='<br><font color="#FF0000;">Auf den Chat konnte nicht zugegriffen werden. &Uuml;berpr&uuml;fe bitte Deine Internetverbindung.</font><br>'
			self.postMessage(data[0]);
		}
	}
	xmlhttp.open("GET", "de_ajaxrpc.php?managechat=1&chatid="+chatid+"&chatidallg="+chatidallg, true);
	xmlhttp.send();
}, false);