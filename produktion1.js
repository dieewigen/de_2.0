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

function berechnepreise()
{
var m=0,d=0,i=0,e=0,k=0,t=0,p=0,pfea=0;

var schiff=new Array(20)
for (var x=0; x < schiff.length; ++x)
 schiff[x] = new Array(6);

//Ewiger
//Nisse
schiff[0][0]="1000";
schiff[0][1]="250";
schiff[0][2]="0";
schiff[0][3]="0";
schiff[0][4]="0";
schiff[0][5]="0";
schiff[0][6]=((schiff[0][0]*1)+(schiff[0][1]*2)+(schiff[0][2]*3)+(schiff[0][3]*4))/10+(schiff[0][4]*1000);

//Jabo
schiff[1][0]="4000";
schiff[1][1]="1000";
schiff[1][2]="0";
schiff[1][3]="0";
schiff[1][4]="0";
schiff[1][5]="0";
schiff[1][6]=((schiff[1][0]*1)+(schiff[1][1]*2)+(schiff[1][2]*3)+(schiff[1][3]*4))/10+(schiff[1][4]*1000);

//zerri
schiff[2][0]="15000";
schiff[2][1]="5000";
schiff[2][2]="1000";
schiff[2][3]="0";
schiff[2][4]="0";
schiff[2][5]="0";
schiff[2][6]=((schiff[2][0]*1)+(schiff[2][1]*2)+(schiff[2][2]*3)+(schiff[2][3]*4))/10+(schiff[2][4]*1000);

//Kreuzer
schiff[3][0]="30000";
schiff[3][1]="10000";
schiff[3][2]="1000";
schiff[3][3]="1500";
schiff[3][4]="0";
schiff[3][5]="20";
schiff[3][6]=((schiff[3][0]*1)+(schiff[3][1]*2)+(schiff[3][2]*3)+(schiff[3][3]*4))/10+(schiff[3][4]*1000);

//schlachter
schiff[4][0]="50000";
schiff[4][1]="20000";
schiff[4][2]="2000";
schiff[4][3]="4000";
schiff[4][4]="2";
schiff[4][5]="80";
schiff[4][6]=((schiff[4][0]*1)+(schiff[4][1]*2)+(schiff[4][2]*3)+(schiff[4][3]*4))/10+(schiff[4][4]*1000);

//Bomber
schiff[5][0]="1500";
schiff[5][1]="500";
schiff[5][2]="0";
schiff[5][3]="0";
schiff[5][4]="0";
schiff[5][5]="0";
schiff[5][6]=((schiff[5][0]*1)+(schiff[5][1]*2)+(schiff[5][2]*3)+(schiff[5][3]*4))/10+(schiff[5][4]*1000);

//Transe
schiff[6][0]="2000";
schiff[6][1]="1000";
schiff[6][2]="0";
schiff[6][3]="0";
schiff[6][4]="0";
schiff[6][5]="0";
schiff[6][6]=((schiff[6][0]*1)+(schiff[6][1]*2)+(schiff[6][2]*3)+(schiff[6][3]*4))/10+(schiff[6][4]*1000);

//Träger
schiff[7][0]="50000";
schiff[7][1]="30000";
schiff[7][2]="5000";
schiff[7][3]="5000";
schiff[7][4]="1";
schiff[7][5]="300";
schiff[7][6]=((schiff[7][0]*1)+(schiff[7][1]*2)+(schiff[7][2]*3)+(schiff[7][3]*4))/10+(schiff[7][4]*1000);

//Frachter
schiff[15][0]="5000";
schiff[15][1]="1500";
schiff[15][2]="500";
schiff[15][3]="0";
schiff[15][4]="0";
schiff[15][5]="0";
schiff[15][6]=((schiff[15][0]*1)+(schiff[15][1]*2)+(schiff[15][2]*3)+(schiff[15][3]*4))/10+(schiff[15][4]*1000);

//Titan
schiff[16][0]="500000";
schiff[16][1]="200000";
schiff[16][2]="20000";
schiff[16][3]="40000";
schiff[16][4]="3";
schiff[16][5]="0";
schiff[16][6]=((schiff[16][0]*1)+(schiff[16][1]*2)+(schiff[16][2]*3)+(schiff[16][3]*4))/10+(schiff[16][4]*1000);

//Türme
//Turm 1
schiff[8][0]="10000";
schiff[8][1]="2500";
schiff[8][2]="0";
schiff[8][3]="0";
schiff[8][4]="0";
schiff[8][5]="0";
schiff[8][6]=((schiff[8][0]*1)+(schiff[8][1]*2)+(schiff[8][2]*3)+(schiff[8][3]*4))/10+(schiff[8][4]*1000);

//Turm 2
schiff[9][0]="800";
schiff[9][1]="550";
schiff[9][2]="0";
schiff[9][3]="0";
schiff[9][4]="0";
schiff[9][5]="0";
schiff[9][6]=((schiff[9][0]*1)+(schiff[9][1]*2)+(schiff[9][2]*3)+(schiff[9][3]*4))/10+(schiff[9][4]*1000);


//Turm 3
schiff[10][0]="250";
schiff[10][1]="500";
schiff[10][2]="0";
schiff[10][3]="0";
schiff[10][4]="0";
schiff[10][5]="0";
schiff[10][6]=((schiff[10][0]*1)+(schiff[10][1]*2)+(schiff[10][2]*3)+(schiff[10][3]*4))/10+(schiff[10][4]*1000);


//Turm 4
schiff[11][0]="2500";
schiff[11][1]="300";
schiff[11][2]="50";
schiff[11][3]="0";
schiff[11][4]="0";
schiff[11][5]="0";
schiff[11][6]=((schiff[11][0]*1)+(schiff[11][1]*2)+(schiff[11][2]*3)+(schiff[11][3]*4))/10+(schiff[11][4]*1000);


//Turm 5
schiff[12][0]="2000";
schiff[12][1]="1000";
schiff[12][2]="500";
schiff[12][3]="0";
schiff[12][4]="0";
schiff[12][5]="0";
schiff[12][6]=((schiff[12][0]*1)+(schiff[12][1]*2)+(schiff[12][2]*3)+(schiff[12][3]*4))/10+(schiff[12][4]*1000);

//Sonde
schiff[13][0]="500";
schiff[13][1]="500";
schiff[13][2]="0";
schiff[13][3]="0";
schiff[13][4]="0";
schiff[13][5]="0";

//Agent
schiff[14][0]="500";
schiff[14][1]="500";
schiff[14][2]="200";
schiff[14][3]="100";
schiff[14][4]="0";
schiff[14][5]="0";

if(document.produktion.b81)
{
m=m+(document.getElementById("b81").value*(schiff[0][0]-(schiff[0][0]*abf/100)));
d=d+(document.getElementById("b81").value*(schiff[0][1]-(schiff[0][1]*abf/100)));
i=i+(document.getElementById("b81").value*(schiff[0][2]-(schiff[0][2]*abf/100)));
e=e+(document.getElementById("b81").value*(schiff[0][3]-(schiff[0][3]*abf/100)));
t=t+(document.getElementById("b81").value*(schiff[0][4]-(schiff[0][4]*abf/100)));
k=k+(document.getElementById("b81").value*schiff[0][5]);
p=p+(document.getElementById("b81").value*schiff[0][6]);
}

if(document.produktion.b82)
{
m=m+(document.getElementById("b82").value*(schiff[1][0]-(schiff[1][0]*abf/100)));
d=d+(document.getElementById("b82").value*(schiff[1][1]-(schiff[1][1]*abf/100)));
i=i+(document.getElementById("b82").value*(schiff[1][2]-(schiff[1][2]*abf/100)));
e=e+(document.getElementById("b82").value*(schiff[1][3]-(schiff[1][3]*abf/100)));
t=t+(document.getElementById("b82").value*(schiff[1][4]-(schiff[1][4]*abf/100)));
k=k+(document.getElementById("b82").value*schiff[1][5]);
p=p+(document.getElementById("b82").value*schiff[1][6]);
}

if(document.produktion.b83)
{
m=m+(document.getElementById("b83").value*(schiff[2][0]-(schiff[2][0]*abf/100)));
d=d+(document.getElementById("b83").value*(schiff[2][1]-(schiff[2][1]*abf/100)));
i=i+(document.getElementById("b83").value*(schiff[2][2]-(schiff[2][2]*abf/100)));
e=e+(document.getElementById("b83").value*(schiff[2][3]-(schiff[2][3]*abf/100)));
t=t+(document.getElementById("b83").value*(schiff[2][4]-(schiff[2][4]*abf/100)));
k=k+(document.getElementById("b83").value*schiff[2][5]);
p=p+(document.getElementById("b83").value*schiff[2][6]);
}

if(document.produktion.b84)
{
m=m+(document.getElementById("b84").value*(schiff[3][0]-(schiff[3][0]*abf/100)));
d=d+(document.getElementById("b84").value*(schiff[3][1]-(schiff[3][1]*abf/100)));
i=i+(document.getElementById("b84").value*(schiff[3][2]-(schiff[3][2]*abf/100)));
e=e+(document.getElementById("b84").value*(schiff[3][3]-(schiff[3][3]*abf/100)));
t=t+(document.getElementById("b84").value*(schiff[3][4]-(schiff[3][4]*abf/100)));
k=k+(document.getElementById("b84").value*schiff[3][5]);
p=p+(document.getElementById("b84").value*schiff[3][6]);
}

if(document.produktion.b85)
{
m=m+(document.getElementById("b85").value*(schiff[4][0]-(schiff[4][0]*abf/100)));
d=d+(document.getElementById("b85").value*(schiff[4][1]-(schiff[4][1]*abf/100)));
i=i+(document.getElementById("b85").value*(schiff[4][2]-(schiff[4][2]*abf/100)));
e=e+(document.getElementById("b85").value*(schiff[4][3]-(schiff[4][3]*abf/100)));
t=t+(document.getElementById("b85").value*(schiff[4][4]-(schiff[4][4]*abf/100)));
k=k+(document.getElementById("b85").value*schiff[4][5]);
p=p+(document.getElementById("b85").value*schiff[4][6]);
}

if(document.produktion.b86)
{
m=m+(document.getElementById("b86").value*(schiff[5][0]-(schiff[5][0]*abf/100)));
d=d+(document.getElementById("b86").value*(schiff[5][1]-(schiff[5][1]*abf/100)));
i=i+(document.getElementById("b86").value*(schiff[5][2]-(schiff[5][2]*abf/100)));
e=e+(document.getElementById("b86").value*(schiff[5][3]-(schiff[5][3]*abf/100)));
t=t+(document.getElementById("b86").value*(schiff[5][4]-(schiff[5][4]*abf/100)));
k=k+(document.getElementById("b86").value*schiff[5][5]);
p=p+(document.getElementById("b86").value*schiff[5][6]);
}

if(document.produktion.b87)
{
m=m+(document.getElementById("b87").value*(schiff[6][0]-(schiff[6][0]*abf/100)));
d=d+(document.getElementById("b87").value*(schiff[6][1]-(schiff[6][1]*abf/100)));
i=i+(document.getElementById("b87").value*(schiff[6][2]-(schiff[6][2]*abf/100)));
e=e+(document.getElementById("b87").value*(schiff[6][3]-(schiff[6][3]*abf/100)));
t=t+(document.getElementById("b87").value*(schiff[6][4]-(schiff[6][4]*abf/100)));
k=k+(document.getElementById("b87").value*schiff[6][5]);
p=p+(document.getElementById("b87").value*schiff[6][6]);
}

if(document.produktion.b88)
{
m=m+(document.getElementById("b88").value*(schiff[7][0]-(schiff[7][0]*abf/100)));
d=d+(document.getElementById("b88").value*(schiff[7][1]-(schiff[7][1]*abf/100)));
i=i+(document.getElementById("b88").value*(schiff[7][2]-(schiff[7][2]*abf/100)));
e=e+(document.getElementById("b88").value*(schiff[7][3]-(schiff[7][3]*abf/100)));
t=t+(document.getElementById("b88").value*(schiff[7][4]-(schiff[7][4]*abf/100)));
k=k+(document.getElementById("b88").value*schiff[7][5]);
p=p+(document.getElementById("b88").value*schiff[7][6]);
}

if(document.produktion.b89)
{
m=m+(document.getElementById("b89").value*(schiff[15][0]-(schiff[15][0]*abf/100)));
d=d+(document.getElementById("b89").value*(schiff[15][1]-(schiff[15][1]*abf/100)));
i=i+(document.getElementById("b89").value*(schiff[15][2]-(schiff[15][2]*abf/100)));
e=e+(document.getElementById("b89").value*(schiff[15][3]-(schiff[15][3]*abf/100)));
t=t+(document.getElementById("b89").value*(schiff[15][4]-(schiff[15][4]*abf/100)));
k=k+(document.getElementById("b89").value*schiff[15][5]);
p=p+(document.getElementById("b89").value*schiff[15][6]);
}

if(document.produktion.b90)
{
m=m+(document.getElementById("b90").value*(schiff[16][0]-(schiff[16][0]*abf/100)));
d=d+(document.getElementById("b90").value*(schiff[16][1]-(schiff[16][1]*abf/100)));
i=i+(document.getElementById("b90").value*(schiff[16][2]-(schiff[16][2]*abf/100)));
e=e+(document.getElementById("b90").value*(schiff[16][3]-(schiff[16][3]*abf/100)));
t=t+(document.getElementById("b90").value*(schiff[16][4]-(schiff[16][4]*abf/100)));
k=k+(document.getElementById("b90").value*schiff[16][5]);
p=p+(document.getElementById("b90").value*schiff[16][6]);
}

if(document.produktion.b100)
{
m=m+(document.getElementById("b100").value*(schiff[8][0]-(schiff[8][0]*abd/100)));
d=d+(document.getElementById("b100").value*(schiff[8][1]-(schiff[8][1]*abd/100)));
i=i+(document.getElementById("b100").value*(schiff[8][2]-(schiff[8][2]*abd/100)));
e=e+(document.getElementById("b100").value*(schiff[8][3]-(schiff[8][3]*abd/100)));
t=t+(document.getElementById("b100").value*(schiff[8][4]-(schiff[8][4]*abd/100)));
k=k+(document.getElementById("b100").value*schiff[8][5]);
p=p+(document.getElementById("b100").value*schiff[8][6]);
}

if(document.produktion.b101)
{
m=m+(document.getElementById("b101").value*(schiff[9][0]-(schiff[9][0]*abd/100)));
d=d+(document.getElementById("b101").value*(schiff[9][1]-(schiff[9][1]*abd/100)));
i=i+(document.getElementById("b101").value*(schiff[9][2]-(schiff[9][2]*abd/100)));
e=e+(document.getElementById("b101").value*(schiff[9][3]-(schiff[9][3]*abd/100)));
t=t+(document.getElementById("b101").value*(schiff[9][4]-(schiff[9][4]*abd/100)));
k=k+(document.getElementById("b101").value*schiff[9][5]);
p=p+(document.getElementById("b101").value*schiff[9][6]);
}

if(document.produktion.b102)
{
m=m+(document.getElementById("b102").value*(schiff[10][0]-(schiff[10][0]*abd/100)));
d=d+(document.getElementById("b102").value*(schiff[10][1]-(schiff[10][1]*abd/100)));
i=i+(document.getElementById("b102").value*(schiff[10][2]-(schiff[10][2]*abd/100)));
e=e+(document.getElementById("b102").value*(schiff[10][3]-(schiff[10][3]*abd/100)));
t=t+(document.getElementById("b102").value*(schiff[10][4]-(schiff[10][4]*abd/100)));
k=k+(document.getElementById("b102").value*schiff[10][5]);
p=p+(document.getElementById("b102").value*schiff[10][6]);
}

if(document.produktion.b103)
{
m=m+(document.getElementById("b103").value*(schiff[11][0]-(schiff[11][0]*abd/100)));
d=d+(document.getElementById("b103").value*(schiff[11][1]-(schiff[11][1]*abd/100)));
i=i+(document.getElementById("b103").value*(schiff[11][2]-(schiff[11][2]*abd/100)));
e=e+(document.getElementById("b103").value*(schiff[11][3]-(schiff[11][3]*abd/100)));
t=t+(document.getElementById("b103").value*(schiff[11][4]-(schiff[11][4]*abd/100)));
k=k+(document.getElementById("b103").value*schiff[11][5]);
p=p+(document.getElementById("b103").value*schiff[11][6]);
}

if(document.produktion.b104)
{
m=m+(document.getElementById("b104").value*(schiff[12][0]-(schiff[12][0]*abd/100)));
d=d+(document.getElementById("b104").value*(schiff[12][1]-(schiff[12][1]*abd/100)));
i=i+(document.getElementById("b104").value*(schiff[12][2]-(schiff[12][2]*abd/100)));
e=e+(document.getElementById("b104").value*(schiff[12][3]-(schiff[12][3]*abd/100)));
t=t+(document.getElementById("b104").value*(schiff[12][4]-(schiff[12][4]*abd/100)));
k=k+(document.getElementById("b104").value*schiff[12][5]);
p=p+(document.getElementById("b104").value*schiff[12][6]);
}

if(document.produktion.b111)
{
m=m+(document.getElementById("b111").value*(schiff[14][0]-(schiff[14][0]*ab/100)));
d=d+(document.getElementById("b111").value*(schiff[14][1]-(schiff[14][1]*ab/100)));
i=i+(document.getElementById("b111").value*(schiff[14][2]-(schiff[14][2]*ab/100)));
e=e+(document.getElementById("b111").value*(schiff[14][3]-(schiff[14][3]*ab/100)));
t=t+(document.getElementById("b111").value*(schiff[14][4]-(schiff[14][4]*ab/100)));

if(isNaN(document.getElementById("b111").value))
document.getElementById("b111").value="";

/*
if(document.getElementById("va").innerHTML<50000)
if(((document.getElementById("b111").value)-(-document.getElementById("va").innerHTML))<=50000)
{
p=(((m*1)+(d*2)+(i*3)+(e*4))/10);
}
else
{
pfea=(((schiff[14][0]*1)+(schiff[14][1]*2)+(schiff[14][2]*3)+(schiff[14][3]*4))/10);
p=pfea*(50000-(document.getElementById("va").innerHTML));
}
*/
//p=document.getElementById("b111").value*(((schiff[14][0]*1)+(schiff[14][1]*2)+(schiff[14][2]*3)+(schiff[14][3]*4))/10);
p=0;
}

if(document.produktion.b110)
{
m=m+(document.getElementById("b110").value*(schiff[13][0]-(schiff[13][0]*ab/100)));
d=d+(document.getElementById("b110").value*(schiff[13][1]-(schiff[13][1]*ab/100)));
i=i+(document.getElementById("b110").value*(schiff[13][2]-(schiff[13][2]*ab/100)));
e=e+(document.getElementById("b110").value*(schiff[13][3]-(schiff[13][3]*ab/100)));
t=t+(document.getElementById("b110").value*(schiff[13][4]-(schiff[13][4]*ab/100)));
}

if(isNaN(m))
m=0;
if(isNaN(d))
d=0;
if(isNaN(i))
i=0;
if(isNaN(e))
e=0;
if(isNaN(t))
t=0;
if(isNaN(k))
k=0;
if(isNaN(p))
p=0;


if(document.produktion.b111)
document.getElementById("p").innerHTML=number_format(p);
else
document.getElementById("p").innerHTML=number_format(p);

var f1='';var f2='';

m=Math.ceil(m);
d=Math.ceil(d);
i=Math.ceil(i);
e=Math.ceil(e);
t=Math.ceil(t);

if(m>hasres[0]){f1='<font color="#FF0000">';f2='</font>';}else{f1='';f2='';}
document.getElementById("m").innerHTML=f1+number_format(m)+f2;
if(d>hasres[1]){f1='<font color="#FF0000">';f2='</font>';}else{f1='';f2='';}
document.getElementById("d").innerHTML=f1+number_format(d)+f2;
if(i>hasres[2]){f1='<font color="#FF0000">';f2='</font>';}else{f1='';f2='';}
document.getElementById("i").innerHTML=f1+number_format(i)+f2;
if(e>hasres[3]){f1='<font color="#FF0000">';f2='</font>';}else{f1='';f2='';}
document.getElementById("e").innerHTML=f1+number_format(e)+f2;
if(t>hasres[4]){f1='<font color="#FF0000">';f2='</font>';}else{f1='';f2='';}
document.getElementById("t").innerHTML=f1+number_format(t)+f2;

if(document.produktion.b81)
{
document.getElementById("k").innerHTML=number_format(k*traegerbonus);
}



}