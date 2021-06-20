function number_format(s) {
var tf,uf,i;
uf="";
tf=s.toString();
j=0;
for(i=(tf.length-1);i>=0;i--)
{
   uf=tf.charAt(i)+uf;
   j++;
   if((j==3) && (i!=0))
   {
      j=0;
      uf="."+uf;
   }
}
return uf;
} 

function in_array(need, searcharray)
{
  var i=0;
  for(i=0; i<searcharray.length; i++)
    if(need==searcharray[i])return true;
}

function sec2time(s)
{
  d=Math.floor(s/86400);
  s=s-d*86400;
  h=Math.floor(s/3600);
  s=s-h*3600;
  m=Math.floor(s/60);
  s=s-m*60;
  if(h<10)o=d+':0'+h;else o=d+':'+h;
  //if(h<10)o='0'+h;else o=h;
  if(m<10)o=o+':0'+m;else o=o+':'+m;
  if(s<10)o=o+':0'+s;else o=o+':'+s;
	
  return(o);
}

function load_content(target, source)
{
  $.getJSON("sou_ajaxrpc.php?getdata="+source,
  function(data)
  {
	  alert(data[0].output);
	$(target).html(data[0].output);
  });
}

function change_chatchannel()
{
  $.getJSON("sou_ajaxrpc.php?changechatchannel=1",
	function(data){
		if(data[0].newchatchannel>0){
			$("#chatchannelchanger").html('<font color="#4a91fc">Fraktion</font>');
			$("#chatinputfield").css('color', "#4a91fc");
		}else{
			$("#chatchannelchanger").html('<font color="#FFFFFF">Allgemein</font>');
			$("#chatinputfield").css('color', "#FFFFFF");
		}
	});
} 

function chat_input()
{
  var inputfield=$("#chatinputfield").val();
  $("#chatinputfield").val('');
  if (inputfield==='') return false;
  inputfield = escape(inputfield);
  inputfield = inputfield.replace(/\+/g, "%2B");
  $.getJSON("sou_ajaxrpc.php?chatinsert=1&insert="+inputfield,
	function(data){
		chatcounter=100;
	});
			  
  return false;
}

function showmap2menu(playerx, playery, sectorx, sectory, speed, reichweite, maxrows, maxcols, picheight, picwidth, layer, gpfad, data, hashblevel)
{
  var wbreite=maxcols*picwidth;
  var whoehe= 0 + maxrows*picheight;
  var fb=220+wbreite;
  
  document.getElementById("ct_map").style.width=wbreite + "px";
  document.getElementById("ct_map").style.height=whoehe + "px";
  
  //parent.document.getElementById('mitte').cols = fb+', *';

  var ident;
  var layertop;
  var layerleft;
  var ac=0;
  ypos=sectory+14;
  for(row=1; row <= maxrows; row++)
  {
    xpos=sectorx;
    for(col=1; col <= maxcols; col++)
    {
      ident=(row * 1000) + (col * 10) + layer;
      layertop = 0 + (row * picheight) - picheight;
      layerleft = (col * picwidth) - picwidth;
         
      
      sysname=data[ac*3];
      fraction=data[(ac*3)+1];
      targethblevel=data[(ac*3)+2];
      if(fraction==0)fraction='-';

      s1=playerx-xpos;
      s2=playery-ypos;

      if(s1<0)s1=s1*(-1);
      if(s2<0)s2=s2*(-1);
      s1=Math.pow(s1,2);
      s2=Math.pow(s2,2);
      w1=s1+s2;
      w3=Math.sqrt(w1);

      distance=Math.round(w3*100)/100;

      if(w3>0)traveltime=Math.round(120+w3*speed+120);else traveltime=0;

      a="new Array('"+sysname+"','"+fraction+"','"+xpos+"','"+ypos+"','"+distance+"','"+traveltime+"','"+reichweite+"','"+hashblevel+"','"+targethblevel+"')";

                  
         document.write('<div id="map' + ident + '"><img src="'+ gpfad +'b.gif" width="'+picwidth +'" height="'+picheight +'" border="0" onmouseover="javascript:showinfo2('+a+')" onClick="javascript:settarget2('+a+')"></div>');
         
         //document.write('<td width="'+picx+'" onMouseover="showinfo('+a+')" onClick="settarget('+a+')">'+si+'</td>');
       xpos=xpos+1;
       ac=ac+1;
     }
     
     ypos=ypos-1;   
   }
   ac=0;
   document.write("<style type='text/css'>");
   for(row=1; row <= maxrows; row++) {
     for(col=1; col <= maxcols; col++)
     {
         ident=(row * 1000) + (col * 10) + layer;
         layertop = 0 + (row * picheight) - picheight;
         layerleft = (col * picwidth) - picwidth;
         document.write("<!--#map" + ident + " {position: absolute;top: " + layertop + "px; left: " + layerleft + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible; z-index: " + ident + "; } --> ");
     }
   }
   document.write("</style>");
}

