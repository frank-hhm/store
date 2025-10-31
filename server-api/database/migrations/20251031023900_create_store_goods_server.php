<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreGoodsServer extends Migrator
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
        $table = $this->table('store_goods_server');
        $table
            ->addColumn(Column::string('server_name')->setLimit(120)->setDefault("")->setComment('服务名称'))
            ->addColumn(Column::string('desc')->setLimit(120)->setDefault("")->setComment('描述'))
            ->addColumn(Column::string('icon')->setLimit(120)->setDefault("")->setComment('图标'))
            ->addColumn(Column::integer('sort')->setLimit(11)->setDefault(0)->setComment('排序'))
            ->addColumn(Column::tinyInteger('status')->setLimit(3)->setDefault(1)->setComment('状态'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setDefault(0)->setComment('新增时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setDefault(0)->setComment('更新时间'))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setDefault(0)->setComment('删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id'])
            ->setComment('商城-商品服务')
            ->create();
    }
}
