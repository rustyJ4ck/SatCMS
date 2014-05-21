<?php

/**
* @package TwoFace
* @version $Id: images.php,v 1.4.2.3.2.1 2011/12/22 11:28:46 Vova Exp $
* @copyright (c) 2007 4style
* @author surgeon <r00t@skillz.ru>
*/    
  
class images extends singleton {
    
    /**
    * @return bool|array false if fail
    */
    function is_image($file) {
        if (!file_exists($file)) return false;
        // (function_exists('exif_imagetype')) 
        return getimagesize($file);
    }
  
    /**
    * Load image
    */
    private function _load_image($file_name, $image_type = false) {
            
            if ($image_type) {
                $ext = $image_type;
            }
            else {
                $ext = $this->get_image_extension($file_name);
            }
            
            switch ($ext)   {
               case "jpeg":
               case "jpg":
                   $src_img = imagecreatefromjpeg($file_name);
                   break;

               case "gif":
                   $src_img = imagecreatefromgif($file_name);
                   break;

                case "png":
                   $src_img = imagecreatefrompng($file_name);
                   break;

               default:
                 return false;
               
            };
            
            return $src_img;
    }
    
    /**
    * Save image
    */
    private function _save_image($dest_img, $out_name, $image_type = false) {
        
        if ($image_type) {
            $ext = $image_type;
        }
        else {
            // $ext = $this->get_image_extension($out_name);
            $path_parts = pathinfo($out_name); 
            $ext = strtolower($path_parts["extension"]);
        }
        
        core::dprint('saving ' . $out_name); 
        
        switch($ext) {
            case "jpeg":
            case "jpg":                                 
                imagejpeg($dest_img, $out_name);
                break;

            case "gif":
                imagegif($dest_img, $out_name);
                break;

              case "png":
                imagepng($dest_img, $out_name);
                break;
           
            default:
              return false;        
         }
    } 
    
    /**
    * Resample only bigger images
    * 
    * @param mixed $src
    * @param mixed $dst
    * @param mixed $new_width
    * @param mixed $new_height
    */
    function resample_image_bigger($src, $dst, $new_width, $new_height = false) {  

        $simg = $this->is_image($src);       
        if (false === $simg) return false;    
        
        if ($new_width  >= 0 && $simg[0] <= $new_width)  $new_width = false;              
        if ($new_height >= 0 && $simg[1] <= $new_height) $new_height = false;
        
        // @todo resample! width, height
        if (!$new_width) {
            // return without changes
            if ($src != $dst) copy($src, $dst);
            return;
        };
        
        return $this->resample_image($src, $dst, $new_width, $new_height = false);
    }
    