function showinfo2(d)
{
s=d[5];
d1=Math.floor(s/86400);
s=s-d1*86400;
h=Math.floor(s/3600);
s=s-h*3600;
m=Math.floor(s/60);
s=s-m*60;
if(h<10)o=d1+':0'+h;else o=d1+':'+h;
if(m<10)o=o+':0'+m;else o=o+':'+m;
if(s<10)o=o+':0'+s;else o=o+':'+s;
	
if(parseFloat(d[4])>parseFloat(d[6])){fca='<font color="#FF0000">';fce='</font>';}else {fca='';fce='';}
if(d[0]=='')
str="Leerraum";
else
str="Systemname: "+d[0]+" ("+d[1]+")";

str=str+"<br>Koordinaten: "+d[2]+":"+d[3]+fca+"<br>Entfernung: "+d[4]+" LJ<br>&Uuml;berlichtantrieb-Reisezeit: "+o+fce;

if(d[7]>0 && d[8]>0)
{
	s=7200-((d[7]*60)+(d[8]*60));
	d1=Math.floor(s/86400);
	s=s-d1*86400;
	h=Math.floor(s/3600);
	s=s-h*3600;
	m=Math.floor(s/60);
	s=s-m*60;
	if(h<10)o=d1+':0'+h;else o=d1+':'+h;
	if(m<10)o=o+':0'+m;else o=o+':'+m;
	if(s<10)o=o+':0'+s;else o=o+':'+s;  
  str=str+'<br><font color="#00FF00">Hyperraumblasentransfer: '+o+'</font>';
}

document.getElementById("info").innerHTML = str;
}

function settarget2(d)
{
s=d[5];
d1=Math.floor(s/86400);
s=s-d1*86400;
h=Math.floor(s/3600);
s=s-h*3600;
m=Math.floor(s/60);
s=s-m*60;
if(h<10)o=d1+':0'+h;else o=d1+':'+h;
if(m<10)o=o+':0'+m;else o=o+':'+m;
if(s<10)o=o+':0'+s;else o=o+':'+s;
	
if(parseFloat(d[4])>parseFloat(d[6])){fca='<font color="#FF0000">';fce='</font>';}else {fca='';fce='';}
if(d[0]=='')
str="Leerraum";
else
str="Systemname: "+d[0]+" ("+d[1]+")";

str=str+"<br>Koordinaten: "+d[2]+":"+d[3]+fca+"<br>Entfernung: "+d[4]+" LJ<br>&Uuml;berlichtantrieb-Reisezeit: "+o+fce;

if(d[7]>0 && d[8]>0)
{
	s=7200-((d[7]*60)+(d[8]*60));
	d1=Math.floor(s/86400);
	s=s-d1*86400;
	h=Math.floor(s/3600);
	s=s-h*3600;
	m=Math.floor(s/60);
	s=s-m*60;
	if(h<10)o=d1+':0'+h;else o=d1+':'+h;
	if(m<10)o=o+':0'+m;else o=o+':'+m;
	if(s<10)o=o+':0'+s;else o=o+':'+s;  
  str=str+'<br><font color="#00FF00">Hyperraumblasentransfer: '+o+'</font>';
}


if((parseFloat(d[4])<=parseFloat(d[6]) && parseFloat(d[4])>0) || (d[7]>0 && d[8]>0))str=str+"<br><a href=sou_main.php?action=sectorpage&tx="+d[2]+"&ty="+d[3]+"><div class=\"b1\" align=\"center\">Start</div></a>";
str=str+"<br><a href=sou_main.php?action=sectorpage&rhtx="+d[2]+"&rhty="+d[3]+"><div class=\"b1\" align=\"center\">Reisehilfe</div></a>";
document.getElementById("rz").innerHTML = str;
}

function showmap2(maxrows, maxcols, picheight, picwidth, layer, gpfad, data)
{
  var wbreite=maxcols*picwidth;
  var whoehe= 0 + maxrows*picheight;
  var fb=220+wbreite;
  
  document.getElementById("ct_map").style.width=wbreite + "px";
  document.getElementById("ct_map").style.height=whoehe + "px";
  
  var ident;
  var layertop;
  var layerleft;
  var ac=0;
  for(row=1; row <= maxrows; row++)
  {
    for(col=1; col <= maxcols; col++)
    {
      if(data[ac]!='b')
      {
        ident=(row * 1000) + (col * 10) + layer;
        layertop = 0 + (row * picheight) - picheight;
        layerleft = (col * picwidth) - picwidth;
        
        document.write("<div id='map" + ident + "'><img src='"+ gpfad + data[ac] +".gif' width='"+picwidth +"' height='"+picheight +"' border='0'></div>");
      }
      ac=ac+1;
    }
  }
  ac=0;
  document.write("<style type='text/css'>");
  for(row=1; row <= maxrows; row++) {
    for(col=1; col <= maxcols; col++)
    {
      if(data[ac]!='b')
      {
        ident=(row * 1000) + (col * 10) + layer;
        layertop = 0 + (row * picheight) - picheight;
        layerleft = (col * picwidth) - picwidth;
        document.write("<!--#map" + ident + " {position: absolute;top: " + layertop + "px; left: " + layerleft + "px; width: " + picwidth + "px; height: " + picheight + "px; border: 0px; overflow: hidden; visibility: visible; z-index: " + ident + "; } --> ");
      }
      ac=ac+1;
    }
  }
  document.write("</style>");
}


