<?php

/**
 * 图片相关
 */
class ImageController extends TCApiControllerBase {

  /**
   * 上传
   */
  public function uploadAction() {
    return $this->writeSuccessJsonResponse($_FILES);
  }


}
