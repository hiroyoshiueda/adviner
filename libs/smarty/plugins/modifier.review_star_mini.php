<?php
function smarty_modifier_review_star_mini($point)
{
//	if (empty($point)) return '（評価しない）';
	if ($point>0 || $point<=5) {
		return '<span class="review-star-mini review-star-mini-'.$point.'">'.$point.'</span>';
	}
	return '';
}
?>