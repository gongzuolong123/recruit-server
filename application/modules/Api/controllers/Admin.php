<?php
/**
 * Created by PhpStorm.
 * User: gzl
 * Date: 2018/7/27
 * Time: 上午1:36
 */

class AdminController extends TCApiControllerBase {


  /**
   * 管理员登陆
   */
  public function loginAction() {
    $userName = $_POST['user_name'];
    $passWord = $_POST['pass_word'];
    if(isset($this->admin_users[$userName]) && $this->admin_users[$userName] == $passWord) {
      $token = md5(uniqid());
      TCRedisManager::getInstance()->redis->set($token, 'admin', 86400 * 2);

      return $this->writeSuccessJsonResponse(['user' => $userName, 'access_token' => $token]);
    }

    return $this->writeErrorJsonResponseCaseParamsError();
  }


  /**
   * 验证access_token是否有效
   */
  public function validateTokenAction() {
    $access_token = $_POST['access_token'];
    $row = TCRedisManager::getInstance()->redis->get($access_token);
    if($row && isset($this->admin_users[$row])) {
      TCRedisManager::getInstance()->redis->setTimeout($access_token, 86400 * 2);

      return $this->writeSuccessJsonResponse();
    }

    return $this->writeErrorJsonResponse();
  }


}