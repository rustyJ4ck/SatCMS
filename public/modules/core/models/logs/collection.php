<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.3 2010/07/21 17:57:16 surg30n Exp $
 */
class logs_collection extends model_collection {


    protected $fields = array(
        'id'   => array('type' => 'numeric')
        , 'title'  => array('type' => 'text')
        , 'date'   => array('type' => 'unixtime')
        , 'data_formatted'   => array('type' => 'virtual', 'title' => 'Описание')
        , 'data'   => array('type' => 'text')
        , 'url'    => array('type' => 'text')
        , 'domain' => array('type' => 'text')
        , 'error'  => array('type' => 'boolean')
    );

    function construct_before(&$config) {

        if (empty($config['order_sql'])) {
            $config['order_sql'] = 'date DESC';
        }

        $this->formats
            ->set('editor.list.data_formatted', array(
                'hidden' => true,
                'type' => 'virtual', 'method' => function($self) {
                        return '<div class=scrollable><code>' . nl2br($self->get_data('data')) . '</code></div>';
                    }
            ))
            ->set('editor.list.data.hidden', true)
        ;

    }

    /**
     * Remove obsolete data
     * @param integer seconds
     */
    function fix_older($time /*$days*/) {

        // $time = time() - 60 * 24 * $days;

        $this->clear(true);
        $this->is_delayed(true);
        $this->set_where('date < %d', $time);
        $this->remove_all_fast();
    }

    /**
     * Test for maximum records
     */
    function fix_overload($max) {
        $this->clear(true);
        $count = $this->count_sql();
        $max   = $max ? $max : 1024;
        if ($count > $max) {
            $max = $count - $max;
            $sql = "DELETE LOW_PRIORITY FROM " . $this->get_table() . " ORDER BY time ASC LIMIT " . $max;
            $this->get_db()->sql_query($sql);
        }
    }
}