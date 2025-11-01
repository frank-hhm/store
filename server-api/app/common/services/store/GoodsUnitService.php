<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsUnitDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;

/**
 * 商城-商品单位
 * Class GoodsUnitService
 */
class GoodsUnitService extends BaseService
{

    /**
     * GoodsUnitService constructor.
     * @param GoodsUnitDao $dao
     */
    public function __construct(GoodsUnitDao $dao)
    {
        $this->dao = $dao;
    }


    public function getGoodsUnitDetail($filter): array
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
    public function getGoodsUnitList(array $where): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        return $this->dao->getGoodsUnitList($where, $page, $limit);
    }

    /**
     * 获取列表
     */
    public function getGoodsUnitSelect(array $where = []): array
    {
        return $this->dao->getGoodsUnitSelect($where);
    }
}