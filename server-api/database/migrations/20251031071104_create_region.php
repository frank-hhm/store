<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class CreateRegion extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {

        $table = $this->table('region');
        $table
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('父id'))
            ->addColumn(Column::string('shortname')->setLimit(100)->setDefault("")->setComment('简称'))
            ->addColumn(Column::string('name')->setLimit(150)->setDefault("")->setComment('名称'))
            ->addColumn(Column::string('merger_name')->setLimit(150)->setDefault("")->setComment('全称'))
            ->addColumn(Column::tinyInteger('level')->setLimit(3)->setDefault(1)->setComment('层级 1 2 3 省市区县'))
            ->addColumn(Column::string('pinyin')->setLimit(100)->setDefault("")->setComment('拼音'))
            ->addColumn(Column::string('code')->setLimit(100)->setDefault("")->setComment('长途区号'))
            ->addColumn(Column::string('zip_code')->setLimit(100)->setDefault("")->setComment('邮编'))
            ->addColumn(Column::string('first')->setLimit(16)->setDefault("")->setComment('首字母'))
            ->addColumn(Column::string('lng')->setLimit(100)->setDefault("")->setComment('经度'))
            ->addColumn(Column::string('lat')->setLimit(100)->setDefault("")->setComment('纬度'))
            ->setPrimaryKey('id')
            ->addIndex(['id',"pid","first","code","pinyin","level"])
            ->setComment('地址')
            ->create();



        $data = $this->getData();
        $data = json_decode($data,true);
        Db::name('region')->insertAll($data);
    }


    public function getData(){
        $data = file_get_contents(root_path() . 'database/region.json');
        return $data;
    }
}
