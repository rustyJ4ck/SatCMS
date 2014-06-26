<?php

/**
 * News collection
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

use SatCMS\Sat\Classes\NewsArticle\CategoryCollection;

class test_article_category_collection extends CategoryCollection {

    public $child_model = "test.article";
}
