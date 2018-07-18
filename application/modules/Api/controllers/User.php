<?php

/**
 * 用户相关
 */
class UserController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("login", "setProfile");
  }

  /**
   * 登陆
   * @param $weixin_id 微信id
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [                     // SampleResultItem, 一个子模型类数组示例
   *     {
   *       "id": 123,                // 用户id
   *       "token": "xxx",           // 用户token
   *       "expire_time": 123,       // token过期时间
   *     }
   *   ],
   * }
   */
  public function loginAction() {
    $weixin_id = $_POST['weixin_id'];
    if(empty($weixin_id)) return $this->writeErrorJsonResponseCaseParamsError();
    $userModel = UserModel::findByAttributes(['weixin_id' => $weixin_id]);
    if(!$userModel) {
      $userModel = new UserModel();
      $userModel->weixin_id = $weixin_id;
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
    ]);
  }

  /**
   * 上传用户资料
   * @param $token   用户token
   * @param $phone   手机号(可选)
   * @param $industry_id  行业id(可选)
   * @param $area_id 地区id(可选)
   * @param $address 地址(可选)
   * @param $license_image 营业执照(可选)
   * @param $id_number 身份证号(可选)
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   * }
   */
  public function setProfileAction() {
    return $this->writeSuccessJsonResponse(array());
  }


}
