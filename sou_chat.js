var chatid=0;

self.addEventListener('message', function(e) {
 
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var data = JSON.parse(xmlhttp.responseText);
			self.postMessage({'output1': data[0].output1, 'output2': data[0].output2});
			chatid=data[0].chatid;
		}
	}
	xmlhttp.open("GET", "sou_ajaxrpc.php?managechat=1&chatid="+chatid, true);
	xmlhttp.send();
}, false);