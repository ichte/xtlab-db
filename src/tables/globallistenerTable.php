<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use Zend\Db\Sql\Ddl\Column\Boolean;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Text;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Insert;

class globallistenerTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('global_listener', 'id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('classname',255,false, ''))
            ->correctColumn(new Varchar('event',1000,false, ''))
            ->correctColumn(new Varchar('description',1000,false, ''))
            ->correctColumn(new Text('code',null,false))
            ->correctColumn(new Integer('priority', false, 1))
            ->correctColumn(new Boolean('active', false, true));

        $ddl->deltebulkrows();


//        $ad = $ddl->getAdapter();
//        $datadefault = [
//            [
//                'classname' => 'ExampleListener',
//                'event' => 'test_event_for_globalistener',
//                'description' => 'block/header/headerblockdefault.phtml',
//                'placeholder' => 'header_layout'
//            ],
//
//        ];
//
//        foreach ($datadefault as $row) {
//            if (!$ad->existrow(['Controller'=>$row['Controller'], 'Action'=> $row['Action']], 'template_blockhtml')) {
//                $ad->insert($row, 'template_blockhtml');
//            }
//        }
    }

}