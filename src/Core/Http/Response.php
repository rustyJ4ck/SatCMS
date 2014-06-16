<?php

namespace SatCMS\Core\Http;

class Response extends \Symfony\Component\HttpFoundation\Response {

    /**
     * Create a new response instance.
     *
     * <code>
     *        // Create a response instance with a given status
     *        return Response::make('Not Found', 404);
     *
     *        // Create a response with some custom responseHeaders
     *        return Response::make(json_encode($user), 200, array('header' => 'value'));
     * </code>
     *
     * @param  mixed $content
     * @param  int $status
     * @param  array $headers
     * @return Response
     */

    public static function make($content, $status = 200, $headers = array()) {
        return new static($content, $status, $headers);
    }

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
