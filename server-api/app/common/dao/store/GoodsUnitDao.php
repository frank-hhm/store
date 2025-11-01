<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\GoodsUnitModel;

/**
 * 商城-商品单位
 * Class GoodsUnitDao
 * @package app\common\dao\store
 */
class GoodsUnitDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return GoodsUnitModel::class;
    }

    /**
     * 列表
     */
    public function getGoodsUnitList($where, int $page, int $limit): array
    {
        return $this->model->where($where)->order('id DESC')->page($page)->paginate($limit)->toArray();
    }

    /**
     * 列表
     */
    public function getGoodsUnitSelect(array $where = []): array
    {
        return $this->model->where($where)->order('id DESC')->select()->toArray();
    }
}