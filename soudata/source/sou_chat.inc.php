<?php
//fix um den chat von der botabfrage unabhängig zu machen, gleichzeitig darf man aber keine credits bekommmen
$eftachatbotdefensedisable=1;
$soucss=1;
include "inc/header.inc.php";
include 'soudata/lib/sou_functions.inc.php';
include "soudata/lib/sou_dbconnect.php";

$_SESSION['sou_chat_lastid']=0;

//farben definieren
$chat_globalcolor='#FFFFFF';
$chat_fraccolor='#4a91fc';

//fraktion aus der db auslesen
$db_daten=mysql_query("SELECT * FROM sou_user_data WHERE user_id='".$_SESSION['sou_user_id']."'",$soudb);  	
$row = mysql_fetch_array($db_daten);
$_SESSION["sou_chat_inputchannel"]=$row["fraction"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Chat</title>
<?php
echo '<script type="text/javascript" src="js/sou_fn.js"></script>';
echo '<script type="text/javascript" src="js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="js/jquery-migrate.min.js"></script>';
//echo '<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>';
 
include "cssinclude.php";

echo '</head>';

echo '<body style="background-color: #000000; overflow: hidden;">';

//die div-container bauen
echo '<div id="chatarea1" class="chatarea1"></div>';
echo '<div id="chatarea2" class="chatarea2"></div>';
if($_SESSION["sou_chat_inputchannel"]==0){
	$linktext='<font color="'.$chat_globalcolor.'">Allgemein</font>';
	$color=$chat_globalcolor;
}else{
	$linktext='<font color="'.$chat_fraccolor.'">Fraktion</font>';
	$color=$chat_fraccolor;
}
echo '<div id="chatinput" class="chatinput">
    <form OnSubmit="return chat_input()">
    <div id="chatchannelchanger" class="link1" onClick="change_chatchannel()">'.$linktext.'</div>
    <input type="text" id="chatinputfield" name="chatinputfield" maxlength="250" style="color: '.$color.'; position: absolute; top: 0px; left: 58px; width:77%; padding: 0%; margin: 0%; text-align: left;"  autocomplete="off">
    <span class="link1" style="position: absolute; right: 1.5%; top: 0%;" onClick="chat_input()">Senden</span>
    </form>
    </div>';


?>
<script language='JavaScript' type='text/javascript'>

window.onresize = setsize;

var chatcounter=100;
var chatid=0;

function setsize(){
}

var worker = new Worker('sou_chat.js');

worker.addEventListener('message', function(e) {
	//alert(e.data.output1);
	if(e.data.output1!='')
	{
	  $('#chatarea1').append(e.data.output1);
	  var objDiv = document.getElementById('chatarea1');
	  objDiv.scrollTop = objDiv.scrollHeight;
	}
	if(e.data.output2!='')
	{
	  $('#chatarea2').append(e.data.output2);
	  var objDiv = document.getElementById('chatarea2');
	  objDiv.scrollTop = objDiv.scrollHeight;
	}

  //alert('Worker said: '+e.data);
}, false);


$(document).ready(function() {

	function get_chatdata()
	{
	  if(chatcounter>=20)
	  {
		  worker.postMessage('getchatdata'); // Send data to our worker.
		  /*
		$.getJSON('sou_ajaxrpc.php?managechat=1&chatid='+chatid,
		function(data){
			chatid=data[0].chatid;
			if(data[0].output1!='')
			{
			  $('#chatarea1').append(data[0].output1);
			  var objDiv = document.getElementById('chatarea1');
			  objDiv.scrollTop = objDiv.scrollHeight;
			}
			if(data[0].output2!='')
			{
			  $('#chatarea2').append(data[0].output2);
			  var objDiv = document.getElementById('chatarea2');
			  objDiv.scrollTop = objDiv.scrollHeight;
			}
		});
		*/
		chatcounter=0;
	  }
	  else chatcounter++;
	}

	var refreshID1 = setInterval(
	function()
	{
	  get_chatdata();
	},1000);

});
</script>

</body>
</html>