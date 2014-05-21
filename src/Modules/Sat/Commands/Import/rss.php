<?php

namespace SatCMS\Modules\Sat\Commands\Import;

use core, tf_exception;

/*
$imp = new rss_importer();
// http://lenta.ru/rss
$imp->import('http://feeds.newsru.com/com/www/news/big');
*/

/*
http://feeds.newsru.com/com/www/news/big

<channel>
<item>
<title>Аномальная жара отступила: тут же потухли пожары и уменьшилось число больных</title>
<link>http://www.newsru.com/russia/19aug2010/moscow_heat_back.html</link>
<description>Долгожданное похолодание пришло в Москву, где за лето поставлено более 20 рекордов. Температура в четверг не поднимется выше +23 градусов, а в пятницу - и вовсе остановится на +17. До выходных будут идти дожди.</description>
<category>В России</category>
<pubDate>Thu, 19 Aug 2010 14:06:00 +0400</pubDate>
</item>
*/

class Rss {

    /**parent site*/
    private $_site_id;
    private $_site;
    /**parent node*/
    private $_node_id;
    private $_node;

    private $_encoding = 'UTF-8';

    /** @var \sat_node_collection */
    private $collection;

    /** @var \tf_sat */
    private $psat;

    function __construct($siteID, $nodeID) {

        $this->_site_id = $siteID;
        $this->_node_id = $nodeID;

        // init core
        $core = core::get_instance()->init();

        $this->psat = core::module('sat');
        $this->collection = $this->psat->get_node_handle();

        if (!($this->_site = $this->psat->get_site($this->_site_id))) {
            throw new tf_exception('Bad site');
        }

        if (!($this->_node = $this->psat->get_node($this->_node_id))) {
            throw new tf_exception('Bad node');
        }

    }

    function iconv($t) {
        if ($this->_encoding != 'UTF-8') {
            $t = iconv($this->_encoding, 'UTF-8', $t);
        }
        return $t;
    }

    function import($url) {

        $response = file_get_contents($url);

        if (!$response) {
            throw new tf_exception('Bad response');
        }

        $xmldoc = new \SimpleXMLElement($response);

        // <pubDate>Thu, 15 May 2014 11:44:00 +0400</pubDate>
        // <enclosure url="http://icdn.lenta.ru/images/2014/05/15/10/20140515104014269/pic_6c38fae752e8c259dd322777aef21dcf.jpg" length="43260" type="image/jpeg"/>

        /** @var \SimpleXMLElement $item */
        foreach ($xmldoc->channel->item as $item) {

            $data = array(
                'text'          => (string)$item->description
              , 'title'         => (string)$item->title
              , 'image_url'     => ($item->enclosure ? (string)$item->xpath('//enclosure/@url')[0] : false)
              , 'updated_at'    => strtotime((string)$item->pubDate)
            );

            $this->create($data);
        }

        // upd.counters
        $this->psat->update_tree($this->_site_id, true);

    }

    /**
     * @param array $data initial
     * @param mixed $parent
     * @return node_collection
     */
    function create($data) {

        $data['pid'] = $this->_node_id;
        $data['owner_uid'] = 1;
        $data['active']    = 1;
        $data['site_id']   = $this->_site_id;

        $result = $this->collection->create($data);

        core::dprint(['created: %d|%s', $result, $data['title']], core::E_MESSAGE);

        break;

        return $this->collection->get_last_item();
    }


}
