var iframe_big_content_filename;

function ang_countdown(seconds, target_id, playsound, on_finish) {
  // current timestamp.
  var now = new Date().getTime();
  // target timestamp; we will compute the remaining time
  // relative to this date.
  var target = new Date(now + seconds * 1000);
  // update frequency; note, this is flexible, and when the tab is
  // inactive, there are no guarantees that the countdown will update
  // at this frequency.
  var update = 1000;

  // Zielelement merken: wird es ersetzt (z.B. beim Nachladen in den Vergessenen Systemen),
  // endet dieser Countdown, statt ein neues Element mit gleicher ID zu ueberschreiben
  var el = document.getElementById(target_id);

  var int = setInterval(function () {
    if (el && !el.isConnected) {
      clearInterval(int);
      return;
    }
    var target_el = el || document.getElementById(target_id);

    // current timestamp
    var now = new Date();
    // remaining time, in seconds
    var remaining = (target - now) / 1000;

    // if done, alert
    if (remaining < 0) {
	  playSound(playsound);
	  clearInterval(int);
	  target_el.innerHTML="00:00";
	  if (typeof on_finish === 'function') {
		on_finish();
	  }
      return;
    }


	var days=Math.floor(remaining / 60 / 60 / 24);
	var hours=Math.floor(remaining / 60 / 60) % 24;
	var minutes=Math.floor(remaining / 60) % 60;
	var seconds=Math.ceil(remaining % 60);

	if(days==0){
		days='';
	}else{
		days=days+':';
	}

	if(hours==0 && days==0){
		hours='';
	}else{
		if(hours<10){
			hours='0'+hours;
		}
		hours=hours+':';
	}

	if(minutes<10){
		minutes='0'+minutes;
	}
	minutes=minutes+':';

	if(seconds<10){
		seconds='0'+seconds;
	}

	target_el.innerHTML = days+hours+minutes+seconds;

  }, update);
}

function format(num) {
  return num < 10 ? "0" + num : num;
}

function onchange_select(target_id){
	setCookie(target_id, $("#"+target_id).val());
}

function setCookie(name, value){
	var date = new Date();
	date.setTime(date.getTime()+(5*365*24*60*60*1000));
	document.cookie=name+"="+value+";path=/;expires="+date.toGMTString();
}

function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
	  var c = ca[i];
	  while (c.charAt(0) == ' ') {
		c = c.substring(1);
	  }
	  if (c.indexOf(name) == 0) {
		return c.substring(name.length, c.length);
	  }
	}
	return "";
  }

function playSound(sound_id){
	if(sound_id>0){
		var audio = new Audio('sound/sound'+sound_id+'.mp3');
		audio.play();		
	}
}

function vs_filter_init(){
	var vsf0a=getCookie("vsf0a");
	var vsf0b=getCookie("vsf0b");
	var vsf0c=getCookie("vsf0c");
	var start_filter=false;

	if(vsf0a!=""){
		$('#vsf0a').val(vsf0a);
		start_filter=true;
	}

	if(vsf0b!=""){
		$('#vsf0b').val(vsf0b);
		start_filter=true;
	}

	if(vsf0c!=""){
		$('#vsf0c').val(vsf0c);
		start_filter=true;
	}	

	if(start_filter){
		console.log();
		vs_filter(1);
	}
}

function vs_filter(status){
	if(status==0){
		$('#vsf0a, #vsf0b, #vsf0c').prop('selectedIndex',0);
		$('.f_system').show();

		setCookie("vsf0a", "");
		setCookie("vsf0b", "");
		setCookie("vsf0c", "");		

	}else{
		$('.f_system').hide();

		if($('#vsf0a').val()=='f_unsy'){
			$('.f_unsy').show();
		}else{
			if($('#vsf0b').val()=='gg'){
				for(var s=$('#vsf0c').val(); s<=10; s++){
					$('.'+$('#vsf0a').val()+'_'+s).show();
				}
			}else if($('#vsf0b').val()=='kg'){
				for(var s=$('#vsf0c').val(); s>=0; s--){
					$('.'+$('#vsf0a').val()+'_'+s).show();
				}
			}else if($('#vsf0b').val()=='g'){
				$('.'+$('#vsf0a').val()+'_'+$('#vsf0c').val()).show();
			}
		}

		setCookie("vsf0a", $('#vsf0a').val());
		setCookie("vsf0b", $('#vsf0b').val());
		setCookie("vsf0c", $('#vsf0c').val());
	}
}

