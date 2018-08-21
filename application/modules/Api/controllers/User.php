<?php

/**
 * 用户相关
 */
class UserController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("login", "sendMsmCode");
  }

  /**
   * 登陆
   * @param $phone_number 手机号
   * @param $code  验证码
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [                     // SampleResultItem, 一个子模型类数组示例
   *     {
   *       "id": 123,                // 用户id
   *       "token": "xxx",           // 用户token
   *       "expire_time": 123,       // token过期时间
   *       "phone_number": 123,      // 手机号
   *     }
   *   ],
   * }
   */
  public function loginAction() {
    $phone_number = $_POST['phone_number'];
    $code = $_POST['code'];
    if(empty($phone_number) || empty($code)) return $this->writeErrorJsonResponseCaseParamsError();
    $redis_key = "MSM_CODE_" . $phone_number;
    $row = TCRedisManager::getInstance()->redis->get($redis_key);
    if(!$row || $row != $code) {
      // 验证码不通过
      return $this->writeErrorJsonResponse('验证码错误或失效');
    }

    $userModel = UserModel::findByAttributes(['phone_number' => $phone_number]);
    if(!$userModel) {
      $userModel = new UserModel();
      $userModel->phone_number = $phone_number;
      $userModel->insert();
    }
    $tokenModel = UserTokenModel::findById($userModel->id);
    if(!$tokenModel) {
      $tokenModel = new UserTokenModel();
      $tokenModel->id = $userModel->id;
      $tokenModel->token = md5(uniqid());
      $tokenModel->expire_time = date('Y-m-d 00:00:00', time() + 86400 * 5);
      $tokenModel->insert();
    }

    return $this->writeSuccessJsonResponse([
      'id' => $tokenModel->id,
      'token' => $tokenModel->token,
      'expire_time' => strtotime($tokenModel->expire_time),
      'phone_number' => $userModel->phone_number,
    ]);
  }


  /**
   * 发送短信验证码
   * @param $phone_number 手机号
   */
  public function sendMsmCodeAction() {
    $phone_number = $_POST['phone_number'];
    if(!preg_match("/^1[345678]{1}\d{9}$/", $phone_number)) $this->writeErrorJsonResponse('手机号格式异常');
    $redis_key = "MSM_CODE_" . $phone_number;
    $row = TCRedisManager::getInstance()->redis->get($redis_key);
    if($row) return $this->writeErrorJsonResponse('验证码发送太频繁,请稍后再发');
    $code = rand('100000', '999999');
    $response = SmsSend::sendSms($phone_number, $code);
    if($response->Code == 'OK') {
      TCRedisManager::getInstance()->redis->set($redis_key, $code, 300);

      return $this->writeSuccessJsonResponse();
    } else {
      return $this->writeErrorJsonResponse($response->Message);
    }
  }


}
