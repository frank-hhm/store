<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsSpecTemplatesDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;

/**
 * Class GoodsSpecTemplatesService
 */
class GoodsSpecTemplatesService extends BaseService
{
    /**
     * GoodsSpecTemplatesService constructor.
     * @param GoodsSpecTemplatesDao $dao
     */
    public function __construct(GoodsSpecTemplatesDao $dao)
    {
        $this->dao = $dao;
    }

    public function getGoodsSpecTemplatesDetail($filter): array
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('数据不存在');
        }
        return $detail->toArray();
    }

    /**
     * 获取服务列表
     */
    public function getGoodsSpecTemplatesList(array $where): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        return $this->dao->getGoodsSpecTemplatesList($where, $page, $limit);
    }

    /**
     * 获取列表
     */
    public function getGoodsSpecTemplatesSelect(): array
    {
        return $this->dao->getGoodsSpecTemplatesSelect();
    }
}