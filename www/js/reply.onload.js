$(function(){
	var alertErrormsgQ = $('#alert-errormsg');
	if (alertErrormsgQ.length) {
		alertErrormsgQ.slideDown('normal', function(){
			var timerID = setInterval(function(){
				clearInterval(timerID);
				alertErrormsgQ.slideUp('normal');
			}, 5000);
		});
	}
	var alertSuccessmsgQ = $('#alert-successmsg');
	if (alertSuccessmsgQ.length) {
		alertSuccessmsgQ.slideDown('normal', function(){
			var timerID = setInterval(function(){
				clearInterval(timerID);
				alertSuccessmsgQ.slideUp('normal');
			}, 5000);
		});
	}
});