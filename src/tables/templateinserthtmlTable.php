<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use Zend\Db\Sql\Ddl\Column\Boolean;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Insert;

class templateinserthtmlTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('template_inserthtml', 'id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('Controller',255,false, ''))
            ->correctColumn(new Varchar('Action',255,false, ''))
            ->correctColumn(new Varchar('Event',255,false, ''))
            ->correctColumn(new Varchar('Block',255,false, ''))
            ->correctColumn(new Boolean('active', false, true));

        $ddl->deltebulkrows();

        $ad = $ddl->getAdapter();
        $datadefault = [
            [
                'Controller' => 'ALL',
                'Action' => 'ALL',
                'Event' => 'start_block_header_layout',
                'Block' => 'header/header.phtml'
            ],

        ];

        foreach ($datadefault as $row) {
            if (!$ad->existrow(['Controller'=>$row['Controller'], 'Action'=> $row['Action']], 'template_inserthtml')) {
                $ad->insert($row, 'template_inserthtml');
            }
        }
    }

}