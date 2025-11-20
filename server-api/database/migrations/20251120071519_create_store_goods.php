<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreGoods extends Migrator
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
        $table = $this->table('store_goods');
        $table
            ->addColumn(Column::string('goods_name')->setLimit(255)->setDefault("")->setComment('商品名称'))
            ->addColumn(Column::string('selling_point')->setLimit(255)->setDefault("")->setComment('商品卖点/简介'))
            ->addColumn(Column::longText('goods_images')->setNull(false)->setComment('商品图片'))
            ->addColumn(Column::integer('goods_unit')->setLimit(11)->setDefault(0)->setComment('商品单位'))
            ->addColumn(Column::tinyInteger('spec_type')->setLimit(3)->setDefault(1)->setComment('1:单规格，2:多规格'))
            ->addColumn(Column::tinyInteger('deduct_stock_type')->setLimit(3)->setDefault(1)->setComment('库存计算方式(1下单减库存 2付款减库存)'))
            ->addColumn(Column::longText('content')->setNull(false)->setComment('商品详情'))
            ->addColumn(Column::integer('sales_initial')->setLimit(11)->setDefault(0)->setComment('初始销量(虚拟销量)'))
            ->addColumn(Column::integer('sales_actual')->setLimit(11)->setDefault(0)->setComment('实际销量'))
            ->addColumn(Column::string('delivery_type')->setLimit(160)->setDefault("")->setComment('配送方式（多）'))
            ->addColumn(Column::tinyInteger('delivery_shipping')->setLimit(3)->setDefault(1)->setComment('运费：1包邮，2固定邮费，3运费模板'))
            ->addColumn(Column::integer('delivery_shipping_temp_id')->setLimit(11)->setDefault(0)->setComment('运费模板ID'))
            ->addColumn(Column::decimal('delivery_shipping_price', 10, 2)->setDefault(0.00)->setComment('运费价格'))
            ->addColumn(Column::integer('sort')->setLimit(11)->setDefault(0)->setComment('商品排序'))
            ->addColumn(Column::tinyInteger('goods_status')->setLimit(3)->setDefault(1)->setComment('商品状态(1上架0下架)'))
            ->addColumn(Column::tinyInteger('buy_limit_status')->setLimit(3)->setDefault(1)->setComment('商品限购状态'))
            ->addColumn(Column::integer('buy_limit')->setLimit(11)->setDefault(0)->setComment('商品限购数量'))
            ->addColumn(Column::tinyInteger('buy_limit_type')->setLimit(3)->setDefault(1)->setComment('0单次，1长期'))
            ->addColumn(Column::tinyInteger('buy_least_status')->setLimit(3)->setDefault(0)->setComment('商品起购状态'))
            ->addColumn(Column::integer('buy_least')->setLimit(11)->setDefault(0)->setComment('商品起购数量'))
            ->addColumn(Column::tinyInteger('buy_least_type')->setLimit(3)->setDefault(1)->setComment('0单次，1长期'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','goods_status','sales_actual'])
            ->setComment('商城-商品表')
            ->create();
    }
}
