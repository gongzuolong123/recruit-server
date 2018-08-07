<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/7/27
 * Time: 上午1:36
 */

class AdminController extends TCApiControllerBase {

  private $users = [
    'admin' => 'admin',
  ];

  /**
   * 管理员登陆
   */
  public function loginAction() {
    $userName = $_POST['user_name'];
    $passWord = $_POST['pass_word'];
    if(isset($this->users[$userName]) && $this->users[$userName] == $passWord) {
      $token = md5(uniqid());
      TCRedisManager::getInstance()->redis->set($token,'admin',86400 * 7);
      return $this->writeSuccessJsonResponse(['user' => 'admin', 'token' => $token]);
    }

    return $this->writeErrorJsonResponseCaseParamsError();
  }


}