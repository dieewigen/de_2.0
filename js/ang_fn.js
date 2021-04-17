var iframe_big_content_filename;

function ang_countdown(seconds, target_id, playsound) {
  // current timestamp.
  var now = new Date().getTime();
  // target timestamp; we will compute the remaining time
  // relative to this date.
  var target = new Date(now + seconds * 1000);
  // update frequency; note, this is flexible, and when the tab is
  // inactive, there are no guarantees that the countdown will update
  // at this frequency.
  var update = 1000;
  
  var int = setInterval(function () {
    // current timestamp
    var now = new Date();
    // remaining time, in seconds
    var remaining = (target - now) / 1000;
    
    // if done, alert
    if (remaining < 0) {
	  playSound(playsound);	
	  clearInterval(int);
	  document.getElementById(target_id).innerHTML="00:00";
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

	document.getElementById(target_id).innerHTML = days+hours+minutes+seconds;

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

function show_tech_typ(typ){
	
	if(typ==-1){
		$(".tech").css("display", "");
		$(".tech_einspaltig").css("display", "");
	}else{
		$(".tech").css("display", "none");
		$(".tech_einspaltig").css("display", "none");

		$(".tech_typ_"+typ).css("display", "");
	}
	setCookie('tech_filter_typ', typ);
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
	$(document).keydown(function(e) {
		if(e.which == 37 || e.which == 65) {
			var href = $('#link_lower').attr('href');
			if(typeof href !== "undefined" && href!='' && href!='undefined'){
				window.location.href = href;
			}
			
		}else if(e.which == 39 || e.which == 68) {
			var href = $('#link_higher').attr('href');
			if(typeof href !== "undefined" && href!='' && href!='undefined'){
				window.location.href = href;
			}

		}else if(e.which == 38 || e.which == 87) {
			var href = $('#link_map').attr('href');
			if(typeof href !== "undefined" && href!='' && href!='undefined'){
				window.location.href = href;
			}

		}else if(e.which == 32) {
			$("#upgrade_all").click();
		}

		console.log(e.which);

	});

}

function reset_map(){

	setCookie('map_zoomfactor', '');
	setCookie('map_newleft', '');
	setCookie('map_newtop',  '');
	setCookie('map_neworigin', '');

	document.getElementById('iframe_map').contentDocument.location.reload(true);
}

function switch_iframe_main_container_big(file){
	/*
	if($('#iframe_main_container_big').css('display')=='none' || iframe_big_content_filename!=file){
		$('#iframe_main_container_big').html('<iframe src="'+file+'" id="iframe_main_big" name="iframe_main_big" scrolling="no" height="100%" width="100%" frameBorder="0"></iframe>');
		$('#iframe_main_container_big').css('display','');
		$('#iframe_main_container').css('display','none');
	}else{
		$('#iframe_main_container').css('display','none');
		$('#iframe_main_container_big').css('display','none');
	}*/
	$('#iframe_main_container_big').html('<iframe src="'+file+'" id="iframe_main_big" name="iframe_main_big" scrolling="no" height="100%" width="100%" frameBorder="0"></iframe>');
	$('#iframe_main_container_big').css('display','');
	$('#iframe_main_container').css('display','none');
	$('#iframe_main_container_closer').css('display','none');
	iframe_big_content_filename=file;
}

function switch_iframe_main_container(file){
	/*
	if($('#iframe_main_container').css('display')=='none' || iframe_big_content_filename!=file){
		$('#iframe_main_container').html('<iframe src="'+file+'" id="iframe_main" name="iframe_main" height="100%" width="100%" frameBorder="0"></iframe>');
		$('#iframe_main_container').css('display','');
		$('#iframe_main_container_big').css('display','none');
	}else{
		$('#iframe_main_container').css('display','none');
		$('#iframe_main_container_big').css('display','none');
	}*/
	
	if($('#iframe_main_container').length>0){
		$('#iframe_main_container').html('<iframe src="'+file+'" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe>');
		$('#iframe_main_container').css('display','');
		$('#iframe_main_container_closer').css('display','');

		$('#iframe_main_container_big').css('display','none');
	}else{
		$('#iframe_main_container', parent.document).html('<iframe src="'+file+'" id="iframe_main" name="h" height="100%" width="100%" frameBorder="0"></iframe>');
		$('#iframe_main_container', parent.document).css('display','');
		$('#iframe_main_container_big', parent.document).css('display','none');		
	}
	
	
	iframe_big_content_filename=file;
	
}

function closeIframeMain(){
	$("#iframe_main_container", parent.document).css("display", "none");
	$('#iframe_main_container_closer').css('display','none');

	$("#iframe_main_container_big", parent.document).css("display", "none");
}
