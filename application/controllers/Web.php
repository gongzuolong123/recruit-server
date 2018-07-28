<?php

/**
 * @name IndexController
 * @author gzl
 */
class WebController extends TCControllerBase {

  public function recruitListAction() {
    Yaf_Dispatcher::getInstance()->autoRender(false);
    $this->getView()->display('web/recruitlist.php');
  }


  public function recruitDetailAction(){
    Yaf_Dispatcher::getInstance()->autoRender(false);
    $this->getView()->display('web/recruitdetail.php');
  }

}
