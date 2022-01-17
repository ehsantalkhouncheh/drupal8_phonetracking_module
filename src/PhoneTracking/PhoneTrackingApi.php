<?php


namespace Drupal\mkt_phone_tracking\PhoneTracking;


use Drupal\mkt_log_manager\Logger\Logger;
use Exception;

/**
 * Class PhoneTrackingApi
 */
class  PhoneTrackingApi extends PhoneTracking {

  /**
   * @var null
   */
  private static $instance = NULL;

  /**
   * @var mixed|null
   */
  private $token;

  /**
   * @var \GuzzleHttp\Client
   */
  private $client;

  /**
   * @var
   */
  private $setting;

  /**
   * @var
   */
  private $api;
  /**
   * @var \string[][]
   */
  private array $form_params;

  /**
   * PhoneTrackingApi constructor.
   */
  private function __construct() {
    $this->setting = parent::getSettingInfo();
    $guzzle = \Drupal::httpClient();
    $api_config = \Drupal::config('mkt_phone_tracking.api_settings');
    switch (getenv('ENVIRONMENT')) {
      case 'DEV':
        $this->api = $api_config->get('dev_api');
        $this->form_params=
          [
            'grant_type' => 'client_credentials',
            'client_id' => '3',
            'client_secret' => '******',
            'scope' => '*',
          ]
        ;
        break;
      case 'STAGE':
        $this->api = $api_config->get('test_api');
        $this->form_params=
          [
            'grant_type' => 'client_credentials',
            'client_id' => '3',
            'client_secret' => '*****',
            'scope' => '*',
          ]
        ;
        break;
      case 'LIVE':
        $this->api = $api_config->get('live_api');
        $this->form_params=
          [
            'grant_type' => 'client_credentials',
            'client_id' => '3',
            'client_secret' => '*******',
            'scope' => '*',
          ]
        ;
        break;
      default:
        $this->api = $api_config->get('live_api');
    }
    try {
      $response = $guzzle->post($this->api . '/oauth/token', [
        'form_params' => $this->form_params,
      ]);

      $this->token = json_decode((string) $response->getBody()
        ->getContents(), TRUE)['access_token'];
      $this->client = \Drupal::httpClient();
    } catch (Exception $e) {
      Logger::errorEvent("Caught exception: " . $e->getMessage());
    }
  }

  /**
   * @return \PhoneTrackingApi|null
   */
  public static function getInstance() {
    if (!isset(self::$instance) || (self::$instance == NULL)) {
      self::$instance = new PhoneTrackingApi();
    }
    return self::$instance;
  }

  /**
   * @return mixed|null
   */
  public function getToken() {
    return $this->token;
  }

  protected function refreshToken($client_id, $refresh_token, $client_secret) {
    /**
     * @todo we should get new token if previous token is expired
     */
  }

  /**
   * @param $domain
   * @param $destination
   * @param $visit_id
   * @param $visitor_id
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getNumber($destination, $domain) {

    try {
      $visit_arr = $this->getVisitInfo();
      $tracking_info = $this->getTrackingInfo();
      $source = $tracking_info->utm_source;
      $medium = $tracking_info->utm_medium;
      $campaign = $tracking_info->utm_campaign;
      $visitor_id = $visit_arr['visitor_id'];
      $visit_id = $visit_arr['visit_id'];
      $response = $this->client->request('POST', $this->api . '/api/tracking', [
        'headers' => [
          'Accept' => 'application/json',
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json',
        ],
        'json' => [
          'domain' => $domain,
          'destination' => $destination,
          'visit_id' => $visit_id,
          'visitor_id' => $visitor_id,
          'source' => $source,
          'medium' => $medium,
          'campaign' => $campaign,
        ],
      ]);
      $result = json_decode((string) $response->getBody()->getContents());
      if ($result->success) {
        return $result->result;
      } else {
        return $result->message;
      }
    } catch (Exception $e) {
      Logger::errorEvent('Exception in getNumber of PhoneTrackingApi', $e);
    }
  }

  /**
   * @param $extension
   * @param $cchsid
   * @param $caller_number
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function callReceived($extension, $cchsid, $caller_number) {
    try {
      if (is_null($cchsid) || empty($cchsid) || !is_string($cchsid)) {
        $cchsid = 'unknown';
      }
      $response = $this->client->request('POST', $this->api . '/api/call', [
        'headers' => [
          'Accept' => 'application/json',
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json',
        ],
        'json' => [
          'extension' => $extension,
          'cchsid' => $cchsid,
          'caller_number' => $caller_number,
        ],
      ]);
      /**
       * make result same as dcms results
       */
      $result = json_decode((string) $response->getBody()->getContents());
      if ($result->success) {
        $arr = [];
        foreach ($result->result as $key => $value) {
          $arr[$key] = $value;
        }
        $arr = array_reverse($arr, TRUE);
        return implode(',', $arr);
      } else {
        return $result->message;
      }
    } catch (Exception $e) {
      Logger::errorEvent('Exception in callReceived of PhoneTrackingApi', $e);
    }
  }

  /**
   * @param $host_name
   * @param $extension
   * @param $caller_number
   * @param $call_length
   * @param $type
   *
   * @return
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function callFinished($host_name, $extension, $caller_number, $call_length, $type) {

    try {
      $response = $this->client->request('POST', $this->api . '/api/callData', [
        'headers' => [
          'Accept' => 'application/json',
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json',
        ],
        'json' => [
          'host_name' => $host_name,
          'extension' => $extension,
          'caller_number' => $caller_number,
          'call_length' => $call_length,
          'type' => $type,
        ],
      ]);
      $result = json_decode((string) $response->getBody()->getContents());
      if ($result->success) {
        return "ok";
      } else {
        return $result->message;
      }
    } catch (Exception $e) {
      Logger::errorEvent("Exception in callFinished of PhoneTrackingApi ", $e);
    }
  }

  /**
   * @param $domain
   *
   * @return mixed
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getDefaultNumber($domain) {
    try {
      $response = $this->client->request('POST', $this->api . 'api/domain', [
        'headers' => [
          'Accept' => 'application/json',
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json',
        ],
        'json' => [
          'domain' => $domain,
        ],
      ]);
      $result = json_decode((string) $response->getBody()->getContents());
      if ($result->success) {
        return $result->result;
      } else {
        return $result->message;
      }
    } catch (Exception $e) {
      Logger::errorEvent("Exception in getDefaultNumber of PhoneTracking ", $e);
    }
  }

  /**
   * @param $extension
   *
   * @return \Psr\Http\Message\StreamInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getDomainId($extension) {

    try {
      $result = $this->client->request('GET', $this->api . "/api/number/$extension");
      if ($result->success) {
        return $result->getBody();
      } else {
        return $result->message;
      }

    } catch (Exception $e) {
      Logger::errorEvent("Exception in getDomainId of PhoneTrackingApi", $e);
    }
  }

}
