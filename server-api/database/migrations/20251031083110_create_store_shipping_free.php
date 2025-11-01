<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreShippingFree extends Migrator
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
        $table = $this->table('store_shipping_free');
        $table
            ->addColumn(Column::integer('temp_id')->setLimit(11)->setDefault(0)->setComment('关联运费模版id'))
            ->addColumn(Column::longText('citys')->setNull(false)->setComment('免邮区域'))
            ->addColumn(Column::integer('number')->setLimit(11)->setDefault(1)->setComment('包邮件数'))
            ->addColumn(Column::decimal('money', 10, 2)->setDefault(0.00)->setComment('包邮金额'))
            ->addIndex(['id',"temp_id"])
            ->setComment('商城-免邮')
            ->create();

    }
}
