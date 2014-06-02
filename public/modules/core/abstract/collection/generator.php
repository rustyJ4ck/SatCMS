<?php
  
/**
 * Code generator
 * @package    SatCMS
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 *
 * @todo doctrine does this better!
 */
 
abstract class abstract_generator {
    
    /** @var collection_generator */
    protected $_parent;
    protected $_objects;
    
    protected $_data;
    
    function __construct($p) {
        $this->_parent = $p;
        $this->_objects = $p->get_objects();
    }      
    
    /**
    * Generates,
    * assign data if nessesary to self::_data
    * @return mixed generated content
    */
    abstract function generate($p = null);
    abstract protected function _execute($p = null);
    
    function execute($p = null) {
        $this->_data = $this->generate($p);
        $this->_execute($p);
        return $this->_data;
    }
}

/**
* Generators collection
*/

class collection_generator {
    
    private $_objects;
    
    private $_generators;
    
    /**
    * @return AbstractGenerator
    */
    function get($id) {
        
        if (!$this->_generators->is_set($id)) {
            // @todo generators
            // require_once "modules/core/abstract/collection/generator/{$id}.php";
            $class = __NAMESPACE__ . "\\{$id}_generator";
            $this->_generators->set($id, new $class ($this));
        }
        
        return $this->_generators->get($id);
        
    }
    
    /**
    * @param string|array classes list
    * modules relative
    * core/class_name
    * content/class_name
    */
    function __construct($classes) {
        if (!is_array($classes)) $classes = empty($classes) ? array() : array($classes);  
        $this->create_objects($classes);      
        $this->_generators = new registry;
    }
    
    private function create_objects($classes) {                   
        $this->_objects = array();
        
        if (!empty($classes))        
        foreach ($classes as $class) {
            list($module, $class) = explode('.', $class);
            $this->_objects["{$module}_{$class}"] = core::module($module)->model($class);
            core::dprint(array("[GENERATOR] Add %s", "{$module}_{$class}"), core::E_MESSAGE);
        }
    }
    
    function clean() {
        $this->_objects = array();
        return $this;
    }

    function append_objects($objects) {
        foreach ($objects as $o) {
            $this->_objects[] = $o;
        }
    }

    function append_object($o, $model = null) {
        if ($model)
        $this->_objects[$model] = $o;
        else
        $this->_objects[] = $o;
    }
    
    /** 
    * @return array of models @see self::create_objects
    */                                                     
    function get_objects() {
        return $this->_objects;
    }
    
    /*
    CREATE TABLE `tf_posts` (
      `id` int(10) unsigned NOT NULL auto_increment,
      `faculty_id` int(10) unsigned NOT NULL,
      `date_posted` int(11) NOT NULL,
      `date_mod` int(11) NOT NULL,
      `name` varchar(127) collate utf8_bin NOT NULL,
      `title` varchar(255) collate utf8_bin NOT NULL,
      `status` int(3) NOT NULL default '0',
      `b_approved` int(1) NOT NULL default '0',
      `b_pay` int(1) NOT NULL default '0',
      `b_vip` int(1) NOT NULL default '0',
      `b_draft` int(1) NOT NULL default '0',
      `type_id` int(5) NOT NULL,
      `owner_id` int(11) NOT NULL,
      `c_comments` int(11) NOT NULL,
      `c_rating` int(9) NOT NULL default '0',
      `c_views` int(11) NOT NULL default '0',
      `c_unique_views` int(11) NOT NULL,
      `cat_id` int(11) NOT NULL default '0',
      `image` mediumtext collate utf8_bin NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `dcp` (`date_posted`,`c_rating`),
      KEY `fview` (`b_approved`,`b_draft`,`faculty_id`,`cat_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;
    */
    
    /*
    ["id"]=>
      array(1) {
        ["type"]=>
        string(7) "numeric"
      }
      ["value"]=>
      array(3) {
        ["type"]=>
        string(8) "position"
        ["space"]=>
        string(10) "faculty_id"
        ["autosave"]=>
        bool(true)
      }
    */

/*
TINYINT     1     -128     127
            0     255
SMALLINT    2     -32768     32767
            0     65535
MEDIUMINT   3     -8388608     8388607
            0     16777215
INT         4     -2147483648     2147483647
            0     4294967295
BIGINT      8     -9223372036854775808     9223372036854775807
            0     18446744073709551615
*/

