<?php

/**
 * Commentable
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

namespace SatCMS\Modules\Sat\Classes\Behaviors;

class Commentable extends BaseAttachs {

    // Options
    protected $key = 'comments';
    protected $model_class = 'sat.comment';

    /** @var  \sat_comment_collection */
    protected $attachs;

    protected $deps = array('user');

    /**
     * Render
     * @return array|bool|void
     */
    function render() {
        $attachs = $this->get_attachs();

        \core::time_check('comments-tree', 1, 1);

        $attachs->make_tree();

        \core::time_check('comments-tree');

        return $attachs->render();
    }

    function remove_before() {
        // remove comments
    }

}