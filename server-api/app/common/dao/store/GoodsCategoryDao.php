<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsCategoryModel;

/**
 * Class GoodsCategoryDao
 * @package app\common\dao\store
 */
class GoodsCategoryDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return GoodsCategoryModel::class;
    }

    /**
     * 分类列表
     */
    public function getGoodsCategorySelect($where, int $page, int $limit): array
    {
        return $this->search($where)->order('sort DESC,id DESC')->page($page, $limit)->select()->toArray();
    }

    /**
     * 获取分类
     */
    public function getGoodsCategoryAll(array $searchWhere = [], array $field = ['*'], array $where = []): array
    {
        return $this->search($searchWhere)->when(count($where), function ($query) use ($where) {
            $query->where($where);
        })->field($field)->order('sort DESC,id DESC')->select()->toArray();
    }
}
