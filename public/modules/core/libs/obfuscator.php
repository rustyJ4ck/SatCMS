<?php

/**
 * Class tf_obfuccator
 */

class tf_obfucator {

    function run($from, $to = null) {
        return obfuscatorBase::run($from, $to);
    }

}

/**
 * Class obfuscatorBase
 */

class obfuscatorBase {

    static private $_IGNORED_TOKENS = array(
          T_COMMENT
        , T_DOC_COMMENT
        , T_WHITESPACE
        //, T_ML_COMMENT //php4 only?
    );

    static private $_head;

    static function head($string) {
        self::$_head = sprintf("<?php\n/*\n%s\n*/\n?>", $string);
    }

    static function run($from, $to = null) {

        $file   = file_get_contents($from);
        $tokens = token_get_all($file);
        $file   = '';

        $prev_token = -1;

        foreach ($tokens as $token) {

            if (is_string($token)) {
// ;{}[]
                $file .= $token;
            } else
                if (isset($token[1]) && !in_array($token[0], self::$_IGNORED_TOKENS)) {

                    $_token = $token[0];

                    $pre_spacer = (
                        $_token == T_AS
                        || $_token == T_EXTENDS
                        || $_token == T_IMPLEMENTS
                        || $_token == T_INSTANCEOF
                    )
                        ? ' '
                        : '';

                    $post_spacer = (
                        $_token == T_VARIABLE
                        || $_token == T_DOUBLE_COLON
                        || $_token == T_STRING
                        || $_token == T_OBJECT_OPERATOR
                        || $_token == T_DOUBLE_ARROW
                        || $_token == T_CONSTANT_ENCAPSED_STRING
                        || $_token == T_LNUMBER
                        || $_token == T_NUM_STRING
                        || $_token == T_DOLLAR_OPEN_CURLY_BRACES
                        || $_token == T_CURLY_OPEN
                        || $_token == T_ENCAPSED_AND_WHITESPACE
                    )
                        ? ''
                        : ' ';

                    if ($_token == T_STRING && lcfirst($token[1]) == 'exception') $post_spacer = ' ';

                    $file .= ($pre_spacer . $token[1] . $post_spacer);


                } else {
                }

            $prev_token = (is_array($token) && isset($token[0]) && $token[0] !== -1) ? $token[0] : -1;
        }

        if (self::$_head) {
            $file = self::$_head . $file;
        }

        if ($to) {
            @mkdir(dirname($to), 0, true);
            file_put_contents($to, $file);
        }

        return $file;
    }
}
