<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsSpecTemplatesModel;

/**
 * Class GoodsSpecTemplatesDao
 * @package app\common\dao\store
 */
class GoodsSpecTemplatesDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return GoodsSpecTemplatesModel::class;
    }

    /**
     * 列表
     */
    public function getGoodsSpecTemplatesList( $where, int $page, int $limit): array
    {
        return $this->model->where($where)->order('id DESC')->page($page)->paginate($limit)->toArray();
    }

    /**
     * 获取全部
     */
    public function getGoodsSpecTemplatesSelect(array $searchWhere = [], array $field = ['*'], array $where = []): array
    {
        return$this->model->where($searchWhere)->when(count($where), function ($query) use ($where) {
            $query->where($where);
        })->field($field)->order('id DESC')->select()->toArray();
    }
}

