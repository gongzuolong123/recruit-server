<?php

/**
 * 企业相关
 */
class EnterpriseController extends TCApiControllerBase {

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
    $models = RecruitModel::findAllByAttributes(['status' => [0, -1]], 'weight', "{$offset},{$limit}");
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->enterpriseName = $model->getEnterpriseModel()->name;
      $item->industryName = IndustryModel::findById($model->getEnterpriseModel()->industry_id)->name;
      $item->areaName = AreaModel::findById($model->getEnterpriseModel()->area_id)->name;
      $item->wordAddress = $model->work_address;
      $data[] = $item;
    }
    $total = RecruitModel::countBySql('select * from recruits');

    return $this->writeSuccessJsonResponse($data, ['total' => $total, 'limit' => $limit]);
  }


}