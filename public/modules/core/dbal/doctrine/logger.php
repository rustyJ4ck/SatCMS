<?php

namespace SatCMS\Modules\Core\Dbal\Doctrine;

use \core;

class Logger implements \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->_time = microtime(1);
        $this->_sql = $sql;
        $this->_params = $params;
        $this->_types = $types;   
    	
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if (class_exists('\core', 0)) {
            $time = microtime(1) - $this->_time;            
            \core::dprint(array('%s -- %.5f', $this->_sql, $time), \core::E_SQL);
        }
    }
}

