<?php

//not implemented
return;

set_time_limit(0);

define('DISABLE_TR', 1);

                                              
require "../_loader.php";    
$core = core::get_instance();  

        $file = loader::get_root() . 'modules/content/tests/news_import/img/1.jpg'; 
                
        $collection = $core->class_register('test_images');

        $data = array();

        $data['title']    = uniqid();
        $data['active']   = 1;
        
        $filename =  $file;
        
        printf("exists %d <br/><br/>", file_exists($filename));
        
        $data['image'] = array(
              'name'      => basename($filename)
            , 'tmp_name'  => $filename
            , 'size'      => 1
         );
                                   
        $id = $collection->create($data);
         
        $item = $collection->get_last_item();
        
        $item = $collection->load_only_id($item->id);
        
        echo "1)<br/><br/>";
        
        var_dump(
        $item->image
        ,
        $item->render()
        
        );
        
        echo "<br/><br/>2)<br/><br/>";
        
        $item = $collection->load_only_id($item->id);
        
        var_dump(
        $item->image
        ,
        $item->render()
        
        );
        
