<?='<?xml version="1.0" encoding="UTF-8"?>'."\n";?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://<?=SERVER;?>/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <? foreach ($razdel as $key => $item):?>
  <url>
    <loc>http://<?=SERVER;?>/<?=$item->url;?>/</loc>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  <? endforeach;?>
</urlset> 