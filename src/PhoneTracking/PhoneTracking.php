<?php


namespace Drupal\mkt_phone_tracking\PhoneTracking;


use Drupal;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\mkt_tracking\Controller\TrackingController;

class PhoneTracking implements PhoneTrackingInterface {

  /**
   * @return array
   */
  public static function getSettingInfo(): array {
    $form_params = [];
    /**
     * get general setting
     */
    $config = Drupal::config('mkt_phone_tracking.settings');
    $form_params['domain'] = $config->get('domain');
    $form_params['destination'] = $config->get('destination');
    $form_params['default_number'] = $config->get('default_number');
    $form_params['use_option'] = $config->get('use_option');
    /**
     * get iframe setting
     */
    if ($config->get('use_option') == 'use_iframe') {
      $iframe_config = Drupal::config('mkt_phone_tracking.iframe_settings');
      $form_params['iframe_url'] = $iframe_config->get('iframe_url');
      $form_params['api'] = $iframe_config->get('iframe_api');
    } /**
     * get api setting
     */
    elseif ($config->get('use_option') == 'use_api') {
      $api_config = Drupal::config('mkt_phone_tracking.api_settings');
      switch (getenv('ENVIRONMENT')) {
        case 'DEV':
          $form_params['api'] = $api_config->get('dev_api');
          break;
        case 'STAGE':
          $form_params['api'] = $api_config->get('test_api');
          break;
        case 'LIVE':
          $form_params['api'] = $api_config->get('live_api');
          break;
        default:
          $form_params['api'] = $api_config->get('live_api');
      }
    }
    return $form_params;
  }

  /**
   * @return array
   */
  public function getVisitInfo(): array {
    $visit_object = TrackingController::getVisitorObject();
    $visitor_id = $visit_object->getVisitorId();
    $visit_id = $visit_object->getVisitInfo()->getVisitId();
    $visit_info = [];
    $visit_info['visitor_id'] = $visitor_id;
    $visit_info['visit_id'] = $visit_id;
    return $visit_info;
  }

  /**
   * @return object
   */
  public function getTrackingInfo(): object {
    return TrackingController::getTrackingObject();
  }

  /**
   * @return mixed|string
   */
  public static function getClientIp() {
    return Drupal::request()->getClientIp();
  }

  /**
   * @return \Drupal\Core\Config\ImmutableConfig
   */
  protected static function getIpAddressConfig(): ImmutableConfig {
    return Drupal::config('mkt_phone_tracking.ip_settings');
  }

  /**
   * @return string|null
   */
  protected static function getIpRange(): ?string {
    return self::getIpAddressConfig()->get('ip_range');
  }

  /**
   * @return bool|null
   */
  public static function checkIpAddress(): ?bool {

    $ranges = self::getIpRange();
    $ip_address = self::getClientIp();
    foreach (explode(',', $ranges) as $range) {
      if (strpos($range, '/') == FALSE) {
        $range .= '/32';
      }
      // $range is in IP/CIDR format eg 127.0.0.1/24
      [$range, $netmask] = explode('/', $range, 2);

      $range_decimal = ip2long($range);
      $ip_decimal = ip2long($ip_address);
      $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
      $netmask_decimal = ~$wildcard_decimal;
      if (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal)) {
        return TRUE;
      } else {
        continue;
      }
    }
    return FALSE;
  }

}
