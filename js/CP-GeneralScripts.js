/* 
General Javascript functions
*/


// Opera 8.0+
var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

// Firefox 1.0+
var isFirefox = typeof InstallTrigger !== 'undefined';

// Safari 3.0+ "[object HTMLElementConstructor]" 
var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

// Internet Explorer 6-11
var isIE = /*@cc_on!@*/false || !!document.documentMode;

// Edge 20+
var isEdge = !isIE && !!window.StyleMedia;

// Chrome 1+
var isChrome = !!window.chrome && !!window.chrome.webstore;

// Blink engine detection
var isBlink = (isChrome || isOpera) && !!window.CSS;



function OpenPopUp(File,Parameter1,Value1,Parameter2,Value2,Parameter3,Value3,Parameter4,Value4,WindowTitle,ParamString)
{
	subWindow = open(File+"?"+Parameter1+"="+Value1+"&"+Parameter2+"="+Value2+"&"+Parameter3+"="+Value3+"&"+Parameter4+"="+Value4,WindowTitle, ParamString);
}

var distingSign = '_over';
var offsign = '_off';
var distingSignLen = distingSign.length;
var offsignLen = offsign.length;
var extensionLen = 4;  // lenght of '.gif'

function changeImage(object)
{//alert(object);
	var tmp = object.src.substring((object.src.length-(extensionLen+distingSignLen)), (object.src.length-extensionLen)); 
	var extension = object.src.substring((object.src.length-extensionLen), object.src.length); 
	if (tmp==distingSign)
	{
		object.src = object.src.substring(0,(object.src.length-(extensionLen+distingSignLen)))+offsign+extension;
	}
	else
	{
		object.src = object.src.substring(0,(object.src.length-extensionLen-offsignLen))+distingSign+extension;
	}

} 

function tdate() {
	var now = new Date();
	return now.getTime();
}

function typeOf(value) {
		var s = typeof value;
		if (s === 'object') {
				if (value) {
						if (value instanceof Array) {
								s = 'array';
						}
				} else {
						s = 'null';
				}
		}
		return s;
}

function DateDiff(part,date1,date2) {
	var mils = 0,
			convertedDiff = 0;
	try {
		//Get the number of milliseconds between the dates
		mils = date2.getTime() - date1.getTime();
		
		//convert the difference based on part
		switch (part) {
			
			case "y":
				//years
				convertedDiff = mils/(1000 * 60 * 60 * 24 * 365);
				break;
				
			case "m":
				//months
				convertedDiff = mils/(1000 * 60 * 60 * 24 * 30);
				break;
			
			case "q":
				//quarters
				convertedDiff = mils/(1000 * 60 * 60 * 24 * 30 * 3);
				break;
					
			case "w":
				//weeks
				convertedDiff = mils/(1000 * 60 * 60 * 24 * 7);
				break;
				
			case "d":
				//days
				convertedDiff = mils/(1000 * 60 * 60 * 24);
				break;
			case "h":
				//hours
				convertedDiff = mils/(1000 * 60 * 60);
				break;
			case "n":
				//minutes
				convertedDiff = mils/(1000 * 60);
				break;	
			case "s":
				//seconds
				convertedDiff = mils/1000;
				break;		
				
			default:
				convertedDiff = mils;
		}
	} catch(e) {
		alert(e);
	}
	
	return convertedDiff;
	
}
	
var postToIframe = function (data, url, target) {
    $('body').append('<form action="' + url + '" method="post" target="' + target + '" id="postToIframe"></form>');
    $.each(data, function (n, v) {
        $('#postToIframe').append('<input type="hidden" name="' + n + '" value="' + v + '" />');
    });
    $('#postToIframe').submit().remove();
};
var postToBlank = function (data, url) {
    $('body').append('<form action="' + url + '" method="post" target="_blank" id="postToBlank"></form>');
    $.each(data, function (n, v) {
        $('#postToBlank').append('<input type="hidden" name="' + n + '" value="' + v + '" />');
    });
    $('#postToBlank').submit().remove();
};

	
function getPageName(url) {
  //Use getPageName(window.location.href) to get the current page
    var index = url.lastIndexOf("/") + 1;
    var filenameWithExtension = url.substr(index);
    var filename = filenameWithExtension.split(".")[0]; 
    return filename;  
}

