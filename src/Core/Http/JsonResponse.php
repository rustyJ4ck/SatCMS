<?php

namespace SatCMS\Core\Http;

class JsonResponse extends \Symfony\Component\HttpFoundation\JsonResponse {

    public static function make($content, $status = 200, $headers = array()) {
        return new static($content, $status, $headers);
    }

}