function ergebnis() {
        if(document.loginform.nummer.value=="") {
                alert("Sie haben vergessen die Aufgabe aus der Grafik zu lösen!");
                return false;
        }
        else return true;
}
function block() {
        return false;
}
function noPaste() {
        document.loginform.nummer.value="";
        event.returnValue = false;
        return false;
}
document.oncontextmenu = block;

function leeren()
{
document.getElementById("nachricht").value = "";
document.getElementById("nachricht").focus();
}

function hilfe()
{
window.open("hfnlegende.php","BitteBeachten","width=572,height=390,left=34,top=75");
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