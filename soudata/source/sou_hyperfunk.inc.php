<script language="javascript">

var resObjekt;
if(navigator.appName.search("Microsoft") > -1) {
  resObjekt = new ActiveXObject("Microsoft.XMLHTTP");
} else {
  resObjekt = new XMLHttpRequest();
}

function handleResponse() {
  //if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_folder').innerHTML = "Hyperfunksystem wird abgefragt..."; }
  if(resObjekt.readyState == 4) {
    var update = new Array();
    var response = resObjekt.responseText ;
    document.getElementById('hyperfunk_folder').innerHTML = unescape(response);
  }
}
function gotofolder(id) {
  resObjekt.open('get','sou_hyperfunk_folder.php?id=' + id , true);
  resObjekt.onreadystatechange = handleResponse;
  resObjekt.send(null);
  datei=0;
}

function handleResponse2() {
  if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunknachricht wird abgefragt..."; }
  if(resObjekt.readyState == 4) {
    var update = new Array();
    var response = resObjekt.responseText ;
    document.getElementById('hyperfunk_box').innerHTML = unescape(response);
  }
}
function showmessage(id) {
  document.getElementById('msga' + id).style.fontWeight = 'normal';
  document.getElementById('msgb' + id).style.fontWeight = 'normal';
  document.getElementById('msgc' + id).style.fontWeight = 'normal';
  resObjekt.open('get','sou_hyperfunk_message.php?id=' + id , true);
  resObjekt.onreadystatechange = handleResponse2;
  resObjekt.send(null);
  datei=0;
}

function handleResponse3() {
  //if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunksystem wird konnektiert... (1)"; }
  if(resObjekt.readyState == 4) {
    var update = new Array();
    var response = resObjekt.responseText ;
    document.getElementById('hyperfunk_box').innerHTML = unescape(response);
  }
}
function newhf() {
  resObjekt.open('get','sou_hyperfunk_message.php', true);
  resObjekt.onreadystatechange = handleResponse3;
  resObjekt.send(null);
  datei=0;
}

function handleResponse4() {
  //if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunksystem wird konnektiert... (2)"; }
  if(resObjekt.readyState == 4) {
    var update = new Array();
    var response = resObjekt.responseText ;
    document.getElementById('empfauswahl').innerHTML = unescape(response);
  }
}
function empfeingabe() {
  document.getElementById('empfauswahl').innerHTML = 'Bitte warten, die möglichen Empf&auml;nger werden ermittelt...'; 
  resObjekt.open('get','sou_hyperfunk_message.php?ilde=empf&empf=' + document.getElementById('empfaenger_eingabe').value , true);
  resObjekt.onreadystatechange = handleResponse4;
  resObjekt.send(null);
}

function handleResponse5() {
  //if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunksystem wird konnektiert... (3)"; }
  if(resObjekt.readyState == 4) {
    var update = new Array();
    var response = resObjekt.responseText ;
    document.getElementById('hf_empf').innerHTML = unescape(response);
  }
}
function takeadress(id) {
  //if(id > 0) { 
    resObjekt.open('get','sou_hyperfunk_message.php?takeempf=' + id , true);
    resObjekt.onreadystatechange = handleResponse5;
    resObjekt.send(null);
  //} else {
    //document.getElementById('hf_empf').innerHTML = "";
  //}
}

function message_senden() {
  //if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunknachricht wird übertragen..."; }
  //resObjekt.open('get','sou_hyperfunk_message.php?hfnempf=' + document.getElementById('hf_empf').innerHTML + '&hfnbetr=' + document.getElementById('hf_betr').value + '&hfntext=' + encodeURI(document.getElementById('hf_mess').value) , true);
  //resObjekt.onreadystatechange = handleResponse6;
  //resObjekt.send(null);

  //document.getElementById('hyperfunk_box').innerHTML = "Hyperfunknachricht wird übertragen...";
  $.post("sou_hyperfunk_message.php", 'hfnempf=' + document.getElementById('hf_empf').innerHTML + '&hfnbetr=' + document.getElementById('hf_betr').value + '&hfntext=' + encodeURIComponent(document.getElementById('hf_mess').value), 
	function(data, textStatus) {
		document.getElementById('hyperfunk_box').innerHTML = unescape(data);
		gotofolder(1);
	}, "text");
  
  
}

function overmessage(id) {
  document.getElementById('msga' + id).style.color = '#ffcc00';
  document.getElementById('msgb' + id).style.color = '#ffcc00';
  document.getElementById('msgc' + id).style.color = '#ffcc00';
}
function outmessage(id) {
  document.getElementById('msga' + id).style.color = '#ffffff';
  document.getElementById('msgb' + id).style.color = '#ffffff';
  document.getElementById('msgc' + id).style.color = '#ffffff';
}
function handleResponse6() {
  // if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunksystem wird konnektiert... (4)"; }
  if(resObjekt.readyState == 4) {
    var update = new Array();
    var response = resObjekt.responseText ;
    document.getElementById('hyperfunk_box').innerHTML = unescape(response);
    gotofolder(1);
  }
}
function hf_loeschen(id) {
  if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunknachricht wird gelöscht..."; }
  resObjekt.open('get','sou_hyperfunk_message.php?hfdelete=' + id , true);
  resObjekt.onreadystatechange = handleResponse6;
  resObjekt.send(null);
}
function hf_archivieren(id) {
  if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunknachricht wird archiviert..."; }
  resObjekt.open('get','sou_hyperfunk_message.php?hfarchiv=' + id , true);
  resObjekt.onreadystatechange = handleResponse6;
  resObjekt.send(null);
}
function hf_antworten(id) {
  if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunksystem wird konnektiert... (5)"; }
  resObjekt.open('get','sou_hyperfunk_message.php?hfreplay=' + id , true);
  resObjekt.onreadystatechange = handleResponse6;
  resObjekt.send(null);
}
function hf_weiterleiten(id) {
  if(resObjekt.readyState < 4) { document.getElementById('hyperfunk_box').innerHTML = "Hyperfunksystem wird konnektiert... (6)"; }
  resObjekt.open('get','sou_hyperfunk_message.php?hfreplay=' + id + '&hffwd=true' , true);
  resObjekt.onreadystatechange = handleResponse6;
  resObjekt.send(null);
}
</script>
