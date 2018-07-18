<?php

/**
 * 地区相关
 */
class AreaController extends TCApiControllerBase {

  /**
   * 获取地区
   * @param $id   上级地区的id，省级地区id是0
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *       "id": 123,                // 地区id
   *       "name": "xxx",            // 地区名
   *     }
   *   ],
   * }
   */
  public function getAction() {
    $id = intval($_GET['id']);
    $data = [];
    $areaModels = AreaModel::findAllByAttributes(['parent_id' => $id]);
    foreach($areaModels as $areaModel) {
      $item = new stdClass();
      $item->id = $areaModel->id;
      $item->name = $areaModel->name;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }


}
