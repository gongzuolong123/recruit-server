<?php

/**
 * 企业相关
 */
class EnterpriseController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("saveRecruit", "save", "setEnterpriseStatus");
  }


  /**
   * 筛选
   */
  public function filtersAction() {
    $data = [];
    // 地区,行业
    $data['area'] = ['param_name' => 'areaId', 'data' => []];

    $sql = "select distinct area_id from enterprises where status=0";
    $models = EnterpriseModel::findAllBySql($sql);
    foreach($models as $model) {
      $v = AreaModel::findById($model->area_id)->name;
      if(empty($v)) continue;
      $data['area']['data'][] = ['id' => $model->area_id, 'name' => $v];
    }
    // 行业
    $data['industry'] = ['param_name' => 'industryId', 'data' => []];
    $sql = "select distinct industry_id from enterprises where status=0";
    $models = EnterpriseModel::findAllBySql($sql);
    foreach($models as $model) {
      $v = IndustryModel::findById($model->industry_id)->name;
      if(empty($v)) continue;
      $data['industry']['data'][] = ['id' => $model->industry_id, 'name' => $v];
    }
    // 职位
    $data['work_post'] = ['param_name' => 'workPost', 'data' => []];
    $sql = "select distinct work_post from recruits where status=0";
    $models = RecruitModel::findAllBySql($sql);
    foreach($models as $model) {
      if(empty($model->work_post)) continue;
      $data['work_post']['data'][] = ['id' => $model->work_post, 'name' => $model->work_post];
    }
    // 薪资
    $data['wages'] = ['param_name' => 'wages', 'data' => [
      ['id' => '2000,3000', 'name' => '2000-3000'],
      ['id' => '3000,4000', 'name' => '3000-4000'],
      ['id' => '4000,5000', 'name' => '4000-5000'],
      ['id' => '5000,6000', 'name' => '5000-6000'],
      ['id' => '6000,7000', 'name' => '6000-7000'],
      ['id' => '7000,8000', 'name' => '7000-8000'],
      ['id' => '8000,9000', 'name' => '8000-9000'],
      ['id' => '10000', 'name' => '10000以上'],
    ]];

    return $this->writeSuccessJsonResponse($data);
  }


  /**
   * 企业列表
   */
  public function listAction() {
    $page = intval($_GET['page']);
    $limit = 8;
    $offset = $page * $limit;
    $data = [];
    $status = !empty($_GET['status']) ? intval($_GET['status']) : 0;
    $sql = "select * from enterprises where status={$status} limit {$offset},{$limit}";
    $models = EnterpriseModel::findAllBySql($sql);
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->name = $model->name;
      $item->industryId = $model->industry_id;
      $item->industryName = IndustryModel::findById($model->industry_id)->name;
      $item->areaId = $model->area_id;
      $item->areaName = AreaModel::findById($model->area_id)->name;
      $item->shopName = $model->shop_name;
      $item->license = $model->license;
      $item->address = $model->address;
      $item->status = $model->status;
      $data[] = $item;
    }
    $total = RecruitModel::countBySql("select * from enterprises where status={$status}");

    return $this->writeSuccessJsonResponse($data, ['total' => $total, 'limit' => $limit]);
  }

  /**
   * 企业详情
   */
  public function detailAction() {
    if($this->current_user) $id = $this->current_user->enterprise_id;
    if(!empty($_GET['id'])) $id = intval($_GET['id']);
    $data = new stdClass();
    $model = EnterpriseModel::findById($id);
    if(!$model) return $this->writeErrorJsonResponseCaseParamsError();
    $data->id = $model->id;
    $data->name = $model->name;
    $data->industryId = $model->industry_id;
    $data->industryName = IndustryModel::findById($model->industry_id)->name;
    $data->areaId = $model->area_id;
    $data->areaIds = AreaModel::getAllAreaId($model->area_id);
    $data->areaName = AreaModel::findById($model->area_id)->name;
    $data->areaNames = AreaModel::getAllAreaName($model->area_id);
    $data->shopName = $model->shop_name;
    $data->license = $model->license;
    $data->license_url = $this->getUrl($model->license);
    $data->address = $model->address;
    $data->contactsName = $model->contacts_name;
    $data->contactsPhone = $model->contacts_phone;
    $data->status = $model->status;

    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 保存企业
   * @param $id 企业id (修改时要传)
   * @param $name    企业名
   * @param $industryId   行业id
   * @param $areaId    地区id
   * @param $shopName  商店名
   * @param $address   地址
   * @param $license   证书图片
   * @param $contactsName  联系人姓名
   * @param $contactsPhone   联系人电话
   */
  public function saveAction() {
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();
    if($this->role == "admin") $id = intval($_POST['id']);
    else $id = $this->current_user->enterprise_id;

    $model = EnterpriseModel::findById($id);
    if(!$model) {
      $model = new EnterpriseModel();
      if($this->current_user) $model->status = EnterpriseModel::STATUS_REVIEW;  // 用户新增企业时默认待审核状态
    }
    if($_POST['name']) $model->name = $_POST['name'];
    if($_POST['industryId']) $model->industry_id = intval($_POST['industryId']);
    if($_POST['areaId']) $model->area_id = intval($_POST['areaId']);
    if($_POST['shopName']) $model->shop_name = $_POST['shopName'];
    if($_POST['address']) $model->address = $_POST['address'];
    $license = $this->saveImage('license');
    if(!empty($license)) $model->license = $license;
    if($_POST['license']) $model->license = $_POST['license'];
    if($_POST['contactsPhone']) $model->contacts_phone = $_POST['contactsPhone'];
    if($_POST['contactsName']) $model->contacts_name = $_POST['contactsName'];
    if($_POST['status'] && $this->role == 'admin') $model->status = intval($_POST['status']);
    $model->save();

    if($this->current_user) $this->current_user->saveAttributes(['enterprise_id' => $model->id]);

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 设置企业的状态
   * @param $id
   * @param $status  状态 0 正常状态，-1 删除状态 1 未审核 2 审核未通过
   */
  public function setEnterpriseStatusAction() {
    if(!$this->role) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $model = EnterpriseModel::findById($id);
    if(!$model) return $this->writeErrorJsonResponseCaseParamsError();
    $model->saveAttributes(['status' => intval($_POST['status'])]);
    if($model->status == EnterpriseModel::STATUS_DELETE) {
      $sql = "update recruits set status=-1 where enterprise_id={$model->id}";
      TCDbManager::getInstance()->db->exec($sql);
    }

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 企业招聘列表
   * @param $page   分页 默认0
   * @param $enterpriseId  企业id
   * @param $status 状态 0 正常状态，-1 下架状态 1 推荐状态 99 全部
   * @param $areaId  地区id
   * @param $industryId  行业id
   * @param $wages
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": [
   *     {
   *       "id":123,                 // id
   *       "enterpriseName":"xxx",   // 企业名称
   *       "industryName":"xx",      // 行业名称
   *       "areaName": "xxx",        // 地区名称
   *       "wordAddress": "xxx",     // 工作地址
   *       "wages":"xxx",            // 工资待遇
   *       "contactsName":"xxx",     // 联系人姓名
   *       "contactsPhone":"xxx",    // 联系人电话
   *     }
   *   ],
   *   "total": 100,  // 总数
   *   "limit": 10,   // 每页的数量
   * }
   */
  public function recruitListAction() {
    $page = intval($_GET['page']);
    $limit = 10;
    $offset = $page * $limit;
    if(isset($_GET['offset'])) {
      $offset = intval($_GET['offset']);
      $limit = ($page + 1) * $limit;
    }
    $data = [];
    $params['status'] = [0];
    if(!empty($_GET['enterpriseId'])) {
      $params['enterprise_id'][] = intval($_GET['enterpriseId']);
    }
    if(!empty($_GET['status'])) {
      switch(intval($_GET['status'])) {
        case 0:
          break;
        case -1:
          $params['status'] = -1;
          break;
        case 1:
          $params['status'] = 1;
          break;
        case 99:
          $params['status'] = [-1, 0, 1];
          break;
      }
    }
    $filter_enterprise_params = [];
    if(!empty($_GET['areaId'])) {
      $filter_enterprise_params['area_id'] = explode(',', $_GET['areaId']);
    }
    if(!empty($_GET['industryId'])) {
      $filter_enterprise_params['industry_id'] = explode(',', $_GET['industryId']);
    }
    if(count($filter_enterprise_params) > 0) {
      $filter_enterprise_params['status'] = 0;
      $filter_enterprise = EnterpriseModel::findAllByAttributes($filter_enterprise_params);
      foreach($filter_enterprise as $model) {
        $params['enterprise_id'][] = $model->id;
      }
    }
    if(!empty($_GET['wages'])) {
      $wages = explode(',', $_GET['wages']);
      if($wages[0] && $wages[0] > 0) $params['wages_1'] = ":_:>=:_:{$wages[0]}";
      if($wages[1] && $wages[1] > 0) $params['wages_2'] = ":_:<=:_:{$wages[1]}";
    }
    if(!empty($_GET['workPost'])) {
      $params['work_post'] = explode(',', trim($_GET['workPost']));
    }

    if(!isset($params['enterprise_id']) && $this->current_user) {
      $params['enterprise_id'][] = $this->current_user->enterprise_id;
    }

    $models = RecruitModel::findAllByAttributes($params, 'status desc,refresh_time desc', "{$offset},{$limit}");
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->enterpriseName = $model->getEnterpriseModel()->name;
      $item->shopName = $model->getEnterpriseModel()->shop_name;
      $item->industryName = IndustryModel::findById($model->getEnterpriseModel()->industry_id)->name;
      $item->areaName = AreaModel::findById($model->getEnterpriseModel()->area_id)->name;
      $item->workAddress = $model->work_address;
      $item->workPost = $model->work_post;
      $item->workRequire = $model->work_require;
      $item->status = $model->status;
      $item->weight = $model->weight;
      $item->contactsName = $model->contacts_name;
      $item->contactsPhone = $model->contacts_phone;
      $item->areaNameAll = AreaModel::getAllAreaName($model->getEnterpriseModel()->area_id, 2);
      $item->wagesType = $model->wages_type;
      $item->wages1 = $model->wages_1;
      $item->wages2 = $model->wages_2;
      $wages = $item->wages1 . '-' . $item->wages2;
      if($item->wages2 <= 0 || $item->wages2 < $item->wages1) $wages = $model->wages_1 . '以上';
      if($item->wages1 <= 0 && $item->wages2 <= 0) $wages = '面议';
      $item->wages = $wages;
      $item->education = $model->education;
      $item->updated_at = $model->updated_at;
      $item->tagNames = RecruitTagModel::getTagNamesByRecruitId($model->id);
      $item->recommend = false;
      if($model->status == 1) $item->recommend = true;
      $data[] = $item;
    }

    $total = RecruitModel::countByAttributes($params);

    return $this->writeSuccessJsonResponse($data, ['total' => $total, 'limit' => $limit]);
  }

  /**
   * 招聘详情
   * @param $id  招聘id
   * @json:{
   *   "status": "success",          // 接口返回状态，sucess表示成功，error表示失敗
   *   "message": "error message",   // 失败原因
   *   "error_code": -100,           // 失败代码
   *   "data": {
   *     "id":123,                 // id
   *     "enterpriseName":"xxx",   // 企业名称
   *     "enterpriseId": 11,       // 企业id
   *     "industryName":"xx",      // 行业名称
   *     "areaName": "xxx",        // 地区名称
   *     "wordAddress": "xxx",     // 工作地址
   *     "wordPost": "xxx",        // 工作岗位
   *     "wordRequire": "xx",      // 工作要求
   *     "wages":"xxx",            // 工资待遇
   *     "contactsName":"xxx",     // 联系人姓名
   *     "contactsPhone":"xxx",    // 联系人电话
   *   },
   * }
   */
  public function recruitDetailAction() {
    $id = intval($_GET['id']);
    $data = new stdClass();
    $model = RecruitModel::findById($id);
    if($model) {
      $data->id = $model->id;
      $data->enterpriseName = $model->getEnterpriseModel()->name;
      $data->enterpriseId = $model->enterprise_id;
      $data->industryName = IndustryModel::findById($model->getEnterpriseModel()->industry_id)->name;
      $data->areaName = AreaModel::findById($model->getEnterpriseModel()->area_id)->name;
      $data->shopName = $model->getEnterpriseModel()->shop_name;
      $data->workAddress = $model->work_address;
      $data->workPost = $model->work_post;
      $data->workRequire = $model->work_require;
      $data->wages = $model->wages;
      $data->contactsName = $model->contacts_name;
      $data->contactsPhone = $model->contacts_phone;
      $data->wagesType = (string)$model->wages_type;
      $data->wages1 = $model->wages_1;
      $data->wages2 = $model->wages_2;
      $wages = $data->wages1 . '-' . $data->wages2;
      if($data->wages2 <= 0) $wages = $data->wages1 . '以上';
      if($data->wages1 <= 0 && $data->wages2 <= 0) $wages = '面议';
      $data->wages = $wages;
      $data->updated_at = $model->updated_at;
      $data->education = (string)$model->education;
      $data->weight = $model->weight;
      $data->status = $model->status;
      $data->tagNames = RecruitTagModel::getTagNamesByRecruitId($model->id);
    }

    return $this->writeSuccessJsonResponse($data);
  }

  /**
   * 保存招聘详情
   * @param $id           招聘id (没有就新增)
   * @param $enterpriseId 企业id (新增时要传)
   * @param $wordAddress  工作地址
   * @param $wordPost     工作岗位
   * @param $wordRequire  工作要求
   * @param $wages1        工资待遇1
   * @param $wages2        工资待遇2
   * @param $wagesType     工资类型 1:月 2:年 3:周
   * @param $contactsName  联系人姓名
   * @param $contactsPhone 联系人电话
   */
  public function saveRecruitAction() {
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $model = RecruitModel::findById($id);
    if(!$model) {
      $model = new RecruitModel();
      if($this->role) $model->enterprise_id = intval($_POST['enterpriseId']);
      else $model->enterprise_id = $this->current_user->enterprise_id;
      $enterpriseModel = EnterpriseModel::findById($model->enterprise_id);
      if(!$enterpriseModel) return $this->writeErrorJsonResponseCaseParamsError();
      if($enterpriseModel->status != EnterpriseModel::STATUS_NOMAL) $model->status = -1;
    }
    $model->work_address = $_POST['workAddress'];
    $model->work_post = $_POST['workPost'];
    $model->work_require = $_POST['workRequire'];
    $model->wages = $_POST['wages'];
    $model->wages_1 = intval($_POST['wages1']);
    $model->wages_2 = intval($_POST['wages2']);
    $model->wages_type = $_POST['wagesType'];
    $model->contacts_name = $_POST['contactsName'];
    $model->contacts_phone = $_POST['contactsPhone'];
    $model->weight = intval($_POST['weight']);
    $model->updated_at = date('Y-m-d H:i:s');
    $model->education = intval($_POST['education']);
    $model->save();

    RecruitTagModel::setTagNamesByRecruitId($model->id, $_POST['tagNames']);

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 设置招聘信息的状态
   */
  public function setRecruitStatusAction() {
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $model = RecruitModel::findById($id);
    if(!$model) return $this->writeErrorJsonResponseCaseParamsError();
    $model->saveAttributes(['status' => intval($_POST['status']), 'refresh_time' => date('Y-m-d H:i:s')]);

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 添加待导入招聘信息的文件
   */
  public function addImportAction() {
    if(!$this->role && !$this->current_user) return $this->writeErrorJsonResponseCaseParamsError();
    if(is_array($_POST['files'])) {
      foreach($_POST['files'] as $file) {
        $model = new ImportModel();
        $model->file_name = $file['name'];
        $model->file_path = $file['path'];
        $model->insert();
      }
    }

    return $this->writeSuccessJsonResponse();
  }


}