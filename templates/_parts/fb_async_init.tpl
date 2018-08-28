<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
	FB.init({
		appId : '{$smarty.const.APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY}',
		status : true,
		cookie : true,
		xfbml : true
	});
	FB.getLoginStatus(function(res) {
		if (!res.authResponse && CONST.USER_INFO.open_id != '') {
			window.location.href = '/logout?rd_url=' + encodeURIComponent(window.location.href);
		}
	});
};
(function() {
	var e = document.createElement('script');
	e.src = document.location.protocol + '//connect.facebook.net/ja_JP/all.js';
	e.async = true;
	document.getElementById('fb-root').appendChild(e);
}());
</script>
