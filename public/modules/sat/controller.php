<?php

/**
 * Core contoller
 * 
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: controller.php,v 1.1.2.1.4.1 2012/09/14 06:20:57 Vova Exp $
 */
 
/**
* @package content
*/
class sat_controller extends module_controller {

    /** @var  tf_sat  */
    protected $context;


    /**
     * sites all
     */
    function action_api_editor_sites() {
        $this->renderer
            ->set_ajax_answer($this->context->get_sites()->as_json())
            ->ajax_flush();
    }

    /**
     * Get tree
     * @todo only for editor!
     */
    function action_api_editor_node_tree() {

        $id = 0 + $this->_params->id;

        if (!$id) {
            throw new controller_exception('Bad ID');
        }

        $tree = array_values($this->context->get_tree($id, tf_sat::TREE_ID));

        $this->renderer
            ->set_ajax_answer($tree)
            ->ajax_flush();
    }

    /**
     * News section
     */
    function section_news() {
        $this->renderer->set_page_template('pages/news');
    }
}