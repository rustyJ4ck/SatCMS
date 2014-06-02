<?php

namespace SatCMS\Modules\Sat\Import;

use core, tf_exception;

/*
http://lenta.ru/rss
http://feeds.newsru.com/com/www/news/big
*/

/*
<channel>
<item>
<title>Аномальная жара отступила: тут же потухли пожары и уменьшилось число больных</title>
<link>http://www.newsru.com/russia/19aug2010/moscow_heat_back.html</link>
<description>Долгожданное похолодание пришло в Москву, где за лето поставлено более 20 рекордов. Температура в четверг не поднимется выше +23 градусов, а в пятницу - и вовсе остановится на +17. До выходных будут идти дожди.</description>
<category>В России</category>
<pubDate>Thu, 19 Aug 2010 14:06:00 +0400</pubDate>
</item>
*/

class RssDriver extends ImportDriver {

    function import($params) {

        $url = $params['url'];

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
              , 'author'        => (string)$item->link
            );

            $this->create($data);
        }

        // upd.counters
        $this->done();

    }




}
