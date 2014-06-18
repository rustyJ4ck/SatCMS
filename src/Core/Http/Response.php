<?php

namespace SatCMS\Core\Http;

class Response extends \Symfony\Component\HttpFoundation\Response {

    /**
     * Create a new response instance containing a view.
     *
     * <code>
     *        // Create a response instance with a view and data
     *        return Response::view('home.index', array('name' => 'Taylor'));
     * </code>
     *
     * @param  string $view
     * @param  array $data
     * @return Response
     */
    public static function view($view, $data = array()) {
        $parser = \tpl_loader::get_parser(true);
        $view .= \loader::DOT_TPL;
        $parser->assign($data);

        return new static($parser->fetch($view));
    }

}
