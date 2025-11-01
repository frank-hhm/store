<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreShippingRegions extends Migrator
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
        $table = $this->table('store_shipping_regions');
        $table
            ->addColumn(Column::integer('temp_id')->setLimit(11)->setDefault(0)->setComment('关联运费模版id'))
            ->addColumn(Column::longText('citys')->setNull(false)->setComment('配送区域id集'))
            ->addColumn(Column::integer('first')->setLimit(11)->setDefault(1)->setComment('首件(个)/首重(Kg)'))
            ->addColumn(Column::decimal('first_money', 10, 2)->setDefault(0.00)->setComment('运费(元)'))
            ->addColumn(Column::integer('add_number')->setLimit(11)->setDefault(1)->setComment('续件/续重'))
            ->addColumn(Column::decimal('add_money', 10, 2)->setDefault(0.00)->setComment('续费(元)'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setDefault(0)->setComment('新增时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setDefault(0)->setComment('更新时间'))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setDefault(0)->setComment('删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id',"temp_id"])
            ->setComment('商城-运费模版区域')
            ->create();

    }
}
