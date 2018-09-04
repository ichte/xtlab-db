<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 21-Aug-17
 * Time: 1:24 AM
 */

namespace XT\Db\tables;


use XT\Core\System\RBAC_PER_DESCRIPTION;
use XT\Core\System\RBAC_PERMISSION;
use XT\Core\System\RBAC_ROLE;
use XT\Db\DDL;


use Zend\Db\Sql\Ddl\Column\Integer;


use Zend\Db\Sql\Ddl\Constraint\ForeignKey;

use Zend\Db\Sql\Ddl\Index\Index;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class rolehierarchyTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        roleTable::execute($ddl);
        permissionTable::execute($ddl);



        $ddl->dropFk('role_hierarchy_child_fk', 'role_hierarchy');
        $ddl->dropFk('role_hierarchy_parent_fk', 'role_hierarchy');


        $ddl->correctTable('role_hierarchy', 'id', $engine = DDL::engine_innodb);

        $ddl

            ->correctColumn(new Integer('parent_role_id'),
                [
                    new Index('parent_role_id', 'parent_role_id_index'),
                    new ForeignKey('role_hierarchy_parent_fk', 'parent_role_id','role', 'id', 'CASCADE', 'CASCADE'),

                ]
            )
            ->correctColumn(new Integer('child_role_id'),
                [
                    new Index('child_role_id', 'child_role_id_index'),
                    new ForeignKey('role_hierarchy_child_fk', 'child_role_id','role', 'id', 'CASCADE', 'CASCADE')
                ]);


        //Inset Default
        $ad = $ddl->getAdapter();

        foreach (RBAC_PER_DESCRIPTION::$rolehiearchy as $parent => $children) {
            $roleParent = $ad->cell('role', 'id', ['name' =>$parent]);
            foreach ($children as $child) {
                    $roleChild  = $ad->cell('role', 'id', ['name' =>$child]);
                    if ($roleChild != null && $roleParent != null) {

                        $select = new Select('role_hierarchy');
                        $select->columns([new Expression('COUNT(1) as C')])
                            ->where->equalTo('child_role_id', $roleChild[0])
                                   ->equalTo('parent_role_id', $roleParent[0]);
                        //echo $select->getSqlString($ad->getPlatform());
                        $c = $ad->get_row_select($select)['C'];

                        if ($c == 0)
                            $ad->insert(['child_role_id' => $roleChild[0], 'parent_role_id' => $roleParent[0]], 'role_hierarchy');
                    }
            }
        }




        $ddl->deltebulkrows();

    }
}
