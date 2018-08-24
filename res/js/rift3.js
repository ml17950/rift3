// last change: 2018-08-10

var lastView = 0;

function log(msg) {
	var theTime = new Date().toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
	console.log(theTime+': '+msg);
// 	alert(msg);
}

var rift3config = {
	updval: function(device_id, cfg_key, cfg_val) {
		log("rift3config.updval: ["+device_id+"/"+cfg_key+"/"+cfg_val.trim()+"]");
		
		$.ajax({
			url:  'api/ui/config/'+device_id+'/set', //?'+now,
			data: 'cmd=CFG:'+cfg_key+'='+cfg_val,
			type: 'POST',
			success: function(data) {
// 				alert(data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	alert(textStatus);
			}
		});
		
		return false;
	},
	writecfg: function(device_id) {
		log("rift3config.writecfg: "+device_id);
		
		$.ajax({
			url:  'api/ui/config/'+device_id+'/save', //?'+now,
			data: 'cmd=WRITECFG',
			type: 'POST',
			success: function(data) {
				window.location.href = 'config.php';
			},
			error: function (xhr, textStatus, thrownError) {
	        	alert(textStatus);
			}
		});
		return false;
	}
};

var rift3log = {
	search: function() {
		var find = $('#js-search').val().toLowerCase();

		$('.log-box').each(function(i, obj) {
			var content = $(this).text();
			var pos = content.toLowerCase().indexOf(find);
			if (pos > -1) {
				if ($(this).hasClass('log-box-hidden')) {
					console.log("remove class log-box-hidden");
					$(this).removeClass('log-box-hidden');
				}
			}
			else {
				if (!$(this).hasClass('log-box-hidden')) {
					console.log("add class log-box-hidden");
					$(this).addClass('log-box-hidden');
				}
			}
		});
		return false;
	}
};

var rift3switch = {
	on: function(id, type) {
		log("rift3switch.on :: "+id+" / "+type);
		
		$('#device-'+id+'-icon').attr('src', 'res/img/ui/switching.gif');
		
// 		var now = Math.round((new Date()).getTime() / 1000);
		
		$.ajax({
			url:  'api/ui/switch/'+id+'/on', //?'+now,
			data: 'v=ON',
			type: 'POST',
			success: function(data) {
				rift3switch.update_state(id, type, data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	rift3switch.update_state(id, type, 'error');
			}
		});
	},
	
	off: function(id, type) {
		log("rift3switch.off :: "+id+" / "+type);
		
		$('#device-'+id+'-icon').attr('src', 'res/img/ui/switching.gif');
		
// 		var now = Math.round((new Date()).getTime() / 1000);
		
		$.ajax({
			url:  'api/ui/switch/'+id+'/off', //?'+now,
			data: 'v=OFF',
			type: 'POST',
			success: function(data) {
				rift3switch.update_state(id, type, data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	rift3switch.update_state(id, type, 'error');
			}
		});
	},
	
	toggle: function(id, type) {
		var current_state = $('#device-'+id+'-icon').attr('data-state');
		
		log("rift3switch.toggle :: "+id+" / "+type+" == "+current_state);
		
		if (current_state == 'off')
			rift3switch.on(id, type);
		else
			rift3switch.off(id, type);
		
		return false;
	},
	
	update_state: function(id, type, state) {
		log("rift3switch.update_state not ready :: "+id+" / "+type+" / "+state);
		
		$('#device-'+id+'-icon').attr('data-state', state);
		
		switch (state) {
			case 'on':
			case 'off':
				$('#device-'+id+'-icon').attr('src', 'res/img/sensors/'+type+'-'+state+'.png');
				break;
			case 'error':
				$('#device-'+id+'-icon').attr('src', 'res/img/ui/error.png');
				break;
			default:
				$('#device-'+id+'-icon').attr('src', 'res/img/ui/unknown.png');
		}
	}
};

function toggle_receipe_trigger(id) {
	$('#js-'+id).slideToggle('slow', function() { });
	return false;
}
