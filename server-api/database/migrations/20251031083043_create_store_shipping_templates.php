<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateStoreShippingTemplates extends Migrator
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
        $table = $this->table('store_shipping_templates');
        $table
            ->addColumn(Column::string('name')->setLimit(120)->setDefault("")->setComment('模版名称'))
            ->addColumn(Column::tinyInteger('type')->setLimit(3)->setDefault(1)->setComment('计费方式：1按件，2按重量，按体积'))
            ->addColumn(Column::tinyInteger('is_free_shipping')->setLimit(3)->setDefault(1)->setComment('是否指定包邮'))
            ->addColumn(Column::integer('sort')->setLimit(11)->setDefault(0)->setComment('排序'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setDefault(0)->setComment('新增时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setDefault(0)->setComment('更新时间'))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setDefault(0)->setComment('删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id'])
            ->setComment('商城-运费模版')
            ->create();
    }
}