	/**
    * РЕСЕМПЛИРОВАНИЕ КАРТИНОК
    * указываются полные пути до файлов DIR_IMAGES
    * @param string
    * @param string
    * @param mixed new_width (px - default, may be '%')
    * @return bool result
    */
    function resample_image($src, $dst, $new_width, $new_height = false) {
        
        $file_name = $src;
        $out_name  = $dst;    
        
        // px
        $x = intval($new_width); // будущий размер изображения в пикселях по ширине
        $simg = $this->is_image($file_name);                           
        
        if (false === $simg) return false;
        
        // %
        if (is_string($new_width) && substr($new_width, -1, 1) == '%') {
            $x = round($simg[0] * $x / 100);
        }
        
        $w = $x;
        $h = $new_height ? intval($new_height) : ($w * $simg[1] / $simg[0]);
        
        // определим коэффициент сжатия изображения, которое будем генерить
        $ratio = $w / $h;
        // создадим пустое изображение по заданным размерам
        $dest_img = imagecreatetruecolor($w, $h);
        // зальём его белым цветом
        imagefill($dest_img, 0, 0, 0xFFFFFF);
        // получим размеры исходного изображения
        $size_img = getimagesize($file_name);
        // получим коэффициент сжатия исходного изображения
        $src_ratio = $size_img[0] / $size_img[1];
        
        // здесь вычисляем размеры, чтобы при масштабировании сохранились
        // 1. Пропорции исходного изображения
        // 2. Исходное изображение полностью помещалось на маленькой копии
        // (не обрезалось)
        
        /*
        if ($src_ratio > $ratio) {
            $old_h          = $size_img[1];
            $size_img[1]    = floor($size_img[0] / $ratio);
            $old_h          = floor($old_h * $h / $size_img[1]);
        }
        else {
            $old_w          = $size_img[0];
            $size_img[0]    = floor($size_img[1] * $ratio);
            $old_w          = floor($old_w * $w / $size_img[0]);
        }
        */

        if ($w == $size_img[0] && $h == $size_img[1]) {
            // just copy
            copy($src, $dst);
        }
        else {
            
            // исходя из того какой тип имеет изображение
            // выбираем функцию создания
            
            $src_img = $this->_load_image($file_name);  

                // масштабируем изображение    функцией imagecopyresampled()
                // $dest_img - уменьшенная копия
                // $src_img - исходной изображение
                // $w - ширина уменьшенной копии
                // $h - высота уменьшенной копии
                // $size_img[0] - ширина исходного изображения
                // $size_img[1] - высота исходного изображения
                
            // if ($new_height) $size_img[1] = $new_height;
        
            imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);
            
            // коцаем если есть
            if ($out_name != $file_name)
                @unlink($out_name);

            $this->_save_image($dest_img, $out_name);
            
            // чистим память от созданных изображений
            imagedestroy($src_img);  
        }

