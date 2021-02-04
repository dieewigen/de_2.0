 function SetMil(obj) {
 	if (anzs == -1) { return false }
 	
	if (firstrun == true) {
		for (var x=0; x <= anzs; ++x) {
			document.getElementById("m"+(x+1)+"_1").value = addPkt(aktf[x][0]);
			document.getElementById("m"+(x+1)+"_2").value = addPkt(aktf[x][1]);
			document.getElementById("m"+(x+1)+"_3").value = addPkt(aktf[x][2]);

			calcf[x] = aktf[x][0] + aktf[x][1] + aktf[x][2];
			document.getElementById("m"+(x+1)+"_0").innerHTML = addPkt(gesamtf[x] - calcf[x]);
		}
		firstrun = false;
	}
	else{
     obj.value = obj.value.replace(/[^0-9]/,"") * 1;
     runagain = false;
     
     for (var x=0; x <= anzs; ++x) {
       htmp = ((gesamtf[x] * 1) - (calcf[x] * 1));

       for (var y=0; y <= 2; ++y) {
         aktf[x][y] = (delPkt(document.getElementById("m"+(x+1)+"_"+(y+1)).value) * 1);
         if ((aktf[x][y] < 0) || (isNaN(aktf[x][y]) == true)) { 
           document.getElementById("m"+(x+1)+"_"+(y+1)).value = 0;
           aktf[x][y] = 0;
         } 
       }

       calcf[x] = aktf[x][0] + aktf[x][1] + aktf[x][2];

       if ((gesamtf[x] - calcf[x]) < 0) {
         vt = ((vt * 1) + (htmp * 1));
         obj.value = vt;
         runagain = true;

         for (var y=0; y <= 2; ++y) {
           aktf[x][y] = (delPkt(document.getElementById("m"+(x+1)+"_"+(y+1)).value) * 1);
           if ((aktf[x][y] < 0) || (isNaN(aktf[x][y]) == true)) { 
             document.getElementById("m"+(x+1)+"_"+(y+1)).value = 0;
             aktf[x][y] = 0;
           } 
         }

         calcf[x] = aktf[x][0] + aktf[x][1] + aktf[x][2];
       }
       else { vt = (obj.value * 1); }

       document.getElementById("m"+(x+1)+"_0").innerHTML = addPkt(gesamtf[x] - calcf[x]);
     }
   }

   if (runagain == true) { SetMil(obj); }
   GetSetTrager();
 }

