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


}