function vs_system_init(){
	//nur einmal binden, auch wenn die Seite nachgeladen wird
	$(document).off('keydown.vs').on('keydown.vs', function(e) {
		//nicht reagieren, waehrend in einem Eingabefeld getippt wird oder mit Strg/Alt/Cmd
		if($(e.target).is('input, textarea, select') || e.ctrlKey || e.altKey || e.metaKey){
			return;
		}

		if(e.which == 37 || e.which == 65) {
			var href = $('#link_lower').attr('href');
			if(typeof href !== "undefined" && href!='' && href!='undefined'){
				vs_navigate(href);
			}

		}else if(e.which == 39 || e.which == 68) {
			var href = $('#link_higher').attr('href');
			if(typeof href !== "undefined" && href!='' && href!='undefined'){
				vs_navigate(href);
			}

		}else if(e.which == 38 || e.which == 87) {
			var href = $('#link_map').attr('href');
			if(typeof href !== "undefined" && href!='' && href!='undefined'){
				vs_navigate(href);
			}

		}else if(e.which == 32) {
			$("#upgrade_all").click();
		}
	});

}

//////////////////////////////////////////////////////////////////////////////
// Vergessene Systeme: Links und Formulare in #vs-main ohne Seitenwechsel laden.
// Ohne JS bzw. bei Fehlern wird ganz normal navigiert.
//////////////////////////////////////////////////////////////////////////////
var vs_ajax_busy=false;

//zeigt die URL auf map_system.php (gleicher Server)?
function vs_is_vs_url(url){
	try{
		var u=new URL(url, location.href);
		return u.origin==location.origin && /\/map_system\.php$/.test(u.pathname);
	}catch(e){
		return false;
	}
}

//fuer die Adresszeile nur id/fieldid behalten, damit ein Neuladen keine Aktion wiederholt
function vs_clean_url(url){
	var u=new URL(url, location.href);
	var clean=new URLSearchParams();
	['id', 'fieldid'].forEach(function(k){
		if(u.searchParams.has(k)){
			clean.set(k, u.searchParams.get(k));
		}
	});
	var q=clean.toString();
	return u.pathname+(q!='' ? '?'+q : '');
}

function vs_ajax_init(){
	if(window.vs_ajax_active || !window.fetch || !window.history.pushState || !document.getElementById('vs-main')){
		return;
	}
	window.vs_ajax_active=true;

	$(document).on('click', '#vs-main a[href]', function(e){
		var href=$(this).attr('href');
		if(!vs_is_vs_url(href) || $(this).attr('target') || e.ctrlKey || e.metaKey || e.shiftKey){
			return;
		}
		e.preventDefault();
		vs_load(href, 'GET', null, false);
	});

	//serialize() kennt den geklickten Absende-Knopf nicht, manche Formulare werten aber dessen Namen aus
	$(document).on('click', '#vs-main form [type=submit][name]', function(){
		$(this.form).data('vs-submitter', this);
	});

	$(document).on('submit', '#vs-main form', function(e){
		var action=$(this).attr('action') || location.href;
		if(!vs_is_vs_url(action)){
			return;
		}
		e.preventDefault();

		var body=$(this).serialize();
		var submitter=$(this).data('vs-submitter');
		$(this).removeData('vs-submitter');
		if(submitter){
			body+=(body!='' ? '&' : '')+encodeURIComponent(submitter.name)+'='+encodeURIComponent(submitter.value);
		}

		vs_load(action, ($(this).attr('method') || 'GET').toUpperCase(), body, false);
	});

	window.addEventListener('popstate', function(e){
		if(e.state && e.state.vs){
			vs_load(location.href, 'GET', null, true);
		}
	});

	history.replaceState({vs: 1}, '', location.href);
}

function vs_navigate(url){
	if(window.vs_ajax_active && vs_is_vs_url(url)){
		vs_load(url, 'GET', null, false);
	}else{
		location.href=url;
	}
}

