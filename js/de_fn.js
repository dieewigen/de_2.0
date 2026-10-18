$( document ).ready(function() {
  setTooltip();
});

function setTooltip(){
    var targets = $('[title]');

    targets.unbind('mouseenter mouseleave touchstart');
    targets.bind('mouseenter', function()
    {
        // target, tooltip und tip gehören zu diesem einen Element. Früher teilten alle Handler dieselben Variablen: Bei
        // title-Elementen in title-Elementen (etwa die Ausrüstungs-Buchstaben im Battleground-Bericht) stellte das
        // Verlassen des inneren den title des äußeren wieder her, der innere ging verloren, und der Browser zeigte den
        // rohen title samt <br> an.
        var target  = $(this),
            titel   = target.attr('title'),
            tip     = titel ? titel.replace('&', '<br>') : titel,
            tooltip;

        // Entferne vorhandene Tooltips
        $('#tt').remove();

        tooltip = $('<div id="tt"></div>');

        if(!tip || tip == '')
            return false;

        target.removeAttr('title');
        target.data('original-title', titel); // Titel unverändert sichern, remove_tooltip stellt ihn wieder her

        tooltip.css('opacity', 0)
               .html(tip)
               .appendTo('body');
 
        var init_tooltip = function()
        {
            if($(window).width() < tooltip.outerWidth() * 1.5)
                tooltip.css('max-width', $(window).width() / 2);
            else
                tooltip.css('max-width', 340);
 
            var pos_left = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                pos_top  = target.offset().top - tooltip.outerHeight() - 20;
 
            if(pos_left < 0)
            {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass('left');
            }
            else
                tooltip.removeClass('left');
 
            if(pos_left + tooltip.outerWidth() > $(window).width())
            {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass('right');
            }
            else
                tooltip.removeClass('right');
 
            if(pos_top < 0)
            {
                pos_top = target.offset().top + target.outerHeight();
                tooltip.addClass('top');
            }
            else
                tooltip.removeClass('top');
 
            tooltip.css({left: pos_left, top: pos_top})
                   .animate({top: '+=10', opacity: 1}, 50);
        };
 
        init_tooltip();
        $(window).resize(init_tooltip);
 
        var remove_tooltip = function()
        {
            // Window resize unbinden
            $(window).unbind('resize', init_tooltip);
            
            tooltip.animate({top: '-=10', opacity: 0}, 50, function()
            {
                $(this).remove();
            });
 
            // Titel aus data-Attribut wiederherstellen
            var originalTitle = target.data('original-title');
            if(originalTitle) {
                target.attr('title', originalTitle);
            }
            
            // Event-Listener entfernen
            target.unbind('mouseleave', remove_tooltip);
            tooltip.unbind('click touchstart', remove_tooltip);
        };
 
        target.bind('mouseleave', remove_tooltip);
        tooltip.bind('click touchstart', remove_tooltip);
    });
};

// Die Aufräum-Handler nur einmal binden: setTooltip() läuft nach jedem Nachladen (Infocenter, Chat) erneut und hatte
// sie bisher bei jedem Aufruf zusätzlich registriert
// Fallback: alle Tooltips bei Scroll oder Resize entfernen
$(window).on('scroll resize', function() {
    $('#tt').remove();
    $('[data-original-title]').each(function() {
        var $this = $(this);
        $this.attr('title', $this.data('original-title'));
    });
});

// Touch-Geräte: Tooltip bei Touch außerhalb entfernen
$(document).on('touchstart', function(e) {
    if(!$(e.target).closest('#tt').length && !$(e.target).closest('[title], [data-original-title]').length) {
        $('#tt').remove();
        $('[data-original-title]').each(function() {
            var $this = $(this);
            $this.attr('title', $this.data('original-title'));
        });
    }
});


function showHiddenInfo(id){
  $('#hidden_info'+id).css('display', 'block');
  $('#hidden_show'+id).css('display', 'none');
}

// Zwei-Klick-Bestätigung für Links mit data-bestaetigen="Text" (z. B. Auktionen, Artefakte) statt confirm():
// der erste Klick zeigt den Text und färbt den Knopf um, der zweite Klick (auch als Doppelklick) führt den Link aus,
// nach 4 Sekunden ohne zweiten Klick geht der Knopf zurück
(function(){
  var bereit = null, text = '', uhr = null;
  function zurueck(){
    if(bereit){
      bereit.classList.remove('mod-btn-bereit');
      bereit.innerHTML = text;
      bereit.style.minWidth = '';
      bereit.style.minHeight = '';
      bereit = null;
    }
  }
  document.addEventListener('click', function(e){
    var b = e.target.closest ? e.target.closest('[data-bestaetigen]') : null;
    if(!b){
      return;
    }
    if(bereit === b){
      b.classList.add('mod-btn-laeuft');
      return;
    }
    e.preventDefault();
    zurueck();
    bereit = b;
    text = b.innerHTML;
    // Größe festhalten: der kürzere Bestätigungstext darf den Knopf nicht schrumpfen lassen, sonst rutscht er (in
    // umbrechenden Reihen sogar in eine andere Zeile) und der zweite Klick geht daneben
    b.style.minWidth = b.offsetWidth + 'px';
    b.style.minHeight = b.offsetHeight + 'px';
    b.classList.add('mod-btn-bereit');
    b.textContent = b.getAttribute('data-bestaetigen');
    clearTimeout(uhr);
    uhr = setTimeout(zurueck, 4000);
  });
})();

