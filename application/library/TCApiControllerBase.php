<?php

/**
 * @name TCApiControllerBase
 * @author gzl
 */
class TCApiControllerBase extends TCControllerBase {

  public function init() {
    parent::init();
    Yaf_Dispatcher::getInstance()->autoRender(false);
    $this->getView()->layout = null;
    header('Access-Control-Allow-Origin:' . "*");
    header('Access-Control-Allow-Methods:' . "*");
    header('Access-Control-Request-Headers:' . " Origin, X-Requested-With, Content-Type, Accept,content-type");
  }


  protected function getUrl($path) {
    if($path[0] == '/') return Yaf_Application::app()->getConfig()->get('api.root.url') . $path;
    else return $path;
  }
}

