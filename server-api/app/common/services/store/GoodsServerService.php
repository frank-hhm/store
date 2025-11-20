<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsServerDao;
use app\common\services\BaseService;

/**
 * Class GoodsServerService
 */
class GoodsServerService extends BaseService
{
    /**
     * GoodsServerService constructor.
     * @param GoodsServerDao $dao
     */
    public function __construct(GoodsServerDao $dao)
    {
        $this->dao = $dao;
    }


    public function getGoodsServerDetail($filter): array
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
    public function getGoodsServerList(array $params): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if (!empty($params["server_name"])){
            $filter[] = ["server_name","like","%".$params["server_name"]."%"];
        }
        return $this->dao->getGoodsServerList($filter, $page, $limit);
    }

    /**
     * 获取列表
     */
    public function getGoodsServerSelect(): array
    {
        return $this->dao->getGoodsServerSelect();
    }
}