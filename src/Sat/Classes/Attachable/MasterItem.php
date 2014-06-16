<?php

namespace SatCMS\Sat\Classes\Attachable;

use core, abs_collection_item;

class MasterItem extends abs_collection_item {

    /**
     * @var MasterCollection
     */
    protected $container;

    /*
    function remove_after() {
    }
    */

    function modify_after($data) {

        if (!empty($data['attach_sid'])) {

            /** @var SlaveCollection $collection */
            foreach ($this->container->attachables as $collection) {
                $collection->update_pid($data['attach_sid'], $this->id);
            }

        } {

            core::dprint('No attach-sid assigned in ' . get_class($this), core::E_ERROR);

        }

        parent::modify_after($data);

    }

}