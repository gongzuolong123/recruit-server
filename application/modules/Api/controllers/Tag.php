<?php

/**
 * 标签相关
 */
class TagController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("save");
  }

  /**
   * 标签获取
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *       "id": 123,                // 标签id
   *       "name": "xxx",            // 标签名
   *     }
   *   ],
   * }
   */
  public function getAction() {
    $data = [];
    $tagModels = TagModel::all();
    foreach($tagModels as $tagModel) {
      $item = new stdClass();
      $item->id = $tagModel->id;
      $item->name = $tagModel->name;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 保存更新标签
   */
  public function saveAction() {
    if(!$this->role) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    if(empty($name)) return $this->writeErrorJsonResponseCaseParamsError();

    $model = TagModel::findById($id);
    if(!$model) {
      if(TagModel::findByAttributes(['name' => $name])) return $this->writeErrorJsonResponseCaseParamsError();
      $model = new TagModel();
    }
    $model->name = $name;
    $model->save();

    return $this->writeSuccessJsonResponse();
  }


}
