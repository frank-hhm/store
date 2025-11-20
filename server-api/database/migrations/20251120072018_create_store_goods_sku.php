<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreGoodsSku extends Migrator
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

        $table = $this->table('store_goods_sku');
        $table
            ->addColumn(Column::integer('goods_id')->setLimit(11)->setDefault(0)->setComment('关联的商品ID'))
            ->addColumn(Column::string('sku_attr')->setLimit(255)->setDefault("")->setComment('商品sku记录索引 (由规格id组成)'))
            ->addColumn(Column::string('sku_attr_text')->setLimit(255)->setDefault("")->setComment('商品sku'))
            ->addColumn(Column::string('goods_image')->setLimit(255)->setDefault("")->setComment('规格图片'))

            ->addColumn(Column::decimal('price', 10, 2)->setDefault(0.00)->setComment('商品价格'))
            ->addColumn(Column::decimal('cost_price', 10, 2)->setDefault(0.00)->setComment('商品成本价'))
            ->addColumn(Column::decimal('market_price', 10, 2)->setDefault(0.00)->setComment('商品市场价'))
            ->addColumn(Column::integer('stock')->setLimit(11)->setDefault(0)->setComment('库存数量'))
            ->addColumn(Column::string('bar_code')->setLimit(120)->setDefault("")->setComment('商品编码'))
            ->addColumn(Column::decimal('weight', 10, 2)->setDefault(0.00)->setComment('重量（kg）'))
            ->addColumn(Column::decimal('volume', 10, 2)->setDefault(0.00)->setComment('体积m3'))
            ->addColumn(Column::integer('goods_sales')->setLimit(11)->setDefault(0)->setComment('商品销量'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->setPrimaryKey('id')
            ->addIndex(['goods_id','sku_attr_text','sku_attr','goods_sales','stock','bar_code'])
            ->setComment('商城-商品Sku表')
            ->create();
    }
}
