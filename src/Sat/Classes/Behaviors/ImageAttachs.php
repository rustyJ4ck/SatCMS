<?php

/**
 * Commentable
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

namespace SatCMS\Sat\Classes\Behaviors;

use core;

class ImageAttachs extends BaseAttachs {

    // Options
    protected $key = 'images';
    protected $model_class = 'sat.node_image';

}