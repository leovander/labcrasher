$(function() {
	fillAvailable(avail_rooms, today, largestGap);
	
	$("#colleges").change(function() {
		var option = $("#colleges option:selected").val()
		available = (function () {
		    var json = null;
		    $.ajax({
		        'async': false,
		        'global': false,
		        'url': 'COE/' + option + '_available.json',
		        'dataType': "json",
		        'success': function (data) {
		            json = data;
		        }
		    });
		    return json;
		})();
		
		today = new Date();
		avail_times = [];
		avail_start = [];
		avail_end = [];
		avail_rooms = getRooms(today, day, avail_start, avail_end);
		largestGap = findBiggestGap(avail_rooms, today);
		
		fillAvailable(avail_rooms, today, largestGap);
	});
});

var available = (function () {
    var json = null;
    $.ajax({
        'async': false,
        'global': false,
        'url': 'COE/CECS_available.json',
        'dataType': "json",
        'success': function (data) {
            json = data;
        }
    });
    return json;
})();
var today = new Date();
var day = getDay(today.getDay());
var avail_start = [];
var avail_end = [];
var avail_rooms = getRooms(today, day, avail_start, avail_end);
var largestGap = findBiggestGap(avail_rooms, today);

function findBiggestGap(avail_rooms, today) {
	var largestGap = 0;
	for(var room in avail_rooms) {
		var start = avail_start[room];
		var end = avail_end[room];
		var length = Math.floor(end.getTime() - today.getTime()) / 60000;
		console.log("room:" + avail_rooms[room]);
		console.log("length:" + length);
		
		if(length > largestGap) {
			largestGap = length;
		}
	}
	
	return largestGap;
}

function fillAvailable(avail_rooms, today, largestGap) {
	$("#available").html("");
	$("#available").html('<tr>' +
			  				'<th>Room</th>' +
			  				'<th>NOW</th>' +
			  				'<th>Start</th>' +
			  				'<th>End</th>' +
			  				'<th>Remaining(End - NOW)</th>' +
			  				'<th>Used Time(NOW - Start)</th>' +
			  				'<th>Length</th>' +
			  				'<th>Check Length</th>' +
			  			'</tr>');
	
	
	for(var room in avail_rooms) {
		var building = avail_rooms[room].split("-");
		if(!(building[0] === 'SPA' || building[0] === 'PH2' || building[0] === 'PH1')) {
			var start = avail_start[room];
			var end = avail_end[room];
			var length = Math.floor(end.getTime() - start.getTime()) / 60000;
			var remainingFree = Math.floor((end.getTime() - today.getTime()) / 60000);
			var usedFree = Math.floor((today.getTime() - start.getTime()) / 60000);
			
			$("#available").append('<tr>' +
								  		'<td>' + avail_rooms[room] + '</td>' +
								  		'<td>' + today.getHours() + ":" + today.getMinutes() + '</td>' +
								  		'<td>' + avail_start[room].getHours() + ":" + avail_start[room].getMinutes() + '</td>' +
								  		'<td>' + avail_end[room].getHours() + ":" + avail_end[room].getMinutes() + '</td>' +
								  		'<td>' + remainingFree + '</td>' +
								  		'<td>' + usedFree + '</td>' +
								  		'<td>' + largestGap + '</td>' +
								  		'<td>' + (usedFree + remainingFree) + '</td>' +
								  	'</tr>');

			if(Math.floor(remainingFree / 60) > 1) {
				$("#available").append("<tr><td colspan=8>" +
										"<div class=\"progress\">" +
											"<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"60\" aria-valuemin=\"0\" aria-valuemax=\"" + length + "\" style=\"width: " + (remainingFree / largestGap) * 100 + "%;\">" + Math.floor(remainingFree / 60) + " hours " + Math.floor(remainingFree % 60) + " mins left</div>" +
										"</div>" +
									"</td></tr>");
			} else if(Math.floor(remainingFree / 60) == 1) {
				$("#available").append("<tr><td colspan=8>" +
										"<div class=\"progress\">" +
											"<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"60\" aria-valuemin=\"0\" aria-valuemax=\"" + length + "\" style=\"width: " + (remainingFree / largestGap) * 100 + "%;\">" + Math.floor(remainingFree / 60) + " hour " + Math.floor(remainingFree % 60) + " mins left</div>" +
										"</div>" +
									"</td></tr>");
			} else {
				$("#available").append("<tr><td colspan=8>" +
										"<div class=\"progress\">" +
											"<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"60\" aria-valuemin=\"0\" aria-valuemax=\"" + length + "\" style=\"width: " + (remainingFree / largestGap) * 100 + "%;\">" + remainingFree + " mins left</div>" +
										"</div>" +
									"</td></tr>");
			}					   
		}
	}
}

function getRooms(today, day, avail_start, avail_end) {
	var possibles = available[day];
	var curr_available = [];
	
	var curr_time = today.getTime();
	
	for(var room in possibles) {
		if(available[day].hasOwnProperty(room)) {
			for(var times in available[day][room].vacant) {
				var start = new Date();
				var start_time = available[day][room].vacant[times].start.split(":");
				start.setHours(start_time[0]);
				start.setMinutes(start_time[1]);
				start.setSeconds(0);
				start.setMilliseconds(0);
				
				var end = new Date();
				var end_time = available[day][room].vacant[times].end.split(":");
				end.setHours(end_time[0]);
				end.setMinutes(end_time[1]);
				end.setSeconds(0);
				end.setMilliseconds(0);
				
				if(curr_time >= start.getTime() && curr_time <= end.getTime()) {
					curr_available.push(room);
					avail_start.push(start);
					avail_end.push(end);
				}
			}
		}
	}
	
	return curr_available;
}

function getDay(day) {
	switch(day) {
		case 1:
			return "Monday";
			break;
		case 2:
			return "Tuesday";
			break;
		case 3:
			return "Wednesday";
			break;
		case 4:
			return "Thursday";
			break;
		case 5:
			return "Friday";
			break;
		default:
			return false;
			break;
	}
}