<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Insert;

class placeholderTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('placeholder', 'placeholder_id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('name',100,false), [new UniqueKey('name')])
            ->correctColumn(new Varchar('description',500,true));

        $ddl->deltebulkrows();

        $ad = $ddl->getAdapter();
        $datadefault = [
            [
              'name' => 'breadcrumbs',
              'description' => 'breadcrumbs'
            ],
            [
                'name' => 'content',
                'description' => 'content'
            ],
            [
                'name' => 'content_aside',
                'description' => 'content_aside'
            ],
            [
                'name' => 'footer_layout',
                'description' => 'footer_layout'
            ],
            [
                'name' => 'header_layout',
                'description' => 'header_layout'
            ],
            [
                'name' => 'nav_sidebar',
                'description' => 'nav_sidebar'
            ],
        ];

        foreach ($datadefault as $row) {
            if (!$ad->existrow(['name'=>$row['name']], 'placeholder')) {
                $ad->insert($row, 'placeholder');
            }
        }
    }

}