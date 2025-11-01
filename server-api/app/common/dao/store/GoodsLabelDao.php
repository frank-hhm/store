<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsLabelModel;

/**
 * Class GoodsLabelDao
 * @package app\common\dao\store
 */
class GoodsLabelDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return GoodsLabelModel::class;
    }

    /**
     * 获取列表
     */
    public function getGoodsLabelList($where, int $page, int $limit): array
    {
        return $this->model->where($where)->order('id DESC')->page($page)->paginate($limit)->toArray();
    }

    /**
     * 获取全部
     */
    public function getGoodsLabelSelect(array $where = []): array
    {
        return $this->model->where($where)->order('id DESC')->select()->toArray();
    }
}