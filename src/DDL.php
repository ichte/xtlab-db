<?php
namespace XT\Db;

use Zend\Db\Sql\Ddl\AlterTable;
use Zend\Db\Sql\Ddl\Column\ColumnInterface;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;
use Zend\Db\Sql\Ddl\Index\Index;
use Zend\Db\Sql\TableIdentifier;


class DDL
{
    const engine_innodb = 'InnoDB';
    const engine_myisam = 'MyISAM';

    public static $tables = [
        //'optiongroups',
        'option_items',
        'placeholder',
        'template_inserthtml',
        'template_blockhtml',
        'log',
        'permission',
        'role',
        'rolepermission',
        'rolehierarchy',
        'users',
        'userrole',
    ];

    public $exec = true;


    protected  $engine = DDL::engine_innodb;
    protected  $id='id';
    protected  $table='table';
    protected  $cols = [];
    /**
     * @var Adapter
     */
    protected $Adapter;
    /**
     * DDL constructor.
     */
    public function __construct($ad)
    {
        $this->Adapter = $ad;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->Adapter;
    }


    /**
     * @param string $engine
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    public function fix($table) {
        if ($table == 'all') {
            foreach (self::$tables as $item) {
                set_time_limit(60000);
                $item = str_replace("_",'',$item);
                $item = str_replace("-",'',$item);
                $this->$item();
            }
            return "ALL UPDATE";
        }
        set_time_limit(60000);
        $table = str_replace("_",'',$table);
        $table = str_replace("-",'',$table);
        return $this->$table();//Call Magic -  $class = "\\XT\Db\\tables\\{$method}Table";

    }



    public function correctTable($table, $id, $engine = DDL::engine_innodb, $primaryint = true, $varcharleng = 50) {

        $this->table = $table;
        $this->id = $id;
        $this->cols = [];

        if (!$this->Adapter->existable($this->table)) {

            $tableCreate = new CreateTable($this->table);

            $id =($primaryint) ?  new Integer($this->id, false, null, ['autoincrement'=>true, 'AUTO_INCREMENT'=>true]) :
                                  new Varchar($this->id, $varcharleng, false, '');


            $tableCreate->addColumn($id);

            $constraint = new PrimaryKey($this->id);
            $tableCreate->addConstraint($constraint);
           // $tableCreate->addConstraint(new UniqueKey($this->id, $this->id.'unique'));
            $tableCreate->addConstraint(new Index($this->id, $this->id));
            if ($this->exec)
                $this->Adapter->executeDDL($tableCreate);


        }
        else {


//            $this->correctColumn(new Integer($this->id, false, null,
//                ['autoincrement'=>true, 'AUTO_INCREMENT'=>true]),
//                   [new Index($this->id, $this->id)]);

        }


        //echo $this->Adapter->sql->getSqlStringForSqlObject($tabale);

        $this->setEngine($engine);

        if ($this->exec)
            $this->Adapter->execute("ALTER TABLE {$this->table} CHARACTER SET = utf8 , COLLATE = utf8_bin , ENGINE = {$this->engine}");

        $keys = $this->Adapter->execute("SHOW INDEX FROM {$this->table} WHERE Key_name <> 'PRIMARY' AND Key_name <> '{$this->id}'");
        //echo "SHOW INDEX FROM {$this->table} WHERE Key_name <> 'PRIMARY' AND Key_name <> '{$this->id}'";
        foreach ($keys as $key) {
           // echo "ALTER TABLE {$this->table} DROP INDEX $key[Key_name]";
            $this->Adapter->execute("ALTER  TABLE {$this->table} DROP INDEX $key[Key_name]");

        }


    }

    /**
     * @param ColumnInterface $cols
     * @param array $constraints
     * @return $this
     */
    public function correctColumn(ColumnInterface $cols, $constraints = []) {

        $this->cols[] = $cols->getName();
        $table_update = new AlterTable($this->table);
        foreach ($constraints as $constraint)
            $table_update->addConstraint($constraint);




        if ($this->Adapter->existColumn($cols->getName(), $this->table)) {

            $table_update->changeColumn($cols->getName(), $cols);
        }
        else
            $table_update->addColumn($cols);
        if ($this->exec)
            $this->Adapter->executeDDL($table_update);
        return $this;


    }


    public function deltebulkrows() {

        $databae = $this->Adapter->getCurrentSchema();
        $tb = new TableIdentifier('COLUMNS', 'information_schema');
        $select = $this->Adapter->sql->select();

        $select
            ->from($tb)
            ->columns(['COLUMN_NAME'])
            ->where([ 'TABLE_NAME' => $this->table, 'TABLE_SCHEMA'=>$databae]);

        $rows = $this->Adapter->get_rows_select($select);
        $this->cols[] = $this->id;
        foreach ($rows as $row) {
            $this->deltecolifnotExist($row['COLUMN_NAME']);
        }


    }




    public function deltecolifnotExist($colname) {
        foreach ($this->cols as $col) {
            if ($col == $colname) return;
        }
        $table_update = new AlterTable($this->table);
        $table_update->dropColumn($colname);
        $this->Adapter->executeDDL($table_update);

    }

    public function __call($method, $arguments)
    {

        $class = "\\XT\Db\\tables\\{$method}Table";

        if (class_exists($class, true)) {

            return $class::execute($this).'OK : success on table => '.$method;
        }

        else
            return "No exist: $class";

    }


    public function existFk($name, $table = null) {
        $table = $table ?? $this->table;
        $databae = $this->Adapter->getCurrentSchema();
        $tb = new TableIdentifier('KEY_COLUMN_USAGE', 'information_schema');
        $select = $this->Adapter->sql->select();

        $select
            ->from($tb)
            ->columns(['CONSTRAINT_NAME'])
            ->where([ 'TABLE_NAME' => $table, 'TABLE_SCHEMA'=>$databae, 'CONSTRAINT_NAME' => $name]);

        $rows = $this->Adapter->get_rows_select($select);
        return ($rows->count() > 0);
    }

    public function dropFk($onstraint, $table = null) {
        $table = $table ?? $this->table;
        if ($this->existFk($onstraint, $table)) {
            $this->Adapter->execute("ALTER TABLE `$table` DROP FOREIGN KEY `$onstraint`");
        }

    }



}