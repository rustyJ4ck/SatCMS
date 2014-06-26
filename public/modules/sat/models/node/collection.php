<?php
  
/**
 * @package    sestat
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.2.6.2.12 2013/12/02 10:35:34 Vova Exp $
 */  
  
define('SAT_TREE_OPTIMIZE', 1);
  
class sat_node_collection extends model_collection {
        
    const CTYPE = 'sat.node';

    protected $behaviors = array(
        'Sat.Commentable',
        'ExtraFS.Fields',
        'Sat.RemoteImage'
    );

    /**
     * Approve switch
     */
    function toggle_active($id, $value) {
        $this->toggle_flag('active', $id, $value);
    }

    /**
     * Switch
     */
    function toggle_flag($flagID, $id, $value) {
        $this->update_item_fields($id,
            array($flagID => $value)
        );
    }
    
    /**
    * Get tree for site 
    * 
    * WARNING!!!
    * This routine explicitly REBUILD tree from DATABASE
    * High memory consume
    * 
    * @see tf_sat::get_tree for getting cached tree
    * 
    * @return array(
    *   tf_sat::TREE_URL => array('url' => $NODE_ID, ...),
    *   tf_sat::TREE_ID  => array('ID' => $NODE, ...)
    * )
    * 
    */
    private static $_tree_index;
    private static $_tree_index_counter;
    
    function get_tree($site_id = 0) {
        
        $with_spl = class_exists('SplFixedArray');
        
        $count = $this->set_where('site_id = %d', $site_id)->count_sql();
        
        if (!$count) return;

        self::$_tree_index_counter = -1;
        self::$_tree_index = $with_spl ? new SplFixedArray(1 + $count) : array();
        $nodes = array();        
        
        $sql = sprintf("SELECT id, pid, title, html_title, url, c_children, active, b_system, b_featured FROM %s WHERE site_id = %d ORDER BY pid, position"
            , $this->get_table()
            , $site_id
        );
           
        $return = array(
            tf_sat::TREE_URL => array()
           , tf_sat::TREE_ID => array()
        );
        
        if ($query_id = $this->db->query($sql)) {
            while ($row = $this->db->fetch_row()) {
                $nodes [(int)$row['id']] = $row;            
            }
        }
        
        
        core::dprint(array('count: %d, nodes: %d', $count,count($nodes)));
        
        if (empty($nodes)) return $return;
        
        $item =  array(
              'url'         => ''
            , 'c_children'  => 1
            , 'id'          => 0
            , 'level'       => -1
        );       

        $this->_get_tree($item, $nodes);    
        
        core::dprint(array('_tree_index_counter: %d, _tree_index: %d, nodes: %d', self::$_tree_index_counter, count(self::$_tree_index), count($nodes)));
        
        // Note: splFixedArray return wrong count(self::$_tree_index)
        // $count = $with_spl ? ($count - 1) : $count;
        
        // 0 goes for root, skip it
        for ($index = 1; $index <= $count ; $index++) {
                
            $check_ti = isset(self::$_tree_index[$index]);
            $check_node = $check_ti && isset($nodes[self::$_tree_index[$index]]);
                  
            if (!$check_ti || !$check_node) {
                core::dprint(array('Waring! Update tree goes wrong, index %d | %s, %s',
                $index, ($check_ti?'Y':'N'), ($check_node?'Y':'N')   ));
            }                      
            else {
                $r = $nodes[self::$_tree_index[$index]];
                $r['url'] = /*site_url*/ '/' . ($r['url']);
                $return[tf_sat::TREE_URL][$r['url']] = $r['id'];
                $return[tf_sat::TREE_ID][$r['id']] = $r;
            }
        }
        
        core::dprint(array('$return[tf_sat::TREE_ID]: %d', count($return[tf_sat::TREE_ID])));
      
        /*
        //debug:
        core::var_dump(
            $count, 
            self::$_tree_index_counter,
            count((array)self::$_tree_index),
            array_diff((array)self::$_tree_index, $return[tf_sat::TREE_URL])            
        );
        */
        
        unset($nodes);
        self::$_tree_index = null;
        
        if ($count != count($return[tf_sat::TREE_ID])) {
           core::dprint(array('Update tree count mismatch! ALL: %d <> INDEX: %d', $count, count($return[tf_sat::TREE_ID])), core::E_CRIT);
        }
        
        return $return;
    }
    
    /**
    * make plain tree (Recursive)
    * Build full urls and assign levels to items
    * 
    * @param mixed $item
    * @param mixed $tree  (nodes ordered by pid, position)
    * @param mixed $parent
    */
    private function _get_tree(&$item, &$tree, $parent = null) {   
        
        if (!isset($item['level'])) $item['level'] = 0;
        
        $item['_url'] =  $item['url'];   
        
        if ($parent) {
            $item['level']  = $parent['level'] + 1;
            $item['url']    = $parent['url']
                ? $parent['url'] . '/' . $item['url']
                : $item['url'];
        } 

        // splFixedArray [] starts with -1
        self::$_tree_index [++self::$_tree_index_counter] = (int)$item['id'];  
        
        // core::dprint(array('-- %3d / %3d : %s [%d]', $item['id'], @$item['pid'], @$item['title'], $item['c_children']));
        
        if (!$item['c_children']) {
            $item['url'] .= '.html';
        }
        else {                        
                $matched = false;
                                
                foreach ($tree as &$pitem) {
                    if ($pitem['pid'] == $item['id']) {
                        $this->_get_tree($pitem, $tree, $item);
                        $matched = true;
                    }
                    else {
                        // break if there are no siblings
                        if ($matched) break;
                    }                        
                }
            
            $item['url'] .= '/';
        }
    }
    
    
    /**
    * @param int $ID
    * @return sat_node_item
    */
    function get_managed_item($id) {

        $item = $this->get_item_by_id($id);

        if (!$item) {
            $item = core::module('sat')->get_node($id);
        }

        return $item;
    }
    
    /** @return sat_node_collection */
    function get_parents(/*sat_node_item*/ $node) {
        
        if (is_numeric($node)) $node = $this->get_managed_item($node);
        
        $chain = array();
        
        if (!$node) {
            throw new collection_exception('Invalid node');
        }
        
        while ($p = $node->get_parent()) {
            array_unshift($chain, $p);
            $node = $p;            
        }
             
        $_this = clone($this);     
        $_this->set_items($chain);      
        return $_this;         
    }
    
    /** 
    * Sync children count for site or node-id 
    * Warn! if all params empty, full sinc performed
    */
    function sync_children_count($site_id = 0, $id = 0) {
        
        /*
            sqlite: not support prefixes?
            
            @todo test for mysql!
            
            UPDATE sat_sat_node  
            SET c_children = (SELECT COUNT(*) FROM (SELECT id, pid FROM sat_sat_node) as p3 WHERE p3.pid = sat_sat_node.id) 
            WHERE site_id = 1;                                                                             ------------ not p1!
        */
        
        $table = $this->get_table();
        
        $sql = array("UPDATE %s SET c_children = (SELECT COUNT(*) FROM (SELECT id, pid FROM %s) as p3 WHERE p3.pid = {$table}.id)"
            , $table
            , $table
            , $table
        );
        
        if ($id) {
            $sql[0] .= sprintf(" WHERE {$table}.id = %d;", $id);
        }
        else
        if ($site_id) {
            $sql[0] .= sprintf(" WHERE {$table}.site_id = %d;", $site_id);
        }
            
        $this->db->query($sql);
    } 
    
    function clear_static($with_parent = true) {  
        foreach ($this as $item) $item->clear_static($with_parent);
    }
    
}