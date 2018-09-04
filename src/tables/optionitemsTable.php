<?php
namespace XT\Db\tables;


use XT\Db\DDL;
use XT\Db\EnumColunm;
use Zend\Db\Sql\Ddl\Column\Column;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\ForeignKey;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Ddl\Index\Index;
use Zend\Db\Sql\Insert;

class optionitemsTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        optiongroupsTable::execute($ddl);

        $ddl->correctTable('option_items', 'id', $engine = DDL::engine_innodb);
        $ddl

            ->correctColumn(new Integer('group_id', 50, 0),
                [new Index('group_id', 'group_id'),
                    new ForeignKey('option_items_group_fk',
                    'group_id',
                        'option_groups',
                        'id', 'CASCADE', 'CASCADE')])

            ->correctColumn(new Varchar('name',50,false, ''))
            ->correctColumn(new Varchar('description',255,false, ''))
            ->correctColumn(new Varchar('value',500,false, ''))
            ->correctColumn(new EnumColunm('type', false, 'string', [], "ENUM('string', 'integer', 'numeric', 'array', 'boolean', 'positive_integer', 'unsigned_integer', 'unsigned_numeric')"));

        $ddl->deltebulkrows();

        $ad = $ddl->getAdapter();
        $datadefault = [
            'common' => [

            ],
            'contactsite' => [

            ],
            'article' => [

            ],
            'product' => [

            ]
        ];

//        foreach ($datadefault as $row) {
//            if (!$ad->existrow(['name'=>$row['name']], 'placeholder')) {
//                $ad->insert($row, 'placeholder');
//            }
//        }
    }

}