function GetSetTrager() {
  for (var x=0; x <= 2; ++x) {
    trager[x] = 0;
    tragbar[x][0] = 0;
    tragbar[x][1] = 0;
  }

  for (var x=0; x <= anzs; ++x) {
    for (var y=0; y <= 2; ++y) {
      if (x == tragers[y]) {
        trager[0] = trager[0] + (aktf[x][0] * tragers[y+3]);
        trager[1] = trager[1] + (aktf[x][1] * tragers[y+3]);
        trager[2] = trager[2] + (aktf[x][2] * tragers[y+3]);
      }

      if (y <= 1) {
        if (x == tragbars[y]) {
          tragbar[0][y] = tragbar[0][y] + (aktf[x][0] * tragbars[y+2]);
          tragbar[1][y] = tragbar[1][y] + (aktf[x][1] * tragbars[y+2]);
          tragbar[2][y] = tragbar[2][y] + (aktf[x][2] * tragbars[y+2]);
        }
      }
    }
  }

  for(var i=0;i<3;i++){
    var tag1=''; var tag2='';
    if(tragbar[0][0] + tragbar[0][1]>trager[0]){
      tag1='<span style="color: #FF0000">';
      tag2='</span>';
    }

    document.getElementById("m"+(i+1)+"_t").innerHTML = tag1+addPkt(tragbar[i][0] + tragbar[i][1])+tag2; 
    document.getElementById("m"+(i+1)+"_t_max").innerHTML = tag1+addPkt(trager[i])+tag2; 

    document.getElementById("m"+(i+1)+"_fk").innerHTML = addPkt(aktf[8][i]*fk_per_ship); 
    
  }

   GetReiseZ();
 }

 function GetReiseZ() {
   for (var rz=0; rz <= 2; ++rz) {
     var z1 = 0;
     var z2 = 0;
     var z3 = 0;

     if ((tragbar[rz][0] + tragbar[rz][1]) > 0) {
       if ((tragbar[rz][0] + tragbar[rz][1]) <= trager[rz]) {
         for (var x=0; x <= anzs; ++x) {
           if (aktf[x][rz] != 0) {
             if ((x != tragbars[0]) && (x != tragbars[1])) {
               //bsmod einbeziehen
               bsspeedupmod=0;
               if (x==tragers[1] && aktf[tragers[1]][rz]>=0) {
                 if (aktf[bsspeedup[0]][rz]>=aktf[tragers[1]][rz]*bsspeedup[2] && aktf[bsspeedup[1]][rz]>=aktf[tragers[1]][rz]*bsspeedup[3])bsspeedupmod=1;
               }
               if (reisez[x][0] > z1) { z1 = reisez[x][0]-bsspeedupmod; }
               if (reisez[x][1] > z2) { z2 = reisez[x][1]-bsspeedupmod; }
               if (reisez[x][2] > z3) { z3 = reisez[x][2]-bsspeedupmod; }
             }
           }
         }
       }
       else {
         // Bomber werden als erstes in Tr�ger gesetzt, da l�ngere Reisezeit
         if (tragbar[rz][1] > trager[rz]) {
           // Bomber-Reisezeit, da mehr Bomber als Platz vorhanden
           z1 = reisez[tragbars[1]][0];
           z2 = reisez[tragbars[1]][1];
           z3 = reisez[tragbars[1]][2];
         }
         else {
           if ((tragbar[rz][0] + tragbar[rz][1]) > trager[rz]) {
             // Hornissen-Reisezeit, da mehr Hornissen als Platz vorhanden
             z1 = reisez[tragbars[0]][0];
             z2 = reisez[tragbars[0]][1];
             z3 = reisez[tragbars[0]][2];
           }
         }
       }
     }
     else {
       for (var x=0; x <= anzs; ++x) {
         if (aktf[x][rz] != 0) {
           if ((x != tragbars[0]) && (x != tragbars[1])) {
             //bsmod einbeziehen
             bsspeedupmod=0;
             if (x==tragers[1] && aktf[tragers[1]][rz]>=0) {
               if (aktf[bsspeedup[0]][rz]>=aktf[tragers[1]][rz]*bsspeedup[2] && aktf[bsspeedup[1]][rz]>=aktf[tragers[1]][rz]*bsspeedup[3])bsspeedupmod=1;
             }
             if (reisez[x][0] > z1) { z1 = reisez[x][0]-bsspeedupmod; }
             if (reisez[x][1] > z2) { z2 = reisez[x][1]-bsspeedupmod; }
             if (reisez[x][2] > z3) { z3 = reisez[x][2]-bsspeedupmod; }

           }
         }
       }
     }

     document.getElementById("rz"+(rz+1)+"_1").innerHTML = (fleetna[rz] == true) ? "" : z1;
     document.getElementById("rz"+(rz+1)+"_2").innerHTML = (fleetna[rz] == true) ? "" : z3;
     //document.getElementById("rz"+(rz+1)+"_3").innerHTML = (fleetna[rz] == true) ? "" : z3;
   }
   CalcFleetPoints();
 }
 
