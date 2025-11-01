<?php
/**
 * @Author: Frank
 * @Email: frank_hhm@163.com
 * @Date: 2025/10/30 13:13
 */
declare(strict_types=1);

namespace app\common\services\store;

use app\common\dao\store\GoodsSpecValueDao;
use app\common\services\BaseService;

/**
 * Class GoodsSpecValueService
 */
class GoodsSpecValueService extends BaseService
{

    /**
     * GoodsSpecValueService constructor.
     * @param GoodsSpecValueDao $dao
     */
    public function __construct(GoodsSpecValueDao $dao)
    {
        $this->dao = $dao;
    }

}

