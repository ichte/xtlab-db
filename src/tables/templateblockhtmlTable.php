<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use Zend\Db\Sql\Ddl\Column\Boolean;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Insert;

class templateblockhtmlTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('template_blockhtml', 'id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('Controller',255,false, ''))
            ->correctColumn(new Varchar('Action',255,false, ''))
            ->correctColumn(new Varchar('Block',255,false, ''))
            ->correctColumn(new Varchar('placeholder',100,false, ''))
            ->correctColumn(new Boolean('active', false, true));

        $ddl->deltebulkrows();

        $ad = $ddl->getAdapter();
        $datadefault = [
            [
                'Controller' => 'ALL',
                'Action' => 'ALL',
                'Block' => 'block/header/headerblockdefault.phtml',
                'placeholder' => 'header_layout'
            ],

        ];

        foreach ($datadefault as $row) {
            if (!$ad->existrow(['Controller'=>$row['Controller'], 'Action'=> $row['Action']], 'template_blockhtml')) {
                $ad->insert($row, 'template_blockhtml');
            }
        }
    }

}