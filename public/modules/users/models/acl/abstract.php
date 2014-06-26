<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: abstract.php,v 1.2 2010/07/21 17:57:23 surg30n Exp $
 */  


abstract class acl_collection_abstract extends model_collection {
    
    const UID_GROUP = 1;
    const UID_USER  = 0;
    
    const DEFAULT_ALLOW = false;
    const DEFAULT_ACTION = 'read';
    
    /** Access actions */
    protected $_actions;
    /** Access objects */
    protected $_objects;
    
    function get_actions() {
        return $this->_actions;
    }
    
    /**
    * [c][op][id] => 'title'
    * 
    * @param mixed $a
    */
    function set_objects($a) {
        $this->_auth_entries = $a;
        return $this;
    }
    
    function construct_after() {        
        if (!isset($this->_actions)) {
            $this->_actions = require __DIR__ . "/actions.php";
        }                           
    }    
    
    function get_action_id($name) {
        $id = null;
        foreach ($this->_actions as $v) {
            if ($v['name'] == $name) { $id = $v['id']; break; } 
        }
        return $id;
    }                  
    
    function load_for_user($id) {
        // user+group, user priority
    }
    
    /**
    * @param mixed $id
    * @return self
    */
    function load_for_group($id) {
        return $this->set_where('type = %d AND uid = %d'
            , self::UID_GROUP, $id
        )->load();
    }
    
    function get_auth_entries() {
        if (!isset($this->_auth_entries)) $this->load_auth_entries();
        return $this->_auth_entries;
    }
    
    // must be loaded                  
    function map_acls() {              
        foreach ($this->_objects as &$object) {
            foreach ($object['items'] as &$item) {
                // @todo section specified?
                $item['actions'] = $this->_actions;
                
                foreach ($item['actions'] as &$action) {
                    $action['allow'] = $this->_is_allow(
                        $object['name'], $item['id'], $action['id']
                    );
                }
            }
        }
        
    }
    
    /**
    * Load access objects
    * 
    * must set $this->_objects
    * after this system call @see self::map_acls() to map access list
    * 
    * @todo customize?
    */
    abstract function load_objects();
   
    /* 
        
        core::dprint('[W:] load auth entries');
        
        $this->_objects = array();
        
        $pr = core::module('sestat')->get_project_handle()->load();
        
        $this->_objects[] = array(
              'title'   => 'Проекты'
            , 'name'    => 'project'
            , 'values'  => array(array('id' => 0, 'title' => '*'))
            
        );
        foreach ($pr as $p) {
            $this->_objects[count($this->_auth_entries)-1]['values']
                []= array('id' => $p->id, 'title' => $p->title);
        }
        
    */
    
    function get_objects() {
        if (!isset($this->_objects)) {
            $this->load_objects();
            $this->map_acls();
        }
        return $this->_objects;
    }
    
    /**
    * Normalized array (keys)
    */
    function get_objects_simple() {
        $this->get_objects();
        
        $out = array();
        
        foreach ($this->_objects as $s) {
            $out[$s['name']] = array();
            foreach ($s['items'] as $a) {
                $out[$s['name']][$a['id']] = array();
                    foreach ($a['actions'] as $ac) {
                        $out[$s['name']][$a['id']][$ac['name']] = $ac['allow'];
                    }
            }
        }
            
        return $out;
        
    }
    
    /*  array(1) {
  [0]=>
  array(3) {
    ["title"]=>
    string(14) "Проекты"
    ["name"]=>
    string(7) "project"
    ["items"]=>
    array(4) {
      [0]=>
      array(3) {
        ["id"]=>
        int(0)
        ["title"]=>
        string(1) "*"
        ["actions"]=>
        array(4) {
          [1]=>
          array(4) {
            ["title"]=>
            string(16) "Создание"
            ["name"]=>
            string(6) "create"
            ["id"]=>
            int(1)
            ["allow"]=>
            bool(false)
          }
          [2]=>
          array(4) {
            ["title"]=>
            string(12) "Чтение"
            ["name"]=>
            string(4) "read"
            ["id"]=>
            int(2)
            ["allow"]=>
            bool(false)
          }
          [3]=>
          array(4) {
            ["title"]=>
            string(18) "Изменение"
            ["name"]=>
            string(6) "update"
            ["id"]=>
            int(3)
            ["allow"]=>
            bool(false)
          }
          [4]=>
          array(4) {
            ["title"]=>
            string(16) "Удаление"
            ["name"]=>
            string(6) "remove"
            ["id"]=>
            int(4)
            ["allow"]=>
            bool(false)
          }
        }
      }
      */
    