function CalcFleetPoints()
{
	
  var fp = new Array();
  fp[0]=0;
  fp[1]=0;
  fp[2]=0;
  fp[3]=0;
  for (var x=0; x <= anzs; ++x) 
  {
    for (var y=0; y <= 3; ++y) 
    {
      //alert(("m"+(x+1)+"_"+(y))+":"+delPkt(document.getElementById("m"+(x+1)+"_"+(y)).value))
      if(y==0)fp[y] = fp[y] + (delPkt(document.getElementById("m"+(x+1)+"_"+(y)).innerHTML) * shipscore[x]);
      else 
      {
    	if(fleetna[y-1]==false)
    	  fp[y] = fp[y] + (delPkt(document.getElementById("m"+(x+1)+"_"+(y)).value) * shipscore[x]);
    	else 
    	  fp[y] = fp[y] + (delPkt(document.getElementById("mn"+(x+1)+"_"+(y)).innerHTML) * shipscore[x]);	
      }
    }
  }	
   
  document.getElementById("fp0").innerHTML = addPkt(fp[0]);
  document.getElementById("fp1").innerHTML = addPkt(fp[1]);
  document.getElementById("fp2").innerHTML = addPkt(fp[2]);
  document.getElementById("fp3").innerHTML = addPkt(fp[3]);
}
 
 
 function DoFleetAction(iFleet, sVar) {
   sVal = sVar.split(":");
   iMode = sVal[0];
   iVal = sVal[1];
 
   switch (iMode) {
     case "0":
       for (var x=0; x <= anzs; ++x) {
         if (iFleet == 1) { aktf[x][0] = gesamtf[x]; } else { aktf[x][0] = 0; }
         if (iFleet == 2) { aktf[x][1] = gesamtf[x]; } else { aktf[x][1] = 0; }
         if (iFleet == 3) { aktf[x][2] = gesamtf[x]; } else { aktf[x][2] = 0; }

         document.getElementById("m"+(x+1)+"_1").value = addPkt(aktf[x][0]);
         document.getElementById("m"+(x+1)+"_2").value = addPkt(aktf[x][1]);
         document.getElementById("m"+(x+1)+"_3").value = addPkt(aktf[x][2]);

         calcf[x] = aktf[x][0] + aktf[x][1] + aktf[x][2];
         document.getElementById("m"+(x+1)+"_0").innerHTML = addPkt(gesamtf[x] - calcf[x]);
       }
       break;
     
     case "1":
     case "2":
     case "3":
     case "4":
       for (var x=0; x <= anzs; ++x) {
         switch (iMode) {
           case "1":
             aktf[x][(iFleet-1)] = aktf[x][(iFleet-1)] + (gesamtf[x] - calcf[x]);
             break;
           case "2":
           case "3":
             aktf[x][(iFleet-1)] = aktf[x][(iFleet-1)] + aktf[x][(iVal-1)];
             aktf[x][(iVal-1)] = 0;
             break;
           case "4":
             aktf[x][(iFleet-1)] = 0;
             break;
         }

         document.getElementById("m"+(x+1)+"_"+iFleet).value = addPkt(aktf[x][(iFleet-1)]);
         if (iVal != -1) { document.getElementById("m"+(x+1)+"_"+iVal).value = addPkt(aktf[x][(iVal-1)]); }

         calcf[x] = aktf[x][0] + aktf[x][1] + aktf[x][2];
         document.getElementById("m"+(x+1)+"_0").innerHTML = addPkt(gesamtf[x] - calcf[x]);
       }
       break;

     case "5":
       for (var x=0; x <= anzs; ++x) {
         aktf[x][(iFleet-1)] = aktf[x][(iFleet-1)] + Math.round((gesamtf[x] - calcf[x]) / 100 * iVal);
         document.getElementById("m"+(x+1)+"_"+iFleet).value = addPkt(aktf[x][(iFleet-1)]);

         calcf[x] = aktf[x][0] + aktf[x][1] + aktf[x][2];
         document.getElementById("m"+(x+1)+"_0").innerHTML = addPkt(gesamtf[x] - calcf[x]);
       }
       break;
   }

     if (iFleet != 0) {document.getElementById("fs_"+iFleet).options.selectedIndex = 0; }   GetSetTrager();
 }

 function addPkt(num) {
   num = num.toString();
   if (num.length > 3) {
     var l = num.length % 3;
     if (l==0) l = 3;
     var result = num.substr(0,l);
     for (ii=0; (ii*3)+l < num.length; ii++) {
       result = result +"."+ num.substr((ii*3)+l,3);
     }
     return result;
   }
   else return num;
 }

function delPkt(num) 
{
  if(num==undefined)num="";	
  while (num.search(/\./) != -1) num = num.replace(/\./,"");
  return parseInt(num);
}