function vs_load(url, method, body, from_history){
	if(vs_ajax_busy){
		return;
	}
	vs_ajax_busy=true;
	$('#vs-main').css('opacity', '0.6');

	var options={method: method, credentials: 'same-origin'};
	if(method=='POST'){
		options.headers={'Content-Type': 'application/x-www-form-urlencoded'};
		options.body=body || '';
	}else if(body){
		url+=(url.indexOf('?')>-1 ? '&' : '?')+body;
	}

	fetch(url, options).then(function(response){
		if(!response.ok){
			throw new Error('HTTP '+response.status);
		}
		return response.text().then(function(html){
			var doc=new DOMParser().parseFromString(html, 'text/html');
			var main=doc.getElementById('vs-main');
			if(!main){
				//z.B. abgelaufene Session: ganz normal laden
				location.href=response.url;
				return;
			}

			//jQuery fuehrt dabei die enthaltenen Skripte aus (Countdowns, Kopfzeile)
			$('#vs-main').html(main.innerHTML);

			var resline=doc.getElementById('vs-resline');
			if(resline){
				$('#vs-resline').html(resline.innerHTML);
			}

			if(doc.title!=''){
				document.title=doc.title;
			}

			if(!from_history){
				history.pushState({vs: 1}, '', vs_clean_url(response.url));
			}

			if(typeof setTooltip==='function'){
				setTooltip();
			}
		});
	}).catch(function(){
		location.href=url;
	}).then(function(){
		vs_ajax_busy=false;
		$('#vs-main').css('opacity', '');
	});
}

//VS-Uebersicht: alle Gebaeude eines Systems upgraden und die Zeile aktualisieren
function vs_upgrade_row(id, btn){
	if($(btn).data('busy')){
		return;
	}
	$(btn).data('busy', 1).css('opacity', '0.5');

	$.post('map_system_ajax.php', {action: 'upgradeall', id: id}, null, 'json').done(function(data){
		if(data.html){
			$('#vsrow'+id).replaceWith(data.html);
		}else{
			$(btn).data('busy', 0).css('opacity', '');
		}

		if(data.res){
			for(var r=1; r<=5; r++){
				$('#restyp'+r).html(data.res[r-1].full);
				$('#tb_res'+r, parent.document).html(data.res[r-1].short).attr('title', data.res[r-1].full);
			}
		}

		var msg=$('<span style="margin-left: 12px;"></span>').addClass(data.ok && data.started>0 ? 'text3' : 'text2').html(data.msg);
		$('#vsrow'+id+' tr:first td:first').append(msg);
		setTimeout(function(){
			msg.fadeOut(400, function(){ msg.remove(); });
		}, 3000);

		if(typeof setTooltip==='function'){
			setTooltip();
		}
	}).fail(function(){
		location.reload();
	});
}

function reset_map(){
	localStorage.removeItem('mapState');
	document.getElementById('iframe_map').contentDocument.location.reload(true);
}

function switch_iframe_main_container_big(file){
	$('#iframe_main_container_big').html('<iframe src="'+file+'" id="iframe_main_big" name="iframe_main_big" scrolling="no" height="100%" width="100%" frameBorder="0"></iframe>');
	$('#iframe_main_container_big').css('display','');
	$('#iframe_main_container').css('display','none');
	$('#iframe_main_container_closer').css('display','none');
	iframe_big_content_filename=file;
}

function switch_iframe_main_container(file){
	if($('#iframe_main_container').length>0){
		$('#iframe_main_container').html('<iframe src="'+file+'" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe>');
		$('#iframe_main_container').css('display','');
		$('#iframe_main_container_closer').css('display','');

		$('#iframe_main_container_big').css('display','none');
	}else{
		$('#iframe_main_container', parent.document).html('<iframe src="'+file+'" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe>');
		$('#iframe_main_container', parent.document).css('display','');
		$('#iframe_main_container_big', parent.document).css('display','none');		
		$('#iframe_main_container_closer', parent.document).css('display','');
	}
	
	
	iframe_big_content_filename=file;
	
}

function closeIframeMain(){
	$("#iframe_main_container", parent.document).css("display", "none");
	$('#iframe_main_container_closer').css('display','none');

	$("#iframe_main_container_big", parent.document).css("display", "none");
}
