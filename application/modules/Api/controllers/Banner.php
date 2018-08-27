<?php

/**
 * banner相关
 */
class BannerController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("save");
  }

  /**
   * banner获取
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *       "id": 123,                // id
   *       "name": "xxx",            // 名称
   *       "image_url": "xxx",       // 图片地址
   *     }
   *   ],
   * }
   */
  public function getAction() {
    $data = [];
    BannerModel::withCache(600);
    $models = BannerModel::findAllBySql("select * from banners order by weight");
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->name = $model->name;
      $item->image_url = $this->getUrl($model->image_path);
      $item->weight = $model->weight;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 保存
   */
  public function saveAction() {
    if(!$this->role) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $model = BannerModel::findById($id);
    if(!$model) {
      $model = new BannerModel();
    }
    $image_path = $this->saveImage('banner');
    if(!empty($image_path)) $model->image_path = $image_path;
    $model->name = $_POST['name'];
    $model->weight = intval($_POST['weight']);
    $model->save();

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 删除
   */
  public function delAction() {
    if(!$this->role) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $model = BannerModel::findById($id);
    if($model) $model->delete();
    return $this->writeSuccessJsonResponse();
  }


}
