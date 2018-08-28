<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
	<channel>
		<title>{$form.htitle}RSS</title>
		<link>{$smarty.const.app_site_url}</link>
{if $form.pagetype=="popular"}
		<description>スポットワードに投稿された電子書籍を評価や閲覧数により集計した結果を人気順に紹介しています。</description>
{else}
		<description>スポットワードに投稿された電子書籍を新着順に紹介しています。</description>
{/if}
{foreach item=d from=$form.list}
		<item>
			<title>{$d.title}</title>
			<link>{$smarty.const.app_site_url}book/{$d.book_id}/</link>
			<description>{$d.description}</description>
		</item>
{/foreach}
	</channel>
</rss>