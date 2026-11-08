//Geisterschiff zu Halloween (src/Model/GhostShip/GhostShipEvent.php): blendet ab der Ankunft vor Sektor 666 den
//Funkspruch ein, einmal je Gerät und ohne Ton, und lässt die Ansicht von Sektor 666 flackern. Daten in window.deGeist.
(function(){
	'use strict';
	var cfg=window.deGeist;
	var ruhig=window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

	//dm.php: über der ganzen Seite, Classic (Frameset): in der Spielspalte, mobil: auf der Seite selbst
	function zielDokument(){
		try{
			return top.document.body.nodeName!=='FRAMESET' ? top.document : top.frames.h.document;
		}catch(e){
			return document;
		}
	}

	function gesehen(){
		try{
			return localStorage.getItem('deGeistSignal')===String(cfg.signal);
		}catch(e){
			return false;
		}
	}

	//merken: false beim erneuten Anzeigen über den Knopf in der 666-Ansicht
	function zeigen(merken){
		var doc=zielDokument(), win=doc.defaultView;
		if(win.deGeistOffen){
			return;
		}
		win.deGeistOffen=true;
		if(!doc.querySelector('link[data-geist]')){
			doc.head.insertAdjacentHTML('beforeend', '<link rel="stylesheet" href="gp/geisterschiff.css" data-geist>');
		}

		var el=doc.createElement('div');
		el.className='geist-signal';
		el.innerHTML='<div class="geist-signal-inhalt"><div class="geist-signal-kopf"></div><canvas width="480" height="70"></canvas>'
			+'<div class="geist-signal-kennung"></div><div class="geist-signal-nachricht"></div><div class="geist-signal-hinweis"></div></div>';
		el.querySelector('.geist-signal-kopf').textContent='▮▮ '+cfg.kopf+' ▮▮';
		el.querySelector('.geist-signal-hinweis').textContent=cfg.hinweis;
		doc.body.appendChild(el);

		var offen=true, spricht=false, timer=0;
		function schliessen(){
			if(!offen){
				return;
			}
			offen=false;
			win.clearTimeout(timer);
			el.classList.add('geist-aus');
			win.setTimeout(function(){ el.remove(); }, 700);
			win.deGeistOffen=false;
			if(merken){
				try{ localStorage.setItem('deGeistSignal', String(cfg.signal)); }catch(e){}
			}
		}
		el.addEventListener('click', schliessen);

		//Zeilen nacheinander eintippen: erst die Kennung zügig, dann der Funkspruch langsam
		var zeilen=cfg.kennung.map(function(t){ return [t, '.geist-signal-kennung', 18]; })
			.concat(cfg.nachricht.map(function(t){ return [t, '.geist-signal-nachricht', 45]; }));
		var i=0, n=0, zeile=null;
		function tippen(){
			if(i>=zeilen.length){
				spricht=false;
				timer=win.setTimeout(schliessen, 6000);
				return;
			}
			if(!zeile){
				zeile=el.querySelector(zeilen[i][1]).appendChild(doc.createElement('div'));
				n=ruhig ? zeilen[i][0].length : 0;
			}
			spricht=true;
			zeile.textContent=zeilen[i][0].substring(0, ++n);
			if(n<zeilen[i][0].length){
				timer=win.setTimeout(tippen, zeilen[i][2]);
			}else{
				spricht=false;
				zeile=null;
				i++;
				timer=win.setTimeout(tippen, ruhig ? 0 : 600);
			}
		}
		timer=win.setTimeout(tippen, 700);

		//Oszilloskop: schlägt aus, solange getippt wird
		var c2d=el.querySelector('canvas').getContext('2d'), pegel=0;
		(function bild(zeit){
			pegel+=((spricht ? 1 : 0.08)-pegel)*0.08;
			c2d.clearRect(0, 0, 480, 70);
			c2d.strokeStyle='#9ff3d6';
			c2d.lineWidth=2;
			c2d.beginPath();
			for(var x=0;x<=480;x+=3){
				c2d.lineTo(x, 35+(Math.sin(x*0.045+zeit*0.006)*0.5+Math.sin(x*0.13-zeit*0.011)*0.3+Math.random()*0.5-0.25)*pegel*28);
			}
			c2d.stroke();
			if(offen && !ruhig){
				win.requestAnimationFrame(bild);
			}
		})(0);
	}

	//666-Ansicht: einzelne Zeichen flackern kurz zu Störzeichen
	var texte=document.querySelectorAll('.s666-text, .geist-funk p');
	if(document.querySelector('.geist-funk') && !ruhig){
		setInterval(function(){
			var knoten=texte[Math.floor(Math.random()*texte.length)].firstChild;
			if(!knoten || knoten.nodeType!==3 || knoten.nodeValue.length<4){
				return;
			}
			var alt=knoten.nodeValue, p=Math.floor(Math.random()*alt.length);
			knoten.nodeValue=alt.substring(0, p)+'▮▒░#%?'.charAt(Math.floor(Math.random()*6))+alt.substring(p+1);
			setTimeout(function(){ knoten.nodeValue=alt; }, 120);
		}, 1400);
	}

	document.addEventListener('click', function(e){
		if(e.target.closest('[data-geist-abspielen]')){
			zeigen(false);
		}
	});

	if(cfg.live && !gesehen()){
		zeigen(true);
	}
})();
