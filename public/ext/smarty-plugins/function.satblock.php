<?php

/**
 * TF Block plugin for smarty
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: function.satblock.php,v 1.1.2.1 2012/06/09 08:52:52 Vova Exp $
 */
  
  /**
  * Block entry point
  * 
  * @param array
  *     module                  // module tag
  *     action                  // block action
  *     cache                   // seconds, cache data
  *     other params
  * @param Smarty3 $smarty
  */

function smarty_function_satblock($params, &$smarty) {    
    
    if (empty($params['action'])) {
        throw new block_exception('Bad action');
    }

    $orig_params = $params;
    $orig_action = $params['action'];
    
    // module.action
    if (strpos($params['action'], '.') !== false) {
        $t = explode('.', $params['action']);
        $params['action'] = $t[1];
        $params['module'] = $t[0];
    }

    $action = @$params['action'];
    $module = @$params['module'];
    $cache  = @$params['cache'];

    $cache_id = null;
    $cacher = null;
    $with_cache = false;
    $cached = false;
    $buffer = null;

    $core = core::selfie();

    // unpack params to local scope
    // extract($params, EXTR_SKIP);

    /**
     * Cache block
     */
    if (!empty($cache)) {
        
        /** @var cache $cacher_factory */
        $cacher_factory = core::lib('cache');
        
        $cacher = $cacher_factory->has_memory() ? $cacher_factory->get_memory_handle() : $cacher_factory->get_file_handle();  
        
        if ($cacher) {

            $cache_time = $cache;
            unset($params['cache']);

            $cache_id = 'block_' . md5(serialize($params));            
            $result = $cacher->get($cache_id, false);

            if (null !== $result) {
                
                core::dprint(('..block cached "' . $orig_action 
                    . '" using ' . get_class($cacher))
                    , core::E_NOTICE);

                $buffer = $result;
                $cached = true;
            }

            $with_cache = true;
        }
    }

    if (!$cached) {
    
        try {

            if (empty($module)) $module = 'core';

            if ($pmod = core::module($module)) {

                    unset($params['action'], $params['module']);

                    // Run block action
                    $buffer = $pmod->run_block($action, $params);

                    if ($with_cache) {
                        $cacher->set($cache_id, $buffer, $cache_time);
                    }

             }
        }
        catch (module_exception $e) {
            return '[block] module-error: ' . $e->getMessage();
        }
        catch (block_exception $e) {
            return '[block] error: ' . $e->getMessage();
        }

    }

    // debug block

    if (core::is_debug() && $core->cfg('debug_templates')) {

        $dparams = array();
        foreach ($orig_params as $pk => $pv) {
            $dparams []= sprintf('data-%s = "%s"' . PHP_EOL, $pk, $pv);
        }

        /** @var Smarty_Template_Source $source */
        $source = $smarty->source;

        // @todo how to get current line?
        // $dparams []= sprintf('data-parent-template = "%s"' . PHP_EOL, $source->filepath);

        $dsparams = join(' ', $dparams);

        $buffer = <<<DBG
        <satblock class="sat-block" {$dsparams}>
        {$buffer}
        </satblock>
DBG;

    }

    return $buffer;
}
