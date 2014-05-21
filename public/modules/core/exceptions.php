<?php

/**
 * Exception
 *
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: exception.php,v 1.10.2.2.2.1 2012/09/10 05:59:52 Vova Exp $
 */

/**
 * Basic exception
 */
class tf_exception extends exception {

    /**
     * Mail for user reports
     */
    private $bugs_email = 'rustyj4ck@gmail.ru';

    private static $last_exception = false;

    const CRITICAL = 666;

    /**
     * Need log
     * default false
     */
    protected $logable = true;
    protected $log_id = false;

    function __construct($title, $err_no = 0) {

        if (class_exists('loader', false) && !loader::_option(loader::OPTION_TESTING)) {

            $this->log_id = false;

            if ($err_no == self::CRITICAL) {
                echo "<h1 style='color:darkred'>Danger! {$title} </h1>";
            } else {

                // override email
                if (class_exists('core', 0) && core::selfie()) {
                    $this->bugs_email = core::selfie()->cfg('email', $this->bugs_email);
                }

                // log if logger available
                if ($this->logable && class_exists('core', 0)
                    && ($libs = core::get_libs()) && $libs->is_registered('logger')
                    && ($logger = core::lib('logger'))
                ) {
                    $this->log_id = $logger->error($title, $err_no, $this->getTraceAsString());
                }


            }
        }


        parent::__construct($title, $err_no);

        self::$last_exception = $this;
    }

    /**
     * Get last exceptions
     */
    public static function get_last_exception() {
        return self::$last_exception;
    }

    /**
     * Static s_display_error
     */
    static function generic_display_error(exception $ex) {
        $title  = $ex->getMessage();
        $err_no = $ex->getCode();

        if (!class_exists('loader', 0) || (class_exists('loader', 0) && !loader::in_ajax())) {
            header('', true, 500);
            $message = '<style>* {font: 0.97em verdana;} a {text-decoration:none;background:#EFEFEF;padding:4px;} a:hover{background: red;}</style><h1>Oops! We have an error here.</h1>';
            $subject = "Error " . " at " . (loader::in_shell() ? 'console' : $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $message .= 'Visit <a href="/">index</a> page.<br/><br/>';
            echo $message;
            // if debug

            if (class_exists('core', 0) && core::is_debug() || loader::$_debug) {
                echo '<br/>Error : ' . $title . ' : ' . $err_no;
                echo '<br/><br/>' . nl2br($ex->getTraceAsString()); // tf_exception::getTraceAsString2($ex)
            }
        }
    }

    /**
     * DisplayError
     */
    function display_error() {

        $title  = $this->getMessage();
        $err_no = $this->getCode();

        // notify interactive user

        if (!class_exists('loader', 0) || (class_exists('loader', 0) && !loader::in_ajax())
            && !($err_no == router_exception::NOT_FOUND && $this instanceOf router_exception)
        ) {

            if (!headers_sent())
                header('', true, 500);

            $message = '<style>* {font: 0.97em verdana;} a {text-decoration:none;background:#EFEFEF;padding:4px;} a:hover{background: red;}</style><h1>Ups! We have an error here.</h1>';
            $subject = "Error " . ($this->log_id ? "#{$this->log_id}" : '') . " at "
                . (loader::in_shell() ? 'console' : $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $message .= "Please send report to <a href='mailto:" . $this->bugs_email . "?subject={$subject}'>Bugs mail</a>.<br/>";
            $message .= 'Thank you.<br/><br/>';
            $message .= 'Visit <a href="/">index</a> page.<br/><br/>';
            echo $message;

            // if debug
            if (class_exists('core', 0) && core::is_debug()) {
                echo '<br/>Error : ' . $title . ' : ' . $err_no;
                echo '<br/><br/>' . nl2br($this->getTraceAsString());
            }
        }
    }

    static function getTraceAsString2($e) {
        $rtn   = "";
        $count = 0;
        foreach ($e->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_array($arg)) {
                        $args[] = "Array";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            $rtn .= sprintf("#%s %s(%s): %s(%s)\n",
                $count,
                isset($frame['file']) ? $frame['file'] : 'unknown file',
                isset($frame['line']) ? $frame['line'] : 'unknown line',
                (isset($frame['class'])) ? $frame['class'] . $frame['type'] . $frame['function'] : $frame['function'],
                $args);
            $count++;
        }

        return $rtn;

    }

}

/**
 * Router exception
 */
class router_exception extends tf_exception {
    const ERROR     = 0;
    const NOT_FOUND = 10;
}

/**
 * Core exception
 */
class core_exception extends tf_exception {
}

/**
 * Abs_collection exception
 */
class dbal_exception extends tf_exception {

    function __construct($title, $err_no = 0) {

        if (is_array($err_no)) {
            $title .= sprintf(' (%d : %s)', $err_no['code'], $err_no['message']);
            $err_no = $err_no['code'];
        }

        parent::__construct($title, $err_no);

    }
}

/**
 * Validator exception
 */
class validator_exception extends tf_exception {

    /**
     * Error case
     */
    const ERROR = 1;

    /**
     * Validation errors
     */
    const VALIDATION = 2;

    protected $logable = false;
}

/**
 * Blocks exception
 */
class block_exception extends tf_exception {
}

/**
 * Abs_collection exception
 */
class collection_exception extends tf_exception {
}

/**
 * Abs_collection_filter exception
 */
class collection_filter_exception extends tf_exception {
    protected $logable = false;
}

/**
 * Controller exception
 */
class controller_exception extends tf_exception {
    const NOT_FOUND = 10;
}

/**
 * Renderer exception
 */
class renderer_exception extends tf_exception {
}

/**
 * Modules exception
 */
class module_exception extends tf_exception {
}

/**
 * Generic libs exception
 */
class lib_exception extends tf_exception {
}

/**
 * Modules exception
 */
class fs_exception extends tf_exception {
}

/**
 * Authorization exception
 */
class auth_exception extends tf_exception {
}

/**
 * Uploader exception
 */
class uploader_exception extends tf_exception {
}

/**
 * System exceptions
 * (replace error handler in @see core::init0)
 */
class system_exception extends tf_exception {
}

/**
 * Video convertor errors
 */
class editor_exception extends tf_exception {
}

/**
 * Video convertor errors
 */
class video_conv_exception extends tf_exception {
}
