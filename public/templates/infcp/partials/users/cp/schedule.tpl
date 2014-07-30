
{*"diflang.days.1"|i18n*}

<style>

table.diflang-sched-table-0{
    border-collapse: collapse;
    width:95%;
}

table.diflang-sched-table-0 > tbody > tr > td{
    padding:0;
    width:14%
}

table.diflang-sched-table-0 > thead > tr > td{
    padding:15px;
    font-size:12px; 
    color:darkgray;
    font-weight:normal;        
}


table.diflang-sched-table-1{
    margin-right:2px;
    margin-bottom:4px;
    height:200px;
}


/* Table 1 Style */
table.diflang-sched-table-1{
    font-size: 12px;    
    line-height: 1.4em;
    font-style: normal;
    border-collapse:separate;
    width:100%;
}
.diflang-sched-table-1 thead th{
    padding:15px;
    color:#fff;
    text-shadow:1px 1px 1px #gray;
    border:1px solid #93CE37;
    border-bottom:3px solid #9ED929;
    background-color:#9DD929;
    background:-webkit-gradient(
        linear,
        left bottom,
        left top,
        color-stop(0.02, rgb(123,192,67)),
        color-stop(0.51, rgb(139,198,66))
       
        );
    background: -moz-linear-gradient(
        center bottom,
        rgb(123,192,67) 2%,
        rgb(139,198,66) 51%
       
        );
    -webkit-border-top-left-radius:5px;
    -webkit-border-top-right-radius:5px;
    -moz-border-radius:5px 5px 0px 0px;
    border-top-left-radius:5px;
    border-top-right-radius:5px;
}
.diflang-sched-table-1 thead th:empty{
    background:transparent;
    border:none;
}
.diflang-sched-table-1 tbody th{
    color:#fff;
    text-shadow:1px 1px 1px gray;
    background-color:#8c4b9a;
    padding:0px 10px;
    background:-webkit-gradient(
        linear,
        left bottom,
        right top,
        color-stop(0.02, #8c4b9a),
        color-stop(0.51, #ac68bb)                      

        );
    background: -moz-linear-gradient(
        left bottom,
        #8c4b9a 2%,
        #ac68bb 51%        
        );
    -moz-border-radius:5px 5px 0px 0px;
    -webkit-border-top-left-radius:5px;
    -webkit-border-top-left-radius:5px;
    border-top-left-radius:5px;
    border-top-right-radius:5px;
}
.diflang-sched-table-1 tfoot td{
    color: #9CD009;
    font-size:32px;
    text-align:center;
    padding:10px 0px;
    text-shadow:1px 1px 1px #444;
}
.diflang-sched-table-1 tfoot th{
    color:#666;
}
.diflang-sched-table-1 tbody td
, .diflang-sched-table-news .dn-text
{
    padding:10px;
    text-align:center;
    background-color:#e6d6ea;
    b1order: 2px solid #E7EFE0;
    -m1oz-border-radius:2px;
    -w1ebkit-border-radius:2px;
    bo1rder-radius:2px;
    color:#666;
    font-size: 10px;
    font-weight:normal;
    
    box-shadow: inset -2px -2px 3px rgba(0,0,0,0.1)
        ;
        
background: -moz-linear-gradient(top,  rgba(230,214,234,1) 0%, rgba(230,214,234,0.54) 46%, rgba(229,229,229,0) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(230,214,234,1)), color-stop(46%,rgba(230,214,234,0.54)), color-stop(100%,rgba(229,229,229,0))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  rgba(230,214,234,1) 0%,rgba(230,214,234,0.54) 46%,rgba(229,229,229,0) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  rgba(230,214,234,1) 0%,rgba(230,214,234,0.54) 46%,rgba(229,229,229,0) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  rgba(230,214,234,1) 0%,rgba(230,214,234,0.54) 46%,rgba(229,229,229,0) 100%); /* IE10+ */
background: linear-gradient(to bottom,  rgba(230,214,234,1) 0%,rgba(230,214,234,0.54) 46%,rgba(229,229,229,0) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e6d6ea', endColorstr='#00e5e5e5',GradientType=0 ); /* IE6-9 */

}

.diflang-sched-table-1 tbody td.ds-date {
    height:15px;
}

.diflang-sched-table-1 tbody td.ds-title , 
.diflang-sched-table-1 tbody td.ds-hw {
    height:95px;
    padding-top:2px;
}

.diflang-sched-table-1 tbody td.ds-title > div, .diflang-sched-table-1 tbody td.ds-hw > div {
    height:80px;
    overflow:hidden;
    cursor:pointer;
}

.diflang-sched-table-1 tbody td.ds-hw, .diflang-sched-table-1 tbody td.ds-title {
    text-align:left;
}

.diflang-sched-table-1 tbody td.ds-hw-head, .diflang-sched-table-1 tbody td.ds-title-head {
    background-color: #d6c0db;    
}

.ds-title-expand {
    position:absolute;
    z-index:1;
    border:none;
    width:220px;
    height:200px !important;
    margin-top:-40px;
    margin-left:-6px;
    background:white;
    cursor:pointer;
    padding:8px;
    border-radius:5px;
    border:1px solid #8c4b9a;
   
}

.diflang-sched-table-news {
    width:80%;
}

.diflang-sched-table-news .dn-text {
    border-radius:8px;
    text-align:left;
    display:inline-block;
    margin-bottom:10px;
    font-size:12px;   
}

.diflang-sched-table-news .dn-title {
    text-indent:10px; 
    color:darkgray;
    font-weight:normal; 
    margin-bottom:4px;
}

#cp-news-block {
    margin-left:10%;
}

</style>

<!--[if IE]>
<style>
 .diflang-sched-table-1 tbody td.ds-date {
    padding-left:0px;
    padding-right:0px;
}

.diflang-sched-table-1 tbody td,
.diflang-sched-table-news .dn-text {
    background-color:transparent;
}
</style>
<![endif]-->

<script>

$(function(){
   $('.ds-title>div,.ds-hw>div').click(function(){
       if ($(this).html().length) {
           $('.ds-title-expand').removeClass('ds-title-expand');
           $(this).toggleClass('ds-title-expand');
       }
   }) 
})

</script>




{if !empty($tpl_diflang_news)}
<div id="cp-news-block">
<h2>Новости для группы &laquo;{$user.group.title}&raquo;</h2>

<ul class="diflang-sched-table-news">
    
{foreach $tpl_diflang_news as $news}

<div class="dn-text">

<li><div class="dn-title">{if !$news.b_global}{$news.date_posted} {/if}{$news.title}</div>
{$news.text}</div>
{*
<tr><td class="ds-news-title">{$news.text}</td>                        </tr>
<tr><td class="ds-news-spacer">&nbsp;</td>                        </tr>
*}
</li>
{/foreach}
</ul>

</div>
{/if}


<h1 align="center">Расписание</h1>


{foreach $tpl_diflang_schedule_week as $week}

<table  class="diflang-sched-table-0">
<thead>
<tr><td colspan="7" align="center">{$week.date|date_format_ex:'d'} - {$week.date_till|date_format_ex:'d m/Y'}</td></tr>
</thead>
<tr>
    
    
    {foreach $week.days as $day}
    <td>
    <table class="diflang-sched-table-1">
    
    <tr><th class="ds-ds">{$day.day_string}</th></tr>
    <tr><td class="ds-date">{$day.date|date_format_ex:"H:i"} {if $day.day}№{$day.day}{/if} {$day.date|date_format_ex:"d/m/Y"}</td>               </tr>
    <tr><td class="ds-title-head">Тема занятия</td></tr>
    <tr><td class="ds-title"><div>{$day.title}</div></td>                        </tr>
    <tr><td class="ds-hw-head">Домашняя работа</td></tr>
    <tr><td class="ds-hw"><div>{$day.hw}</div></td>                      </tr>
    
    
    </table>
    </td>    
    {/foreach}
    

</tr>
</table>

{/foreach}


{*
<code style="height:600px;overflow:scroll;display:block;">
{$tpl_diflang_schedule_week|debug_print_var}
</code>
*}


