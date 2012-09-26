<?php
namespace Nuxeo\Utilities;

class DateConverter {

  public function phpToNuxeo($date){

    $time = '';
    try {
      $datetime = new \DateTime($date);
      $time = $datetime->format('Y-m-d');
    } catch (Exception $e) {
      $time = null;
    }

    return $time;
  }

  public function nuxeoToPhp($date){
    $newDate = explode('T', $date);
    $phpDate = new \DateTime($newDate[0]);
    return $phpDate;
  }

  public function inputToPhp($date){
    /**
     * If given a date from user input and DateTime fails to parse it correctly,
     * then it must not be correct, thus we can safely exit.
     */
    try {
      $datetime = new \DateTime($date);
    } catch (Exception $e) {
      echo 'date not correct';
      exit;
    }

    $phpDate = $datetime->format('Y-m-d');

    return $phpDate;
  }

  public function inputToNuxeo($date){
    $php_date     = $this->inputToPhp($date);
    $return_date  = $this->phpToNuxeo($php_date);

    return $return_date;
  }
}