<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsLabelDao;
use app\common\services\BaseService;

/**
 * Class GoodsLabelService
 */
class GoodsLabelService extends BaseService
{
    /**
     * GoodsLabelService constructor.
     * @param GoodsLabelDao $dao
     */
    public function __construct(GoodsLabelDao $dao)
    {
        $this->dao = $dao;
    }



    public function getGoodsLabelDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('数据不存在');
        }
        return $detail->toArray();
    }

    /**
     * 获取列表
     */
    public function getGoodsLabelList(array $params = [])
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if (!empty($params["label_name"])){
            $filter[] = ["label_name","like","%".$params["label_name"]."%"];
        }
        $list = $this->dao->getGoodsLabelList($filter, $page, $limit);
        return $list;
    }
    /**
     * 获取列表
     */
    public function getGoodsLabelSelect()
    {
        $list = $this->dao->getGoodsLabelSelect();
        return $list;
    }
}