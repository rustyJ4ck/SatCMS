<?php

/**
 * Image
 * 
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */
 
 /*
  'name' => string 'images.jpeg' (length=11)
      'type' => string 'jpeg' (length=4)
      'size' => int 3845
      'file' => string '' (length=98)
      'url' => string ''
 */
 
 class control_image extends model_control {
     
    /**
    * Filter with wideimage (must be installed)
    *  
    * @param mixed $from
    * @param mixed $to
    * @param mixed $format
    */
    static function runWideImage(
        $from,
        $to,
        $format
    ) {
        
         if (!class_exists('WideImage')) {
             core::dprint('WideImage not exists!', core::E_CRIT);
             return;
         }
        
         core::dprint('<<Wideimage ' . __FUNCTION__ . ' ' . join(', ', $format) . ' -- ' . (string)$to, core::E_DEBUG4);

         $image = WideImage::load($from);

         /** @var $converted \WideImage_Image */
         $converted = false;

         $method =   array_shift($format);

         /*
          Fatal error: Uncaught exception 'WideImage_Operation_InvalidResizeDimensionException' with message
         'Both dimensions must be larger than 0.' in vendor\spekkionu\wideimage\WideImage\Operation\Resize.php:123
          */

         if (is_callable(array($image, $method))) {
             try {
                $converted = call_user_func_array(array($image, $method), $format);
             }
             catch (Exception $e) {
                 core::dprint(' ..convert image failed: ' . $e->getMessage(), core::E_DEBUG4);
             }
         }

         if ($converted) {
             $converted->saveToFile($to);
         }         
        
    }
     
    /*
    on success

        'name' => string 'icq_avatar_sm.jpg.gif' (length=21)
        'type' => string 'image/gif' (length=9)
        'tmp_name' => string '/tmp/phpBINfkc' (length=14)
        'error' => int 0
        'size' => int 2745  

    on error

        'name' => string '' (length=0)
        'type' => string '' (length=0)
        'tmp_name' => string '' (length=0)
        'error' => int 4
        'size' => int 0
    */

     /**
      * Process upload
      * Return data for saving
      *
      * @param model_collection $citem
      * @param array $vf
      * @param array $fld
      * @param array $current
      * @return array|string
      * @throws collection_exception
      */
     static function process_modify($citem, $vf, $fld, $current) {

         /** @var tf_uploader $uploader */
         $uploader = core::lib('uploader');
         
        // remove file
        if ($fld == 'remove' && !empty($current['file'])) {                
            core::dprint('[COLLECTION] delete file ');
            // function format_field_on_remove($vf, &$fld, $current) {
            $citem->format_field_on_remove($vf, $fld, $current); 
            $fld = ''; 
            return $fld;
        }               
        
        if (!is_array($fld)) {
            core::dprint('Field must be submitted via multipart from enctype="multipart/form-data", add _FILES to submit vars: ');
            $fld = '';
        } else {
            $error = core::lib('uploader')->get_upload_error(@$fld['error']);
            if (false !== $error) { 
                core::dprint(array('Upload error : %s', $error), core::E_ERROR);
                $fld = '';
            }
        }

        $pinfo = array();                      
                     
        if (!empty($fld['name'])) {
            $pinfo = (isset($fld['name']) ? pathinfo($fld['name']) : false);
            $pinfo['extension'] = strtolower($pinfo['extension']);
        }
        
        if (!empty($fld) && !empty($fld['size'])
            && (empty($vf['allow']) || (!empty($vf['allow']) && in_array($pinfo['extension'], $vf['allow'])))) {
                
            $path = loader::get_uploads($vf['storage']);
            
            // reuse name, if it here already
            
            if (!empty($current['file'])) {
                // unlink?
                $citem->format_field_on_remove($vf, $fld, $current);
            }
                            
            // $naming = isset($vf['unique']) ? (md5(microtime(true)) . '.' . $pinfo['extension']) : false;

            $naming = functions::url_hash() . '.' . $pinfo['extension'];

            // dd($naming, $path);
            
            if (!empty($vf['spacing'])) {
                $path .= ('/' . substr($naming, 0, $vf['spacing']));
            }

            if (!is_dir($path) && !@mkdir($path, 0777, 1)) {
                throw new collection_exception('Upload error. Cant create directory: ' . $path);
            }
            
            $file = $uploader->upload_file($fld, $path, $naming, array('force_override' => true));

            // check for bad image
            $exists = false;
            if (!$file || !($exists = file_exists($file)) || !getimagesize($file)) {
                if ($exists) {
                    unlink($file);
                }
                throw new collection_exception('Upload error. invalid file');
            }
             
            // fix bad \\
            $fld['file'] = str_replace(array('\\\\','\\'), '/', $file);
            
            // override type with extension
            $fld['type'] = $pinfo['extension'];
            
                if (!empty($vf['original'])) {
                    copy(
                          $fld['file']
                        , preg_replace('@\.([^\.]+)$@', '.orig.$1', $fld['file'])
                    );
                }

                // make max_width
                if (!empty($vf['max_width']) || !empty($vf['max_height'])) {
                    
                    core::lib('images')->resample_image_bigger(
                          $fld['file']
                        , $fld['file']
                        , @$vf['max_width']                        
                        , @$vf['max_height']                        
                    );
                }
                
                // {{{thumbnail}}}
                // ------------------------------------------------
                
                if (!empty($vf['thumbnail'])) {
                    
                    $t_props = $vf['thumbnail'];
                    $t_file = preg_replace('@\.([^\.]+)$@', '.thumb.$1', $fld['file']);
                    
                    $t_height = false;
                    $t_filter = false;
                    
                    if (is_array($t_props)) {
                        if (isset($t_props['width'])) {
                             //fullpros
                            $t_height = @intval($t_props['height']);
                            $t_width  = @intval($t_props['width']);
                            $t_filter = @$t_props['filter'];
                        }
                        elseif (isset($t_props['format'])) {   
                            // see below, for b.c.
                        }   
                        else {
                            //(x,y)
                            $t_height = $t_props[1];
                            $t_width  = $t_props[0];
                        }
                    }
                    else {
                        $t_width = (int)$t_props;
                    } 
                    
                    if (isset($t_props['format'])) {                                                          
                        // with Wideimage                                                                                  
                        self::runWideImage($fld['file'], $t_file, $t_props['format']);                        
                    }
                    
                    else {
                        
                        // resample_image($src, $dst, $new_width, $new_height = false)
                        if ($t_width > 0) {
                            core::lib('images')->resample_image(
                                  $fld['file']
                                , $t_file
                                , $t_width
                                , $t_height                                                
                            );
                        }
                        else {
                            // just copy
                            copy($fld['file'], $t_file);
                        }  
                    }  
                    
                    // filter for thumbnail
                    if (isset($t_filter['id'])) {
                         core::lib('images')->image_filter(
                              $t_file
                            , $t_file
                            , $t_filter['id']
                            , @$t_filter['params']
                         );
                    }
                    
                }
                // {{{/thumbnail}}}
                
                // make width
                if (!empty($vf['width']) && !empty($vf['height'])) {
                    core::lib('images')->resample_image(
                          $fld['file']
                        , $fld['file']
                        , $vf['width']                        
                        , $vf['height']                        
                    );
                }
                
                
                if (isset($vf['format'])) {                                                          
                    // with Wideimage                                                                                  
                    self::runWideImage($fld['file'], $fld['file'], $vf['format']);                        
                }
                
                // filter
                if (isset($vf['filter'])) {
                     core::lib('images')->image_filter(
                          $fld['file']
                        , $fld['file']
                        , $vf['filter']['id']
                        , @$vf['filter']['params']
                     );
                }                
                
                if (!empty($vf['watermark'])) {
                    $wm_file = loader::get_public($vf['watermark']['file']);
                    core::lib('images')->watermark(
                        $fld['file'], $fld['file'], $wm_file, @$vf['watermark']['options']
                    );
                }
                
            unset($fld['error']);
            unset($fld['tmp_name']);
            
            // relative to app-path?
            // what if on another server                
        } 
        else {
            
            $fld = $current;
        }
         
         
        return $fld;
         
     }
 }