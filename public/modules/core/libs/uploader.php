<?php

/**
 * Uploads
 * 
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: uploader.php,v 1.2.30.2 2011/10/19 06:45:27 Vova Exp $
 */


class tf_uploader {
    
    /*
        $file_type = $core->FILES[$file_key]['type'];
        $file_tmpfile = $core->FILES[$file_key]['tmp_name'];
        $file_name = $core->FILES[$file_key]['name'];
        $file_size = $core->FILES[$file_key]['size'];    
    */
    
    /**
    * Upload
    * @param array $_FILES[key]
    * @param string dst_path (dir)
    * @param string force use name
    * @param array config for uploader
    *   force_override => bool
    */
    function upload_file(array $file_data, $to_file, $use_name = false, $config = array()) {
        
        if (!is_dir($to_file) || !is_writable($to_file))
             throw new uploader_exception('Destanation not available ' . $to_file);

        // $file_type      = $file_data['type'];
        $file_tmpfile   = $file_data['tmp_name'];
        $file_name      = $file_data['name'];
        $file_size      = $file_data['size'];
        
        if (substr($to_file, -1, 1) != '/') $to_file .= '/';
                                    
        if ($file_size <= 0) 
            throw new uploader_exception('Nothing uploaded ' . $to_file);

            if ($use_name) {
                $file_name = $use_name;
            }
            else {
                // remove bad charz
                $file_name = preg_replace('[^a-z0-9_]i', '', $file_name);

                if (substr($file_name, 0, 1) == '.') 
                    $file_name = date('jny_His') . $file_name;    //пустое имя файла заменяем на дату
                
            }
            
            $to_file = $to_file . $file_name;  

            if (file_exists($to_file) && empty($config['force_override'])) {
                throw new uploader_exception ("File $file_name exists. Cant overwrite.");
            }                           

            $u_uploadsucc = false;
            
            core::dprint(array('Uploading %s -> %s', $file_tmpfile, $to_file)); 

            if (!empty($file_tmpfile) && is_file($file_tmpfile))
             {

                if ( copy ( $file_tmpfile, $to_file ) ) {
                      $u_uploadsucc = true;
                      @chmod($to_file,0777);
                   }
                 else
                  if ( move_uploaded_file ( $file_tmpfile, $to_file ) ) {
                      $u_uploadsucc = true;
                      @chmod($to_file,0777);
                  }

                  $l_size_after = filesize($to_file);
             }
       
        return $to_file;

    }

    /**
    * From web 
    */      
    function upload_file_from_web($file, $to_file) {

        $sz_errors ='';

        if (!is_dir($to_file))
            return 'Неверно указано назначаение';

        $file_name = basename($file);
        $sz_errors = 'Загрузка ' . $file_name . ' : ';

            // remove bad charz
            $badcharz = array( '(', ')', '{', '}', '$', '%', '^', '&', '+', '|', '\\' ,'/', '[', ']', '*', '@', '!', '~', '?', '<', '>','#', ',' ,'`','=');

            while (list($key, $val)=each($badcharz)) {
                $file_name = str_replace($val,'',$file_name);
            }

            if ($file_name{0}=='.')    $file_name = date('jny_His') . $file_name;    //пустое имя файла заменяем на дату

            $to_file = $to_file . $file_name;

            // var_dump($to_file, getcwd());

            if (file_exists($to_file)) {
                $sz_errors .= "Ошибка. Файл $file_name уже существует. Удалите его вручную, либо переименуйте загружаемый.";
            }
            else
            {
                   $file_url = $file;
                $fl = @fopen($file_url, 'br');

                if (!$fl) {
                    return ('Error opening URL ' . $file);
                }

                $contents = '';
                while (!feof($fl)) {
                  $contents .= fread($fl, 8192);
                }
                fclose($fl);

                $fl_out = fopen($to_file, 'wb');
                fwrite($fl_out, $contents);
                fclose($fl_out);

                $l_size_after = filesize($to_file);

                $sz_errors = 'успешно сохранен как ' . htmlspecialchars($to_file) . ", " .round($l_size_after/1024,2) . " Кб ";
            }


        if (empty($sz_errors))
            $sz_errors = 'Неизвестная ошибка загрузки файла.';

        return $sz_errors;

    }
    
    function get_upload_error($e) {           
        switch ($e) {
            case UPLOAD_ERR_OK:
                return false;
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
}
