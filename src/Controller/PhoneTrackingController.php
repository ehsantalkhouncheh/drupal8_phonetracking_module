<?php

namespace Drupal\mkt_phone_tracking\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mkt_log_manager\Logger\Logger;
use Drupal\mkt_phone_tracking\PhoneTracking\PhoneTracking;
use Drupal\mkt_phone_tracking\PhoneTracking\PhoneTrackingApi;
use Drupal\mkt_phone_tracking\PhoneTracking\PhoneTrackingIframe;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class PhoneTrackingController extends ControllerBase {

  /**
   * @var array
   */
  private static $setting;

  /**
   * @var PhoneTrackingApi|\PhoneTrackingApi|null
   */
  private static $phone_tracking;

  /**
   * @return array
   */
  protected static function getSetting() {
    return self::$setting = PhoneTracking::getSettingInfo();
  }

  /**
   * @return PhoneTrackingApi|\PhoneTrackingApi|null
   */
  protected static function getPhoneTrackingInstance() {
    return self::$phone_tracking = PhoneTrackingApi::getInstance();
  }

  /**
   * @return bool
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public static function refreshNumber() {
    self::getSetting();
    self::getPhoneTrackingInstance();
    if (self::$setting['use_option'] == 'use_api') {
      if (!empty(self::$phone_tracking)) {
        try {
          if (self::getNumber()) {
            return new JsonResponse('refresh number happened successfully', 200);
          } else {
            return new JsonResponse('refresh number failed', 422);
          };

        } catch (ClientException $e) {
          Logger::errorEvent('Exception in refreshNumber', $e);
        }
      }
    }
    return new JsonResponse('phone tracking api is disable', 422);;
  }

  /**
   * @return string|null
   * @throws GuzzleException
   */
  public static function getNumber() {
    self::getSetting();
    self::getPhoneTrackingInstance();
    if (self::$setting['use_option'] == 'use_api') {
      if (!empty(self::$phone_tracking)) {
        try {
          return self::$phone_tracking->getNumber(self::$setting['destination'], self::$setting['domain']);
        } catch (ClientException $e) {
          Logger::errorEvent('Exception in getNumber', $e);
        }
      }
    } elseif (self::$setting['use_option'] == 'use_iframe') {
      return PhoneTrackingIframe::getDcmsNumber();
    } else {
      if (!is_null(self::$setting['default_number'])) {
        return self::$setting['default_number'];
      } else {
        return '900 838 524';
      }
    }
  }

  /**
   * @return JsonResponse
   * @throws GuzzleException
   */
  public static function callReceived() {
    if (PhoneTracking::checkIpAddress()) {
      $extension = \Drupal::request()->request->get('extension');
      $cchsid = \Drupal::request()->request->get('cchsid');
      $caller_number = \Drupal::request()->request->get('callernumber');
      if (is_null($extension) || is_null($caller_number)) {
        Logger::errorEvent('extension or caller_number did not set');
        return new Response('extension or caller_number did not set correctly');
      } else {
        self::getSetting();
        self::getPhoneTrackingInstance();
        if (self::$setting['use_option'] == 'use_api') {
          if (!empty(self::$phone_tracking)) {
            try {
              return new Response(self::$phone_tracking->callReceived($extension, $cchsid, $caller_number));
            } catch (ClientException $e) {
              Logger::errorEvent('Exception in callReceived', $e);
              return new Response($e->getMessage());
            }
          }
        }
      }
    } else {
      throw new AccessDeniedHttpException();
    }
  }

  /**
   * @return JsonResponse
   * @throws GuzzleException
   */
  public static function callFinished() {
    if (PhoneTracking::checkIpAddress()) {
      $host_name = 'www.secretaria.es';
      $extension = \Drupal::request()->request->get('extension');
      $caller_number = \Drupal::request()->request->get('callernumber');
      $data = \Drupal::request()->request->get('data');
      $call_length = 0;
      if (isset($data) && !empty($data) && NULL !== ($data = json_decode($data, TRUE))) {
        foreach ($data as $sKey => $sValue) {
          if ($sKey == 'visit_id') {
            continue;
          }
          if ($sKey == 'calllength') {
            $call_length = $sValue;
          }
        }
      }
      $type = \Drupal::request()->request->get('type');
      if (is_null($extension) || is_null($caller_number)) {
        Logger::errorEvent('extension or caller_number did not set');
        return new Response('extension or caller_number did not set correctly');
      } else {
        self::getSetting();
        self::getPhoneTrackingInstance();
        if (self::$setting['use_option'] == 'use_api') {
          if (!empty(self::$phone_tracking)) {
            try {
              return new Response(self::$phone_tracking->callFinished($host_name, $extension, $caller_number, $call_length, $type));
            } catch (ClientException $e) {
              Logger::errorEvent('Exception in callFinished', $e);
              return new Response($e->getMessage());
            }
          }
        }
      }
    } else {
      throw new AccessDeniedHttpException();
    }
  }

}