function montre(id)
{
	with (document)
	{
		if (getElementById)
			getElementById(id).style.display = 'block';
		else if (all)
			all[id].style.display = 'block';
		else
			layers[id].display = 'block';
	}
}

function cache(id)
{
	with (document)
	{
		if (getElementById)
			getElementById(id).style.display = 'none';
		else if (all)
			all[id].style.display = 'none';
		else
			layers[id].display = 'none';
	}
}

function counter(c, targetmain, targetcounter, endtext)
{
s=c;
d=Math.floor(s/86400);
s=s-d*86400;
h=Math.floor(s/3600);
s=s-h*3600;
m=Math.floor(s/60);
s=s-m*60;
//s=c-d*86400-h*3600-m*60;
if(h<10)o=d+':0'+h;else o=d+':'+h;
if(m<10)o=o+':0'+m;else o=o+':'+m;
if(s<10)o=o+':0'+s;else o=o+':'+s;
document.getElementById(targetcounter).innerHTML = 'Verbleibende Zeit: '+ o;
c=c-1;
if(c<0){document.getElementById(targetmain).innerHTML = endtext;}
else {setTimeout("counter("+c+",'"+targetmain+"','"+targetcounter+"','"+endtext+"')", 1000);}
}

function init(thisCode) {
with ( document.getElementById("nachricht").value ) {
switch(thisCode) {

case "fett":
insert("[b] [/b]");
break;

case "kursiv":
insert("[i] [/i]");
break;

case "under":
insert("[u] [/u]");
break;

case "center":
insert("[center] [/center]");
break;

case "mail":
insert("[email] [/email]");
break;

case "www":
insert("[url] [/url]");
break;

case "pre":
insert("[pre] [/pre]");
break;

case "bild":
insert("[img] [/img]");
break;

case "rot":
insert("[CROT]");
break;

case "gelb":
insert("[CGELB]");
break;

case "gruen":
insert("[CGRUEN]");
break;

case "weiss":
insert("[CW]");
break;

case "blau":
insert("[CDE]");
break;

case "farbe":
insert("[color=#] [/color]");
break;

case "size":
insert("[size=] [/size]");
break;

case "smile1":
insert(":)");
break;

case "smile2":
insert(":D");
break;

case "smile3":
insert(";)");
break;

case "smile4":
insert(":x");
break;

case "smile5":
insert(":(");
break;

case "smile6":
insert("x(");
break;

case "smile7":
insert(":p");
break;

case "smile8":
insert("(?)");
break;

case "smile9":
insert("(!)");
break;

case "smile10":
insert(":{");
break;

case "smile11":
insert(":}");
break;

case "smile12":
insert(":L");
break;

case "smile13":
insert(":nene:");
break;

case "smile14":
insert(":eek:");
break;

case "smile15":
insert(":applaus:");
break;

case "smile16":
insert(":cry:");
break;

case "smile17":
insert(":sleep:");
break;

case "smile18":
insert(":rolleyes:");
break;

case "smile19":
insert(":wand:");
break;

case "smile20":
insert(":dead:");
break;
}
document.getElementById("nachricht").focus();
}
}

function hilfe()
{
window.open("hfnlegende.php","BitteBeachten","width=572,height=390,left=34,top=75");
}

function leeren()
{
document.getElementById("nachricht").value = "";
document.getElementById("nachricht").focus();
}

function cursor()
{
if ((navigator.appName=="Netscape")||(navigator.userAgent.indexOf("Opera") != -1)||(navigator.userAgent.indexOf("Netscape") != -1)) {
text_before = document.getElementById("nachricht") .value;
text_after = "";
}
else
{
document.getElementById("nachricht").focus();
var sel = document.selection.createRange();
sel.collapse();
var sel_before = sel.duplicate();
var sel_after = sel.duplicate();
sel.moveToElementText(document.getElementById("nachricht"));
sel_before.setEndPoint("StartToStart",sel);
sel_after.setEndPoint("EndToEnd",sel);
text_before = sel_before.text;
text_after = sel_after.text;
}

}
function insert(AddCode) {
cursor();
document.getElementById("nachricht").value = text_before + AddCode + text_after;
document.getElementById("nachricht").focus();
}