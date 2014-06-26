<?php

/**
 * News collection
 *
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.9 2013/10/02 07:38:27 Vova Exp $
 */

use SatCMS\Sat\Classes\NewsArticle\NewsCollection;

/**
 * Class sat_news_collection
 */
class test_article_collection extends NewsCollection {

    const CTYPE = 'test.article';

    public $item_type = 'article';
    public $category_model = 'test.article_category';

/*
    protected $behaviors = array(
        'sat.commentable',
        'sat.imageAttachs',
        'sat.remoteImage'
    );
*/

}