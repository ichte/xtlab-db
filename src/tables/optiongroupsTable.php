<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use XT\Db\EnumColunm;
use Zend\Db\Sql\Ddl\Column\Column;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Ddl\Index\Index;
use Zend\Db\Sql\Insert;

class optiongroupsTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        $ddl->correctTable('option_groups', 'id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('name',50,false, ''), [new UniqueKey('name', 'name_unique'), new Index('name', 'name')])
            ->correctColumn(new Varchar('description', 255, false, ''));

        $ddl->deltebulkrows();

        $ad = $ddl->getAdapter();
        $datadefault = [
            [
              'name' => 'common',
              'description' => 'Basic setting for website'
            ],
            [
                'name' => 'contactsite',
                'description' => 'Contact options'
            ],
            [
                'name' => 'article',
                'description' => 'Option for Article'
            ],
            [
                'name' => 'product',
                'description' => 'Options for Products Module'
            ],

        ];

        foreach ($datadefault as $row) {
            if (!$ad->existrow(['name'=>$row['name']], 'option_groups')) {
                $ad->insert($row, 'option_groups');
            }
        }
    }

}