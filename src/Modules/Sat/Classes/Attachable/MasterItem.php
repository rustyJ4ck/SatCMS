<?php

namespace SatCMS\Modules\Sat\Classes\Attachable;

use abs_collection_item;

class MasterItem extends abs_collection_item {

    /**
     * @var MasterCollection
     */
    protected $container;

    function remove_after() {

    }

    function modify_after($data) {

        if ($data['attach_sid']) {

            foreach ($this->container->attachables as $collection) {
                $collection->update_pid($data['attach_sid'], $this->id);
                //update_pid($sid, $pid)
            }

        }

        parent::modify_after($data);

    }

}