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
    if($userName == 'admin' && $passWord == 'admin') {
      $_SESSION['user'] = 'admin';
      return $this->writeSuccessJsonResponse();
    }
    return $this->writeErrorJsonResponseCaseParamsError();
  }


}