// Esc in der Desktop-Ansicht (dm.php): die Spielseiten laufen in iframes, darum reichen sie die Taste an dm_esc() der
// Hauptseite weiter, die damit die Spielspalte bzw. das große Fenster schließt; in Eingabefeldern bleibt Esc dem Feld
document.addEventListener('keydown', function(e){
  if(e.key !== 'Escape' || e.defaultPrevented){
    return;
  }
  var feld = document.activeElement;
  if(feld && (feld.tagName === 'TEXTAREA' || feld.tagName === 'SELECT' || feld.isContentEditable ||
      (feld.tagName === 'INPUT' && !/^(button|submit|reset|checkbox|radio|image)$/i.test(feld.type)))){
    return;
  }
  var haupt = (window.parent !== window) ? window.parent : window;
  try{
    if(typeof haupt.dm_esc === 'function'){
      haupt.dm_esc();
    }
  }catch(err){}
});

// "Als Text kopieren" (Kampfbericht): data-kopieren nennt die ID eines versteckten Textfelds, dessen Inhalt in die
// Zwischenablage geht; ohne Clipboard-API wird das Feld kurz eingeblendet und markiert kopiert
document.addEventListener('click', function(e){
  var b = e.target.closest ? e.target.closest('[data-kopieren]') : null;
  if(!b){
    return;
  }
  var quelle = document.getElementById(b.getAttribute('data-kopieren'));
  if(!quelle){
    return;
  }
  var fertig = function(){
    var alt = b.textContent;
    b.textContent = 'Kopiert';
    b.classList.add('mod-btn-bereit');
    setTimeout(function(){
      b.textContent = alt;
      b.classList.remove('mod-btn-bereit');
    }, 2000);
  };
  var ersatz = function(){
    quelle.hidden = false;
    quelle.select();
    try{
      document.execCommand('copy');
    }catch(err){}
    quelle.hidden = true;
    fertig();
  };
  if(navigator.clipboard && window.isSecureContext){
    navigator.clipboard.writeText(quelle.value).then(fertig, ersatz);
  }else{
    ersatz();
  }
});

// "WhatsApp" (Kampfbericht): data-teilen nennt dasselbe Textfeld; der offizielle Teilen-Link öffnet am Handy die App,
// am PC WhatsApp Web, bei blockiertem Popup im ganzen Fenster (wie deirc() in secstatus.php)
document.addEventListener('click', function(e){
  var b = e.target.closest ? e.target.closest('[data-teilen]') : null;
  if(!b){
    return;
  }
  var quelle = document.getElementById(b.getAttribute('data-teilen'));
  if(!quelle){
    return;
  }
  var url = 'https://wa.me/?text=' + encodeURIComponent(quelle.value);
  var wa = window.open(url, '_blank');
  if(wa){
    wa.opener = null;
  }else{
    window.top.location.href = url;
  }
});

