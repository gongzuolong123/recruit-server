<?php

/**
 * 用户相关
 */
class UserController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("login", "sendMsmCode", "saveProfile");
  }

  /**
   * 登陆
   * @param $phone_number 手机号
   * @param $code  验证码
   * @param $type  用户类型 1:b端 2:c端
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
    $type = intval($_POST['type']);
    if(!$type) $type = 1;
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
      $userModel->type = $type;
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
      TCRedisManager::getInstance()->redis->set($redis_key, $code, 60);

      return $this->writeSuccessJsonResponse();
    } else {
      return $this->writeErrorJsonResponse($response->Message);
    }
  }

  /**
   * 保存用户信息
   * @param $name    姓名
   * @parma $gender  男:1 女:2
   * @parma $birth_date  出生日期
   * @param $city    城市
   */
  public function saveProfileAction() {
    if(!$this->current_user) return $this->writeErrorJsonResponse();
    $model = UserPorfileModel::findById($this->current_user->id);
    if(!$model) {
      $model = new UserPorfileModel();
      $is_new = true;
    }
    if(!empty($_POST['name'])) $model->name = trim($_POST['name']);
    if(!empty($_POST['gender'])) $model->gender = intval($_POST['gender']);
    if(!empty($_POST['birth_date'])) $model->birth_date = $_POST['birth_date'];
    if(!empty($_POST['city'])) $model->city = $_POST['city'];
    if($is_new) $model->insert();
    else $model->save();

    return $this->writeSuccessJsonResponse();
  }


  /**
   * 获取用户信息
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [                     // SampleResultItem, 一个子模型类数组示例
   *     {
   *       "name": 'xxx',            // 姓名
   *       "gender": 0,              // 性别 1:男 2:女 0:未知
   *       "birth_date": '',       // 出生日期
   *       "city": '',      // 城市
   *     }
   *   ],
   * }
   */
  public function getProfileAction() {
    if(!$this->current_user) return $this->writeErrorJsonResponse();
    $data = [
      'name' => '',
      'gender' => 0,
      'birth_date' => '',
      'city' => '',
    ];
    $model = UserPorfileModel::findById($this->current_user->id);
    if($model) {
      $data = [
        'name' => $model->name,
        'gender' => $model->gender,
        'birth_date' => $model->birth_date,
        'city' => $model->city,
      ];
    }

    return $this->writeSuccessJsonResponse($data);
  }


}
