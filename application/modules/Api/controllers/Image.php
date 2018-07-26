<?php

/**
 * 图片相关
 */
class ImageController extends TCApiControllerBase {

  /**
   * 上传
   */
  public function uploadAction() {
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


}
