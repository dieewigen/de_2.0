//<script>
function load_infobar()
{
$.getJSON("sou_ajaxrpc.php?loadinfobar1=1",
function(data)
{
  $("#infobar1").html(data[0].output);
  $('#ibcounterbox, #agedesc').tooltip({ 
	    track: true, 
	    delay: 0, 
	    showURL: false, 
	    showBody: "&",
	    extraClass: "design1", 
	    fixPNG: true,
	    opacity: 0.15,
	    left: 0
	});
  hb_counter_c=data[0].zeit;
  hb_counter_targetmain='ibcounterbox';
  hb_counter_targetcounter='ibcounter';
  hb_counter_endtext='';
  hb_counter_sound=1;
});
}

function timeboost()
{
$.getJSON("sou_ajaxrpc.php?timeboost=1",
function(data)
{
  if(data[0].output>0)load_infobar();
});
}