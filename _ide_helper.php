<?php

// Ide autocomplete helper

/*Symfony\Component\HttpFoundation\Response*/
class Response extends SatCMS\Core\Http\Response  {};
class JsonResponse extends SatCMS\Core\Http\JsonResponse {};
class MessageResponse extends SatCMS\Core\Http\MessageResponse {};

class sat_content_type_collection extends model_collection {};

class Html extends SatCMS\Core\Html\Bootstrap\Html {
    static function File(array $params) {}
    static function Image(array $params) {}
    static function Text(array $params) {}
    static function Date(array $params) {}
};