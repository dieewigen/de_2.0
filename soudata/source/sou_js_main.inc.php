//<script>
var hb_counter_c=0;
var hb_counter_targetmain='';
var hb_counter_targetcounter='';
var hb_counter_endtext='';
var hb_counter_sound=0;

var heartbeatID = setInterval(
function()
{
  //counter
  if(hb_counter_c>-1)
  {
	try
	{
	  var o='';
	  s=hb_counter_c;
	  d=Math.floor(s/86400);
	  s=s-d*86400;
	  h=Math.floor(s/3600);
	  s=s-h*3600;
	  m=Math.floor(s/60);
	  s=s-m*60;
      //if(h<10)o=d+':0'+h;else o=d+':'+h;
	  if(h<10)o='0'+h;else o=h;
	  if(m<10)o=o+':0'+m;else o=o+':'+m;
	  if(s<10)o=o+':0'+s;else o=o+':'+s;
	  document.getElementById(hb_counter_targetcounter).innerHTML = o;
	  hb_counter_c=hb_counter_c-1;
	  if(hb_counter_c<0)
	  {
  	    if(hb_counter_sound>0 && jQuery.data(document.body, 'player_soundenable')==1)hb_counter_endtext=hb_counter_endtext+'<span style="visibility: hidden;"><object type="application/x-shockwave-flash" data="sound/player.swf" width="110" height="34"><param name="movie" value="sound/player.swf"><param name="bgcolor" value="#000000"><param name="FlashVars" value="src=sound%2Fsound'+hb_counter_sound+'.mp3&amp;autostart=yes" /></object></span>';
  	    document.getElementById(hb_counter_targetmain).innerHTML = hb_counter_endtext;
	  }
	}

	catch(e)
	{}
  }
},1000);