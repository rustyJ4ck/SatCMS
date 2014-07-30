<?php

/**
 * Test bootstrap
 */

error_reporting(E_ALL);
ini_set('display_errors', 'on');  

test_assertions::$start_time = microtime(1);

// this prevent init10 on modules!
register_shutdown_function('test_done');

require dirname(__FILE__) . "/../loader.php";

$BOOT_OPTIONS = array(
    loader::OPTION_TESTING => true,
    loader::OPTION_CORE_PARAMS => array(
        'config' => array('database' => 'test')
    )
);

loader::bootstrap($BOOT_OPTIONS);

if (!loader::in_shell()) {
    die('Tests available only from console' . PHP_EOL);
}

// fix unicode
if (loader::is_windows() && loader::in_shell()) {
  system('chcp 65001 > NUL');
}

/**
 * Tests api:
 *
 * test_head(title)
 * test_print(var, var, ...)
 *
 * test_assert($assert, $title)
 * test_except(closure, $title)
 *
 * test_done - called on shutdown
 */

/**
 * Print test head
 * @param null $title
 */
function test_head($title = null) {
    if ($title) echo strings::nl() . strings::strtoupper($title) . strings::nl();
    echo str_repeat('-', 50) . strings::nl();
}

/**
 * Debug print
 */
function test_print() {
    $args = func_get_args();
    foreach ($args as $v)
        echo (is_scalar($v) ? $v : var_export($v, true))
             . strings::nl();
}

/**
 * Exception assertion
 * @param $exception_class
 * @param null $res
 * @param string $title
 * @return bool|null
 */
function test_except($exception_class, $action = null, $title = '') {

    $res = false;

    // closure
    if ($action instanceof Closure) {
        try {
            $res = $action();
        } catch (Exception $e) {
            if ($exception_class == get_class($e)) {
                $res = true;
            }
            $title .= (" !Exception: " . get_class($e) . ', ' . $e->getMessage());
        }
    }

    return test_assert($res, $title);

}

/**
 * Assertion
 * @param null $res
 * @param string $title
 * @return bool|null
 */
function test_assert($res = null, $title = '') {

    // closure
    if ($res instanceof Closure) {
        try {
            $res = $res();
        } catch (Exception $e) {
            $res = false;
            $title .= (" Exception: " . get_class($e) . ', ' . $e->getMessage());
        }
    }

    if ($res)  test_assertions::$successed++;
    if (!$res) test_assertions::$failed++;

    $return = $res ? ' OK ' : 'FAIL';

    if (!is_null($title)) {
        printf('ASSERT: [%4s] %s %s'
            , $return
            , $title
            , strings::nl()
        );
    }

    return $res;
}

/**
 * Complete
 */
function test_done() {

    list($oks, $fails) = test_assertions::get();

    test_head(sprintf('%-10s (+%d, -%d) %s',
        (test_assertions::$failed ? 'FAILED' : 'PASSED'),
        $oks, $fails,
        basename($_SERVER['PHP_SELF'])
    ));

    printf('Time: %.5f ms,  Mem: %.3f MB %s', microtime(1) - test_assertions::$start_time, memory_get_peak_usage()/1048576, strings::nl());

    // outro
    exit($fails?1:0);
}

/**
 * Test helper
 * Class test_assertions
 */
class test_assertions {

    const OK = 'OK';
    const FAIL = 'FAIL';

    static $successed = 0;
    static $failed = 0;
    static $start_time;

    static function get() {
        return [self::$successed, self::$failed];
    }
}
