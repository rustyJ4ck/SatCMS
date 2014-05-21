<?php

if (!class_exists('Smarty', false)) {
    require dirname(__FILE__) . "/Smarty.class.php";
}

class Smarty3 extends Smarty {
    
    /**
     * clear the given assigned template variable.
     *
     * @param string $tpl_var the template variable to clear
     */
    public function clear_assign($tpl_var)
    {
        $this->clearAssign($tpl_var);
    }
}
