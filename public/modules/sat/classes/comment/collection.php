<?php
    
/**
 * Comments
 * 
 * @package    satcms
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.1.4.1 2012/05/17 08:58:22 Vova Exp $
 */  
 
class sat_comment_collection extends abs_collection {

    const CTYPE = 'sat.comment';

    /**
    * @var abs_collection_item wich comments belong
    */
    private $_parent;
    
    public function set_parent($p) {
        $this->_parent = $p;
        return $this;
    } 

    /**
    * Get parent post
    */
    function get_parent() {
        return $this->_parent;
    }
    
    /**
    * Load
    */

    /*
    function load($auto_tree = false) {
        $ret = parent::load();
        if ($auto_tree) 
            $this->make_tree(); 
        return $ret;
    }
    */
    
    /**
    * Loads comments for post
    * @param abs_collection_item
    */
    function load_for($post) {               
        $this->set_parent($post);
        $this->set_where('pid = %d AND ctype_id = %d', $post->get_id(), $post->get_ctype_id());
        $this->load();        
        $this->make_tree(); 
        return $this;
    }    

    /**
    * Loads comments for pid+ctype
    */
    function load_for_pid($pid, $ctype = 200) {               
        $this->set_where('pid = %d AND ctype_id = %d', $pid, $ctype)
            ->load()
            ->make_tree();
        return $this;
    }    
    
    function before_create(&$data) {
        if (!isset($data['ctype_id'])) $data['ctype_id'] = $this->parent->get_ctype_id();
        if (empty($data['ctype_id'])) throw new content_exception('Cant create comment without specified ctype');
    }
    
    function render_parents() { 
        foreach ($this as $item) $item->render_parent();
        return $this;
    }
    
    /**
    * Load rates
    */
    function load_rates() {
                        
        $logged = core::lib('auth')->logged_in();
        $user   = core::lib('auth')->get_user();
        $rates  = false;
        
/*   
// disable rating

        if ($logged) {
            $rates = core::module('content')->get_rates(
                "(SELECT id FROM " . $this->get_table() . " WHERE pid = " . $post->id . ")"
                , self::CTYPE, core::lib('auth')->get_user());
        }
        
        foreach ($this->items as $item) {
            if (!$logged || $user->id == $item->user_id || $rates->is_rated($item->id))
            $item->disable_rating();
        }
*/        
        return $this;
    }
    
    /**
    * Get last comment time
    * @param integer pid
    * @param integer uid
    * @return unixtime or zero
    */
    function get_last_time($pid, $uid) {
        $sql = "SELECT created_at FROM " . $this->get_table() . " WHERE pid = {$pid} AND user_id = {$uid} ORDER BY created_at DESC LIMIT 1";
        $row = $this->db->fetchrow($this->db->query($sql));
        return @intval($row['created_at']);
    }
    
    /**
    * @refactor ?
    * Make it tree!
    * Recursive
    * Warning: items structure changes!
    */
    function make_tree($tpid = 0, $level = 0) {
        
        static $out;

        //
        // intro
        // 
        if (0 === $tpid) {
            $out   = array();
        }    
        
        // order by tpid
        foreach ($this->items as $item) {
            if ($tpid == $item->tpid) {  
                /*
                if ($tpid) {
                    
                    // childrends
                    $tmp_parent = $this->get_item_by_id($tpid);                    
                    if ($tmp_parent) {
                        // @todo tree c_children recalc!
                        $tmp_parent->c_children = (int)$tmp_parent->get_data('c_children_ids') + 1;
                        if (false === $tmp_parent->get_data('c_children_ids')) $tmp_parent->c_children_ids = array();
                        $tmp_parent->c_children_ids = array_merge($tmp_parent->c_children_ids, array($item->id));
                        // Notice: Indirect modification of overloaded property comments_item::$data has no effect 
                        // array_push($tmp_parent->data['c_children_ids'], $item->id);
                    }
                }
                */
                
                $item->level = $level;                
                $out[] = $item->id; 
                
                $this->make_tree($item->id, (1 + $level));                                
                
            }
        }       
        
        
        //
        // outro        
        // final make tree
        // 
        if (0 === $tpid) {

            $temp_array = array(); 
            
            $new_items = array();
            foreach ($out as $id) {
                $tmp = $this->get_item_by_id($id);
                $tmp->c_children = 0;
                $temp_array[] = array($id, $tmp->tpid);
            } 

            // count recursive
            for ($cnt = (count($temp_array)-1), $i = $cnt; $i > 0; $i--) {
                $this_item = $temp_array[$i];
                if ($this_item[1]) {
                    $tmp = $this->get_item_by_id($this_item[1]);
                    $tmp_child = $this->get_item_by_id($this_item[0]);
                    $tmp->c_children += (false === $this->get_item_by_prop('tpid', $this_item[0]) ? 1 : $tmp_child->c_children);
                    if ($tmp->tpid) {
                        $tmp_parent = $this->get_item_by_id($tmp->tpid);
                        $tmp_parent->c_children++;
                    }
                }
            }            
            
            foreach ($out as $id) {
                $new_items[] = ($tmp = $this->get_item_by_id($id));
                $temp_array[] = array($id, $tmp->tpid);
            } 

            
            // Parse nesting containers
            foreach ($new_items as $index => $item) {                  
                $prev = isset($new_items[$index - 1]) ? $new_items[$index - 1] : false;
                $next = isset($new_items[$index + 1]) ? $new_items[$index + 1] : false;

                $close_levels = 0;
                if (false === $next || $next->level <= $item->level) $close_levels = $item->level - (false !== $next ? $next->level : 0) + 1;
                $item->close_levels = $close_levels;               
            }
            
            $this->items = $new_items;
            
            // var_dump($temp_array);
            
            
        }
        
        return $out;
    }
    
    /**
    * Get last items 
    */
    function get_last($limit = 20) {
        $this->clear();
        $this->set_limit($limit);
        $this->set_order('created_at DESC');
        $this->load();
        return $this->get_items();
    }
    
    /**
    * toggle_delete_flag
    */
    function toggle_delete_flag($id) {
        $sql = "UPDATE LOW_PRIORITY " . $this->get_table() . " SET deleted = NOT deleted WHERE id = " . (int)$id;
        $this->db->sql_query($sql);
    }
    
}