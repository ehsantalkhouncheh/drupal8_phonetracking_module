<?php

namespace Drupal\mkt_phone_tracking\PhoneTracking;

use Drupal;
use Drupal\mkt_log_manager\Logger\Logger;
use Drupal\mkt_phone_tracking\Database\DatabaseTrait;
use GuzzleHttp\Exception\GuzzleException;
use PDO;

class PhoneTrackingIframe extends PhoneTracking {

  public static function getDcmsNumber() {
    try {
      return (new PhoneTrackingIframe)->saveVisitInfo();
    } catch (\Exception $e) {
      Logger::errorEvent('Exception in getDcmsNumber of PhoneTrackingIframe', $e);
    }
  }

  public function getDcmsVisitInfo(): ?array {
    $config = parent::getSettingInfo();
    if (($config['use_option'] == 'use_iframe') && !is_null($config['iframe_url']) && !empty($config['iframe_url'])) {
      echo '<iframe src=' . $config['iframe_url'] . ' width="100" height="100" scrolling="no" style="overflow:hidden; margin-top:-4px; margin-left:-4px; border:none;display: none"></iframe>';
      if (isset($config['domain']) && isset($config['destination'])) {
        $option['domain'] = $config['domain'];
        $option['destination'] = $config['destination'];
        $client = \Drupal::httpClient();
        $retry = 0;
        while ($retry < 3) {
          try {
            $request = $client->request('POST', $config['api'], ['form_params' => $option]);
            $response = $request->getBody()->getContents();
            return json_decode($response);
          } catch (\Exception $e) {
            $retry++;
          } catch (GuzzleException $e) {
            echo $e->getMessage();
          }
        }
      }
      return NULL;
    }
  }

  /**
   * @return string
   * @throws \Exception
   */
  public function saveVisitInfo(): ?string {
    $show_number = $this->getOldVisitInfo();
    if (!is_null($show_number) && !empty($show_number)) {
      return $show_number;
    } else {
      $dcms_visit_info = $this->getDcmsVisitInfo();
      var_dump($dcms_visit_info);
      $visit_info = parent::getVisitInfo();
      if ($visit_info) {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : FALSE;
        $domain = str_replace('www.', '', $domain);
        if ($dcms_visit_info) {
          $show_number = $dcms_visit_info->show_number;
          $dcms_visit_id = $_COOKIE['cms_visit_id'];
          $dcms_visitor_id = $_COOKIE['cms_visitor_id'];
        } else {
          $dcms_visit_id = 0;
          $dcms_visitor_id = 0;
          setcookie('cms_visit_id', 'unknown', 0, '/', $domain);
          setcookie('cms_visitor_id', 'unknown', 0, '/', $domain);
        }
        $visitor_id = $visit_info['visitor_id'];
        $visit_id = $visit_info['visit_id'];
        $values = [
          'visitor_id' => $visitor_id,
          'visit_id' => $visit_id,
          'dcms_visitor_id' => $dcms_visitor_id,
          'dcms_visit_id' => $dcms_visit_id,
          'phone_number' => $show_number,
          'created' => REQUEST_TIME,
        ];
        $insert_result = DatabaseTrait::insertRecordInDb('mkt_phone_tracking', $values);
        if (is_numeric($insert_result)) {
          return ($show_number);
        }
      } else {
        /*
         * if could not fetch number from dcms just show default number
         */
        $settings = parent::getSettingInfo();
        return $settings['default_number'];
      }
    }
  }

  /**
   * @return string|null
   */
  public function getOldVisitInfo(): ?string {
    $visit_arr = parent::getVisitInfo();
    if (!empty($visit_arr)) {
      $select_query = Drupal::database()
        ->select('mkt_phone_tracking', 'mpt');
      $select_query->addField('mpt', 'phone_number');
      $select_query->condition('visitor_id', $visit_arr['visitor_id'], '=');
      $select_query->condition('dcms_visitor_id', 0, '=');
      $select_query->range(0, 1);
      $result = $select_query->execute()->fetchAll(PDO::FETCH_ASSOC);
      if (!empty($result)) {
        return $result[0]['phone_number'];
      }
    }
    return NULL;
  }


  /**
   * This function use when Landing page in dcms has a visit
   *
   * @param string $dcms_visit_id
   * @param string $dcms_visitor_id
   * @param string $dcms_show_number
   *
   * @return string
   * @throws \Exception
   */
  public function saveDcmsVisitInfo(string $dcms_visit_id, string $dcms_visitor_id, string $dcms_show_number): string {
    if (!is_null($dcms_visit_id) && !empty($dcms_visit_id) && !is_null($dcms_visitor_id) && !empty($dcms_visitor_id) && !is_null($dcms_show_number) && !empty($dcms_show_number)) {
      $visit_arr = parent::getVisitInfo();
      $visitor_id = $visit_arr['visitor_id'];
      $visit_id = $visit_arr['visit_id'];
      $values = [
        'visitor_id' => $visitor_id,
        'visit_id' => $visit_id,
        'dcms_visitor_id' => $dcms_visitor_id,
        'dcms_visit_id' => $dcms_visit_id,
        'phone_number' => $dcms_show_number,
      ];
      $insert_result = DatabaseTrait::insertRecordInDb('mkt_phone_tracking', $values);
      if ($insert_result) {
        return ($insert_result);
      }
    }
  }


}
