//Technologien (ang_techs.php): Filter nach Typ und Status, Ansicht Baum/Liste und Ton ohne Neuladen (Cookies),
//dazu werden Kacheln nachgezogen, wenn ein laufender Auftrag fertig wird, waehrend die Seite offen ist

function tc_init(){
	var tc=$('#tc');

	tc.on('click', '[data-filter-typ]', function(){
		tc.attr('data-typ', $(this).attr('data-filter-typ'));
		setCookie('tech_filter_typ', tc.attr('data-typ'));
		tc_filter();
	});

	tc.on('click', '[data-filter-status]', function(){
		tc.attr('data-status', $(this).attr('data-filter-status'));
		setCookie('tech_status', tc.attr('data-status'));
		tc_filter();
	});

	tc.on('click', '[data-ansicht]', function(){
		var liste=$(this).attr('data-ansicht')=='1';
		tc.toggleClass('tc-liste', liste).toggleClass('tc-baum', !liste);
		setCookie('tech_anordnung', liste ? 1 : 0);
		tc_schalter();
	});

	//tech_sound: 0 = an, 1 = aus
	tc.on('click', '[data-ton]', function(){
		var aus=$(this).attr('data-ton')=='1';
		tc.attr('data-ton', aus ? 0 : 1);
		setCookie('tech_sound', aus ? 1 : 0);
		tc_schalter();
	});

	tc_filter();

	//nach einem Start oder Abbruch die Aktion aus der Adresszeile nehmen, damit ein Neuladen sie nicht wiederholt
	if(/[?&](start_tech|cancel_tech)=/.test(location.search) && window.history && history.replaceState){
		history.replaceState(null, '', location.pathname);
	}
}

//aktive Knoepfe der Schalter markieren
function tc_schalter(){
	var tc=$('#tc');
	tc.find('[data-filter-typ]').each(function(){
		$(this).toggleClass('ally-reiter-aktiv', $(this).attr('data-filter-typ')==tc.attr('data-typ'));
	});
	tc.find('[data-filter-status]').each(function(){
		$(this).toggleClass('tc-an', $(this).attr('data-filter-status')==tc.attr('data-status'));
	});
	tc.find('[data-ansicht]').each(function(){
		$(this).toggleClass('tc-an', ($(this).attr('data-ansicht')=='1')==tc.hasClass('tc-liste'));
	});
	tc.find('[data-ton]').each(function(){
		$(this).toggleClass('tc-an', ($(this).attr('data-ton')=='0')==(tc.attr('data-ton')=='1'));
	});
}

//Kacheln nach Typ und Status ein- und ausblenden, leere Stufen ausblenden, Anzahlen an den Statusknoepfen
function tc_filter(){
	var tc=$('#tc');
	var typ=tc.attr('data-typ'), status=tc.attr('data-status');
	var anzahl={baubar: 0, offen: 0, erledigt: 0, alle: 0};

	tc.find('.tc-tech').each(function(){
		var t=$(this), st=t.attr('data-status');
		if(typ!='-1' && t.attr('data-typ')!=typ){
			t.addClass('tc-aus');
			return;
		}
		anzahl.alle++;
		if(st=='baubar'){
			anzahl.baubar++;
		}
		if(st=='erledigt'){
			anzahl.erledigt++;
		}else{
			anzahl.offen++;
		}
		var zeigen=status=='alle' || (status=='offen' && st!='erledigt') || status==st;
		t.toggleClass('tc-aus', !zeigen);
	});

	tc.find('.tc-stufe').each(function(){
		$(this).toggleClass('tc-aus', $(this).find('.tc-tech:not(.tc-aus)').length==0);
	});
	tc.find('.tc-leer').prop('hidden', tc.find('.tc-stufe:not(.tc-aus)').length>0);

	$.each(anzahl, function(k, n){
		tc.find('[data-anzahl="'+k+'"]').text(n);
	});
	tc_schalter();
}

//ein laufender Auftrag ist fertig: Ton, Bauplatz frei, die Technologie erledigt, bei anderen die Voraussetzung erfuellt
function tc_fertig(tech_id, typ){
	var tc=$('#tc');
	if(tc.attr('data-ton')=='1'){
		playSound(1);
	}

	tc.find('[data-platz="'+typ+'"]').attr('data-frei', '1')
		.find('.tc-platz-inhalt').html('<span class="tc-frei">frei</span> <span class="mod-chip mod-chip-gruen">fertig</span>');

	tc_setze(tc.find('[data-tech-id="'+tech_id+'"]'), 'erledigt');

	tc.find('[data-vor-id="'+tech_id+'"]').remove();
	tc.find('.tc-tech').each(function(){
		var t=$(this);
		var fehlt=$.grep((t.attr('data-fehlt') || '').split(','), function(id){
			return id!='' && id!=String(tech_id);
		});
		t.attr('data-fehlt', fehlt.join(','));
		if(t.find('.tc-vor .tc-fehlt').length==0){
			t.find('.tc-vor').remove();
		}
		tc_neu(t);
	});

	tc_filter();
}

//Status einer offenen Kachel neu bestimmen, in derselben Reihenfolge wie ang_techs.php
function tc_neu(t){
	var st=t.attr('data-status');
	if(st=='erledigt' || st=='laeuft'){
		return;
	}
	if(t.attr('data-fehlt')!='' || t.attr('data-sonder')=='1'){
		st='vor';
	}else if(t.attr('data-res')!='1'){
		st='res';
	}else if($('#tc [data-platz="'+t.attr('data-typ')+'"]').attr('data-frei')!='1'){
		st='platz';
	}else{
		st='baubar';
	}
	tc_setze(t, st);
}

function tc_setze(t, st){
	if(t.length==0){
		return;
	}
	var texte=$('#tc').data('texte');
	t.attr('data-status', st)
		.removeClass('tc-st-baubar tc-st-res tc-st-platz tc-st-vor tc-st-laeuft tc-st-erledigt')
		.addClass('tc-st-'+st);
	t.find('.tc-chip').text(texte[st])
		.toggleClass('mod-chip-gruen', st=='baubar')
		.toggleClass('mod-chip-warn', st=='res');

	if(st=='erledigt'){
		t.find('.tc-kosten, .tc-vor').remove();
	}

	//Starten bei erfuellten Voraussetzungen, ob Rohstoffe und Bauplatz reichen, prueft der Start
	if(st=='baubar' || st=='res' || st=='platz'){
		if(t.find('.tc-fuss').length==0){
			t.find('.tc-unten').append('<div class="tc-fuss"><a href="ang_techs.php?start_tech='+t.attr('data-tech-id')+'" class="mod-btn ally-btn-klein">Starten</a></div>');
		}
		t.find('.tc-fuss .mod-btn').toggleClass('mod-btn-leise', st!='baubar');
	}else{
		t.find('.tc-fuss').remove();
	}
}
