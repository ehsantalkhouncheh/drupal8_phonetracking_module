<?php


namespace Drupal\mkt_phone_tracking\Ipam;


use Drupal\mkt_log_manager\Logger\Logger;
use Exception;
use GuzzleHttp\Client;

class IpamApi {

  /**
   * @var string
   */
  private $api_url;

  /**
   * @var string
   */
  private $api_app_id;

  /**
   * @var string
   */
  private $api_key;

  /**
   * @var string
   */
  private $api_user;

  /**
   * @var string
   */
  private $api_pass;

  /**
   * @var Client
   */
  private $client;

  public function __construct() {
    /**
     * @todo  Here we hardcode the url , username , password and token for IPAM api so far as soon as possible
     *        we should put them in AWS and get them from environmental variables
     */
    $this->api_url = '********';
    $this->api_app_id = '****';
    $this->api_key = '******';
    $this->api_user = '******';
    $this->api_pass = '******';
    $this->client = new Client();
  }

  /**
   * in this function based on this tikcet :MKT-11724 we only get all subnet ip
   * range for id 8
   *
   * @return array
   */
  public function getSubnets() {
    try {
      $url = $this->api_url . $this->api_app_id . '/sections/8/subnets/';
      $client = $this->client->get($url,
        [
          'headers' => [
            'Content-Type' => 'application/json',
            'token' => $this->api_key,
          ],
        ]
      );
      $result = json_decode($client->getBody())->data;
      return $this->prepareSubnetResult($result);
    } catch (Exception $e) {
      Logger::errorEvent('Exception in getIpAddresses of IpamApi class', $e);
    }
  }

  /**
   * this function get and return all of Ip Address that we have
   *
   * @return array
   */
  public function getIpAddresses() {
    try {
      $url = $this->api_url . $this->api_app_id . '/addresses';
      $client = $this->client->get($url,
        [
          'headers' => [
            'Content-Type' => 'application/json',
            'token' => $this->api_key,
          ],
        ]
      );
      $result = json_decode($client->getBody())->data;
      return $this->prepareIPResult($result);
    } catch (Exception $e) {
      Logger::errorEvent('Exception in getIpAddresses of IpamApi class', $e);
    }
  }

  /**
   * his function prepare the ip array result based on api call result
   *
   * @param $data
   *
   * @return array
   */
  private function prepareIPResult($data) {
    try {
      $result = [];
      foreach ($data as $record) {
        if (isset($record->ip) && !empty($record->ip)) {
          if (!in_array($record->ip, $result)) {
            array_push($result, $record->ip);
          }
        }
      }
      return $result;

    } catch (Exception $e) {
      Logger::errorEvent('Exception in prepareIPResult of IpamApi class ', $e);
    }
  }

  /**
   * this function prepare the subnets array result based on api call result
   *
   * @param $data
   *
   * @return array
   */
  private function prepareSubnetResult($data) {
    try {
      $result = [];
      foreach ($data as $record) {
        if (isset($record->subnet) && !empty($record->subnet)) {
          $subnet = [
            "description" => $record->description,
            "subnet" => $record->subnet . '/' . $record->mask,
          ];
          if (!in_array($subnet, $result)) {
            array_push($result, $subnet);
          }
        }
      }
      return $result;

    } catch (Exception $e) {
      Logger::errorEvent('Exception in prepareSubnetResult of IpamApi class', $e);
    }
  }

}