<?php

/**
 * 广告相关
 */
class AdvertisementController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("upload","del");
  }

  /**
   * 企业上传广告
   * @param $adId        广告id（更新时传该参数）
   * @param $enterpriseId 企业id (新增时传该参数)
   * @param $image       图片
   * @param $title       标题
   * @param $describe    描述
   */
  public function uploadAction() {
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();

    $ad_id = intval($_POST['adId']);
    $model = AdvertisementModel::findById($ad_id);
    if(!$model) {
      $model = new AdvertisementModel();
      if($this->role) $model->enterprise_id = $_POST['enterpriseId'];
      else $model->enterprise_id = $this->current_user->enterprise_id;
    }
    $model->image_path = $this->saveImage('image');
    $model->title = $_POST['title'];
    $model->describe = $_POST['describe'];
    $model->save();

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 广告获取
   * @param $enterpriseId  企业id
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *       "id":123,                 // id
   *       "enterpriseName":"xxx",   // 企业名称
   *       "industryName":"xx",      // 行业名称
   *     }
   *   ]
   * }
   */
  public function getAction() {
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();

    if($this->current_user) $enterpriseId = $this->current_user->enterprise_id;
    else $enterpriseId = intval($_GET['enterpriseId']);
    if($enterpriseId) {
      $params['enterprise_id'] = $enterpriseId;
    }
    $params['status'] = 0;
    $models = AdvertisementModel::findAllByAttributes($params, 'weight');
    $data = array();
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->image_url = $this->getUrl($model->image_path);
      $item->title = $model->title;
      $item->describe = $model->describe;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 删除广告
   * @param $id  广告id
   */
  public function delAction(){
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();

    $id = intval($_POST['id']);
    $model = AdvertisementModel::findById($id);
    if($model) {
      if(($this->current_user && $this->current_user->enterprise_id == $model->enterprise_id) || $this->role == 'admin') {
        $model->delete();
        return $this->writeSuccessJsonResponse();
      }
    }
    return $this->writeErrorJsonResponseCaseParamsError();

  }


}
