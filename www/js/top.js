var _gaq = _gaq || [];
$(document).ready(function(){
	var ajaxTracking = false;
	var tabSelectedClass = 'htitle_tab_selected';
	var advicePage = 0;
	var consultPage = 0;
	var followPage = 0;
	var actionPage = 0;
	var qaPage = 0;
	var init = function() {
		ajaxTracking = false;
		advicePage = 0;
		consultPage = 0;
		followPage = 0;
		actionPage = 0;
		qaPage = 0;
	};
	var selectedTab = function(){
		$('#advice-tab, #consult-tab, #qa-tab, #follow-tab, #action-tab').removeClass(tabSelectedClass);
	};
	var hideList = function(){
		$('#advice-list, #consult-list, #qa-list, #follow-list, #action-list').css({'display':'none'});
	};
	var hideMore = function(){
		$('#advice-more, #consult-more, #qa-more, #follow-more, #action-more').css({'display':'none'});
	};
	var showAdvice = function(){
		selectedTab();
		if (advicePage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var listQ = $('#advice-list');
		var moreQ = $('#advice-more');
		var postData = {
			'pagenum' : advicePage
		};
		$('#advice-tab').addClass(tabSelectedClass);
		ajaxPost('/api/top/advice/get_list', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (advicePage == 0) {
						listQ.html('').css({'display':'block'});
					}
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					if (data.lists.more == 1) {
						moreQ.css({'display':'block'});
					}
				} else {
					listQ.fadeIn();
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
		var listQ = $('#consult-list');
		var moreQ = $('#consult-more');
		var postData = {
			'pagenum' : consultPage
		};
		$('#consult-tab').addClass(tabSelectedClass);
		ajaxPost('/api/top/consult/get_list', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (consultPage == 0) {
						listQ.html('').css({'display':'block'});
					}
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					if (data.lists.more == 1) {
						moreQ.css({'display':'block'});
					}
				} else {
					listQ.fadeIn();
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showFollow = function(){
		selectedTab();
		if (followPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var listQ = $('#follow-list');
		var moreQ = $('#follow-more');
		var postData = {
			'pagenum' : followPage
		};
		$('#follow-tab').addClass(tabSelectedClass);
		ajaxPost('/api/top/follow/get_list', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (followPage == 0) {
						listQ.html('').css({'display':'block'});
					}
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					if (data.lists.more == 1) {
						moreQ.css({'display':'block'});
					}
				} else {
					listQ.fadeIn();
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
		var listQ = $('#action-list');
		var moreQ = $('#action-more');
		var postData = {
			'pagenum' : actionPage
		};
		$('#action-tab').addClass(tabSelectedClass);
		ajaxPost('/api/top/action/get_list', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					lastDatetime = data.lists.last_datetime;
					var htmlQ = $(data.lists.html).css({'display':'none'});
					Adviner.bindConsultThreadEvent(htmlQ);
					if (actionPage == 0) {
						listQ.html('').css({'display':'block'});
					}
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse(listQ.get(0));
					if (data.lists.more == 1) {
						moreQ.css({'display':'block'});
					}
				} else {
					listQ.fadeIn();
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	var showQa = function(){
		selectedTab();
		if (qaPage == 0) {
			hideList();
		}
		hideMore();
		$('#loading').css({'display':'block'});
		var listQ = $('#qa-list');
		var moreQ = $('#qa-more');
		var postData = {
			'pagenum' : qaPage
		};
		$('#qa-tab').addClass(tabSelectedClass);
		ajaxPost('/api/top/qa/get_list', postData, function(data, dataType){
			if (data.lists.result == 1) {
				$('#loading').css({'display':'none'});
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					if (qaPage == 0) {
						listQ.html('').css({'display':'block'});
					}
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					if (data.lists.more == 1) {
						moreQ.css({'display':'block'});
					}
				} else {
					listQ.fadeIn();
				}
			} else if (data.lists.errmsg!='') {
				//alert(data.lists.errmsg);
			}
		});
		return;
	};
	$('#advice-more').click(function(){
		_gaq.push(['_trackPageview', '/advice-more']);
		advicePage++;
		showAdvice();
	});
	$('#consult-more').click(function(){
		_gaq.push(['_trackPageview', '/consult-more']);
		consultPage++;
		showConsult();
	});
	$('#follow-more').click(function(){
		_gaq.push(['_trackPageview', '/follow-more']);
		followPage++;
		showFollow();
	});
	$('#action-more').click(function(){
		_gaq.push(['_trackPageview', '/action-more']);
		actionPage++;
		showAction();
	});
	$('#qa-more').click(function(){
		_gaq.push(['_trackPageview', '/qa-more']);
		qaPage++;
		showQa();
	});
	var dispatch = function() {
		//if (window.location.hash == '' || window.location.hash == '#_') return;
		var parse = decodeURIComponent(window.location.hash).split('/');
		var pname = parse[1] || '';
		if (ajaxTracking) {
			_gaq.push(['_trackPageview', pname]);
		} else {
			ajaxTracking = true;
		}
		switch (pname) {
		case 'consult':
			showConsult();
			break;
		case 'follow':
			showFollow();
			break;
		case 'action':
			showAction();
			break;
		case 'qa':
			showQa();
			break;
//		case 'search':
//			searchWord = trimZen(parse[2] || '');
//			$('#search-keyword').val(searchWord);
//			$('#search-category').val('');
//			if (searchWord != '') {
//				showSearch();
//			}
//			break;
//		case 'category':
//			searchWord = (parse[3] && parse[3] == 'search' && parse[4]) ? trimZen(parse[4]) : '';
//			$('#search-keyword').val(searchWord);
//			searchCategory = (parse[2] && parse[2]>0) ? parse[2] : 0;
//			$('#search-category').val(searchCategory);
//			if (searchWord != '') {
//				showSearch();
//			}
//			break;
		default:
			showAdvice();
			break;
		}
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
	if ($('#please-advice-form-textarea').size()) {
		//$('#please-advice-form-textarea').focus(Adviner.focusPleaseFormInput).blur(Adviner.blurPleaseFormInput).autosize();
		$('#please-advice-form-textarea').autosize();
		$('#please-advice-form-post').click(Adviner.clickQuestionPostButton);
	}
	$('div.qa-tool > ul > li > a.answer-form-btn').live('click', Adviner.clickAnswerFormButton);
});