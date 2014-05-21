{* search *}



<form id="search-box-form" method="post" action="{$config.site_url}search/q/" class="validable">


    <p class="left">Поиск по сайту</p><p class="right"> 
    <input id="id_keyword" name="keyword" size="30" type="text" value="{$return.keyword}"
    validate="{ldelim}required:1,minlen:3,maxlen:32{rdelim}"
    />
    <label class="error" for="id_keyword">Обязательное поле</label>      
    &nbsp; <input style="" type="submit" value="{$lang.search_it}" style="padding:2px 2px 2px 2px" />
    </p>


</form>

{if $return.posts}

<div class="clear"><!-- --></div>
<br/>
<br/>

{*include file="inc/title.tpl" title="`$lang._content.search_results`"*}
                          
{include file="sat/search/result_list.tpl"} 

{/if}



{include file="pagination.tpl" pagination=$return.posts.pagination}

<script type="text/javascript"> 

$(function(){
    
$("#id_keyword").autocomplete('{$config.site_url}search/suggest/', {
        dataType: "json",
        minChars: 2,
        //width: 130,
        selectFirst: false,
        matchContains: false,
        autoFill: false,
        cacheLength: 10,
        

        parse: function(data) {
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: row.keyword,
                    result: row.keyword
                }
            });
        },
        
        formatItem: function(item) {
           return item.keyword;
        }
 

    }) ;

    

    
});            

</script>

