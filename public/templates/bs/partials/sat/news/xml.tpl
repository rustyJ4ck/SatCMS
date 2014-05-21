<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" {if $posts[0].yandex_text} xmlns="http://backend.userland.com/rss2" xmlns:yandex="http://news.yandex.ru"{/if}>
<channel>
 <title>{$site.html_title}</title>
 <link>{$site.urls.self}</link>

{if $site.image.thumbnail.url}
<image>
    <url>{$site.urls.self}{$site.image.thumbnail.url}</url>
    <title>{$site.title}</title>
    <link>{$site.urls.self}</link>
</image>
{/if}

<description>{$site.md}</description>
<language>ru</language>
<generator>{$site.domain}</generator>
<pubDate>{0|date_format_ex:"r"}</pubDate>
<lastBuildDate></lastBuildDate>   

{foreach $posts as $post}

    <item>
        <title>{$post.title}</title>
        {if $post.category.title}<category>{$post.category.title}</category>{/if}
        <link>{$site.urls.self}{$post.urls.self}</link>

        {*
        {if $post.image.url}
            <enclosure url="{$site.urls.self}{$post.image.url}" type="image/jpeg"/>
        {/if}
        *}
        
        <description><![CDATA[
            {if $post.image.thumbnail.url}
                <img src="{$site.urls.self}{$post.image.thumbnail.url}" type="image/jpeg" align="left" vspace="4" hspace="8"/>
            {/if}
            {$post.description}
        ]]></description>        
        
        {if $post.yandex_text}
        <yandex:genre>message</yandex:genre>
        <yandex:full-text><![CDATA[{$post.yandex_text}]]></yandex:full-text>
        {/if}
        
        {if $post.novoteka_text}        
        <novoteka:full-text><![CDATA[{$post.novoteka_text}]]></novoteka:full-text>
        {/if}        
        
        <pubDate>{$post.date_posted|date_format_ex:"r"}</pubDate>
    </item>

{/foreach}


</channel>
</rss>
        
