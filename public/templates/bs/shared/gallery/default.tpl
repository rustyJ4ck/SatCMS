{*$return.news_item.images|debug_print_var*}

{if !empty($images)}

    <section class="gallery">

        {foreach $images as $item}

            <a href="{$item.image.url}" class="bootbox"
                data-title="{$item.title}"
                data-content="<a href='#' data-dismiss='modal'><img class='width100' src='{$item.image.url}'/></a>{if $item.comment}<div class='padded-top'>{$item.comment|e}</div>{/if}"
            >
                <img src="{$item.image.thumbnail.url}" class="thumbnail"/>
            </a>

        {/foreach}

    </section>

{/if}