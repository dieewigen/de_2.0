/*
 * logview.js - Log View functional for Die-Ewigen Support Tool
 *
 * This Script will using jQuery Framework, please read the Licence Information too
 *
 * Copyright (c) 2008 Rainer Zerbe <Corwin@die-ewigen.com>
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * $Version: 2008.02.12 v0.1
 *
 */


    $(document).ready(function()
    {
         LOG.SITES.sort();
         $.each(LOG.SITES, function(i,item)
         {
         	$('<input type="Checkbox" onchange="" id="hide_'+item+'"/>/'+item+'.php<br>').appendTo('#hide');
         });
         $('#sidebar').slideDown('fast');
         $('#timefrom').attachDatepicker({ dateFormat: 'yy-mm-dd 01:00:00',yearRange: '2007:2010' });
    });


var LOG = new function()
{
    this.SITES 		= [	'ally_ablehnen','ally_annehmen','ally_antrag','ally_partner','ally_register','ally_register2','ally_scan',
				'ally_austritt','ally_bar','ally_coleader','ally_delete','ally_detail','ally_finance','ally_fleet',
                                 'ally_forum','ally_forumvt','ally_history','ally_join','ally_kick','ally_leader','ally_members',
                                 'ally_message','ally_message2','ally_message_leader','ally_search','ally_settings','ally_war','allymain',
                                 'archeology','artefacts','aufgabengenerator','bkmenu','botcheck','bounty','buildings','chat','defense',
                                 'details','dezindex','efta_chat','efta_empty','efta_menu','efta_topban','eftaindex','eftamain',
                                 'eftastart','empty','geloescht','help','hfnlegende','hyperfunk','imagegenerator','index','menu',
                                 'military','militarybs','newklicken','newspaper','options','overview','pass_act','politics','production',
                                 'radio','research','resource','secforum','secforumvt','secret','secstatus','sector','showpic','sinfo',
                                 'skforum','skforumvt','sklegende','sou_chat','sou_index','sou_main','sou_start','sou_topban','sstat',
                                 'statistics','statistics_genpic','sysnews','tauktion','techtree','toplist','trade','urlaub','userdetails',
                                 'vote'];
    this.lastitem	= {};

    this.getLog          = function(go)
    {
    		$('#working').show();
                 $('#sidebar').slideUp('fast')
                 var get = {};
                 $.each( $('#hide > input'), function(i,item)
                 {
                 	if( $('#'+item.id).attr('checked') ) get[i] = '/'+item.id.replace(/hide_/, "")+'.php';
                 });

		$.ajax({type: 		"POST",
	        	url: 		"getLog.php?log="+$('#uid').val()+"&timefrom="+$('#timefrom').val()+"&range="+$('#range').val()+"&lines="+$('#lines').val()+"&go="+go,
	        	dataType: 	"json" ,
	        	cache:		false,
                         data: 		get ,
                 	success: 	function(data, textStatus) { LOG.parseLog(data); }
	        });
    }
    this.parseLog	= function (d)
    {

    	$('#info').html(d.info.ds+' | '+d.info.from);
         $('#timefrom').val(d.info.from );
	$('#data').html('');
	if(d.data != null )
         {

         	$('<table style="table-layout:fixed; empty-cells:show; border-collapse:separate;"/>').appendTo('#data');
                 $('<tr style="display:block;border-bottom:1px solid;"><td class="Zeit">Zeit</td><td class=IP>IP</td><td class="Datei">Datei</td><td class="Get">Get</td><td class="Post">Post</td></tr>').appendTo('#data > table');
    		$.each(d.data, function(i,item)
         	{
         	        if(item.TS > 0 )
         	        {
         	        	var b = item.Datei.replace(/\//, "").replace(/.php/, "");

				var color = {};
                                 if(item.TS-LOG.lastitem.TS > 60*3)    color.date = '#00FF00';
                                 if(item.TS-LOG.lastitem.TS > 60*12)   color.date = '#009F00';
                                 if(item.TS-LOG.lastitem.TS > 60*60)   color.date = '#E0FF00';
                                 if(item.TS-LOG.lastitem.TS > 60*60*8) color.date = '#FF1F00';
                                 if(item.IP != LOG.lastitem.IP )	      color.ip   = '#FF1F00';

         	        	if($('#hide_'+b).attr('checked') ) 	LOG.insertData('data',i,item,color);
         	        	LOG.lastitem = item;
         	        }
         	});

         	LOG.Redraw();
         }
         $('#working').hide('fast');
    };
    this.insertData	= function(typ,i,item,color)
    {
         switch (typ) {
	  case 'data':
         	$('<tr id="log'+i+'" style="display:block; border-bottom:1px dotted;"/>')		.appendTo('#data > table');
           	$('<td class="Zeit" style="color:'+color.date+';"/>')	.html(item.Zeit)		.appendTo('#log'+i);
           	$('<td class="IP" style="color:'+color.ip+';"/>')	.html(item.IP)			.appendTo('#log'+i);
           	$('<td class="Datei"/>')				.html(item.Datei).attr('name',item.Datei).appendTo('#log'+i);
           	$('<td class="Get"/>')					.html( LOG.showObj(item.Get) )	.appendTo('#log'+i);
           	$('<td class="Post"/>')					.html( LOG.showObj(item.Post) )	.appendTo('#log'+i);
                 break;
	  case 'over1h':
         	$('<tr id="over1h'+i+'" style="display:block;"/>').appendTo('#data > table');
                 $('<td colspan="5"/>')	.html('1h')		.appendTo('#over1h'+i);
                 break;
	  case 'ipChange':
         	$('<tr id="ipChange'+i+'" style="display:block;"/>').appendTo('#data > table');
                 $('<td colspan="5"/>')	.html('ipChange')		.appendTo('#over1h'+i);
                 break;
         }
    };
    this.showObj        = function (d)
    {
         var out = '';
         var clickme='';
         var z = 0;
    	$.each(d, function(i,item)
       	{
//         	 out = out + i + ' => ' + decodeURI(item).replace(/\+/g, " ") + '<br>';
         	 out = out + i + ' => ' + item + '<br>';
                  z++;
       	});
         if(z >= 2)
         	return 	'<span onclick="var tmp = $(this).next(); tmp.slideToggle(\'slow\') ;">ansehen...  ---></span>'
                       + '<div style="display:none" onclick="$(this).slideToggle(\'slow\');">' +out+ '</div>';
         else 	return  out;
    }
    this.Redraw        = function ()
    {
    	$('#working').show();
    	$('#data').hide();

         $.each(LOG.SITES, function(a,b)
         {
         	if( !$('#hide_'+b).attr('checked') )
            		$.each( $('#data > table tr [name="/'+b+'.php"] ') , function (i,item) { $(item.parentNode).empty(); });
         });

         $('#data').show('fast');
         $('#working').hide();

    }
    this.ShowHide	=function(todo,ary)
    {
    	$('#working').show();
         if(typeof ary == 'undefined' ) $.each(LOG.SITES,function(a,b){$('#hide_'+b).attr('checked',todo)});
    	else 			       $.each(ary      ,function(a,b){$('#hide_'+b).attr('checked',todo)});
         LOG.Redraw();
    }
};