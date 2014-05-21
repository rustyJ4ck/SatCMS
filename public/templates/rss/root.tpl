<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
<title>{$config.title} {if $current.faculty.seo_title}- {$current.faculty.seo_title}{/if}</title>
<link>{$config.domain_url}{$config.site_url}</link>
<description><![CDATA[{if $current.faculty.seo_md}{$current.faculty.seo_md}{else}{$config.seo_md}{/if}]]></description>
<language>ru</language>
<generator>TFEngine</generator>
<pubDate>{0|date_format_ex:"r"}</pubDate>
<lastBuildDate></lastBuildDate>   

{foreach $posts as $post}

    <item>
        <title>{$post.title}</title>
        <guid isPermaLink="true">{$config.domain_url}{$post.url}</guid>
        <link>{$config.domain_url}{$post.url}</link>
        <description><![CDATA[
        
        {if $post.image.url}
        <img src="{$config.static_url}{$post.image.url}" vspace="4" hspace="8" align="left" title="{$post.title}"/>
        {/if}
        
        {$post.description}
        
        ]]></description>
        <pubDate>{$post.created_at|date_format_ex:"r"}</pubDate>
        <author>{$post.user.nick}</author>
        
        {*if $post.image.url}
        <enclosure url="{$post.image.url}" length="{$post.image.size}" type="image/{if $post.image.type == 'jpg'}jpeg{else}{$post.image.type}{/if}" />
        {/if*}
        
    </item>

{/foreach}


</channel>
</rss>
        
