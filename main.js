function log(msg) {
	var theTime = new Date().toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
	console.log(theTime+': '+msg);
}
function dbg(msg) {
	$('.js-debug').html(msg);
}
function doDebug(msg) {
	var old_data = $('.js-debug').html();
	var new_data = msg+'<br>'+old_data;
	$('.js-debug').html(new_data);
}
function DebugTest() {
	checkNewData();
	return false;
}
function getNewData() {
	log('getNewData');
	
	$.ajax({
		url: 'api/json/get/?w=debug&x='+new Date().getTime(),
		data:'',
		type:'GET',
		success: function(data) {
			dbg(data);
// 			log('getNewData: '+data);
// 			localStorage.setItem('lastData', data);
		},
		error: function (xhr, textStatus, thrownError) {
        	alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
		}
	});
}
function checkNewData() {
	var now = Math.round((new Date()).getTime() / 1000);
	var lastChanged = localStorage.getItem('lastChanged');
	if (lastChanged === null)
		lastChanged = 0;
	
	localStorage.setItem('lastUpdate', now);
	
	$.ajax({
		url: 'api/json/get/?w=lastchg&x='+new Date().getTime(),
		data:'',
		type:'GET',
		success: function(data) {
			if (data != lastChanged) {
				log('checkNewData: YES, new data - '+data+'/'+lastChanged);
				
				localStorage.setItem('lastChanged', data);
// 				dbg(data);
				getNewData();
			}
			else {
				log('checkNewData: no new data');
			}
		},
		error: function (xhr, textStatus, thrownError) {
        	alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
		}
	});
}