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
function DebugTest2() {
	updateUI();
	return false;
}
function showError(msg) {
	var now = new Date().toTimeString().substr(0, 8);
	$('.js-errmsg').text(now+' : '+msg);
	$('.js-errmsg').fadeIn('fast');
	window.setTimeout(hideError, 5000);
}
function hideError() {
	$('.js-errmsg').fadeOut('fast');
}
function showSuccess(msg) {
	var now = new Date().toTimeString().substr(0, 8);
	$('.js-sucmsg').text(now+' : '+msg);
	$('.js-sucmsg').fadeIn('fast');
	window.setTimeout(hideError, 5000);
}
function hideSuccess() {
	$('.js-sucmsg').fadeOut('fast');
}
function showLoading() {
	$('.js-loading').show();
}
function hideLoading() {
	$('.js-loading').hide();
}
function displaySwitches() {
	$('.js-widgets').show();
	$('.js-switches').show();
	$('.js-sensors').hide();
	$('.js-receipes').hide();
	$('.js-log').hide();
	
	checkNewData();
}
function displaySensors() {
	$('.js-widgets').hide();
	$('.js-switches').hide();
	$('.js-sensors').fadeIn('fast');
	$('.js-receipes').hide();
	$('.js-log').hide();
	
	checkNewData();
}
function displayReceipes() {
	$('.js-widgets').hide();
	$('.js-switches').hide();
	$('.js-sensors').hide();
	$('.js-receipes').fadeIn('fast');
	$('.js-log').hide();
}
function displayLog() {
	$('.js-widgets').hide();
	$('.js-switches').hide();
	$('.js-sensors').hide();
	$('.js-receipes').hide();
	$('.js-log').fadeIn('fast');
	
	getLogData();
}
function updateLogData() {
	log('updateLogData');
	
	var now = new Date().toTimeString().substr(0, 8);
	var lastData = localStorage.getItem('lastLogData');
	if (lastData === null) {
		// Todo
		alert('ERROR:updateLogData');
		return;
	}
	
	var jsonObj = JSON.parse(lastData);
	var newhtml = '<h1>Ereignisse '+now+'</h1>';
	
	for (var k in jsonObj['log']) {
		newhtml += "<div class='log-box'>";
		newhtml += "<div class='log-client'><img src='res/img/clients/"+jsonObj['log'][k]['c']+".png' width='16' height='16' alt='"+jsonObj['log'][k]['c']+"'></div>";
		newhtml += "<div class='log-time'>"+jsonObj['log'][k]['d']+"</div>";
		if (jsonObj['log'][k]['t'].length > 0)
			newhtml += "<div class='log-trigger'>"+jsonObj['log'][k]['t']+"</div>";
		newhtml += "<div class='log-action'>"+jsonObj['log'][k]['a']+"</div>";
		if (jsonObj['log'][k]['v'] == 'ON')
			newhtml += "<div class='log-value is-on'>"+jsonObj['log'][k]['v']+"</div>";
		else if (jsonObj['log'][k]['v'] == 'OFF')
			newhtml += "<div class='log-value is-off'>"+jsonObj['log'][k]['v']+"</div>";
		else
			newhtml += "<div class='log-value is-val'>"+jsonObj['log'][k]['v']+"</div>";
		newhtml += "</div>";
	}
	
	$('.js-log').html(newhtml);
}
function updateUI() {
	log('updateUI');
	
// 	var now = Math.round((new Date()).getTime() / 1000);
	var now = new Date().toTimeString().substr(0, 8);
	var lastData = localStorage.getItem('lastData');
	if (lastData === null) {
		// Todo
		alert('ERROR:updateUI');
		return;
	}
	
	
	
	
// 	log('lastData: '+lastData);
	
	var jsonObj = JSON.parse(lastData);
	var widget_html = ''; //<h1>Schalter '+now+'</h1>';
	var switch_html = ''; //<h1>Schalter '+now+'</h1>';
	var sensor_html = '<h1>Sensoren '+now+'</h1>';
	
// 	widget_html = 'X:'+jsonObj['sensors'].length;
	
// 	if (jsonObj['widgets'].length > 0) {
		for (var k in jsonObj['widgets']) {
			widget_html += "<div class='widget-box'>";
			widget_html += "<div class='widget-icon'><img src='res/img/switches/"+jsonObj['widgets'][k]['t']+"-"+jsonObj['widgets'][k]['v']+".png' width='56' height='56' alt='"+jsonObj['widgets'][k]['t']+"-"+jsonObj['widgets'][k]['v']+"'></div>";
			widget_html += "<div class='widget-name'>"+jsonObj['widgets'][k]['n']+"</div>";
			widget_html += "</div>";
		}
// 	}


	
// 	sensor_html += "<div class='log-container'>";
	for (var k in jsonObj['sensors']) {
		switch (jsonObj['sensors'][k]['t']) {
			//case 'computer':
			case 'light':
				switch_html += "<div class='switch-box hand' id='switch-"+k+"' onclick='return rift_switch.toggle(\""+k+"\", \""+jsonObj['sensors'][k]['t']+"\");'>";
				switch_html += "<div class='switch-icon'><img id='switch-"+k+"-icon' data-state='"+jsonObj['sensors'][k]['v']+"' src='res/img/switches/"+jsonObj['sensors'][k]['t']+"-"+jsonObj['sensors'][k]['v']+".png' width='32' height='32' alt='"+jsonObj['sensors'][k]['t']+"'></div>";
				switch_html += "<div class='switch-name'>"+jsonObj['sensors'][k]['n']+"</div>";
				switch_html += "<div class='switch-time'>"+jsonObj['sensors'][k]['c']+"</div>";
				switch_html += "</div>";
				break;
		}
		
		sensor_html += "<div class='sensor-box' id='sensor-"+k+"' onclick='return sensor.show(\""+k+"\", \""+jsonObj['sensors'][k]['n']+"\", \""+jsonObj['sensors'][k]['t']+"\");'>";
		sensor_html += "<div class='sensor-icon'><img src='res/img/types/"+jsonObj['sensors'][k]['t']+".png' width='32' height='32' alt='"+jsonObj['sensors'][k]['t']+"'></div>";
		sensor_html += "<div class='sensor-name'>"+jsonObj['sensors'][k]['n']+"</div>";
		sensor_html += "<div class='sensor-time'>"+jsonObj['sensors'][k]['c']+"</div>";
		if (jsonObj['sensors'][k]['v'] == 'ON')
			sensor_html += "<div class='sensor-value is-on'>"+jsonObj['sensors'][k]['v']+"</div>";
		else if (jsonObj['sensors'][k]['v'] == 'OFF')
			sensor_html += "<div class='sensor-value is-off'>"+jsonObj['sensors'][k]['v']+"</div>";
		else
			sensor_html += "<div class='sensor-value is-val'>"+jsonObj['sensors'][k]['v']+"</div>";
		sensor_html += "</div>";
		
// 		debug_html += "{"+k+"}<br>";
// 		debug_html += "n:["+jsonObj['sensors'][k]['n']+"]<br>";
// 		debug_html += "v:["+jsonObj['sensors'][k]['v']+"]<br>";
// 		debug_html += "c:["+jsonObj['sensors'][k]['c']+"]<br>";
// 		debug_html += "t:["+jsonObj['sensors'][k]['t']+"]<br>";
// 		debug_html += "<hr>";
	}
// 	sensor_html += "</div>";
	
	$('.js-widgets').html(widget_html);
	$('.js-switches').html(switch_html);
	$('.js-sensors').html(sensor_html);
}
function getLogData() {
	log('getLogData');
	
	var now = Math.round((new Date()).getTime() / 1000);
	
	showLoading();
	
	$.ajax({
		url: 'api/json/get/?w=logdata&x='+now,
		data:'',
		type:'GET',
		success: function(data) {
// 			log('getLogData: '+data);
			localStorage.setItem('lastLogData', data);
			updateLogData();
			hideLoading();
		},
		error: function (xhr, textStatus, thrownError) {
        	//alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
        	showError('Sync-Error #3');
		}
	});
}
function getNewData() {
	log('getNewData');
	
	var now = Math.round((new Date()).getTime() / 1000);
	
	showLoading();
	
	$.ajax({
		url: 'api/json/get/?w=lastdata&x='+now,
		data:'',
		type:'GET',
		success: function(data) {
// 			log('getNewData: '+data);
			localStorage.setItem('lastData', data);
			updateUI();
			hideLoading();
		},
		error: function (xhr, textStatus, thrownError) {
        	//alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
        	showError('Sync-Error #2');
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
		url: 'api/json/get/?w=lastchg&x='+now,
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
        	//alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
        	showError('Sync-Error #1');
		}
	});
}

