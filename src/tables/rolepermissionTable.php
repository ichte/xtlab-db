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

class rolepermissionTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        roleTable::execute($ddl);
        permissionTable::execute($ddl);



        $ddl->dropFk('role_id_fk', 'role_permission');
        $ddl->dropFk('permission_id_fk', 'role_permission');


        $ddl->correctTable('role_permission', 'id', $engine = DDL::engine_innodb);

        $ddl
            ->correctColumn(new Integer('role_id'),
                [
                    new Index('role_id', 'role_id_index'),
                    new ForeignKey('role_id_fk', 'role_id','role', 'id', 'CASCADE', 'CASCADE')
                ])
            ->correctColumn(new Integer('permission_id'),
                [
                    new Index('permission_id', 'permission_id_index'),
                    new ForeignKey('permission_id_fk', 'permission_id','permission', 'id', 'CASCADE', 'CASCADE'),

                ]
            );


        //Inset Default
        $ad = $ddl->getAdapter();
        foreach (RBAC_PER_DESCRIPTION::$rolepermission as $role => $pers) {
            foreach ($pers as $per) {
                $select = new Select('role_permission');
                $select->columns([new Expression('COUNT(1) as C')])
                    ->join('role', 'role.id=role_permission.role_id', [])
                    ->join('permission', 'permission.id=role_permission.permission_id', [])
                    ->where->equalTo('permission.name', $per)->equalTo('role.name', $role);
                //echo $select->getSqlString($ad->getPlatform());

                $c = $ad->get_row_select($select)['C'];
                if ($c == 0) {
                    $roleID = $ad->cell('role', 'id', ['name' =>$role]);
                    $perID = $ad->cell('permission', 'id', ['name' =>$per]);

                    if ($roleID != null && $perID != null) {
                        $ad->insert(['role_id' => $roleID[0], 'permission_id' => $perID[0]], 'role_permission');
                    }
                }



            }
        }



        $ddl->deltebulkrows();

    }
}
