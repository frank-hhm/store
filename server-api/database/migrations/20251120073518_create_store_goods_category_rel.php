<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreGoodsCategoryRel extends Migrator
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
        $table = $this->table('store_goods_category_rel');
        $table
            ->addColumn(Column::integer('category_id')->setLimit(11)->setDefault(0)->setComment('分类ID'))
            ->addColumn(Column::integer('goods_id')->setLimit(11)->setDefault(0)->setComment('关联的商品ID'))
            ->setPrimaryKey('id')
            ->addIndex(['category_id','goods_id'])
            ->setComment('商城-商品分类中间表')
            ->create();
    }
}
