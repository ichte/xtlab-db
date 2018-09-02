<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use Zend\Db\Sql\Ddl\Column\Datetime;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Text;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;

class logTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('log', 'log_id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Datetime('date',100))
            ->correctColumn(new Integer('type',true))
            ->correctColumn(new Text('event',1000,true));

        $ddl->deltebulkrows();
    }

}