        // чистим память от созданных изображений
        imagedestroy($dest_img);
       
            
        return array($w, $h);
    
    }

    private $_background = 0xFFFFFF;
    
    function set_background_color($color) {
        $this->_background = $color;
    }
    
    /**
    * Cropper
    * with aspect
    * 
    * @param int width
    * @param int height
    * @param string path to source image
    * @param string path to output file (if false, same as src used)
    * @return bool result
    */
    function crop_to_aspect($width, $height, $src, $dst = false) {
       
        if (!($s_src = $this->is_image($src))) {
            return false;
        }
   
        if (empty($dst)) $dst = $src;
        
        $s_dst = array($width, $height);

        $is_horizontal = ($s_src[0] / $s_src[1]) > ($width / $height); // ($s_src[0] >= $s_src[1]);
        
        $src_aspect = $is_horizontal ? $s_src[0] / $s_src[1] : $s_src[1] / $s_src[0];
        $dst_aspect = $is_horizontal ? $s_dst[0] / $s_dst[1] : $s_dst[1] / $s_dst[0];
        
        #core::var_dump($src_aspect, $dst_aspect);
            
        // 30% differ
        if (intval($dst_aspect * 30) == intval(30 * $src_aspect)) {
            if ($s_src == $s_dst)
                copy($src, $dst); 
            else
                $this->resample_image($src, $dst, $width, $height);
        }
        else {   
            $new_width  = $is_horizontal ? (int)round($s_src[1] * $dst_aspect) : $s_src[0];
            $new_height = $is_horizontal ? $s_src[1] : (int)round($s_src[0] * $dst_aspect);
            
            $x_offset = 0;
            $y_offset = 0;

            if ($is_horizontal) {
                if ($new_width >= $s_src[0]) 
                    $new_width = $s_src[0];
                else //less                    
                    $x_offset = (int)round(($s_src[0] - $new_width) / 2);    
            }
            else {
                // vertical
                 if ($new_height >= $s_src[1]) 
                    $new_height = $s_src[1];
                else //less                    
                    $y_offset = (int)round(($s_src[1] - $new_height) / 2);   
            }
            
            //$dest_img = imagecreatetruecolor($new_width, $new_height);
            $dest_img = imagecreatetruecolor($width, $height);
            
            imagefill($dest_img, 0, 0, $this->_background);
            $src_img = $this->_load_image($src);
            
            $s_src[0] -= $x_offset;
            $s_src[1] -= $y_offset;
            
            // [500x670 -- 176x131] 0, 0, 0, 149, 500, 372, 500, 670
            core::dprint(($is_horizontal ? 'H' : 'V') . " [{$s_src[0]}x{$s_src[1]} -- {$s_dst[0]}x{$s_dst[1]}] 0, 0, $x_offset, $y_offset, $new_width, $new_height, {$s_src[0]}, {$s_src[1]}");

            imagecopyresampled($dest_img, $src_img, 0, 0
                , $x_offset, $y_offset
                //, $new_width, $new_height
                , $width, $height
                , $s_src[0], $s_src[1]);

            $this->_save_image($dest_img, $dst, $this->get_image_extension($src));
                
            // clear handles
            imagedestroy($dest_img);
            imagedestroy($src_img);
        }
        
        return true;
                
    } 
    
    /**
    * IMG_FILTER_GRAYSCALE = 1
    */
    function image_filter($src, $dst, $id, $params = null) {
        
        $simg = $this->is_image($src);       
        if (false === $simg || !$id) return false;                                            
        $src_img = $this->_load_image($src);                                   
        imagefilter($src_img, $id, @$params[0], @$params[1], @$params[2]);         
        $this->_save_image($src_img, $dst, $this->get_image_extension($src));        
        imagedestroy($src_img);     
        
        return true;
    }
        
    /**
    * get image extension by file
    */
    function get_image_extension($filename) {
        
        /*
        if ( ! function_exists ( 'mime_content_type ' ) ) {
           function mime_content_type ( $f ) {
               return trim ( exec ('file -bi ' . escapeshellarg ( $f ) ) ) ;
           }
        } 
        */
   
         if (function_exists('exif_imagetype'))
         {
             switch (exif_imagetype($filename))
             {
                 case 1:
                     return 'gif';
                 case 2:
                     return 'jpg';
                 case 3:
                     return 'png';
               
                 default:
                     return false;
             }
         }

         $info = getimagesize($filename);
         
         switch (@$info['mime']) { 
                 case 'image/gif':
                     return 'gif';
                 case 'image/jpeg':
                 case 'image/jpg':
                     return 'jpg';
                 case 'image/png':
                     return 'png';
         }
         
         return false;
    } 
    
    
    
    /**
    * Cropper
    * with aspect
    */
    function crop_to($width, $height, $src, $dst) {
        
        $s_src = getimagesize($src);
        $s_dst = array($width, $height);
        
        $aspect = $s_dst[0] / $s_dst[1];

        // 30% deffer
        if (intval($aspect * 30) == intval(30 * $s_src[0] / $s_src[1])) {
            copy($src, $dst); // nop            
        }
        else {   
            $new_width = (int)round($s_src[1] * $aspect);
            $x_offset = false;

            if ($new_width >= $s_src[0]) $new_width = $s_src[0];
            else {
                //less
                $x_offset = (int)round(($s_src[0] - $new_width) / 2);    
            }    

//            var_dump($aspect, $new_width, $x_offset, $new_width + $x_offset * 2);
            
            $dest_img = imagecreatetruecolor($width, $height);
            imagefill($dest_img, 0, 0, 0xFFFFFF);
            $src_img = $this->_load_image($src);

            imagecopyresampled($dest_img, $src_img, 0, 0
                , $x_offset, 0, $new_width, $s_src[1]
                , $s_src[0], $s_src[1]);
                
            $this->_save_image($dest_img, $dst);
                
            // чистим память от созданных изображений
            imagedestroy($dest_img);
            imagedestroy($src_img);
        }
                
    } 
    
    
    /**
    * РЕСАЙЗ КАРТИНОК
    * указываются полные пути до файлов DIR_IMAGES
    * @param string
    * @param string
    * @param mixed new_width (px - default, may be '%')
    * @return bool result
    */
    function resize_image0($src, $dst, $new_width, $new_height = false) {
        
        $file_name = $src;
        $out_name  = $dst;    
        
        // px
        $x = intval($new_width); // будущий размер изображения в пикселях по ширине
        $simg = getimagesize($file_name);                           
        $w = $x;
        $h = $new_height;
        
        // определим коэффициент сжатия изображения, которое будем генерить
        $ratio = $w/$h;
        // создадим пустое изображение по заданным размерам
        $dest_img = imagecreatetruecolor($w, $h);
        // зальём его белым цветом
        imagefill($dest_img, 0, 0, 0xFFFFFF);
        // получим размеры исходного изображения
        $size_img = getimagesize($file_name);
        // получим коэффициент сжатия исходного изображения
        $src_ratio=$size_img[0] / $size_img[1];

        // здесь вычисляем размеры, чтобы при масштабировании сохранились
        // 1. Пропорции исходного изображения
        // 2. Исходное изображение полностью помещалось на маленькой копии
        // (не обрезалось)
        if ($src_ratio > $ratio) {
            $old_h          = $size_img[1];
            $size_img[1]    = floor($size_img[0] / $ratio);
            $old_h          = floor($old_h * $h / $size_img[1]);
        }
        else {
            $old_w          = $size_img[0];
            $size_img[0]    = floor($size_img[1] * $ratio);
            $old_w          = floor($old_w * $w / $size_img[0]);
        }

        // исходя из того какой тип имеет изображение
        // выбираем функцию создания
        
        $path_parts = pathinfo($file_name);

        $ext = strtolower($path_parts["extension"]);
        
        switch ($ext)   {
           case "jpeg":
           case "jpg":
               $src_img = imagecreatefromjpeg($file_name);
               break;

           case "gif":
               $src_img = imagecreatefromgif($file_name);
               break;

            case "png":
               $src_img = imagecreatefrompng($file_name);
               break;

           default:
             return false;
           
        };
            // масштабируем изображение    функцией imagecopyresampled()
            // $dest_img - уменьшенная копия
            // $src_img - исходной изображение
            // $w - ширина уменьшенной копии
            // $h - высота уменьшенной копии
            // $size_img[0] - ширина исходного изображения
            // $size_img[1] - высота исходного изображения
            
        if ($new_height) $size_img[1] = $new_height;
        
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $size_img[0], $size_img[1]);
            
        // коцаем если есть
        if ($out_name != $file_name)
            @unlink($out_name);

        // в зависимости от типа файла выбирем функцию сохранения в файл
        switch($ext) {
            case "jpeg":
            case "jpg":                                 
                imagejpeg($dest_img, $out_name);
                break;

            case "gif":
                imagegif($dest_img, $out_name);
                break;

              case "png":
                imagepng($dest_img, $out_name);
                break;
           
            default:
              return false;        
       }
            
            // чистим память от созданных изображений
            imagedestroy($dest_img);
            imagedestroy($src_img);
            
        return array($w, $h);
    
    }
    
    /**
    * Watermark image
    * 
    * @param mixed $src
    * @param mixed $dst
    * @param mixed $wm
    * @param mixed $options
    *   x,y,opacity,min_width,min_height
    */
    function watermark($src, $dst, $wm, $options = false) {
        if (empty($options)) $options = array();
        if (!isset($options['opacity'])) $options['opacity'] = 100;
        if (!isset($options['x'])) $options['x'] = -5;
        if (!isset($options['y'])) $options['y'] = -5;                
        
        $watermark = $this->_load_image($wm);  
        $watermark_width = imagesx($watermark);  
        $watermark_height = imagesy($watermark);  
        $image = imagecreatetruecolor($watermark_width, $watermark_height);  
        $image = $this->_load_image($src);  
        $size = getimagesize($src);  
        
        if (isset($options['min_width']) && $options['min_width'] > $size[0]) return false;
        if (isset($options['min_height']) && $options['min_height'] > $size[1]) return false;
        
        $dest_x = ($options['x'] > 0 ? $options['x'] : ($size[0] - $watermark_width - 5));  
        $dest_y = ($options['y'] > 0 ? $options['y'] : ($size[1] - $watermark_height - 5));     
        imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $options['opacity']);  
        $this->_save_image($image, $dst);
        
        imagedestroy($image);  
        imagedestroy($watermark); 
        
        return true;
    }
}