    private function int_length_to_string($int) {
        switch ($int) {
            case 1: return 'TINYINT';
            case 2: return 'SMALLINT';
            case 3: return 'MEDIUMINT';
            case 4: return 'INT';
            case 8: return 'BIGINT';
            default: return 'INT';
        }
    }
    
    private function string_length_to_string($size, $allow_size) {
        $int = intval($size);
        $str = strtoupper($size);

        if (!$allow_size) {
            return $int < 1024 ? 'VARCHAR' : 'TEXT';
        }
          
        switch (-1) {                         
            case ($int < 256    && $int > 0):      return "VARCHAR({$int})";
            case ($int < 65536  && $int > 0):      return 'TEXT';                      
            case 'MEDIUM':
            case ($int < 16777216  && $int > 0):   return 'MEDIUMTEXT';                
            case 'LONG':
            case ($int < 4294967296  && $int > 0): return 'LONGTEXT';            
            default: return 'TEXT';
        }
    }

    /**
    * @param $object
    * @return bool
    */
    function table_exists($object) {
        $table = $object->get_table();
        // prefix_table | false
        // $result = $object->connection()->fetch_all('SHOW TABLES LIKE \'' . $table . '\'');
        $result = $object->connection()->get_tables($table);
        return (bool)$result;
    }

    /**
     * Get columns
     */
    function get_colums($object) {

        $table = $object->get_table();

        //$columns = $object->connection()->fetch_all('SHOW COLUMNS FROM ' . $table);
        $columns = $object->connection()->get_columns($table);

        $out = [];

        if (!empty($columns))
        foreach ($columns as $c) {

            $id = isset($c['field']) ? $c['field'] : @$c['name'];

            if (empty($id)) {
                throw new collection_exception('No key name in columns');
            }

            $out [$id]= $c;
        }
/*
 *  [10]=>
 array(6) {
   ["field"]=>   string(2) "dt"
   ["type"]=>   string(8) "datetime"
   ["null"]=>   string(2) "NO"
   ["key"]=>   string(0) ""
   ["default"]=>   NULL
   ["extra"]=>   string(0) ""
 }
 */
        return $out;
    }

    /**
     * @param $object Collection
     */
    function get_indexes($object) {

        // --SHOW INDEX FROM content_page
        $table = $object->get_table();

        //$columns = $object->connection()->fetch_all('SHOW INDEX FROM ' . $table);
        $columns = $object->connection()->get_indexes($table);

        $out = [];

        foreach ($columns as $c) {
            $out [$c['key_name']] = $c;
        }

/*
[2]=>
array(12) {
  ["table"]=>  string(12) "content_page"
  ["non_unique"]=>  int(1)
  ["key_name"]=>  string(4) "base" / "PRIMARY"
  ["seq_in_index"]=>  int(2)
  ["column_name"]=>  string(3) "pid"
  ["collation"]=>  string(1) "A"
  ["cardinality"]=>  NULL
  ["sub_part"]=>  NULL
  ["packed"]=>  NULL
  ["null"]=>  string(0) ""
  ["index_type"]=>  string(5) "BTREE"
  ["comment"]=>  string(0) ""
}
*/

        return $out;
    }

        /*
        TINYBLOB, TINYTEXT     L + 1 bytes, where L <  2^8          256
        BLOB, TEXT     L + 2 bytes, where L < 2^16                  65536
        MEDIUMBLOB, MEDIUMTEXT     L + 3 bytes, where L < 2^24      16777216
        LONGBLOB, LONGTEXT     L + 4 bytes, where L < 2^32          4294967296
        */
    
