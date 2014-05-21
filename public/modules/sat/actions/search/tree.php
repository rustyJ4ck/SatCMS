<?php

/**
 * @package    inforcom
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: tree.php,v 1.1.2.1 2014/03/22 20:11:07 jack Exp $
 */

/**
 * Suggest data
 */
class sat_search_tree_action extends controller_action {

    function run() {

        /**@var tf_sat $sat */
        $sat = core::module('sat');

        $site = $sat->get_current_site();

        $json_file = loader::get_public('assets/' . $site->domain . '.json');

        $last_mod = @filemtime($json_file);

        $tree_last_mod = 0;

        if ($last_mod) {

            $last_node = $sat->get_node_handle()
                ->set_where('site_id = %d', $sat->get_current_site_id())
                ->set_order('updated_at DESC')
                ->load_first();

            $tree_last_mod = $last_node ? $last_node->updated_at : 0;

            core::dprint(__METHOD__ . ': cached');

            // uptodate
            if ($tree_last_mod <= $last_mod) {
                $this->renderer->set_ajax_answer(
                    file_get_contents($json_file)
                )->ajax_flush();

                return;
            }

        }

        core::dprint(__METHOD__ . ': fetch!');

        $tree = $sat->get_current_site_tree();

        $allowedKeys = array('title', 'url');

        array_walk($tree, function(&$v) use ($allowedKeys) {
            $v = (array_intersect_key($v, array_flip($allowedKeys)));
        });

        $tree = array_values($tree);

        // cache
        file_put_contents($json_file, functions::json_encode($tree));

        $this->renderer->set_ajax_answer($tree)->ajax_flush();
    }
}