function saveSensor() {
	var jsonArray = $('#frm-newrec'+type).serializeArray();
	var jsonString = JSON.stringify(jsonArray);
	
	return false;
}

var rift_switch = {
	on: function(id, type) {
// 		log("rift_switch.on :: "+id+" / "+type);
		
		$('#switch-'+id+'-icon').attr('src', 'res/img/switches/switching.gif');
		
		var now = Math.round((new Date()).getTime() / 1000);
		
		$.ajax({
			url:  'api/switch/set/?id='+id+'&x='+now,
			data: 'v=ON',
			type: 'POST',
			success: function(data) {
				rift_switch.update_state(id, type, data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	//alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
	        	//showError('Sync-Error #5');
	        	rift_switch.update_state(id, type, 'ERROR');
			}
		});
	},
	
	off: function(id, type) {
// 		log("rift_switch.off :: "+id+" / "+type);
		
		$('#switch-'+id+'-icon').attr('src', 'res/img/switches/switching.gif');
		
		var now = Math.round((new Date()).getTime() / 1000);
		
		$.ajax({
			url:  'api/switch/set/?id='+id+'&x='+now,
			data: 'v=OFF',
			type: 'POST',
			success: function(data) {
				rift_switch.update_state(id, type, data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	//alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
	        	//showError('Sync-Error #6');
	        	rift_switch.update_state(id, type, 'ERROR');
			}
		});
	},
	
	toggle: function(id, type) {
		var current_state = $('#switch-'+id+'-icon').attr('data-state');
		
// 		log("rift_switch.toggle :: "+id+" / "+type+" == "+current_state);
		
		if (current_state == 'OFF')
			rift_switch.on(id, type);
		else
			rift_switch.off(id, type);
		
		return false;
	},
	
	update_state: function(id, type, state) {
// 		log("rift_switch.update_state not ready :: "+id+" / "+type+" / "+state);
		
		$('#switch-'+id+'-icon').attr('data-state', state);
		
		switch (state) {
			case 'ON':
			case 'OFF':
				$('#switch-'+id+'-icon').attr('src', 'res/img/switches/'+type+'-'+state+'.png');
				break;
			case 'ERROR':
				$('#switch-'+id+'-icon').attr('src', 'res/img/switches/error.png');
				break;
			default:
				$('#switch-'+id+'-icon').attr('src', 'res/img/switches/unknown.png');
		}
	}
};

var sensor = {
	show: function(id, name, typ) {
		log("sensor.show");
		log(id+"/"+name+"/"+typ);
		
		$('#js-sensorfrm input[name=oid]').val(id);
		$('#js-sensorfrm input[name=sid]').val(id);
		$('#js-sensorfrm input[name=sname').val(name);
		$('#js-sensorfrm input[name=stype').val(typ);
		
		$('.js-sensor-edit').show();
		return false;
	},
	
	reset: function() {
		log("sensor.reset");
		$('#js-sensorfrm')[0].reset();
		$('.js-sensor-edit').hide();
		return false;
	},
	
	create: function() {
		log("sensor.create not ready");
	},
	
	save: function() {
		log("sensor.save");
		
		var now = Math.round((new Date()).getTime() / 1000);
		var params = $('#js-sensorfrm').serialize();
		log(params);
		
		$.ajax({
			url:  'api/sensor/save/?x='+now,
			data: params,
			type: 'POST',
			success: function(data) {
				log(data);
				sensor.reset();
			},
			error: function (xhr, textStatus, thrownError) {
	        	//alert('Error '+xhr.status+': '+textStatus+' / '+thrownError);
	        	showError('Sync-Error #4');
			}
		});
		
		return false;
	},
	
	rename: function() {
		log("sensor.rename not ready");
	},
	
	remove: function() {
		log("sensor.remove not ready");
	}
};

function onAppResume() {
	log('onAppResume');
	
	window.setTimeout(checkNewData, 250);
}
function onAppInitialize() {
	log('onAppInitialize');
	
	updateUI();
	updateLogData();
	
	window.setTimeout(getNewData, 500);
	window.setInterval(checkNewData, 60000);
	
}

window.setTimeout(onAppInitialize, 10);
// window.setInterval(checkNewData, 30000);
// window.addEventListener('focus', onAppResume);
// window.addEventListener('pageshow', onAppInitialize);
