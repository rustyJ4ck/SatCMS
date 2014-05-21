<?php

/**
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: sat_node_image.php,v 1.1.4.2.2.2 2011/12/22 11:28:47 Vova Exp $
 */
 
class_exists('core', 0) or die('Invisuxcruensseasrjit');
  
class sat_node_image_controller extends editor_controller {

    protected $title = 'Изображения';
    
    protected $_where = 'ctype_id = %d AND pid = %d AND sid = %d';
    
    function construct_after() {

        $this->params->sid          = 0 + $this->request->postget('sid', $this->params->sid);
        $this->params->ctype_id     = 0 + $this->request->postget('ctype_id', $this->params->ctype_id);
        $this->params->pid          = 0 + $this->request->postget('pid', $this->params->pid);

        if (empty($this->params->pid) && empty($this->params->sid)) {
            if (!core::is_debug()) {
                throw new controller_exception('Empty pid / no sid');
            } else {
                $this->_where = null;
            }
        }
        else {
            $this->_where = sprintf($this->_where,
                $this->params->ctype_id,
                $this->params->pid,
                $this->params->sid
            );
        }

        // $this->_where = sprintf($this->_where, $this->params->pid);
    }

    /**
     * Commit
     */
    function action_modify_before() {

        // Force pid
        // Obsolete stuff?

        if (empty($this->params->id)) {
            $this->postdata['pid'] = $this->params->pid;
        }
        else {
            $this->postdata['pid'] = $this->_load_id()->pid;
        }
    }

    function render_after() {
        // core::var_dump($this->collection->get_last_query());
    }


}

