<?php

/**
* Transler bridge
*/

function elFinderIsAuthed() {       
  
    if (!defined('ELFIINDER_AUTHED')) {
    
        require_once dirname(__FILE__) . '/../../../modules/core/loader.php';
        loader::bootstrap();

        /** @var users_item */
        $user = core::lib('auth')->get_user();
        $authed = $user->level >= 50;
        
        define('ELFIINDER_AUTHED', $authed);
    
    }
    
    return ELFIINDER_AUTHED;           

}