<?php
/**
 * {chapter_pager chapter=$form.chapter total=$form.total id=$form.id}
 * @param $params
 * @param $smarty
 * @return unknown_type
 */
function smarty_function_chapter_pager($params, &$smarty)
{
	if (isset($params['chapter'])===false) $smarty->trigger_error('plugin "chapter_pager": missing or empty parameter: chapter');
	if (isset($params['total'])===false) $smarty->trigger_error('plugin "chapter_pager": missing or empty parameter: total');
	if (isset($params['id'])===false) $smarty->trigger_error('plugin "chapter_pager": missing or empty parameter: id');

	$chapter = $params['chapter'];
	$total = $params['total'];
	$id = $params['id'];

	$main_class = empty($params['main_class']) ? 'chapter-pager' : $params['main_class'];
	$base_href = '/book/'.$id.'/';

	$html = '<div class="'.$main_class.'">';

	if ($chapter > 1) {
		$prev_chapter = $chapter - 1;
		$prev_href = $base_href;
		if ($prev_chapter > 1) $prev_href .= 'chapter/'.$prev_chapter.'/';
		$html .= '<span class="chapter-pager-prev">';
		$html .= '<a href="'.$prev_href.'#body">';
		$html .= '<img src="/img/chapter_prev.png" width="16" height="16" alt="＜" align="top" />';
		$html .= '前章';
		$html .= '</a>';
		$html .= '</span>';
	}

	if ($chapter < $total) {
		$next_chapter = $chapter + 1;
		$next_href = $base_href;
		$next_href .= 'chapter/'.$next_chapter.'/';
		$html .= '<span class="chapter-pager-next">';
		$html .= '<a href="'.$next_href.'#body">';
		$html .= '次章';
		$html .= '<img src="/img/chapter_next.png" width="16" height="16" alt="＞" align="top" />';
		$html .= '</a>';
		$html .= '</span>';
	}

	$html .= '</div>';

	return $html;
}
?>