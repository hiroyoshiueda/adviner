<?php
function smarty_modifier_review_star($point)
{
	$classname = 'review-star-00';
	if ($point>0 && $point<=0.5) {
		$classname = 'review-star-05';
	} else if ($point>0.5 && $point<=1.0) {
		$classname = 'review-star-10';
	} else if ($point>1.0 && $point<=1.5) {
		$classname = 'review-star-15';
	} else if ($point>1.5 && $point<=2.0) {
		$classname = 'review-star-20';
	} else if ($point>2.0 && $point<=2.5) {
		$classname = 'review-star-25';
	} else if ($point>2.5 && $point<=3.0) {
		$classname = 'review-star-30';
	} else if ($point>3.0 && $point<=3.5) {
		$classname = 'review-star-35';
	} else if ($point>3.5 && $point<=4.0) {
		$classname = 'review-star-40';
	} else if ($point>4.0 && $point<=4.5) {
		$classname = 'review-star-45';
	} else if ($point>4.5 && $point<=5.0) {
		$classname = 'review-star-50';
	}
	return '<span class="review-star '.$classname.'">点数：</span>';
}
?>