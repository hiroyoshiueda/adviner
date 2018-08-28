<?php
function smarty_modifier_follow_btn($follow_id, $id, $type, $title='', $is_frame=true)
{
	$html = '';
	// フォロー解除
	if ($follow_id > 0) {
		if ($type == 'a') {
			if ($title != '') {
				$title = ' title="相談窓口「'.htmlspecialchars($title, ENT_QUOTES).'」のフォローを解除する"';
			} else {
				$title = ' title="フォローを解除する"';
			}
			$html = '<a onclick="Adviner.followAdvice('.$id.', \'unfollow\');" class="tiny-btn red-btn unfollow-btn"'.$title.'>フォロー解除</a>';
		} else if ($type == 'u') {
			if ($title != '') {
				$title = ' title="'.htmlspecialchars($title, ENT_QUOTES).'さんのフォローを解除する"';
			} else {
				$title = ' title="フォローを解除する"';
			}
			$html = '<a onclick="Adviner.followUsers('.$id.', \'unfollow\');" class="tiny-btn red-btn unfollow-btn"'.$title.'>フォロー解除</a>';
		}
	// フォロー
	} else {
		if ($type == 'a') {
			if ($title != '') {
				$title = ' title="相談窓口「'.htmlspecialchars($title, ENT_QUOTES).'」をフォローする"';
			} else {
				$title = ' title="フォローする"';
			}
			$html = '<a onclick="Adviner.followAdvice('.$id.', \'follow\');" class="tiny-btn green-btn follow-btn"'.$title.'>フォローする</a>';
		} else if ($type == 'u') {
			if ($title != '') {
				$title = ' title="'.htmlspecialchars($title, ENT_QUOTES).'さんをフォローする"';
			} else {
				$title = ' title="フォローする"';
			}
			$html = '<a onclick="Adviner.followUsers('.$id.', \'follow\');" class="tiny-btn green-btn follow-btn"'.$title.'>フォローする</a>';
		}
	}
	if ($is_frame) {
		$html = '<div id="follow-'.$type.'-btn-'.$id.'">'.$html.'</div>';
	}
	return $html;
}
?>