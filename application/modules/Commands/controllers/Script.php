<?php

/**
 * 脚本命令
 */
class ScriptController extends TCControllerBase {

  private $asynFetchClientNumber = 0;

  /**
   * 从京东抓取地区数据
   */
  public function fetchAreaDataFromJDAction() {
    $this->fetchAreaDataFromJDById(0, 0);
  }

  private function fetchAreaDataFromJDById($id, $level) {
    if($this->asynFetchClientNumber >= 25) {
      swoole_timer_after(1000, function() use ($id, $level) {
        $this->fetchAreaDataFromJDById($id, $level);
      });

      return;
    }
    $this->asynFetchClientNumber++;
    $url = "https://d.jd.com/area/get?fid={$id}";
    $url_info = parse_url($url);
    $url_info['path'] .= '?' . $url_info['query'];
    AsyncHttpClient::httpGet($url_info, function($body, $statusCode) use ($id, $level) {
      $this->asynFetchClientNumber--;
      if($statusCode != 200) {
        echo 'ID: ' . $id . ' error !' . PHP_EOL;
        return;
      }
      foreach(json_decode($body) as $item) {
        $area_id = $item->id;
        if($level == 0 && ($area_id > 32 || $area_id < 0)) continue; // 中国省级地区 id {1 ~ 32}
        $name = $item->name;
        $sql = 'insert into areas (id,name,level,parent_id) values (:id,:name,:level,:parent_id)';
        try {
          TCDbManager::getInstance()->db->prepare($sql)->execute([':id' => $area_id, ':name' => $name, ':level' => $level, ':parent_id' => $id]);
        } catch(PDOException $e) {
        }

        $this->fetchAreaDataFromJDById($area_id, $level + 1);
      }
    });
  }


  /**
   * 从execl导入企业数据
   */
  public function importAction() {
    $file_path = APPLICATION_PATH . '/1.csv';
    $file = fopen($file_path,'r');
    $str = fgetcsv($file);
    while(! feof($file)) {
      $line = fgetcsv($file);
      foreach($line as $index => $item) {
        if($index < 10) {
          switch($index) {
            case 0:
              $industry_id = IndustryModel::findOrCreateByName($item);
              break;
            case 1:
              if($item == '玉山镇') $area_id = 48030;
              if($item == '开发区') $area_id = 48039;
              break;
            case 2:
              $name = $item;
              break;
            case 3:
              $shop_name = $item;
              break;
            case 4:
              $work_address = $item;
              break;
            case 5:
              $work_post = $item;
              break;
            case 6:
              $work_require = $item;
              break;
            case 7:
              $wages = $item;
              break;
            case 8:
              $contacts_name = $item;
              break;
            case 9:
              $contacts_phone = $item;
              break;
          }
        }
      }
      if(!$industry_id) continue;

      $enterpriseModel = new EnterpriseModel();
      $enterpriseModel->industry_id = $industry_id;
      $enterpriseModel->name = $name;
      $enterpriseModel->area_id = $area_id;
      $enterpriseModel->shop_name = $shop_name;
      $enterpriseModel->insert();

      $recruitModel = new RecruitModel();
      $recruitModel->enterprise_id = $enterpriseModel->id;
      $recruitModel->work_address = $work_address;
      $recruitModel->work_post = $work_post;
      $recruitModel->work_require = $work_require;
      $recruitModel->wages = $wages;
      $recruitModel->contacts_name = $contacts_name;
      $recruitModel->contacts_phone = $contacts_phone;
      $recruitModel->insert();
    }
    fclose($file);
  }


}
