<?php
/**
* SatCMS  http://satcms.ru/
* @author Golovkin Vladimir <rustyj4ck@gmail.com> http://www.skillz.ru
*/

class sat_content_type_item extends model_item {

    /**
     * Make url
     */
    function make_urls() {

        if (core::in_editor()) {
            $this->append_urls('children', sprintf('?m=sat&c=content&type_id=%d', $this->id));
            // $this->append_urls('self', sprintf('?m=sat&c=content&conteny_type=%d', $this->id));
        }

    }
}