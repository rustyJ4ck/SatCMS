<?php
  
/**
 *  Video convertor lib
 * 
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: video_conv.php,v 1.3 2010/07/21 17:57:17 surg30n Exp $
 */
 
class video_conv extends abs_config {
    
    /*
        On freebsd use - /usr/local/bin
        default - linux
    */
    protected $ffmpeg_exe   = '/usr/bin/ffmpeg';
    protected $flvtool_exe  = '/usr/bin/flvtool2 -U';
    protected $qt_faststart_exe = '/usr/bin/qt-faststart';
    
    protected $ffmpeg_params;    
    protected $source;
    
    /** Override existed files */
    protected $_force_override = true;
    
    /**
    * Last output
    */
    private $_output;
    
    /**
    * Under construct
    */
    function __construct() {
        $this->clear();
        
        if (loader::is_windows()) {
            // where is it?
            $this->ffmpeg_exe  = 'c:\\bin\\flvencoder\\ffmpeg.exe';
            $this->flvtool_exe = 'c:\\bin\\flvencoder\\flvtool2.exe u';
        }
        
        if (PHP_OS == 'FreeBSD') {
            $this->ffmpeg_exe  = str_replace('/bin', '/local/bin', $this->ffmpeg_exe);
            $this->flvtool_exe = str_replace('/bin', '/local/bin', $this->flvtool_exe);
            $this->qt_faststart_exe = str_replace('/bin', '/local/bin', $this->qt_faststart_exe);
        }
        
        if (!$this->valid_encoder()) core::dprint('[video_conv] Invalid encoder', core::E_ERROR);
    }
    
    /**
    * Clear internal params
    */
    function clear() {
        $this->source = '';
        $this->ffmpeg_params = '';
    }
    
    /**
    * Set overriode mode
    */
    function set_override_mode($force) {
        $this->_force_override = $force;
    }
    
    /**
    * Write file
    * @todo make this work!
    */
    function file_put_contents($file, $data) {
        if (file_exists($file) && $this->_force_override) unlink($file);        
    }
    
    /**
    * Set source files to proccess
    * @param mixed 
    */
    function set_source($file) {
        $this->clear();
        $this->source = $file; //(is_string($files)) ? array($files) : $files;
        if (empty($this->source) || !file_exists($this->source)) throw new lib_exception('Source not exists : ' . $this->source);
    }
    
    /**
    * Set param to ffmpeg
    */
    function set_params($params) {
        $this->ffmpeg_params = $params;
    }
    
    /**
    * Get file info
    * 
    * ffmpeg -i ./ef913476336b2d00a63d00a53c722351.avi
    */
    function get_video_info($src_file = false) {
        
        $params  = '-i ';
        $params .= ($src_file ? $src_file : $this->source);
        $this->set_params($params); 
        $output = $this->run();           
                
     /*
       
       FLV
         Input #0, avi, from './ef913476336b2d00a63d00a53c722351.avi':
         Duration: 00:01:56.0, start: 0.000000, bitrate: 427 kb/s
         Stream #0.0: Video: mpeg4, yuv420p, 320x240, 25.00 fps(r)
         Stream #0.1: Audio: liba52, 48000 Hz, stereo, 256 kb/s";
       
       MP4
          Duration: 00:00:16.2, start: 0.000000, bitrate: 602 kb/s
          Stream #0.0(und): Video: h264, yuv420p, 384x288, 29.97 fps(r)
          Stream #0.1(und): Audio: mp3, 44100 Hz, stereo, 96 kb/s
     
       win
         Stream #0.0: Video: flv, 320x240, 1000.00 fps
         Stream #0.1: Audio: mp3, 22050 Hz, mono
     */
         
        $return = array();
        
        preg_match('#Duration: ([\d]+)\:([\d]+)\:([\d]+).* bitrate\: ([\d]+\s[\w\/]+)#', $output, $matches);
        
        $return += array(
              'time'         => @intval($matches[1] * 3600 + $matches[2] * 60 + $matches[3])
            , 'v_bitrate'    => @intval($matches[4])
        );
        
        if (!preg_match('#Video: ([\w\d]+), ([\w\d]+), ([\d]+)x([\d]+), ([\d]+)#', $output, $matches)) {
            // ffmpeg windows?
            preg_match('#Video: ([\w\d]+),(\s*)([\d]+)x([\d]+), ([\d]+)#', $output, $matches);
        }
        
        $return += array( 
              'v_container' => $matches[1]
            , 'v_codec'     => $matches[2]
            , 'v_width'     => $matches[3]
            , 'v_height'    => $matches[4]
            , 'v_fps'       => $matches[5]
        );

        preg_match('#Audio: ([\w\d]+), ([\d]+) Hz, ([\w]+)(?:, ([\d]+\s[\w\/]+))?#', $output, $matches);
        
        $return += array( 
              'a_codec'     => $matches[1]
            , 'a_rate'      => $matches[2]
            , 'a_format'    => $matches[3]
            , 'a_bitrate'   => @intval($matches[4])            
        );   
        
        $return['size'] = filesize($src_file);             
        
        return $return;                     
    }
    
