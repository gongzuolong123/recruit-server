<?php

/**
 * 记录相关
 */

class HistoryController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("callPhoneUp");
  }

  /**
   * 上传打电话记录
   * @param $enterprise_id  招聘id
   */
  public function callPhoneUpAction() {
    if(!$this->current_user || empty($_POST['enterprise_id'])) return $this->writeErrorJsonResponseCaseParamsError();
    $sql = "insert into history_call_phone (user_id,enterprise_id,updated_at) values (:user_id,:enterprise_id,:updated_at) on 
    duplicate key update updated_at=values(updated_at)";
    try {
      TCDbManager::getInstance()->db->prepare($sql)->execute([
        ':user_id' => $this->current_user->id,
        ':enterprise_id' => intval($_POST['enterprise_id']),
        ':updated_at' => date('Y-m-d H:i:s'),
      ]);

      return $this->writeSuccessJsonResponse();
    } catch(PDOException $e) {
      return $this->writeErrorJsonResponse();
    }
  }

  /**
   * 获取打电话的记录
   */
  public function callPhoneGetAction() {
    if(!$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();
    $data = [];
    $sql = "select * from history_call_phone where user_id={$this->current_user->id} order by updated_at desc";
    $rows = TCDbManager::getInstance()->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row) {
      $recruitModel = RecruitModel::findById($row['enterprise_id']);
      $item = new stdClass();
      $item->id = $recruitModel->id;
      $item->enterpriseName = $recruitModel->getEnterpriseModel()->name;
      $item->shopName = $recruitModel->getEnterpriseModel()->shop_name;
      $item->industryName = IndustryModel::findById($recruitModel->getEnterpriseModel()->industry_id)->name;
      $item->areaName = AreaModel::findById($recruitModel->getEnterpriseModel()->area_id)->name;
      $item->workAddress = $recruitModel->work_address;
      $item->workPost = $recruitModel->work_post;
      $item->workRequire = $recruitModel->work_require;
      $item->status = $recruitModel->status;
      $item->weight = $recruitModel->weight;
      $item->contactsName = $recruitModel->contacts_name;
      $item->contactsPhone = $recruitModel->contacts_phone;
      $item->areaNameAll = AreaModel::getAllAreaName($recruitModel->getEnterpriseModel()->area_id, 2);
      $item->wagesType = $recruitModel->wages_type;
      $item->wages1 = $recruitModel->wages_1;
      $item->wages2 = $recruitModel->wages_2;
      $wages = $item->wages1 . '-' . $item->wages2;
      if($item->wages2 <= 0) $wages = $recruitModel->wages_1 . '以上';
      if($item->wages1 <= 0 && $item->wages2 <= 0) $wages = '面议';
      $item->wages = $wages;
      $item->education = $recruitModel->education;
      $item->updated_at = $recruitModel->updated_at;
      $item->tagNames = RecruitTagModel::getTagNamesByRecruitId($recruitModel->id);
      $item->recommend = false;
      if($recruitModel->status == 1) $item->recommend = true;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }
}