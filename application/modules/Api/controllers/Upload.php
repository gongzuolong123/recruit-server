<?php

/**
 * 上传相关
 */
class UploadController extends TCApiControllerBase {

  /**
   * 上传图片
   */
  public function imageAction() {
    if($_FILES['license'] && $_FILES['license']['error'] == 0) {
      $file_name = md5(uniqid()) . '.jpg';
      $file_path = '/image/license/';
      if(!is_dir(APPLICATION_PATH . $file_path)) mkdir(APPLICATION_PATH . $file_path,0777, true);
      $status = move_uploaded_file($_FILES['license']['tmp_name'],APPLICATION_PATH . $file_path . $file_name);
      if($status) {
        $data = new stdClass();
        $data->url = Yaf_Application::app()->getConfig()->get('api.root.url') . $file_path . $file_name;
        $data->image_path = $file_path . $file_name;
        return $this->writeSuccessJsonResponse($data);
      }
    }
    return $this->writeErrorJsonResponseCaseParamsError();
  }


  /**
   * 上传csv文件
   */
  public function csvAction() {
    if($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
      $file_name = $_FILES['csv']['name'];
      $file_path = '/image/csv/';
      if(!is_dir(APPLICATION_PATH . $file_path)) mkdir(APPLICATION_PATH . $file_path,0777, true);
      $status = move_uploaded_file($_FILES['csv']['tmp_name'],APPLICATION_PATH . $file_path . $file_name);
      if($status) {
        $data = new stdClass();
        $data->url = Yaf_Application::app()->getConfig()->get('api.root.url') . $file_path . $file_name;
        $data->image_path = $file_path . $file_name;
        return $this->writeSuccessJsonResponse($data);
      }
    }
    return $this->writeErrorJsonResponseCaseParamsError();
  }


}
