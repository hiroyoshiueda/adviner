$(document).ready(function(){
	var idName = 'login-pickup-list';
	var pickupOffset = 0;
	var pickupShow = 0;
	var pickupLimit = 5;
	var pickupMax = 0;
	var pickupQ = null;
	var lineQ = null;
	var timerId = null;
	var pickupList = function()
	{
		var listQ = $('#'+idName);
		var lineQ = $('#'+idName + ' > li');
		lineQ.css({'display':'none'});
		pickupMax = lineQ.length;
		pickupQ = $('<ul id="'+idName+'-scroll"></ul>');
		listQ.before(pickupQ);
		pickupShowLine();
	};
	var pickupShowLine = function()
	{
		if (pickupOffset >= pickupMax) {
			pickupOffset = 0;
		}
		if (pickupShow >= pickupLimit) {
			$('li:last', pickupQ).fadeOut(500, function(){
				$('li:last', pickupQ).remove();
				_pickupShow(1500, 5000);
			});
		} else if (pickupShow == pickupLimit - 1) {
			pickupShow++;
			_pickupShow(500, 5000);
			pickupQ.hover(function(){
				clearTimeout(timerId);
			},function(){
				clearTimeout(timerId);
				timerId = setTimeout(pickupShowLine, 3000);
			});
		} else {
			pickupShow++;
			_pickupShow(500, 500);
		}
	};
	var _pickupShow = function(et, t)
	{
		var copyQ = $('#'+idName + ' > li:eq('+pickupOffset+')').clone();
		pickupQ.prepend(copyQ);
		pickupOffset++;
		$('li:first', pickupQ).fadeIn(et, function(){
			clearTimeout(timerId);
			timerId = setTimeout(pickupShowLine, t);
		});
	};
	pickupList();
});