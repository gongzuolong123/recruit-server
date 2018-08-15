<?php

/**
 * @name TCApiControllerBase
 * @author gzl
 */
class TCApiControllerBase extends TCControllerBase {

  protected $role = null;

  protected $current_user = null;

  protected $admin_users = [
    'admin' => 'admin',
  ];

  public function init() {
    parent::init();
    Yaf_Dispatcher::getInstance()->autoRender(false);
    $this->getView()->layout = null;
    header('Access-Control-Allow-Origin:' . "*");

    // 是否是admin帐户
    if(!empty($_GET['access_token'])) {
      $row = TCRedisManager::getInstance()->redis->get($_GET['access_token']);
      if($row && isset($this->admin_users[$row])) {
        $this->role = 'admin';
      }
    }
    $this->current_user = UserModel::current();
  }

  protected function saveImage($image) {
    $file_name = md5(uniqid()) . '.jpg';
    if($_FILES[$image] && $_FILES[$image]['error'] == 0) {
      $file_path = '/image/' . $image . '/';
      if(!is_dir(APPLICATION_PATH . $file_path)) mkdir(APPLICATION_PATH . $file_path,0777, true);
      $status = move_uploaded_file($_FILES[$image]['tmp_name'],APPLICATION_PATH . $file_path . $file_name);
      if($status) {
        return $file_path . $file_name;
      }
    }
    return '';
  }


  protected function getUrl($path) {
    if($path[0] == '/') return Yaf_Application::app()->getConfig()->get('api.root.url') . $path;
    else return $path;
  }
}

