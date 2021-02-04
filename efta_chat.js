self.addEventListener('message', function(e) {
 
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var data = JSON.parse(xmlhttp.responseText);
			self.postMessage(data[0].id);
		}else if (xmlhttp.readyState == 4 && xmlhttp.status != 200) {
				self.postMessage(-1);
		}
	}
	
	xmlhttp.open("GET", "efta_chat.php?check4new=1", true);
	xmlhttp.send();
}, false);