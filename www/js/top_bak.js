var _gaq = _gaq || [];
$(document).ready(function(){
	var ajaxTracking = false;
	var feedPage = 0;
	var allfeedPage = 0;
	var pleasePage = 0;
	var actionPage = 0;
	var noticePage = 0;
	var popularPage = 0;
	var consultPage = 0;
	var searchPage = 0;
	var reviseCount = 0;
	var searchWord = '';
	var searchCategory = 0;
	var tabSelectedClass = 'tab_btn_selected';
	var newTimerId = null;
//	var newInterval = 180000;
	var newInterval = 30000;
	var lastNoticeId = 0;
	var newItemTotal = 0;
	var newItemHeapQ = null;
	var newItemBarQ = null;
	var lastDatetime = '';
	var lastKey = '';
	var selectedTab = function(){
		$('#feed-tab, #allfeed-tab, #please-tab, #action-tab, #notice-tab, #popular-tab, #consult-tab').removeClass(tabSelectedClass);
		$('#search-tab').css({'display':'none'});
	};
	var hideList = function(){
		$('#feed-list, #allfeed-list, #please-list, #action-list, #notice-list, #popular-list, #consult-list, #search-list').css({'display':'none'});
	};
	var hideMore = function(){
		$('#feed-more, #allfeed-more, #please-more, #action-more, #notice-more, #popular-more, #consult-more, #search-more').css({'display':'none'});
	};
	var showFeed = function(){
		selectedTab();
		if (feedPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'pagenum' : feedPage,
			'revise' : reviseCount
		};
		$('#feed-tab').addClass(tabSelectedClass);
		ajaxPost('/api/stream/get_follow', postData, function(data, dataType){
			if (data.lists.result == '1') {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (feedPage == 0) {
						$('#feed-list').html('').css({'display':'block'});
						$('#feed-list').css({'display':'block'});
					}
					//Adviner.good.parse_button(htmlQ);
					$('#feed-list').append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse($('#feed-list').get(0));
					if (data.lists.more == 1) {
						$('#feed-more').css({'display':'block'});
					}
					if (data.lists.last_datetime != '' && data.lists.last_key != '') {
						lastDatetime = data.lists.last_datetime;
						lastKey = data.lists.last_key;
					}
				} else {
					$('#feed-list').fadeIn();
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		newTimerId = setInterval(getNewFeed, newInterval);
		return;
	};
	var getNewFeed = function(){
		var postData = {
				'last_datetime' : lastDatetime,
				'last_key' : lastKey
		};
		ajaxPost('/api/stream/get_new_follow', postData, function(data, dataType){
			if (data.lists.result == 1) {
				if (data.lists.html != '') {
					var newFeedQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(newFeedQ);
					$('#feed-list').prepend(newFeedQ);
					newFeedQ.fadeIn();
					//Adviner.FBParse($('#feed-list').get(0));
					if (data.lists.last_datetime != '' && data.lists.last_key != '') {
						lastDatetime = data.lists.last_datetime;
						lastKey = data.lists.last_key;
					}
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showAllFeed = function(){
		selectedTab();
		if (allfeedPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'pagenum' : allfeedPage,
			'revise' : reviseCount
		};
		$('#allfeed-tab').addClass(tabSelectedClass);
		ajaxPost('/api/stream/get_all', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (allfeedPage == 0) {
						$('#allfeed-list').html('').css({'display':'block'});
						$('#allfeed-list').css({'display':'block'});
					}
					//Adviner.good.parse_button(htmlQ);
					$('#allfeed-list').append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse($('#allfeed-list').get(0));
					if (data.lists.more == 1) {
						$('#allfeed-more').css({'display':'block'});
					}
					if (data.lists.last_datetime != '' && data.lists.last_key != '') {
						lastDatetime = data.lists.last_datetime;
						lastKey = data.lists.last_key;
					}
				} else {
					$('#allfeed-list').fadeIn();
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		newTimerId = setInterval(getNewAllFeed, newInterval);
		return;
	};
	var getNewAllFeed = function(){
		var postData = {
			'last_datetime' : lastDatetime,
			'last_key' : lastKey
		};
		ajaxPost('/api/stream/get_new_all', postData, function(data, dataType){
			if (data.lists.result == 1) {
				if (data.lists.html != '') {
					var newFeedQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(newFeedQ);
					$('#allfeed-list').prepend(newFeedQ);
					newFeedQ.fadeIn();
					//Adviner.FBParse($('#allfeed-list').get(0));
					if (data.lists.last_datetime != '' && data.lists.last_key != '') {
						lastDatetime = data.lists.last_datetime;
						lastKey = data.lists.last_key;
					}
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showPlease = function(){
		selectedTab();
		if (pleasePage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'pagenum' : pleasePage,
			'revise' : reviseCount
		};
		$('#please-tab').addClass(tabSelectedClass);
		ajaxPost('/api/stream/get_please_advice', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (pleasePage == 0) {
						$('#please-list').html('').css({'display':'block'});
					}
					$('#please-list').append(htmlQ);
					htmlQ.fadeIn();
					if (data.lists.more == 1) {
						$('#please-more').css({'display':'block'});
					}
					if (data.lists.last_key != '') {
						lastKey = data.lists.last_key;
					}
				} else {
					$('#please-list').fadeIn();
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		newTimerId = setInterval(getNewPlease, newInterval);
		return;
	};
	var getNewPlease = function(){
		var postData = {
			'last_key': lastKey
		};
		ajaxPost('/api/stream/get_new_please_advice', postData, function(data, dataType){
			if (data.lists.result == 1) {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html);
					//$('a.click_consult_response', htmlQ).click(Adviner.clickConsultResponse);
					if (newItemHeapQ == null) {
						newItemHeapQ = htmlQ.clone();
					} else {
						//newItemHeapQ.before(htmlQ);
						newItemHeapQ = htmlQ.append(newItemHeapQ);
						newItemBarQ.remove();
					}
					var list_total = data.lists.total - 0;
					newItemTotal += list_total;
					reviseCount += list_total;
					if (newItemTotal > 0) {
						newItemBarQ = $('<div class="new_item_bar">新しい「アドバイスください」が'+newItemTotal+'件あります。</div>').click(clickNewPleaseBar);
						$('#please-list').prepend(newItemBarQ);
					}
					lastKey = data.lists.last_key;
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showAction = function(){
		selectedTab();
		if (actionPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'pagenum' : actionPage,
			'revise' : reviseCount
		};
		$('#action-tab').addClass(tabSelectedClass);
		ajaxPost('/api/stream/get_action', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					lastDatetime = data.lists.last_datetime;
					var htmlQ = $(data.lists.html).css({'display':'none'});
					Adviner.bindConsultThreadEvent(htmlQ);
					if (actionPage == 0) {
						$('#action-list').html('').css({'display':'block'});
					}
					//Adviner.good.parse_button(htmlQ);
					$('#action-list').append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse($('#action-list').get(0));
					if (data.lists.more == 1) {
						$('#action-more').css({'display':'block'});
					}
				} else {
					$('#action-list').fadeIn();
				}
				//$('#top-notice').html(data.lists.unread_html);
				//$('#tab-notice').html(data.lists.unread_html);
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		//newTimerId = setInterval(getNewAction, newInterval);
		return;
	};
	var getNewAction = function(){
		var postData = {
			'last_datetime': lastDatetime
		};
		ajaxPost('/api/stream/get_new_action', postData, function(data, dataType){
			if (data.lists.result == 1) {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html);
					//$('a.click_consult_response', htmlQ).click(Adviner.clickConsultResponse);
					if (newItemHeapQ == null) {
						newItemHeapQ = htmlQ.clone();
					} else {
						//newItemHeapQ.before(htmlQ);
						newItemHeapQ = htmlQ.append(newItemHeapQ);
						newItemBarQ.remove();
					}
					newItemTotal += data.lists.total - 0;
					reviseCount += data.lists.total - 0;
					if (newItemTotal > 0) {
						newItemBarQ = $('<div class="new_item_bar">新しいあなたの相談が'+newItemTotal+'件あります。</div>').click(clickNewActionBar);
						$('#action-list').prepend(newItemBarQ);
					}
					lastNoticeId = data.lists.last_notice_id;
				}
				//$('#top-notice').html(data.lists.unread_html);
				//$('#tab-notice').html(data.lists.unread_html);
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
//	var showNotice = function(){
//		selectedTab();
//		if (noticePage == 0) {
//			hideList();
//		}
//		hideMore();
//		$('#loading').css({'display':'block'});
//		var postData = {
//			'pagenum' : noticePage,
//			'revise' : reviseCount
//		};
//		$('#notice-tab').addClass(tabSelectedClass);
//		Adviner.isNoticeTab = true;
//		ajaxPost('/api/notice/get_notice', postData, function(data, dataType){
//			if (data.lists.result == 1) {
//				$('#loading').css({'display':'none'});
//				lastNoticeId = data.lists.last_notice_id;
//				var htmlQ = $(data.lists.html).css({'display':'none'});
//				$('a.click_consult_response', htmlQ).click(Adviner.clickConsultResponse);
//				if (noticePage == 0) {
//					$('#notice-list').html('').css({'display':'block'});
//				}
//				$('#notice-list').append(htmlQ);
//				htmlQ.fadeIn();
//				if (data.lists.more == 1) {
//					$('#notice-more').css({'display':'block'});
//				}
//				$('#top-notice').html(data.lists.unread_html);
//				$('#tab-notice').html(data.lists.unread_html);
//			} else if (data.lists.errmsg!='') {
//				alert(data.lists.errmsg);
//			}
//		});
//		newTimerId = setInterval(getNewNotice, newInterval);
//		return;
//	};
//	var getNewNotice = function(){
//		var postData = {
//			'last_notice_id': lastNoticeId
//		};
//		ajaxPost('/api/notice/get_new_notice', postData, function(data, dataType){
//			if (data.lists.result == 1) {
//				if (data.lists.html != '') {
//					var htmlQ = $(data.lists.html);
//					$('a.click_consult_response', htmlQ).click(Adviner.clickConsultResponse);
//					if (newItemHeapQ == null) {
//						newItemHeapQ = htmlQ.clone();
//					} else {
//						//newItemHeapQ.before(htmlQ);
//						newItemHeapQ = htmlQ.append(newItemHeapQ);
//						newItemBarQ.remove();
//					}
//					newItemTotal += data.lists.total - 0;
//					reviseCount += data.lists.total - 0;
//					if (newItemTotal > 0) {
//						newItemBarQ = $('<div class="new_item_bar">新しい通知が'+newItemTotal+'件あります。</div>').click(clickNewNoticeBar);
//						$('#notice-list').prepend(newItemBarQ);
//					}
//					lastNoticeId = data.lists.last_notice_id;
//				}
//				$('#top-notice').html(data.lists.unread_html);
//				$('#tab-notice').html(data.lists.unread_html);
//			} else if (data.lists.errmsg!='') {
//				//alert(data.lists.errmsg);
//			}
//		});
//		return;
//	};
	var clickNewPleaseBar = function(){
		newItemBarQ.remove();
		var htmlQ = newItemHeapQ.clone();
		htmlQ.css({'display':'none'});
		$('#please-list').prepend(htmlQ);
		htmlQ.fadeIn();
		newItemHeapQ = null;
		newItemBarQ = null;
		newItemTotal = 0;
	};
	var clickNewActionBar = function(){
		newItemBarQ.remove();
		var htmlQ = newItemHeapQ.clone();
		htmlQ.css({'display':'none'});
		$('#notice-list > div:eq(0)').removeClass('notice_item_top');
		$('#notice-list > div').removeClass('notice_item_new');
		$('#notice-list').prepend(htmlQ);
		htmlQ.fadeIn();
		newItemHeapQ = null;
		newItemBarQ = null;
		newItemTotal = 0;
	};
	var showPopular = function(){
		selectedTab();
		if (popularPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'type':'popular',
			'pagenum':popularPage
		};
		$('#popular-tab').addClass(tabSelectedClass);
		ajaxPost('/get_list_api', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				$('#htitle-tab').html(data.lists.htitle);
				if (popularPage == 0) {
					$('#popular-list').html(data.lists.html);
					$('#popular-list').css({'display':'block'});
				} else {
					$('#popular-list').append(data.lists.html);
				}
				if (data.lists.more == 1) {
					$('#popular-more').css({'display':'block'});
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showConsult = function(){
		selectedTab();
		if (consultPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'type':'consult',
			'pagenum':consultPage
		};
//		ajaxPost('/get_list_api', postData, function(data, dataType){
		ajaxPost('/api/list/get_watch', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				$('#consult-tab').addClass(tabSelectedClass);
				$('#htitle-tab').html(data.lists.htitle);
				if (consultPage == 0) {
					$('#consult-list').html(data.lists.html);
					$('#consult-list').css({'display':'block'});
				} else {
					$('#consult-list').append(data.lists.html);
				}
				if (data.lists.more == 1) {
					$('#consult-more').css({'display':'block'});
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showSearch = function(){
		selectedTab();
		if (searchPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var postData = {
			'type':'popular',
			'pagenum':searchPage,
			'q':searchWord,
			'target':'search'
		};
		if (searchCategory > 0) {
			postData.main_category_id = searchCategory;
		}
		$('#search-tab').css({'display':'block'});
		ajaxPost('/get_search_api', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				$('#htitle-tab').html(data.lists.htitle);
				if (searchPage == 0) {
					if (data.lists.html != '') {
						var msg = '<p id="search-msg" class="notice">'+searchWord+' の検索結果を表示しています。</p>';
						$('#search-list').html(msg + data.lists.html);
					} else {
						var msg = '<p id="search-msg" class="notice">'+searchWord+' の検索結果は見つかりませんでした。</p>';
						$('#search-list').html(msg);
					}
					$('#search-list').css({'display':'block'});
				} else {
					$('#search-list').append(data.lists.html);
				}
				if (data.lists.more == 1) {
					$('#search-more').css({'display':'block'});
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	$('#feed-more').click(function(){
		_gaq.push(['_trackPageview', '/feed-more']);
		feedPage++;
		showFeed();
	});
	$('#allfeed-more').click(function(){
		_gaq.push(['_trackPageview', '/all-feed-more']);
		allfeedPage++;
		showAllFeed();
	});
	$('#please-more').click(function(){
		_gaq.push(['_trackPageview', '/please-more']);
		pleasePage++;
		showPlease();
	});
	$('#action-more').click(function(){
		_gaq.push(['_trackPageview', '/action-more']);
		actionPage++;
		showAction();
	});
	$('#notice-more').click(function(){
		_gaq.push(['_trackPageview', '/notice-more']);
		noticePage++;
		showNotice();
	});
	$('#popular-more').click(function(){
		_gaq.push(['_trackPageview', '/popular-more']);
		popularPage++;
		showPopular();
	});
	$('#consult-more').click(function(){
		_gaq.push(['_trackPageview', '/consult-more']);
		consultPage++;
		showConsult();
	});
	$('#search-more').click(function(){
		_gaq.push(['_trackPageview', '/search-more']);
		searchPage++;
		showSearch();
	});
	var init = function() {
		// 初期化
		Adviner.isNoticeTab = false;
		feedPage = 0;
		allfeedPage = 0;
		pleasePage = 0;
		actionPage = 0;
		noticePage = 0;
		popularPage = 0;
		consultPage = 0;
		searchPage = 0;
		reviseCount = 0;
		searchWord = '';
		searchCategory = 0;
		lastNoticeId = 0;
		newItemTotal = 0;
		newItemHeapQ = null;
		newItemBarQ = null;
		lastDatetime = '';
		lastKey = '';
	};
	var dispatch = function() {
		//if (window.location.hash == '' || window.location.hash == '#_') return;
		var parse = decodeURIComponent(window.location.hash).split('/');
		var pname = parse[1] || '';
		if (ajaxTracking) {
			_gaq.push(['_trackPageview', pname]);
		} else {
			ajaxTracking = true;
		}
		if (newTimerId) clearInterval(newTimerId);
		switch (pname) {
		case 'feed':
			showFeed();
			break;
		case 'allfeed':
			showAllFeed();
			break;
		case 'please':
			showPlease();
			break;
		case 'action':
			showAction();
			break;
		case 'notice':
			showNotice();
			break;
		case 'popular':
			showPopular();
			break;
		case 'recent':
			showRecent();
			break;
		case 'consult':
			showConsult();
			break;
		case 'search':
			searchWord = trimZen(parse[2] || '');
			$('#search-keyword').val(searchWord);
			$('#search-category').val('');
			if (searchWord != '') {
				showSearch();
			}
			break;
		case 'category':
			searchWord = (parse[3] && parse[3] == 'search' && parse[4]) ? trimZen(parse[4]) : '';
			$('#search-keyword').val(searchWord);
			searchCategory = (parse[2] && parse[2]>0) ? parse[2] : 0;
			$('#search-category').val(searchCategory);
			if (searchWord != '') {
				showSearch();
			}
			break;
		default:
			showAllFeed();
			break;
		}
	};
	var runTopStream = function() {
		init();
		dispatch();
	};
	init();
	dispatch();
	$(window).hashchange(function(){
		init();
		dispatch();
	});
	if ($('#header-search-form-top').size()) {
		$('#header-search-type').click(Adviner.clickSearchType);
		$('#header-search-opt').click(Adviner.clickSearchOpt);
		$('#header-search-q').blur(Adviner.hideSearchBox);
		$('#header-search-form-top').submit(function(){
			Adviner.postSearch();
//			var q = $('#header-search-q').val();
//			q = trimZen(q);
//			if (q != '') {
//				var url = '/#!/search/' + q;
//				if ($('#search-category').val() != '') {
//					url = '/#!/category/' + $('#search-category').val() + '/search/' + q;
//				}
//				window.location.href = url;
//			}
			return false;
		});
	}
	Adviner.bindOpenPopupPayment('.click_popup_payment');
	if ($('#please-advice-form-textarea').size()) {
		$('#please-advice-form-textarea').focus(Adviner.focusPleaseFormInput).blur(Adviner.blurPleaseFormInput).autosize();
		$('#please-advice-form-post').click(Adviner.clickPleaseFormButton);
	}
	$('a.please_advice_btn').live('click', Adviner.clickPleaseAdviceButton);
	if ($('#open-follow-friends').size()) {
		$('#open-follow-friends, #open-follow-friends-advice').openDOMWindow({
			borderColor:'#d8d098',
			borderSize:'8',
			height:500,
			width:680,
			eventType:'click',
			windowSource:'iframe',
			windowPadding:0,
			overlay: 1,
			overlayOpacity:'10',
			loader:1,
			loaderImagePath:'/img/ajax-loader.gif',
			loaderHeight:32,
			loaderWidth:32
		});
	}
});