<?php
declare(strict_types=1);

namespace app\common\services\store;

use app\common\services\BaseService;
use app\common\dao\store\GoodsDao;

/**
 * Class GoodsService
 */
class GoodsService extends BaseService
{
    /**
     * GoodsService constructor.
     * @param GoodsDao $dao
     */
    public function __construct(GoodsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 添加商品
     */
    public function createGoods(array $data,array $spec = []){
        $skuService = app(GoodsSkuService::class);
        // 保存数据
        return $this->transaction(function () use ($data,$spec,$skuService) {

            $res = $this->dao->create($data);
            if (!$res) throw new CommonException('保存失败');
            $id = (int)$res->id;
            $cateList = [];
            foreach ($data['cate_id'] as $key => $item) {
                $cateList[] = [
                    'cate_id' => $item
                ];
            }
            $labelList = [];
            foreach ($data['goods_label'] as $key => $item) {
                $labelList[] = [
                    'label_id' => $item
                ];
            }
            $serverList = [];
            foreach ($data['goods_server'] as $key => $item) {
                $serverList[] = [
                    'server_id' => $item,
                    'goods_id' => $id
                ];
            }
            $skuService->saveSku($spec,(int)$data['spec_type'],$id);
            $res->categoryRel()->saveAll($cateList);
            $res->labelRel()->saveAll($labelList);
            $res->serverRel()->saveAll($serverList);
            return true;
        });
    }
}
