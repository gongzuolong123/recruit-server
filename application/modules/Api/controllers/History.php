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
   * @param $enterprise_id  企业id
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
      $enterpriseModel = EnterpriseModel::findById($row['enterprise_id']);
      $item = new stdClass();
      $item->id = $enterpriseModel->id;
      $item->name = $enterpriseModel->name;
      $data[] = $item;
    }

    return $this->writeSuccessJsonResponse($data);
  }
}