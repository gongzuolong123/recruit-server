<?php

/**
 * 行业相关
 */
class IndustryController extends TCApiControllerBase {

  /**
   * 行业获取
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *       "id": 123,                // 行业id
   *       "name": "xxx",            // 行业名
   *     }
   *   ],
   * }
   */
  public function getAction() {
    $data = [];
    $industryModels = IndustryModel::all();
    foreach($industryModels as $industryModel) {
      $item = new stdClass();
      $item->id = $industryModel->id;
      $item->name = $industryModel->name;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }


}
