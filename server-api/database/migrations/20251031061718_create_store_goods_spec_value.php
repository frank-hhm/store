<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreGoodsSpecValue extends Migrator
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


        $table = $this->table('store_goods_spec_value');
        $table
            ->addColumn(Column::string('name')->setLimit(120)->setDefault("")->setComment('规格组名称'))
            ->addColumn(Column::integer('spec_id')->setLimit(11)->setDefault(0)->setComment('关联规格组ID'))
            ->addColumn(Column::integer('goods_id')->setLimit(11)->setDefault(0)->setComment('关联的商品ID'))
            ->addColumn(Column::tinyInteger('is_img')->setLimit(3)->setDefault(0)->setComment('是否存在图片'))
            ->addColumn(Column::string('img')->setLimit(255)->setDefault("")->setComment('图片地址'))
            ->setPrimaryKey('id')
            ->addIndex(['id',"goods_id","spec_id"])
            ->setComment('商城-商品规格表')
            ->create();
    }
}
