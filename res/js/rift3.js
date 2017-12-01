// last change: 2017-11-28

function log(msg) {
	var theTime = new Date().toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
	console.log(theTime+': '+msg);
// 	alert(msg);
}

var rift3switch = {
	on: function(id, type) {
// 		log("rift3switch.on :: "+id+" / "+type);
		
		$('#switch-'+id+'-icon').attr('src', 'res/img/switching.gif');
		
		var now = Math.round((new Date()).getTime() / 1000);
		
		$.ajax({
			url:  'api/switch/set/?id='+id+'&x='+now,
			data: 'v=ON',
			type: 'POST',
			success: function(data) {
				rift3switch.update_state(id, type, data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	rift3switch.update_state(id, type, 'ERROR');
			}
		});
	},
	
	off: function(id, type) {
// 		log("rift3switch.off :: "+id+" / "+type);
		
		$('#switch-'+id+'-icon').attr('src', 'res/img/switching.gif');
		
		var now = Math.round((new Date()).getTime() / 1000);
		
		$.ajax({
			url:  'api/switch/set/?id='+id+'&x='+now,
			data: 'v=OFF',
			type: 'POST',
			success: function(data) {
				rift3switch.update_state(id, type, data);
			},
			error: function (xhr, textStatus, thrownError) {
	        	rift3switch.update_state(id, type, 'ERROR');
			}
		});
	},
	
	toggle: function(id, type) {
		var current_state = $('#switch-'+id+'-icon').attr('data-state');
		
// 		log("rift3switch.toggle :: "+id+" / "+type+" == "+current_state);
		
		if (current_state == 'OFF')
			rift3switch.on(id, type);
		else
			rift3switch.off(id, type);
		
		return false;
	},
	
	update_state: function(id, type, state) {
// 		log("rift3switch.update_state not ready :: "+id+" / "+type+" / "+state);
		
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