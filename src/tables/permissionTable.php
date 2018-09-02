<?php

namespace XT\Db\tables;


use XT\Core\System\RBAC_CONST;
use XT\Db\DDL;

use Zend\Db\Sql\Ddl\Column\Datetime;


use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;


class permissionTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {
        $ddl->correctTable('permission', 'id', $engine = DDL::engine_innodb);
        $ddl
            ->correctColumn(new Varchar('name', 128, false), [new UniqueKey('name', 'name_idx')])
            ->correctColumn(new Varchar('description', 1024, false, ''))
            ->correctColumn(new Datetime('date_created', false));


        if (class_exists('XT\Core\System\RBAC_CONST')) {
            
            $ad = $ddl->getAdapter();
            $datadefault = [];
            $oClass = new \ReflectionClass(RBAC_CONST::class);
            $oClass->getConstants();
            foreach ($oClass->getConstants() as $constant) {
                $datadefault[] = [
                    'name' => $constant,
                    'description' => $constant,
                    'date_created' => date('Y-m-d H:i:s')
                ];
            }


            foreach ($datadefault as $row) {
                if (!$ad->existrow(['name' => $row['name']], 'permission')) {
                    $ad->insert($row, 'permission');
                }
            }
        }


        $ddl->deltebulkrows();

    }
}
