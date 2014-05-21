<?php

/**
 * @package TwoFace
 * @version $Id: fs.php,v 1.5.2.1 2011/02/24 09:17:43 Vladimir Exp $
 * @copyright (c) 2007 4style
 * @author surgeon <r00t@skillz.ru>
 */


/**
 * FileSystem
 *
 * This is not core lib,
 * use it as static class fs::
 */
class fs {

    /** use with self::req */
    const ONCE = true;

    /**
     * Get File Full Path
     */
    public static function get_full_path($name) {
        return loader::get_public() . $name;
    }

    /**
     * Check file presents
     * @param string file name
     * @param mixed FALSE(default) - filename must be full path, otherwise ROOT_PATH added
     */
    public static function file_exists($name, $prefix = false) {
        return file_exists(($prefix === false ? '' : loader::get_public()) . $name);
    }

    /**
     * Сканировать директорию на файлы
     */
    public static function scan_dir_for_files($o_dir, $files_preg = '') {
        $ret = array();
        $dir = @opendir($o_dir);

        if (!$dir) return $ret;

        while (false !== ($file = @readdir($dir))) {
            $path = $o_dir . '/' . $file;
            if (!is_dir($path) && $file != '..' && $file != '.'
                && (empty($files_preg) || (!empty($files_preg) && preg_match("#{$files_preg}#", $file)))
            ) {
                $ret [] = $path;
            }
        }
        @closedir($dir);

        return $ret;

    }

    /**
     * Сканировать директории
     */
    public static function scan_dir_for_dirs($o_dir) {

        $ret = array();
        $dir = @opendir($o_dir);

        if (!$dir) return $ret;

        while (false !== ($file = @readdir($dir))) {
            $path = $o_dir . '/' . $file;
            if (is_dir($path) && $file != '..' && $file != '.') {
                $ret [] = $path;
            }
        }

        @closedir($dir);

        return $ret;

    }

    /**
     * Строим дерево из директорий/файлов
     *
     * @desc build tree
     * @param string Корневая директория для индекса
     * @param array возвращаемые данные
     * @param array фильтр директорий (массив)
     * @param string рег.выр для фильтра файлов
     * @return array['files','dirs']
     */
    public static function build_tree($root_path, array &$data, $dirs_filter = array(), $files_preg = '.*') {

        if (empty($data)) {
            $data['files'] = array();
            $data['dirs']  = array();
        }

        if (!$root_path || !is_dir($root_path)) return false;

        if (substr($root_path, -1, 1) == '/') $root_path = substr($root_path, 0, -1);

        $dirs  = self::scan_dir_for_dirs($root_path);
        $files = self::scan_dir_for_files($root_path, $files_preg);

        $data['dirs'][] = $root_path;
        $data['files']  = array_merge($data['files'], $files);

        foreach ($dirs as $dir) {
            // проверяем фильтр
            if (empty($dirs_filter) || !in_array(preg_replace('/^.*\/(.*)$/', '\1', $dir), $dirs_filter))
                self::build_tree($dir, $data, $dirs_filter, $files_preg);
        }
    }

    /**
     * Require system wrapper
     * @param string path (relative to include_dir)
     * @param bool (use fs::ONCE if need _once operation)
     */
    public static function req($file, $once = false) {
        $once ? $result = include_once($file) : $result = include($file);
        if (!$result) throw new fs_exception('File ' . $file . ' not found ');

        return true;
    }

    /**
     * Include system wrapper
     */
    public static function inc($file, $once = false) {
        return ($once ? include_once $file : include $file);
    }

    /**
     * Unlink
     * @return bool status
     */
    public static function unlink($file, $is_dir = false) {
        if (!file_exists($file)) return false;
        $res = ($is_dir) ? @rmdir($file) : @unlink($file);
        core::dprint(array('fs::unlink %s : %s', $file, $res ? 'OK' : 'FAIL'));

        return true;
    }

    /**
     * uniq file name
     */
    public static function unique_filename($path, $ext) {
        return ($path . md5(microtime(true)) . '.' . $ext);
    }

    /**
     * Get mime for a file
     * @param string path to file
     */
    static function get_mime($file) {
        $mime  = false;
        $finfo = class_exists('finfo', 0) ? new finfo(FILEINFO_MIME) : false;
        if ($finfo) {
            $mime = $finfo->file($file);
            // $finfo->close();     
        } else {
            $mime = mime_content_type($file);
        }

        return $mime;
    }

    /**
     * File ext by mime
     * @return string mime type
     */
    static function get_ext_by_mime($file) {
        $mime   = self::get_mime($file);
        $return = false;
        switch ($mime) {
            case 'image/gif':
                $return = 'gif';
                break;
            case 'image/jpeg':
                $return = 'jpg';
                break;
            case 'image/png':
                $return = 'png';
                break;
        }

        return $return;
    }


}
