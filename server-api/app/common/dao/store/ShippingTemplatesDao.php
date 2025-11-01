<?php

/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\ShippingTemplatesModel;

/**
 * Class ShippingTemplatesDao
 * @package app\common\dao\store
 */
class ShippingTemplatesDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return ShippingTemplatesModel::class;
    }

    public function getDetail($filter)
    {
        if(is_array($filter)){
            return $this->model->where($filter)->with(['citys','free_citys'])->find();
        }else{
            return $this->model->where(['id'=>$filter])->with(['citys','free_citys'])->find();
        }
    }

    /**
     * 列表
     */
    public function getShippingTemplatesList($where, int $page, int $limit): array
    {
        return $this->model->with(['citys','free_citys'])->where($where)->order('sort DESC,id DESC')->page($page)->paginate($limit)->toArray();
    }

    /**
     * 列表
     */
    public function getShippingTemplatesSelect(array $where = []): array
    {
        return $this->model->where($where)->order('sort DESC,id DESC')->select()->toArray();
    }

}