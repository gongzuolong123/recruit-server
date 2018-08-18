<?php

/**
 * 企业相关
 */
class EnterpriseController extends TCApiControllerBase {

  protected function postOnlyActions() {
    return array("saveRecruit", "save");
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
      $data[] = $item;
    }
    $total = RecruitModel::countBySql('select * from enterprises');

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
    $data->areaName = AreaModel::findById($model->area_id)->name;
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
    if($_POST['license'])$model->license = $_POST['license'];
    if($_POST['contactsPhone']) $model->contacts_phone = $_POST['contactsPhone'];
    if($_POST['contactsName']) $model->contacts_name = $_POST['contactsName'];
    if($_POST['status'] && $this->role == 'admin') $model->status = intval($_POST['status']);
    $model->save();

    if($this->current_user) $this->current_user->saveAttributes(['enterprise_id' => $model->id]);

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 删除企业
   */
  public function deleteAction() {
    if(!$this->role) return $this->writeErrorJsonResponseCaseParamsError();
    $id = intval($_POST['id']);
    $model = EnterpriseModel::findById($id);
    if($model) $model->status = EnterpriseModel::STATUS_DELETE;

    return $this->writeSuccessJsonResponse();
  }

  /**
   * 企业招聘列表
   * @param $page   分页 默认0
   * @param $enterpriseId  企业id
   * @param $status 状态 0 正常状态，-1 删除状态 1 全部
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
      $params['enterprise_id'] = intval($_GET['enterpriseId']);
    }
    if(!empty($_GET['status'])) {
      switch(intval($_GET['status'])) {
        case 0:
          break;
        case -1:
          $params['status'] = -1;
          break;
        case 1:
          $params['status'] = [0, -1];
          break;
      }
    }
    $models = RecruitModel::findAllByAttributes($params, 'status desc,weight', "{$offset},{$limit}");
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
      $item->wages = $model->wages;
      $item->contactsName = $model->contacts_name;
      $item->contactsPhone = $model->contacts_phone;
      $item->areaNameAll = AreaModel::getAllAreaName($model->getEnterpriseModel()->area_id, 2);
      $item->wagesType = $model->wages_type;
      $item->wages1 = $model->wages_1;
      $item->wages2 = $model->wages_2;
      $item->education = $model->education;
      $item->updated_at = $model->updated_at;
      $item->tagNames = RecruitTagModel::getTagNamesByRecruitId($model->id);
      $data[] = $item;
    }
    $total_sql = "select * from recruits";
    if($params['enterprise_id']) $where[] = 'enterprise_id=' . $params['enterprise_id'];
    if(!empty($_GET['status']) && intval($_GET['status']) != 1) $where[] = 'status=' . $_GET['status'];
    if(count($where) > 0) {
      $total_sql .= ' where ' . implode(' and ', $where);
    }

    $total = RecruitModel::countBySql($total_sql);

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
   * @param $wages        工资待遇
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
    }
    $model->work_address = $_POST['workAddress'];
    $model->work_post = $_POST['workPost'];
    $model->work_require = $_POST['workRequire'];
    $model->wages = $_POST['wages'];
    $model->wages_1 = $_POST['wages1'];
    $model->wages_2 = $_POST['wages2'];
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
    $model->status = intval($_POST['status']);
    $model->save();

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