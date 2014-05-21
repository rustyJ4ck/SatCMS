<?php

/**
 * @package    TwoFace
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: modify.php,v 1.1.4.4 2013/12/24 07:09:14 Vova Exp $
 */

class sat_comment_modify_route_filter extends route_filter {
    
    protected $_regex = '@comment/modify$@';
    
    function _match(&$uri, &$route) {
        return preg_match($this->_regex, $uri, $m);
    }
    
    /**
    * Remember!
    * Assign current item in controller for comment linking!
    */
    function run() {

        if (loader::in_ajax() !== true) {
            throw new controller_exception('Cant touch this ' . __METHOD__);
            return false;
        }

        core::dprint('run comment modify');
        
        $pctl = core::modules()->get_router()->get_controller();
        
        $user = core::lib('auth')->get_user();

        /**
         * Parent item, must be assigned thru @see module_controller::set_current_item()
         * @var abs_collection_item
         */
        $post = $pctl->get_current_item(); 
        
        // var_dump(get_class($post), core::get_modules()->get_router()->get_name());
        
        if (!$post) {
            throw new controller_exception('No item assigned');
        }

        if (!$post->has_behavior('sat.commentable')) {
            throw new controller_exception('Not commentable');
        }

        $comments = $post->behavior('sat.commentable')->get_attach_model(); //get_comments();

        $request  = core::lib('request');
        $renderer = core::lib('renderer');
        
        $user_id = core::lib('auth')->get_user()->id;
        $pid     = (int)$request->post('pid'     , 0);
        
        $limit = core::selfie()->cfg('comment_interval', 60);
        
        $auth = core::lib('auth');

        /** @var aregistry $sd */
        $sd = $auth->get_current_session()->get_storage();
        
        $time = $sd->comments_last_time;
          
        //$time = $comments->get_last_time($pid, $user_id);
        
        // disallow by interval
        if ($time && (($time + $limit) > time())) {
            
                $pctl->set_null_template();

                $renderer->set_ajax_answer(array(
                        'status'  => false
                      , 'id'      => 0
                      , 'message' => vsprintf(i18n::T('sat\\comment_interval_restriction'), ($time + $limit - time()))
                ))
                ->ajax_flush();
                ;
                                  
            // else core::get_instance()->set_message(array('content', 'comment_interval_restriction'));
            
            return;
            // exit
        }
        
        $sd->comments_last_time = time();
        
        $username = functions::request_var('username', '');
        $text     = functions::request_var('text'    , '');

        $api =  functions::request_var('api');
        
        $id = $comments->modify(array(
             'user_ip'  => core::lib('auth')->get_user_ip(true)
           , 'user_id'  => $user_id
           , 'ctype_id' => $post->get_ctype_id()
           , 'username' => $username
           , 'pid'      => $pid
           , 'text'     => $text
           , 'type'     => functions::request_var('type'    , 0)
           , 'tpid'     => functions::request_var('tpid'    , 0)
           , 'api'      => $api    
        ));

        $comment = $comments->get_item_by_id($id);

        if (!$comment) throw new controller_exception('[ajax] Comment create failed');

        $comment->load_secondary();

        $renderer->set_data('comment', $comment->render())
            ->set_ajax_answer(array(
                'status'  => true
              , 'id'      => $id
          ))->set_ajax_message(i18n::T('sat\\comment_posted'));
        //->set_main_template('content/comment/view');  
        
        $renderer->ajax_flush('shared/comments/comment');

        // alright, update counter
        return $id;                       
    }
    
}