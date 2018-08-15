<?php

/**
 * 行业相关
 */
class IndustryController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("save");
  }

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

  /**
   * 保存更新行业
   */
  public function saveAction() {
    if(!$this->role) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    if(empty($name)) return $this->writeErrorJsonResponseCaseParamsError();

    $model = IndustryModel::findById($id);
    if(!$model) {
      if(IndustryModel::findByAttributes(['name' => $name])) return $this->writeErrorJsonResponseCaseParamsError();
      $model = new IndustryModel();
    }
    $model->name = $name;
    $model->save();

    return $this->writeSuccessJsonResponse();
  }


}