    /**
    * Extract frames
    * @param string  template with %d placeholder for frame number
    * @param integer parts count
    */
    function extract_frames($dst_file, $count = 10, $dim = array()) {
        
        $info = $this->get_video_info();
        $frame_offset = 10;
        $length = $info['time'] - $frame_offset;
        $parts_length = (int)($length / $count);
        
        for ($i = 1; $i <= $count; $i++) {    
            $this->extract_frame(
                  sprintf($dst_file, $i)
                , $frame_offset + $parts_length * ($i - 1)
                , $dim
            );
        }
    }
        
    /**    
    * Get Frame to file
    * 
    * Format is jpg
    * /usr/local/bin/ffmpeg -i ./268988.wmv -an -ss 00:00:03 -t 00:00:01 -r 1 -y -s 320x240 ./frame%d.wmv.jpg
    * 
    * @param string  dest file (must be .jpg)
    * @param integer frame sec
    * @param mixed array(width, height)
    * @return bool result 
    */
    function extract_frame($dst_file, $sec = 5, $dim = array()) {      
        
        $dst_file_ = preg_replace('/(\.[^.]+)$/', '%d$1', $dst_file);
        
        $params  = '-i ';
        $params .= $this->source;
        $params .= " -an -vframes 1 -ss {$sec} -t 00:00:01 -r 1 -y ";
        if (!empty($dim)) 
            $params .= "-s {$dim[0]}x{$dim[1]} ";
        $params .= $dst_file_;
                
        $this->set_params($params);  
        
        $this->run(); 
        
        // move to original dst
        @unlink($dst_file);
        rename(sprintf($dst_file_, 1), $dst_file);
        
        return true;             
    }
    
    /*
    
      NEW MP4 STUFF  
    
      infile ="video.avi"
      tmpfile="video_tmp.mp4"
      outfile="video.mp4"
      options="-vcodec libx264 -b 512k -flags +loop+mv4 -cmp 256 \
           -partitions +parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 \
           -me_method hex -subq 7 -trellis 1 -refs 5 -bf 3 \
           -flags2 +bpyramid+wpred+mixed_refs+dct8x8 -coder 1 -me_range 16 \
               -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10\
           -qmax 51 -qdiff 4"

      ffmpeg -y -i "$infile" -an -pass 1 -threads 2 $options "$tmpfile"

      ffmpeg -y -i "$infile" -acodec libfaac -ar 44100 -ab 96k -pass 2 -threads 2 $options "$tmpfile"

      qt-faststart "$tmpfile" "$outfile"

    */
    
    /**
    * MP4 h264
    */
    function convert_to_mp4($dst_file, $length = false, $dim = false /*array(320, 240)*/) {
        
        $source = $this->source;
        
        $tmp_file = loader::get_temp() . microtime(true) . '.mp4';
        
        // -loop 1 -me hex -me_range 16 
        $options = "-vcodec libx264 -b 512k -flags +loop+mv4 -cmp 256 \
                   -partitions +parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 \
                   -me_method hex -subq 7 -trellis 1 -refs 5 -bf 3 \
                   -flags2 +bpyramid+wpred+mixed_refs+dct8x8 -coder 1 -me_range 16 \
                   -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10\
                   -qmax 51 -qdiff 4";

        if (!empty($dim)) 
            $options .= " -s {$dim[0]}x{$dim[1]}";
        
        if (!empty($length)) {
            $options .= " -t {$length}";
        }                                                  
                   
        $this->set_params("-y -i \"$source\" -an -pass 1 -threads 2 $options \"$tmp_file\"");
        $this->run(); 
        
        // libmp3lame
        $acodec = "-acodec libfaac -ar 44100 -ab 96k";
        
        $this->set_params("-y -i \"$source\" {$acodec} -pass 2 -threads 2 $options \"$tmp_file\"");
        $this->run();                           
        
        $this->run_qt_faststart($tmp_file, $dst_file);
        
        @unlink($tmp_file);            
        
        return true;
    }
    