    /**
    * Generate sql
    * @param abs_collection $object
    */
    function generate_object_sql($object) {

        $columns = [];
        $existing = $this->table_exists($object);

        if ($existing) {
            $columns = $this->get_colums($object);
        }

        $schema = $object->config->get('schema');

        $engine = false;

        $key_allow_autoincrement = true;
        $field_allow_size = true;
        $field_unsigned = 'UNSIGNED';
        $field_primary_type = false;
        $allow_modify_column = true;

        $driver = $object->connection()->type();

        if ($driver == 'mysql') {
            $engine = empty($schema['engine']) ? 'MyISAM' : $schema['engine'];
        } else {
            // sqlite
            // sqlite not allow column modify
            // sqlite not allow multi column add/alter
            $key_allow_autoincrement = false;
            $field_unsigned = '';
            $field_allow_size = false;
            $field_primary_type = 'INTEGER';
            $allow_modify_column = false;
        }

        $isChanged = false;
        
        $sql = '';      
        
        $sql .= ("-- Table for " . get_class($object) . " \n\n");

        $head = ($existing ? 'ALTER TABLE ' : 'CREATE TABLE ') . $object->get_table();

        $sql .= $head;

        $sql .=  !$existing ? " (" : ' ';

        $vfs =  $object->get_fields();

        // $controls = $object->controls();

            $i = 1;
            foreach ($vfs as $key => $vf) {

                $sql .= '';

                $cfg = $vf; // $controls->control($key)->config;
                
                if (!in_array($vf['type'], array(
                    'virtual'
                ))) {

                  $sql_part_uptodate = 0;

                  if ($existing && $object->get_key() == $key) {
                      $sql_part_uptodate = 1;
                  }

                  $sql_part = '';
                  $sql_part .= ("\n" . ($i > 1 ? ', ' : ''));

                  if ($existing) {
                     if (isset($columns[$key])) {
                         $sql_part .= "MODIFY COLUMN `{$key}` ";

                         if (!$allow_modify_column) {
                             $sql_part_uptodate = true;
                         }

                     }
                     else {
                         $sql_part .= "ADD COLUMN `{$key}` ";
                     }
                  }
                  else {
                      $sql_part .= "`{$key}` ";
                  }
                }
                
                switch ($vf['type']) {

                    case 'list':
                    case 'relation':
                    case 'numeric':

                        if ($key == 'id' && $field_primary_type) {
                            $sql_part .= ($field_primary_type . ' ');
                        } else {
                            $size = (isset($vf['size']) ? $vf['size'] : 4);
                            $sql_part .= ((isset($vf['float']) && $vf['float']) ? 'FLOAT ' : ($this->int_length_to_string($size) . ' '));
                        }
                        $sql_part .= ($key == 'id' || (isset($vf['unsigned']) && $vf['unsigned']) ? ($field_unsigned . ' ') : '');

                        // sqlite: UPDATE without DEFAULT cant be NULL
                        if (!$existing || isset($vf['default'])) $sql_part .= 'NOT NULL ';

                        $sql_part .= (isset($vf['default']) ? "DEFAULT {$vf['default']} " : '');

                        if ($key == 'id' && $key_allow_autoincrement) $sql_part .= "AUTO_INCREMENT";
                        break;
                        
                    case 'boolean':
                        $default = empty($vf['default']) ? '0' : '1';
                        $sql_part .= "TINYINT {$field_unsigned} DEFAULT {$default} NOT NULL";
                        break;
                        
                    case 'file':
                    case 'image':
                        $sql_part .= "MEDIUMTEXT NOT NULL DEFAULT ''";
                        break;
                        
                    case 'unixtime':
                        $sql_part .= "INT {$field_unsigned}"; //NOT NULL DEFAULT 0
                        break;

                    case 'datetime':
                        $sql_part .= "DATETIME NOT NULL"; // DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        break;

                    case 'timestamp':
                        $sql_part .= "TIMESTAMP NOT NULL"; // DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        break;

                    case 'currency':
                    case 'decimal':
                        $size = (isset($cfg['size']) ? $cfg['size'] : [8,2]);
                        $sql_part .= ("DECIMAL(" . join(',', $size) . ") NOT NULL");
                        break;

                    case 'position':
                        $size = (isset($vfs['id']['size']) ? $vfs['id']['size'] : 4);
                        $sql_part .= ($this->int_length_to_string($size) . " {$field_unsigned} NOT NULL");
                        break;
                    
                    case 'text':
                        $sql_part .= ($this->string_length_to_string(@$vf['size'], $field_allow_size) . ' NOT NULL DEFAULT \'\'');
                        break;

                    case 'virtual':
                        $sql_part_uptodate = 1;
                        break;

                    // virtuals
                    default:
                        break;
                }

                if (!$sql_part_uptodate) {
                    $isChanged = true;
                    $sql .= $sql_part;
                    $i++;
                }
                
            }

        // DROP obsoletes

        /*
         * Oracle: ALTER TABLE table_name DROP (column_name1, column_name2);
         * MS SQL: ALTER TABLE table_name DROP COLUMN column_name1, column_name2
         *  MySql: ALTER TABLE table_name DROP column_name1, DROP column_name2;
         */

        // sqlite doesn suport dropping columns
        if ($existing) {
            $i = 0;
            foreach ($columns as $key => $v) {
                if (!isset($vfs[$key])) {
                    $isChanged = true;
                    $sql .= (($i > 0 ? ', ' : '') . "\n");
                    $sql .= "DROP COLUMN `{$key}`";
                    $i++;
                }
            }

            // SQLITE-multicolumn
            if (strpos($driver, 'sqlite')===0) {
                $sql = str_replace(',', ";\n" . $head, $sql);
            }
        }


        if (!$existing) {
            if (($pkey = $object->get_key()) && isset($vfs[$pkey])) {
                $sql .= "\n, PRIMARY KEY  (`{$pkey}`) ";
            }

            $sql .= "\n)";

            if ($engine) {
                $sql .= " ENGINE={$engine}";
            }
        }

        $sql .= ";\n\n";

        if ($existing && !$isChanged) {
            $sql = false;
        }

        return $sql;
    }
    