    /*                                                
    [проект] [создать][смотреть][править][удалять]
        [0]      x         x        x        x
       [id]
       ....
        [N]
    
    */
    
    function render($fields = null) {

        $this->get_objects();
        
        return array(
              'actions' => $this->_actions
            , 'objects' => $this->_objects
        );
        
    }
    
    /*
    array(1) { ["project"]=>  array(4) { 
        [0]=>  array(4) { [1]=>  string(1) "1" [2]=>  string(1) "1" [3]=>  string(1) "1" [4]=>  string(1) "1" 
    } 
    */
    
    function clear_for($uid, $uid_type = acl_collection_abstract::UID_GROUP) {
    }
    
    function update_group_array($acls, $uid) {
         // clear current
         // must be loaded for this group only
         $this->remove_all_fast();
         
         // just clear
         if (empty($acls)) return;
         
         foreach ($acls as $ks => $section) {
            foreach ($section as $ks_id => $action) {
                foreach ($action as $action_id => $allow) {
                $this->create_acl(array(
                      'section'    => $ks
                    , 'section_id' => $ks_id
                    , 'action'     => $action_id
                ), $uid, true);
            }
         }    
        }
    }  
    
    function create_acl($data, $uid, $allow, $uid_type = acl_collection_abstract::UID_GROUP) {
        $data['uid']    = $uid;
        $data['type']   = $uid_type;
        $data['allow']  = $allow;
        $this->create($data);               
    }
    
    function get_object($name) {
        foreach ($this->_objects as $v) {
            if ($v['name'] == $name) return $v;
        }
        return null;
    }
    
    function acls_in_section($section) {
        $t = $this->get_object($section);
        return count($t['items']);
    }
    
    /**
    * Renderer stuff
    * 
    * @param mixed $section
    * @param mixed $section_id
    * @param mixed $action
    */
    function _is_allow($section, $section_id = 0, $action = self::DEFAULT_ACTION) {       
        
        if (is_array($section)) {
            extract($section);
            if (!isset($section_id)) $section_id = 0;
            if (!isset($action))     $action = self::DEFAULT_ACTION;
        }
        
        if (empty($section)) return false;           
        
        $acl = $this->get_acl($section, $section_id, $action);
        
        return $acl ? $acl->allow : self::DEFAULT_ALLOW;
    }
    
    /**
    * Real acl check
    * 
    * @param mixed $section
    * @param mixed $section_id
    * @param mixed $action
    */
    function is_allow($section, $section_id = 0, $action = self::DEFAULT_ACTION) {       
        
        if (is_array($section)) {
            extract($section);
            if (!isset($section_id)) $section_id = 0;
            if (!isset($action))     $action = self::DEFAULT_ACTION;
        }
        
        if (empty($section)) return false;           
        
        if ($section_id && $this->acls_in_section($section) == 1) {
            // if used section_id when only root acl, get it
            $acl = $this->get_acl($section, 0, $action);
        }
        else         
            $acl = $this->get_acl($section, $section_id, $action);
        
         core::dprint(array('[AUTH] %s %s %s : %s : %s',
            $section, $section_id, $action, ($acl ? '+' : '-'), (($acl && $acl->allow) ? 'YES' : '')
        ), core::E_DEBUG2);        
        
        return $acl ? $acl->allow : self::DEFAULT_ALLOW;
    }
    
    function get_acl($section, $section_id, $action) {
        
        $action = (is_numeric($action) || ctype_digit($action)) 
            ? $action : $this->get_action_id($action);
            
        foreach ($this as $acl) {
            if ($acl->section       == $section
                && $acl->section_id == $section_id
                && $acl->action == $action) return $acl;
        }
        return null;
    }
    
    /*
    function get_acl($section, $section_id, $action, $uid, $uid_type = acl_collection_abstract::UID_GROUP) {
        return null;
    }

    function get_acl2($params) {
    
    }
    */
    
    
}

abstract class acl_item_abstract extends model_item {
    
    
    /**
    * Render
    * 
    * all projects
    * 
    */                               
    
    /*
    function render() {
        return 'a';        
    }
    */
    
}
