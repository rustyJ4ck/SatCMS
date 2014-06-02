<?php

namespace SatCMS\Modules\Sat\Import;

use core, tf_exception;

abstract class ImportDriver {

    /**parent site*/
    protected $site_id;
    protected $site;

    /**parent node*/
    protected $node_id;

    /** @var  \sat_node_item */
    protected $node;

    protected $encoding = 'UTF-8';

    /** @var \sat_node_collection */
    protected $collection;

    /** @var \tf_sat */
    protected $psat;

    protected $dry_run = false;
    protected $with_clean = false;
    protected $limit = 0;

    protected $counter = 0;

    function __construct($siteID, $nodeID) {

        $this->site_id = $siteID;
        $this->node_id = $nodeID;

        // init core
        $core = core::selfie();

        $this->psat = core::module('sat');
        $this->collection = $this->psat->get_node_handle();

        if (!($this->site = $this->psat->get_site($this->site_id))) {
            throw new tf_exception('Bad site');
        }

        if (!($this->node = $this->psat->get_node($this->node_id))) {
            throw new tf_exception('Bad node');
        }
    }

    function dry_run($flag) {
        $this->dry_run = $flag;
        return $this;
    }

    function with_clean($flag) {
        $this->with_clean = $flag;
        return $this;
    }

    function limit($n) {
        $this->limit = $n;
        return $this;
    }

    /** Cyrillic shit */
    function detect_encoding($string, $pattern_size = 50) {

        if (preg_match('//u', $string)) {
            return 'UTF-8';
        }

        $list = array('CP1251', 'UTF-8', 'ASCII', '855', 'KOI8R', 'ISO-IR-111', 'CP866', 'KOI8U');
        $c    = strlen($string);
        if ($c > $pattern_size) {
            $string = substr($string, floor(($c - $pattern_size) / 2), $pattern_size);
            $c      = $pattern_size;
        }

        $reg1 = '/(\xE0|\xE5|\xE8|\xEE|\xF3|\xFB|\xFD|\xFE|\xFF)/i';
        $reg2 = '/(\xE1|\xE2|\xE3|\xE4|\xE6|\xE7|\xE9|\xEA|\xEB|\xEC|\xED|\xEF|\xF0|\xF1|\xF2|\xF4|\xF5|\xF6|\xF7|\xF8|\xF9|\xFA|\xFC)/i';

        $mk  = 10000;
        $enc = 'ascii';
        foreach ($list as $item) {
            $sample1 = @iconv($item, 'CP1251', $string);
            $gl      = @preg_match_all($reg1, $sample1, $arr);
            $sl      = @preg_match_all($reg2, $sample1, $arr);
            if (!$gl || !$sl) continue;
            $k = abs(3 - ($sl / $gl));
            $k += $c - $gl - $sl;
            if ($k < $mk) {
                $enc = $item;
                $mk  = $k;
            }
        }

        return $enc;
    }

    function iconv($str) {

        if (empty($str)) return '';

        // console encodings normalize
        // $charset = mb_detect_encoding($str, "UTF-8, WINDOWS-1251, CP866");
        $charset = $this->detect_encoding($str);

        if ($charset != 'UTF-8') {
            $str = iconv($charset, 'UTF-8', $str);
        }
        return $str;
    }

    function clean() {
        $this->node->get_children()->remove_all();
    }

    abstract function import($params);
    
    /**
     * Call this on finish
     */
    function done() {
        // upd.counters
        $this->psat->update_tree($this->site_id, true);

        core::dprint('Done');
    }

    /**
     * @param array $data initial
     * @param mixed $parent
     * @return node_collection
     */
    function create($data, $isDir = false) {

        $data['pid'] = @$data['pid'] ?: $this->node_id;
        $data['owner_uid'] = 1;
        $data['active']    = 1;
        $data['site_id']   = $this->site_id;

        $data['title']          = $this->iconv(@$data['title']);

        $data['text']           = htmlspecialchars($this->iconv(@$data['text']), ENT_COMPAT, 'UTF-8');
        $data['description']    = htmlspecialchars($this->iconv(@$data['description']), ENT_COMPAT, 'UTF-8');

        $this->counter++;

        // skip
        if ($this->limit && $this->counter > $this->limit) {
            return false;
        }

        if ($this->dry_run) {
            $result = $this->counter;
        } else {
            $result = $this->collection->create($data);
        }

        core::dprint(array('created: %-4d|%-4d|%s%s', $data['pid'], $result, ($isDir?'*':''), $data['title']), core::E_MESSAGE);

        return $result;
    }


}
