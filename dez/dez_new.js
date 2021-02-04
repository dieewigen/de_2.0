var bbtags   = new Array();

// browser detection
var myAgent   = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);
var is_ie   = ((myAgent.indexOf("msie") != -1)  && (myAgent.indexOf("opera") == -1));
var is_win   =  ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));


function getArraySize(theArray) {
 	for (i = 0; i < theArray.length; i++) {
  		if ((theArray[i] == "undefined") || (theArray[i] == "") || (theArray[i] == null)) return i;
	}
 	
 	return theArray.length;
}

function pushArray(theArray, value) {
 	theArraySize = getArraySize(theArray);
 	theArray[theArraySize] = value;
}

function popArray(theArray) {
	theArraySize = getArraySize(theArray);
 	retVal = theArray[theArraySize - 1];
 	delete theArray[theArraySize - 1];
 	return retVal;
}



function bbcode(theForm, theTag, promptText) {
	var donotinsert = false;
	for (i = 0; i < bbtags.length; i++) {
		if (bbtags[i] == theTag) donotinsert = true;
	}
	
	if (!donotinsert) {
		if((theTag == "COLOR") || (theTag == "SIZE")) {
			if(addText("[" + theTag + "=" + promptText + "]", "[/" + theTag + "]", true, theForm)){
				pushArray(bbtags, theTag);
			}
		}
		else {
			if(theTag == "BR") {
				addText("[" + theTag + "]", "", true, theForm)
			}
			else {
				if(addText("[" + theTag + "]", "[/" + theTag + "]", true, theForm)){
					pushArray(bbtags, theTag);
				}
			}
		}
	}
	else {
		var lastindex = 0;
		
		for (i = 0 ; i < bbtags.length; i++ ) {
			if ( bbtags[i] == theTag ) {
				lastindex = i;
			}
		}
		
		while (bbtags[lastindex]) {
			tagRemove = popArray(bbtags);
			addText("[/" + tagRemove + "]", "", false, theForm);
		}
	}
}

function namedlink(theForm,theType) {
	var selected = getSelectedText(theForm);
 
	var linkText = prompt("Geben Sie einen Linknamen ein (optional):",selected);
	var prompttext;
 
	if (theType == "URL") {
 		prompt_text = "Geben Sie die volle Adresse des Links ein:";
 		prompt_contents = "http://";
		mail_code = "";
	}
 
	linkURL = prompt(prompt_text,prompt_contents);
 
 
	if ((linkURL != null) && (linkURL != "")) {
		var theText = '';
		
		if (linkText.length > 40) { linkText = linkText.substr(0,26) + '....' + linkText.substr((linkText.length - 10),10); }
		if ((linkText == null) || (linkText == "")) { linkText = linkURL.substr(0,26) + '....' + linkURL.substr((linkURL.length - 10),10); }

		theText = "[a href="+mail_code+linkURL+"]"+linkText+"[/a]";
  		
  		addText(theText, "", false, theForm);
 	}
}



function addText(theTag, theClsTag, isSingle, theForm)
{
	var isClose = false;
	var a_text = theForm.a_text;
	var set=false;
  	var old=false;
  	var selected="";
  	
  	if(navigator.appName=="Netscape" &&  a_text.textLength>=0 ) { // mozilla, firebird, netscape
  		if(theClsTag!="" && a_text.selectionStart!=a_text.selectionEnd) {
  			selected=a_text.value.substring(a_text.selectionStart,a_text.selectionEnd);
  			str=theTag + selected+ theClsTag;
  			old=true;
  			isClose = true;
  		}
		else {
			str=theTag;
		}
		
		a_text.focus();
		start=a_text.selectionStart;
		end=a_text.textLength;
		endtext=a_text.value.substring(a_text.selectionEnd,end);
		starttext=a_text.value.substring(0,start);
		a_text.value=starttext + str + endtext;
		a_text.selectionStart=start;
		a_text.selectionEnd=start;
		
		a_text.selectionStart = a_text.selectionStart + str.length;
				
		if(old) { return false; }
		
		set=true;
		
		if(isSingle) {
			isClose = false;
		}
	}
	if ( (myVersion >= 4) && is_ie && is_win) {  // Internet Explorer
		if(a_text.isTextEdit) {
			a_text.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null){
				if(theClsTag != "" && rng.text.length > 0)
					theTag += rng.text + theClsTag;
				else if(isSingle)
					isClose = true;
	
				rng.text = theTag;
			}
		}
		else{
			if(isSingle) isClose = true;
	
			if(!set) {
      				a_text.value += theTag;
      			}
		}
	}
	else
	{
		if(isSingle) isClose = true;

		if(!set) {
      			a_text.value += theTag;
      		}
	}

	a_text.focus();
	
	return isClose;
}	


function getSelectedText(theForm) {
	var a_text = theForm.a_text;
	var selected = '';
	
	if(navigator.appName=="Netscape" &&  a_text.textLength>=0 && a_text.selectionStart!=a_text.selectionEnd ) 
  		selected=a_text.value.substring(a_text.selectionStart,a_text.selectionEnd);	
  	
	else if( (myVersion >= 4) && is_ie && is_win ) {
		if(a_text.isTextEdit){ 
			a_text.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			
			if((sel.type == "Text" || sel.type == "None") && rng != null){
				if(rng.text.length > 0) selected = rng.text;
			}
		}	
	}
		 
  	return selected;
}

function setFocus(theForm) {
 	theForm.a_text.focus();
}