    /**
    * @param \TF\Core\abs_collection
    */
    function generate_object_sql_extra($object) {

        $schema = $object->config->get('schema');

        if (empty($schema)) return;
        
        $sql = array(
            'indexes' => array()
        );

        $indexes = [];

        if ($this->table_exists($object)) {
            $indexes = $this->get_indexes($object);
        }

        if (isset($schema['indexes'])) {
            foreach ($schema['indexes'] as $index_k => $index_v) {
                if (!is_array($index_v)) $index_v = array($index_v);
                
                foreach ($index_v as &$v) $v = sprintf('`%s`', $v); //@todo escape thru db-layer
                $index_v = join(',', $index_v);

                if (isset($indexes[$index_k])) {
                    $sql['indexes'] []= sprintf('ALTER TABLE `%s` DROP INDEX `%s`;' . "\n", $object->get_table(), $index_k);
                }
                $sql['indexes'] []= sprintf('ALTER TABLE `%s` ADD INDEX `%s` (%s);' . "\n", $object->get_table(), $index_k, $index_v);

            }
        }
        
        return $sql;
    }

    /**
    * @return string sql schema
    */
    function render_table_structure() {
        $sql = '';        
        foreach ($this->_objects as $object) {   
            $sql .= $this->generate_object_sql($object);                 
        }   
        
        $sql .= "\n\n-- Extra\n\n";
        $sql .= $this->render_table_structure_extra($object);
                 
        return $sql;
    }
    
    /**
    * @return string sql schema
    */
    function render_table_structure_extra() {
        $sql = '';        
        foreach ($this->_objects as $object) {   
            $_sql = $this->generate_object_sql_extra($object);
            if (!empty($_sql['indexes'])) {
                foreach ($_sql['indexes'] as $v) $sql .= $v;
            }
        }            
        return $sql;
    }    
    
    function drop_table($object) {
            $sql = '';                
            $sql .= ("-- Drop " . get_class($object) . " \n\n");             
            $sql .= 'DROP TABLE ';
            $sql .= ($object->get_table() . ";\n");
            return $sql;                                  
    }
    
    
    /**
    * Update structure
    * Warning! This method do direct changes to database
    */
    function update_table_structure($drop = false) {

        /** @var abs_collection $object */
        foreach ($this->_objects as $object) {

            $db = $object->connection();

            if ($drop) {
                $sql = $this->drop_table($object);
                core::dprint(__METHOD__ . ' drop table', core::E_MESSAGE);
                $this->run_queries($db, $sql);
            }

            $sql = $this->generate_object_sql($object);

            core::dprint(__METHOD__ . ' generate object: ' . get_class($object), core::E_MESSAGE);

            if ($sql) {
                $this->run_queries($db, $sql);
            } else {
                core::dprint('..uptodate', core::E_MESSAGE);
            }
            
            // indexes
            $_sql = $this->generate_object_sql_extra($object);
            if (!empty($_sql['indexes'])) {
                foreach ($_sql['indexes'] as $sql) {
                    core::dprint(__METHOD__ . ' indexes', core::E_MESSAGE);
                    $this->run_queries($db, $sql);
                }
            }            
        }            
    }

    /**
     * @param dbal $db
     * @param string $sql
     */
    function run_queries($db, $sql) {
        if (strpos($sql, ';') !== false) {
            $sql = explode(';', $sql);
            foreach ($sql as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $db->query($query);
                }
            }
        } else {
            $db->query($sql);
        }
    }
}
