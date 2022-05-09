// custom-scripts.js

$(function() {
	var lastUpdate = 0;
	var initdone = false;

	updateNav();

	$('a.disabled').click(function(e) {
		e.preventDefault();
	});
	
	$('a.acct-btn').click(function(e) {
		e.preventDefault();
		$(this).toggleClass('active');
		$('ul#account-menu').toggleClass('active');
	});
	
	$('a.quick-view-profile').click(function(e) {
		e.preventDefault();
		$('ul#account-menu').removeClass('active');
		$('div#profile-quick-view').addClass('active');
	});
	
	$('a.menu-close').click(function(e) {
		e.preventDefault();
		$('a.acct-btn, .header-menu').removeClass('active');
	});
	
	$('a.help-btn').click(function(e) {
		e.preventDefault();
		$('#contact-modal, #shader').addClass('active');
	});


	$('.modal-close').click(function(e) {
		e.preventDefault();
		$('.modal, #shader').removeClass('active');
		$("form").trigger('reset');
	});

	$('.message-ok','#message-modal').click(function(e) {
		e.preventDefault();
		$('.modal, #shader').removeClass('active');
		$("form").trigger('reset');
	});

	$("#edit-profile-btn").click(function(){
		$('#edit-profile, #shader').addClass('active');
	});
	
	$("#change-pwd-btn").click(function(){
		$('#change-password, #shader').addClass('active');
	});

	$(".logout").click(function(){
		$('#logout-modal, #shader').addClass('active');
	});

	$('.logout-close').click(function(){
		$('#logout-modal, #shader').removeClass('active');
	});
	
	$('a.video-link').click(function(e) {
		e.preventDefault();

		var thisVideo = $(this).attr('href'),
			thisResource = $(this).data('resourceid'),
			thisIsSigned = $(this).data('issigned'),
			thisIsComplete = $(this).data('iscomplete');

		/*var vidEvtObj = {
			event: "video_progress",
			"gtm.videoPercent": "Opened Video",
			"gtm.videoTitle": $("#VidLink_"+thisResource).data('vidtitle'),
			"gtm.videoUrl": thisVideo
		};
		dataLayer.push(vidEvtObj);*/

		$('#video-container').data('resourceid',thisResource).data('issigned',thisIsSigned);


		if ($('#video-container').hasClass("compliance") && thisIsComplete == 1 && !thisIsSigned == 1) {
			//compliance complete but not signed
			$('#signature-modal, #shader').addClass('active');
		} else {
			//show video
			$('#video-container, #shader').addClass('active');
			
			var thisPlayer = videojs('#speaker-video');
			
			thisPlayer.poster('assets/' + thisVideo + '.png');
			thisPlayer.src({
				type: 'video/mp4',
				src: 'assets/' + thisVideo + '.mp4'
			});
			
			thisPlayer.ready(function(){
				var myPlayer = this;
				var vidid = $('#video-container').data('resourceid'), 
					vidlink = $("#VidLink_" + vidid),
					videoPosition = vidlink.data('currtime'),
					lastUpdate = 0;
				myPlayer.on('timeupdate', function(){
					//UpdateLog(11,myPlayer.currentTime(),0);
					var currTime = Math.floor(myPlayer.currentTime()),
						timeDiff = currTime - lastUpdate,
						currVidID = $('#video-container').data('resourceid');
					//run update every 5 seconds
					if (vidid == currVidID && timeDiff >=5) {
						console.log('Update time: resource:'+vidid+' ('+currVidID+'); timeidx:'+currTime );

						UpdateLog(currVidID,currTime,0);
						lastUpdate = currTime;
						vidlink.data('currtime',currTime);
					}
					
				});
				
				myPlayer.on('ended', function() {
					$('#video-container').data('currtime',0);
					vidlink.data('currtime',0);
					lastUpdate = 0;
					UpdateLog(vidid,0,1);
					
					if (myPlayer.isFullscreen()) {
						myPlayer.exitFullscreen();
					}
					$('#video-container, #shader').removeClass('active');
					if ($('#video-container').hasClass("compliance") && $('#video-container').data('issigned') === 0) {
						$('#signature-modal, #shader').addClass('active');
					}
				});
	
				myPlayer.on('loadedmetadata', function(){
					//alert('ready md: ' + videoPosition);
					myPlayer.currentTime(videoPosition);
					if ($('#video-container').hasClass("compliance") && $('#video-container').data('issigned') === 0) {
						$("#Training_"+vidid).find("a.video-link").text("Continue");
						$("#Training_"+vidid).find("li.training-status").text("In Progress");
					}
				});
				myPlayer.on('canplaythrough', function(){
					if(!initdone)
					{
						myPlayer.currentTime(videoPosition);
						initdone = true;
					}
				});
				
			});
	
		}
//end click		
	});
	

	$('a.video-close').click(function(e) {
		e.preventDefault();
		videojs('#speaker-video').on('timeupdate', function(){});
		videojs('#speaker-video').currentTime(0);
		videojs('#speaker-video').pause();
		videojs('#speaker-video').trigger('loadstart');
		//videojs('#speaker-video').dispose();
		$('#video-container, #shader').removeClass('active');
		$('#video-container').data('resourceid',0).data('issigned',0);

//console.log($('#video-container').data('issigned'));
	});


	$('.signature-close').click(function(){
		var resourceID = $('#video-container').data('resourceid'),
			resourceName = $("#Training_"+resourceID).find("li.activity-name").text();
		
		$.post(
			'helpers/resource-log-helper.php'
			,{"logAction":"SignTraining",
				"resourceID":resourceID,
				"resourceName":resourceName,
				"isSigned":1
			}
		).then(function(data){
			//success
			//alert('done');
			//console.log(data);
			var $trainRow = $("#Training_"+data.ResourceID);
			$trainRow.find("li.training-status").text("Completed");
			$trainRow.find("li.sig-status").text("Signed");
			$trainRow.find("li.complete-date").text(data.dSignedOn);
			$trainRow.find("a.video-link").text("Rewatch").data("issigned",1);

		}).fail(function(jqXHR, textStatus, errorThrown){
			//console.log(jqXHR);
			//let response = JSON.parse(jqXHR['responseText']);
			//alert(response.msg.replace(/^'(.*)'$/, '$1'));
			//alert('fail');
		});
		$('#signature-modal, #shader').removeClass('active');
		$('#video-container, #shader').removeClass('active');
	});

	$('a.file-pdf').click(function(e) {
		e.preventDefault();
				
		$('#content-area').addClass('pdf-blur');

		$('<div>').attr({id: 'pdf-shader', class: 'pdf-modal active'}).appendTo('body');
		var pdfContainer = $('<div>').attr('id', 'pdf-viewer').addClass('active');
		pdfContainer.appendTo('#pdf-shader');

		var pdfurl = $(this).attr('href');
		var pageidx =  $(this).data('page');
		var id = $(this).attr('id');
		var resid = $(this).data("resourceid");
		
		if(parseInt(pageidx) > 1){
			pdfurl += '&page=' + parseInt(pageidx);
		}



		if ( $(this).hasClass('slide-deck') ) {
			pdfurl += "&downloadable=false";
		} else {
			pdfurl += "&downloadable=true";
		}

		

		pdfContainer.prepend($('<a>').addClass('close').html('<img src="images/close.svg"></img>'));
		$('<iframe>').attr({'id': 'pdf-iframe', 'class': 'slide-deck', 'src':pdfurl, 'scrolling':'no'}).appendTo(pdfContainer);


		
		$('div#pdf-shader, a.close').click(function(e) {

			var iFrame =  document.getElementById('pdf-iframe');
			if ( iFrame.contentDocument ) {
				currentPageNum= parseInt(iFrame.contentDocument.getElementById('pageNumber').value);
				$('#'+id ).data('page', currentPageNum);
				totalPageNum = parseInt(iFrame.contentDocument.getElementById('numPages').innerText.substring(2).trim());

				//Logging current page number 
				var isGComplete = totalPageNum == currentPageNum ? 1:0; 
				UpdateLog(resid, 0, isGComplete , currentPageNum);
			}

			

			$('div#pdf-shader, #pdf-iframe, #pdf-viewer').remove();
			$('#content-area').removeClass('pdf-blur');
		});
		


	});

});