    /**
    * Create trailer
    */
    function create_mp4_trailer($dst_file, $sec = 60) {
        $this->convert_to_mp4($dst_file, $sec);
    }
    
    /**
    * Convert
    * /usr/local/bin/ffmpeg -i ./268988.wmv -ar 22050 -ab 32 -f flv -s 320x240 ./wmv.flv
    */
    function convert_to_flv($dst_file, $dim = false /*array(320, 240)*/) {
        
        $params  = '-i ';  
        $params .= $this->source;
        $params .= " -y -ar 22050 -ab 32k -f flv ";
        if (!empty($dim)) 
            $params .= "-s {$dim[0]}x{$dim[1]} ";
        $params .= $dst_file;
        
        $this->set_params($params);          
        $this->run(); 
        $this->run_flvtool($dst_file);
        return true;
    }

    /**
    * Create trailer
    */
    function create_flv_trailer($dst_file, $sec = 60) {
        $params  = '-i ';  
        $params .= $this->source;
        $params .= " -t {$sec} -ar 22050 -ab 32k -f flv ";      
        $params .= $dst_file;
        
        $this->set_params($params);          
        $this->run(); 
        $this->run_flvtool($dst_file);
        return true;
    }
    
    /**
    * flv tool
    */
    protected function run_flvtool($file) {
        $cmd = $this->flvtool_exe . ' ' . $file . ' 2>&1';
        
        // check exists
        preg_match('#^([^\s]+)#', $this->flvtool_exe, $matches);
        $check = $matches[1];
        if (file_exists($check) && is_executable($check)) {
             shell_exec($cmd);        
             core::dprint($cmd); 
        }    
        else {
            core::dprint('[video_conv] flvtool failed', core::E_ERROR);
        }
        return true;
    }
    
    /**
    * flv tool
    */
    protected function run_qt_faststart($in, $out) {
        $cmd = $this->qt_faststart_exe . ' "' . $in . '" "' . $out . '" 2>&1';
        
        // check exists
        preg_match('#^([^\s]+)#', $this->qt_faststart_exe, $matches);
        $check = $matches[1];
        if (file_exists($check) && is_executable($check)) {
             shell_exec($cmd);        
             core::dprint($cmd); 
        }    
        else {
            core::dprint('[video_conv] qt_faststart failed', core::E_ERROR);
        }
        return true;
    }    
    
    /*
    # ffmpeg2pass-0.log
    # x264_2pass.log

        X="./26.mpg"
        tmpfile="./tmp-file.mp4"

        options="-vcodec libx264 -b 512k -bf 3 -subq 6 -cmp 256 -refs 5 -qmin 10 \
                 -qmax 51 -qdiff 4 -coder 1 -loop 1 -me hex -me_range 16 -trellis 1 \
                 -flags +mv4 -flags2 +bpyramid+wpred+mixed_refs+brdo+8x8dct \
                 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 -g 250 \
                 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71"

        ffmpeg -y -i "$X" -an -pass 1 -threads 2 $options "$tmpfile"

        # libfaac
        ffmpeg -y -i "$X" -acodec libmp3lame -ar 44100 -ab 96k -pass 2 \
             -threads 2 $options "$tmpfile"
     */
    
    /**
    * MP4
    */
    
    /**
    * Run 
    * /usr/local/bin/ffmpeg -i ./268988.wmv -an -ss 00:00:03 -t 00:00:01 -r 1 -y -s 320x240 ./frame%d.wmv.jpg
    */
    protected function run() {             
        $cmd = $this->ffmpeg_exe . ' ' . $this->ffmpeg_params . ' 2>&1';        
        $this->_output = false;
        if ($this->valid_encoder()) {
            $this->_output = shell_exec($cmd);         
        }   
        core::dprint($cmd);
        return $this->_output;
    }
    
    /**
    * Verify all files in right place
    * 
    * @todo this will fail when openbasedir used
    */
    protected function valid_encoder() {
        return (file_exists($this->ffmpeg_exe) && is_executable($this->ffmpeg_exe));
    }
    
    /*
    /usr/local/bin/ffmpeg -i /www/p1.ru/htdocs/uploads/posts/videos/3a6945e21908d7fac75086e0b1145786.wmv -ar 22050 -ab 32 -f flv /usr/local/www/vhosts/p1.ru/tmp/f4d63bad3dd31aa0cdd5066f86e050cc.flv
    */
    
    /**
    * get last output
    */
    function get_output() {
        return $this->_output;
    }
        
}
