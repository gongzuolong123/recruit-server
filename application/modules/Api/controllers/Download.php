<?php

/**
 * 下载相关
 */
class DownloadController extends TCApiControllerBase {


  public function imageAction() {
    $file_path = $_GET['file_path'];
    if(empty($file_path)) return $this->writeErrorJsonResponseCaseParamsError();
    $file_path = APPLICATION_PATH . $file_path;
    if(!file_exists($file_path)) return $this->writeErrorJsonResponseCaseParamsError();
    set_time_limit(0);
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($file_path));
    header('Content-Disposition: filename=test.jpg');
    $download_rate = 2000;
    ob_flush();
    $file = fopen($file_path, "r");
    while(!feof($file)) {
      // send the current file part to the browser
      print fread($file, round($download_rate * 1024));
      // flush the content to the browser
      ob_flush();
      flush();
      // sleep one second
    }
    fclose($file);
    return false;
  }


}