var UpdateLog = function(resourceId, timeInt, isComplete, pageIndex=0) {
	var thisHref = $("#VidLink_"+resourceId).attr('href'),
		thisTitle = $("#VidLink_"+resourceId).data('vidtitle'),
		thisDuration = $("#VidLink_"+resourceId).data('duration'),
		thisProgressPointsFull = $("#VidLink_"+resourceId).data('progresspoints')+'',
		percentForLog = "N/A",
		percentComplete = Math.floor((timeInt / thisDuration) * 100),
		markers = [10,25,50,75,90],
		lastMarker=0,
		nextMarker = 0
		logAction = 'LogView';
		thisProgressPoints = thisProgressPointsFull.split(',');
		lastMarker=thisProgressPoints.pop();


	if (isComplete) {
		percentForLog = "100%";
		nextMarker = 100;
		logAction = 'Complete';
	} else if (timeInt > 5) {
		for(var i=0; i<markers.length; i++) {
			if (markers[i] > lastMarker && percentComplete > markers[i]) {
				nextMarker = markers[i];
				thisProgressPoints.push(markers[i]);
			}
		}
	}

	if (nextMarker > lastMarker) {
		$("#VidLink_"+resourceId).data('progresspoints',thisProgressPoints.join(','));
		percentForLog = nextMarker.toString() +"%";
		/*var vidEvtObj = {
			event: "video_progress",
			"gtm.videoPercent": percentForLog,
			"gtm.videoTitle": $("#VidLink_"+resourceId).data('vidtitle'),
			"gtm.videoUrl": thisHref
		};
		dataLayer.push(vidEvtObj);*/
	}
	//console.log('Video:'+resourceId+' nextMarker:'+nextMarker+' lastmarker:'+lastMarker+' percentForLog:'+percentForLog);
	//console.log('Video'+resourceId+' timeInt:'+timeInt+' isComplete:'+ isComplete);
	$.post(
		'helpers/resource-log-helper.php'
		,{"logAction":logAction,
		  "resourceID":resourceId,
		  "timeIndex":timeInt,
		  "isComplete":isComplete,
		  "pageIndex":pageIndex
		 }
		,function(data) {
			//success
			console.log(data);
		}
		,"json"
	);
	
	
};
var GetStatus = function(resourceId) {
	var ResourceStatus={};

	$.post(
		'helpers/resource-log-helper.php'
		,{"logAction":"GetProgress",
			"resourceID":resourceId
		 }
	).done(function(data){
		ResourceStatus = {
			"LastPosition":data.TimeIndex,
			"IsComplete":data.IsComplete
		};
	}).fail(function(jqXHR, textStatus, errorThrown){
		ResourceStatus = {
			"LastPosition":0,
			"IsComplete":0
		};
	});
	console.log(ResourceStatus);
	return ResourceStatus;
};
var JumpToSave = function(resourceId) {
	var lastPosition=0;
	$.post(
		'helpers/resource-log-helper.php'
		,{"logAction":"GetProgress",
			"resourceID":resourceId
		 }
		,function(data) {
			//success
			//console.log(data);
			lastPosition = data.TimeIndex;
			//alert('last position:' + lastPosition);
			/*
			vplayer = videojs("#speaker-video");
			vplayer = document.getElementById('speaker-video_html5_api');
			vplayer.currentTime(data.TimeIndex);
			*/
		}
		,"json"
	);
	//alert('returning:' + lastPosition);
	console.log(data);
	return lastPosition;
};

function updateNav() {
	switch ( window.location.href.split("/").pop() ) {
		case 'index.php':
			$('nav a.home-nav').addClass('active');
			break;
		case 'updates.php':
			$('nav a.updates-nav').addClass('active');
			break;
		case 'contact_directors.php':
			$('nav a.question-nav').addClass('active');
			break;
		case 'presentation_resources.php':
			$('nav a.resources-nav').addClass('active');
			break;
		case 'compliance.php':
			$('nav a.training-nav').addClass('active');
			break;
	}
}

