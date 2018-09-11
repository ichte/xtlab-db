<?php
namespace XT\Db\tables;


use XT\Core\Event\Listener\ViewPlace\SampleViewplace;
use XT\Db\DDL;
use Zend\Db\Sql\Ddl\Column\Boolean;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Insert;

class viewplaceTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('viewplace', 'id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('Controller',255,false, ''))
            ->correctColumn(new Varchar('Action',255,false, ''))
            ->correctColumn(new Varchar('Event',255,false, ''))
            ->correctColumn(new Varchar('Class',255,false, ''))
            ->correctColumn(new Boolean('active', false, true));

        $ddl->deltebulkrows();

        $ad = $ddl->getAdapter();
        $datadefault = [
            [
                'Controller' => 'XT\Admin\Controller\AdminController',
                'Action' => 'index',
                'Event' => 'end_html_footer_layout',
                'Class' => SampleViewplace::class
            ],

        ];

        foreach ($datadefault as $row) {
            if (!$ad->existrow(['Controller'=>$row['Controller'], 'Action'=> $row['Action']], 'viewplace')) {
                $ad->insert($row, 'viewplace');
            }
        }
    }

}