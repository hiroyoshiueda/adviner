(function($){
	$.fn.adviner_good = function(options) {
		var defaults = {
			'href':'',
			'width':'90',
			'height':'20'
		};
		var setting = $.extend(defaults, options);
		var _show = function(btnObj) {
			var href = btnObj.attr('data-href') || setting.href;
			var w = btnObj.attr('data-width') || setting.width;
			$.ajax({
				'url': '/api/good/button',
				'type': 'GET',
				'dataType': 'html',
				'timeout': 60000,
				'cache': false,
				'data': {"href":href},
				'success': function(htmlData, textStatus){
					var htmlQ = $(htmlData).css({"width":90,"height":20});
					$('a.ad_good_send', htmlQ).click(_good_send);
					$('a.ad_good_cancel', htmlQ).click(_good_cancel);
					$('a.ad_good_login', htmlQ).click(_good_login);
					btnObj.html(htmlQ).css({"width":w});
				},
				'error': function(XMLHttpRequest, textStatus, errorThrown){
					//alert(textStatus+': '+errorThrown);
				}
			});
		};
		var _load_ifram = function(btnObj) {
			var href = btnObj.attr('data-href') || setting.href;
			var w = btnObj.attr('data-width') || setting.width;
			var h = btnObj.attr('data-height') || setting.height;
			var url = '/api/good/button?href=' + encodeURI(href);
			var fQ = $('<iframe scrolling="no" frameborder="0" style="border:none;overflow:hidden;" allowTransparency="true"></iframe>').css({"width":w,"height":h}).attr('src', url);
			btnObj.html(fQ);
		};
		return this.each(function(i){
			_load_ifram($(this));
		});
	};
})(jQuery);
