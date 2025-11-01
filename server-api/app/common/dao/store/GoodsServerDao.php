<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsServerModel;

/**
 * 商城-商品服务
 * Class GoodsServerDao
 * @package app\common\dao\store
 */
class GoodsServerDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return GoodsServerModel::class;
    }

    /**
     * 列表
     */
    public function getGoodsServerList(array $where, int $page, int $limit): array
    {
        return $this->model->where($where)->order('sort DESC,id DESC')->page($page)->paginate($limit)->toArray();
    }

    /**
     * 全部
     */
    public function getGoodsServerSelect(array $searchWhere = [], array $field = ['*'], array $where = []): array
    {
        return $this->model->where($searchWhere)->when(count($where), function ($query) use ($where) {
            $query->where($where);
        })->field($field)->order('sort DESC,id DESC')->select()->toArray();
    }
}