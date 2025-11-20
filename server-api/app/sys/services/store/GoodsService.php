<?php

declare(strict_types=1);

namespace app\sys\services\store;

use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\store\GoodsSkuService;
use think\facade\Db;

/**
 * Class GoodsService
 */
class GoodsService extends \app\common\services\store\GoodsService
{


    public function getGoodsFormDetail($goodsId): array
    {
        $detail = $this->dao->model->where(['id'=>$goodsId])->with(['category','sku','label','server'])->find();
        if (!$detail) {
            throw new CommonException('商品不存在了！');
        }
        $spec = [
            'names'=>[],
            'attrs'=>[]
        ];
        $specData = [];
        if($detail['spec_type']['value'] == 2){
            $specData = GoodsSpecService::instance()->getGoodsSpecData($detail['id']);
            $spec = GoodsSkuService::instance()->attrFormat($specData);
            foreach ( $detail['sku'] as $key => &$item) {
                $skuAttr = explode(':',$item['sku_attr']);
                $specValueSku = ArrayHelper::getArray2Column($specData,'values');
                foreach ( $skuAttr as $k => $v) {
                    $specValueItem = ArrayHelper::getArrayItemByColumn($specValueSku,'id',$v);
                    if(!empty($specValueItem)){
                        $item['attr'.$k] = [
                            'image'=>$specValueItem['image'],
                            'name'=>$specValueItem['name']
                        ];
                    }
                }
            }
        }
        return ['detail'=>$detail->toArray(),'spec'=>$specData,'spec_attr'=>$spec];
    }
    /**
     * 获取列表
     */
    public function getGoodsList($params = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if (!empty($params["goods_name"])){
            $filter[] = ["goods_name","like","%".$params["goods_name"]."%"];
        }
        return $this->dao->model->with(["category"])->where($filter)->order('goods_status DESC')->page($page)->paginate($limit)->toArray();
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
            foreach ($data['category_id'] as $key => $item) {
                $cateList[] = [
                    'category_id' => $item
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

    /**
     * 修改商品
     */
    public function updateGoods(int $goodsId,array $data,array $spec){
        $skuService = app(GoodsSkuService::class);
        // 保存数据
        $status = $this->transaction(function () use ($goodsId,$data,$spec,$skuService) {
            $res = $this->dao->update($goodsId,$data);
            if (!$res) throw new CommonException('保存失败');
            Db::name('store_goods_category_rel')->where('goods_id',$goodsId)->delete();
            Db::name('store_goods_label_rel')->where('goods_id',$goodsId)->delete();
            Db::name('store_goods_server_rel')->where('goods_id',$goodsId)->delete();
            $cateList = [];
            foreach ($data['category_id'] as $key => $item) {
                $cateList[] = [
                    'category_id' => $item,
                    'goods_id' => $goodsId
                ];
            }
            $labelList = [];
            foreach ($data['goods_label'] as $key => $item) {
                $labelList[] = [
                    'label_id' => $item,
                    'goods_id' => $goodsId
                ];
            }
            $serverList = [];
            foreach ($data['goods_server'] as $key => $item) {
                $serverList[] = [
                    'server_id' => $item,
                    'goods_id' => $goodsId
                ];
            }
            $skuService->saveSku($spec,(int)$data['spec_type'],$goodsId);
            $res->categoryRel()->saveAll($cateList);
            $res->labelRel()->saveAll($labelList);
            $res->serverRel()->saveAll($serverList);

            return true;
        });
        $this->deleteCache($goodsId);
        return $status;
    }
}
