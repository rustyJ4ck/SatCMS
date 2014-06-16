<?php
  
/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: collection.php,v 1.2.2.1.4.1 2012/10/25 09:52:43 Vova Exp $
 */  
 
 
require_once "modules/users/classes/acl/abstract.php";
  
class acl_collection extends acl_collection_abstract {   
    
    /**
    * Load access objects
    * @todo customize?
    */
    function load_objects() {
        
        core::dprint('[W:] load auth entries');
        
        $this->_objects = array();

        /*
           'core' =>
    array (size=3)
      'config' =>
        array (size=3)
          'url' => string '?m=core&c=config' (length=16)
          'title' => string 'Переменные' (length=20)
          'default' => boolean true
      'logs' =>
        array (size=2)
          'url' => string '?m=core&c=logs' (length=14)
          'title' => string 'Логи' (length=8)
      'mail_tpl' =>
        array (size=2)
          'url' => string '?m=core&c=mail_tpl' (length=18)
          'title' => string 'Шаблоны писем' (length=25)
         */

        $actions = array();

        $actions ['core'] = $this->core->get_editor_actions();

        foreach (core::modules() as $module) {
            $actions [$module->get_name()]= $module->get_editor_actions();
        }

        foreach ($actions as $name => $operations) {

            $object = array(
                  'title' => $module->i18n->T(array($name, '_name'))
                , 'name'  => $name
                , 'items'   => array(array('id' => 0, 'title' => '*'))
            );

            if (!empty($operations))
            foreach ($operations as $opID => $op) {
                $object ['items'][]= array(
                    'id'    => $opID,
                    'title' => $op['title']
                );
            }

            $this->_objects[] = $object;

        }

        return;

        /*
        $pr = core::module('sestat')->get_project_handle()->load();

        $this->_objects[] = array(
              'title'   => 'Статистика'
            , 'name'    => 'mod_sestat'
            , 'items'   => array(array('id' => 0, 'title' => '*'))            
        );
        
        $this->_objects[] = array(
              'title'   => 'Пользователи'
            , 'name'    => 'mod_users'
            , 'items'   => array(array('id' => 0, 'title' => '*'))            
        );  
        
        $this->_objects[] = array(
              'title'   => 'Настройки'
            , 'name'    => 'mod_core'
            , 'items'   => array(array('id' => 0, 'title' => '*'))            
        );  

        $this->_objects[] = array(
              'title'   => 'Проекты'
            , 'name'    => 'projects'    // section name
            , 'items'   => array(array('id' => 0, 'title' => '*'))            
        );
        
        foreach ($pr as $p) {
            $c = count($this->_objects) - 1;
            $this->_objects[$c]['items']
                []= array('id' => $p->id, 'title' => $p->title);
        }
        */
    }    
}  

class acl_item extends acl_item_abstract {
}