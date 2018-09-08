<?php
namespace XT\Db\tables;


use XT\Core\Common\Common;
use XT\Core\IDE\Config\ArticleConfig;
use XT\Core\IDE\Config\CommonConfig;
use XT\Core\IDE\Config\ContactConfig;
use XT\Core\IDE\Config\ProductConfig;
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

        $ddl->dropFk('option_items_group_fk', 'option_items');

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
            ->correctColumn(new Varchar('hint', 100, false, ''))
            ->correctColumn(new Varchar('description',255,false, ''))
            ->correctColumn(new Varchar('value',500,false, ''))
            ->correctColumn(new EnumColunm('type', false, 'string', [], "ENUM('string', 'integer', 'numeric', 'array', 'boolean', 'positive_integer', 'unsigned_integer', 'unsigned_numeric')"));

        $ddl->deltebulkrows();

         

        $ad = $ddl->getAdapter();
        $datadefault = [
            'common' => CommonConfig::$data,
            'contactsite' => ContactConfig::$data,
            'article' => ArticleConfig::$data,
            'product' => ProductConfig::$data
        ];

        //name
        //hint
        //description
        //value
        //type

        foreach ($datadefault as $groupname => $options) {

            if ($ad->existrow(['name'=>$groupname], 'option_groups')) {
                $groupid = $ad->cell('option_groups', 'id', ['name' => $groupname])[0];
                foreach ($options as $option) {
                    if (!$ad->existrow(['name'=>$option[0]], 'option_items')) {
                        $isdata = [
                            'name' => $option[0],
                            'hint' => $option[1],
                            'description' => $option[2],
                            'value' => $option[3],
                            'type' => $option[4],
                            'group_id' => $groupid
                        ];

                        $ad->insert($isdata, 'option_items');

                    }
                }
            }

        }
    }

}