// "Kampf abspielen" (Kampfbericht): data-abspielen nennt die ID des Ablauf-Kastens aus src/View/Kampfbericht.php. Sein
// data-schritte enthält die Schüsse des Kampftick als [Phase, Seite, Schütze, Ziel, Prozent] (Phase 1 EMP-Waffen lähmen,
// Phase 2 konventionelle Waffen zerstören; Seite a Angreifer, v Verteidiger; Klassen 0-9 Schiffe, 10-14 Türme). Jeder
// Schritt hebt Schütze und Ziel hervor und füllt beim Ziel den gelähmten bzw. zerstörten Anteil seines Balkens. Der Ablauf
// läuft von selbst (nicht bei reduzierter Bewegung), "Pause" und "Schritt" steuern ihn, am Ende geht es von vorn.
(function(){
  var SEITE = {a: 'Angreifer', v: 'Verteidiger'};
  var PHASE = {
    1: 'Phase 1, EMP-Waffen: Jede Klasse lähmt gegnerische Klassen in fester Reihenfolge, mit jedem weiteren Ziel schwächer. Gelähmte Einheiten feuern in Phase 2 nicht.',
    2: 'Phase 2, konventionelle Waffen: Alle nicht gelähmten Einheiten feuern gleichzeitig, jede Klasse in ihrer Reihenfolge auf die Trefferpunkte der gegnerischen Klassen.'
  };
  var reduziert = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function ablauf(kasten){
    if(kasten.kbAblauf){
      return kasten.kbAblauf;
    }
    var schuesse = JSON.parse(kasten.getAttribute('data-schritte') || '[]');
    var schritte = [];
    [1, 2].forEach(function(p){
      schritte.push({phase: p});
      schuesse.forEach(function(s){
        if(s[0] === p){
          schritte.push({schuss: s});
        }
      });
    });
    schritte.push({ende: true});
    var text = kasten.querySelector('.kb-ab-text');
    var zaehler = kasten.querySelector('.kb-ab-zaehler');
    var knopf = kasten.querySelector('[data-ab="lauf"]');
    var zeilen = kasten.querySelectorAll('.kb-ab-zeile');
    var stand = 0, uhr = null, laeuft = false;
    var gegner = function(seite){
      return seite === 'a' ? 'v' : 'a';
    };
    var zeile = function(seite, klasse){
      return kasten.querySelector('.kb-ab-' + seite + ' [data-k="' + klasse + '"]');
    };
    var name = function(z){
      return '<b>' + (z ? z.querySelector('.kb-ab-name').firstChild.textContent : '?') + '</b>';
    };
    // Zustand nach Schritt n: Balken aus allen Schüssen bis dahin, Hervorhebung und Text des Schritts
    var zeigen = function(n){
      stand = n;
      var summe = {};
      for(var i = 0; i <= n; i++){
        var t = schritte[i].schuss;
        if(t){
          var k = gegner(t[1]) + t[3];
          summe[k] = summe[k] || [0, 0];
          summe[k][t[0] - 1] = Math.min(100, summe[k][t[0] - 1] + t[4]);
        }
      }
      Array.prototype.forEach.call(zeilen, function(z){
        var w = summe[(z.parentNode.classList.contains('kb-ab-v') ? 'v' : 'a') + z.getAttribute('data-k')] || [0, 0];
        z.querySelector('.kb-ab-gelaehmt').style.width = w[0] + '%';
        z.querySelector('.kb-ab-zerstoert').style.width = w[1] + '%';
        z.classList.remove('kb-ab-schuetze', 'kb-ab-ziel');
      });
      var s = schritte[n].schuss;
      if(s){
        var von = zeile(s[1], s[2]), auf = zeile(gegner(s[1]), s[3]);
        if(von){
          von.classList.add('kb-ab-schuetze');
        }
        if(auf){
          auf.classList.add('kb-ab-ziel');
        }
        text.innerHTML = name(von) + ' (' + SEITE[s[1]] + ') ' + (s[0] === 1 ? 'lähmt' : 'zerstört') + ' <b>' +
          String(s[4]).replace('.', ',') + ' %</b> der ' + name(auf) + ' (' + SEITE[gegner(s[1])] + ').';
      }else if(schritte[n].phase){
        text.textContent = PHASE[schritte[n].phase] + (schritte[n].phase === 1 && kasten.getAttribute('data-hinweis') ? ' ' + kasten.getAttribute('data-hinweis') : '');
      }else{
        text.textContent = 'Ergebnis: ' + kasten.getAttribute('data-ergebnis');
      }
      zaehler.textContent = (n + 1) + ' / ' + schritte.length;
      knopf.textContent = laeuft ? 'Pause' : (n >= schritte.length - 1 ? 'Von vorn' : 'Abspielen');
    };
    var anhalten = function(){
      laeuft = false;
      clearTimeout(uhr);
      knopf.textContent = stand >= schritte.length - 1 ? 'Von vorn' : 'Abspielen';
    };
    var weiter = function(){
      if(stand >= schritte.length - 1){
        anhalten();
        return;
      }
      zeigen(stand + 1);
      uhr = setTimeout(weiter, schritte[stand].schuss ? 900 : 2000);
    };
    var starten = function(){
      laeuft = true;
      if(stand >= schritte.length - 1){
        zeigen(0);
      }
      knopf.textContent = 'Pause';
      clearTimeout(uhr);
      uhr = setTimeout(weiter, schritte[stand].schuss ? 900 : 2000);
    };
    kasten.addEventListener('click', function(e){
      var b = e.target.closest ? e.target.closest('[data-ab]') : null;
      if(!b){
        return;
      }
      if(b.getAttribute('data-ab') === 'schritt'){
        anhalten();
        zeigen(stand >= schritte.length - 1 ? 0 : stand + 1);
      }else if(laeuft){
        anhalten();
      }else{
        starten();
      }
    });
    kasten.kbAblauf = {zeigen: zeigen, starten: starten, anhalten: anhalten};
    return kasten.kbAblauf;
  }

  document.addEventListener('click', function(e){
    var b = e.target.closest ? e.target.closest('[data-abspielen]') : null;
    if(!b){
      return;
    }
    var kasten = document.getElementById(b.getAttribute('data-abspielen'));
    if(!kasten){
      return;
    }
    var a = ablauf(kasten);
    if(kasten.hidden){
      kasten.hidden = false;
      b.textContent = 'Ablauf schließen';
      a.zeigen(0);
      if(!reduziert){
        a.starten();
      }
      if(kasten.scrollIntoView){
        kasten.scrollIntoView({block: 'nearest', behavior: reduziert ? 'auto' : 'smooth'});
      }
    }else{
      a.anhalten();
      kasten.hidden = true;
      b.textContent = 'Kampf abspielen';
    }
  });
})();
