<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$form.list item=arr}
	<url>
		<loc>{$arr.loc}</loc>
		<lastmod>{$arr.lastmod}</lastmod>
		<changefreq>{$arr.changefreq}</changefreq>
		<priority>{$arr.priority}</priority>
	</url>
{/foreach}
</urlset>
