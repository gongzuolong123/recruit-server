<?php

/**
 * 脚本命令
 */
class ScriptController extends TCControllerBase {

  public function init() {
    parent::init(); // TODO: Change the autogenerated stub
    $pid_file_dir = APPLICATION_PATH . '/application/runtime/process';
    $action = $this->getRequest()->action;
    $pid_file = APPLICATION_PATH . '/application/runtime/process/' . $action;
    if(!is_dir($pid_file_dir)) {
      mkdir($pid_file_dir, 0777, true);
    }

    if(file_exists($pid_file) &&
      ($pid = file_get_contents($pid_file)) &&
      exec('ps -p ' . $pid . ' | grep ' . $pid, $row) &&
      !empty($row[0])
    ) {
      echo 'pid-fetch-all : ' . $pid . ' Already Run' . PHP_EOL;   // 脚本如果是运行状态

      return;
    }
    $pid = getmypid();
    file_put_contents($pid_file, $pid);
  }

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
   * 从csv文件导入企业招聘数据
   */
  public function importAction() {
    $models = ImportModel::findAllByAttributes(['status' => -1]);
    foreach($models as $model) {
      $file_path = APPLICATION_PATH . $model->file_path;
      $file = fopen($file_path, 'r');
      $str = fgetcsv($file);
      while(!feof($file)) {
        $line = fgetcsv($file);
        foreach($line as $index => $item) {
          if($index < 10) {
            switch($index) {
              case 0:
                $industry_id = IndustryModel::findOrCreateByName($item);
                break;
              case 1:
                if($item == '玉山镇') $area_id = 48030;
                elseif($item == '开发区') $area_id = 48039;
                else $area_id = intval(AreaModel::findByAttributes(['name' => $item])->id);
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

        $enterpriseModel = EnterpriseModel::findByAttributes(['name' => $name]);
        if(!$enterpriseModel) {
          $enterpriseModel = new EnterpriseModel();
          $enterpriseModel->industry_id = $industry_id;
          $enterpriseModel->name = $name;
          $enterpriseModel->area_id = $area_id;
          $enterpriseModel->shop_name = $shop_name;
          $enterpriseModel->insert();
        }

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
      $model->status = 0;
      $model->save();
    }

  }

  public function testAction() {
    $path = '/Users/gzl/Downloads/zhihuiicon/';
    foreach(['content', 'header', 'home', 'login', 'sidebar'] as $type) {
      if(!is_dir($path . $type . "_svg")) {
        mkdir($path . $type . "_svg", 0777, true);
      }
      $dirHandle = opendir($path . $type);
      while(false !== ($file = readdir($dirHandle))) {
        if($file == '.' || $file == '..') continue;
        if(substr($file, stripos($file, '.') + 1) == 'png') {
          $file_name = substr($file, 0, stripos($file, '.'));
          $png_file_path = $path . $type . '/' . $file;
          $pbm_file_path = $path . $type . '/' . $file_name . '.pbm';
          $svg_file = $path . $type . "_svg/" . $file_name . '.svg';
          exec("convert -flatten {$png_file_path} {$pbm_file_path}");
          exec("potrace -s {$pbm_file_path} -o {$svg_file}");
          exec("rm -f {$pbm_file_path}");
        }
      }
    }
  }


  public function test2Action() {
    $data = $this->test2();
    $this->printd($data);

  }

  public function printd($data, $level = 0) {
    foreach($data as $v) {
      for($i = 0; $i < $level; $i++) {
        echo "\t";
      }
      echo $v->name . PHP_EOL;
      if(count($v->areas) > 0) $this->printd($v->areas, $level + 1);
    }
    echo PHP_EOL;
  }

  public function test2($parent_id = 0) {
    $data = [];
    $models = AreaModel::findAllByAttributes(['parent_id' => $parent_id]);
    foreach($models as $model) {
      $item = new stdClass();
      $item->id = $model->id;
      $item->name = $model->name;
      $s_data = $this->test2($model->id);
      if($s_data) $item->areas = $s_data;
      $data[] = $item;
    }

    return $data;
  }

  public function fixWeightAction() {
    $time = time();
    $sql = "select * from recruits order by refresh_time";
    $models = RecruitModel::findAllBySql($sql);
    foreach($models as $model) {
      $model->saveAttributes(['refresh_time' => date('Y-m-d H:i:s',--$time)]);
    }
  }


}
