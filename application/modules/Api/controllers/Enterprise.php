<?php

/**
 * 企业相关
 */
class EnterpriseController extends TCApiControllerBase {

  /**
   * 企业列表
   */
  public function listAction(){
    $page = intval($_GET['page']);
    $limit = 8;
    $offset = $page * $limit;
    $data = [];
    $sql = "select * from enterprises limit {$offset},{$limit}";
    $models = EnterpriseModel::findAllBySql($sql);
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->name = $model->name;
      $item->industryId = $model->industry_id;
      $item->industryName = IndustryModel::findById($model->industry_id)->name;
      $item->areaId = $model->area_id;
      $item->areaName = AreaModel::findById($model->area_id)->name;
      $item->shopName = $model->shop_name;
      $item->license = $model->license;
      $item->address = $model->address;
      $data[] = $item;
    }
    $total = RecruitModel::countBySql('select * from enterprises');

    return $this->writeSuccessJsonResponse($data, ['total' => $total, 'limit' => $limit]);
  }

  /**
   * 企业详情
   */
  public function detailAction(){
    $id = intval($_POST['id']);
    $data = new stdClass();
    $model = EnterpriseModel::findById($id);
    if(!$model) return $this->writeErrorJsonResponseCaseParamsError();
    $data->id = $model->id;
    $data->name = $model->name;
    $data->industryId = $model->industry_id;
    $data->areaId = $model->area_id;
    $data->areaName = AreaModel::findById($model->area_id)->name;
    $data->shopName = $model->shop_name;
    $data->licese = $model->license;
    $data->address = $model->address;
    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 保存企业
   */
  public function saveAction(){
    $id = intval($_POST['id']);
    $model = EnterpriseModel::findById($id);
    if(!$model) $model = new EnterpriseModel();
    $model->name = $_POST['name'];
    $model->industry_id = $_POST['industryId'];
    $model->area_id = $_POST['areaId'];
    $model->shop_name = $_POST['shopName'];
    $model->address = $_POST['address'];
    $model->save();
    return $this->writeSuccessJsonResponse();
  }

  /**
   * 删除企业
   */
  public function deleteAction(){
    $id = intval($_POST['id']);
    $model = EnterpriseModel::findById($id);
    if($model) $model->delete();
    return $this->writeSuccessJsonResponse();
  }

  /**
   * 企业招聘列表
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *     }
   *   ],
   * }
   */
  public function recruitListAction() {
    $page = intval($_GET['page']);
    $limit = 8;
    $offset = $page * $limit;
    $data = [];
    $models = RecruitModel::findAllByAttributes(['status' => [0, -1]], 'status desc,weight', "{$offset},{$limit}");
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->enterpriseName = $model->getEnterpriseModel()->name;
      $item->industryName = IndustryModel::findById($model->getEnterpriseModel()->industry_id)->name;
      $item->areaName = AreaModel::findById($model->getEnterpriseModel()->area_id)->name;
      $item->wordAddress = $model->work_address;
      $item->status = $model->status;
      $item->weight = $model->weight;
      $data[] = $item;
    }
    $total = RecruitModel::countBySql('select * from recruits');

    return $this->writeSuccessJsonResponse($data, ['total' => $total, 'limit' => $limit]);
  }

  /**
   * 招聘详情
   */
  public function recruitDetailAction(){
    $id = intval($_GET['id']);
    $data = new stdClass();
    $model = RecruitModel::findById($id);
    if($model) {
      $data->id = $model->id;
      $data->enterpriseName = $model->getEnterpriseModel()->name;
      $data->industryName = IndustryModel::findById($model->getEnterpriseModel()->industry_id)->name;
      $data->areaName = AreaModel::findById($model->getEnterpriseModel()->area_id)->name;
      $data->wordAddress = $model->work_address;
      $data->wordPost = $model->work_post;
      $data->wordRequire = $model->work_require;
      $data->wages = $model->wages;
      $data->contactsName = $model->contacts_name;
      $data->contactsPhone = $model->contacts_phone;
      $data->weight = $model->weight;
      $data->status = $model->status;
    }
    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 保存招聘详情
   */
  public function saveRecruitAction(){
    $id = intval($_POST['id']);
    $model = RecruitModel::findById($id);
    if(!$model) return $this->writeErrorJsonResponseCaseParamsError();
    $model->work_address = $_POST['wordAddress'];
    $model->work_post = $_POST['wordPost'];
    $model->work_require = $_POST['wordRequire'];
    $model->wages = $_POST['wages'];
    $model->contacts_name = $_POST['contactsName'];
    $model->contacts_phone = $_POST['contactsPhone'];
    $model->weight = $_POST['weight'];
    $model->save();
    return $this->writeSuccessJsonResponse();
  }

  /**
   * 设置招聘信息的状态
   */
  public function setRecruitStatusAction() {
    $id = intval($_POST['id']);
    $model = RecruitModel::findById($id);
    if(!$model) return $this->writeErrorJsonResponseCaseParamsError();
    $model->status = intval($_POST['status']);
    $model->save();
    return $this->writeSuccessJsonResponse();
  }


}