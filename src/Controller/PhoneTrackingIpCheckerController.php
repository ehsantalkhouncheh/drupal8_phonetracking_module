<?php


namespace Drupal\mkt_phone_tracking\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\mkt_log_manager\Logger\Logger;
use Drupal\mkt_phone_tracking\Ipam\IpamApi;
use Drupal\mkt_phone_tracking\Helper\PhoneTrackingConfig;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class PhoneTrackingIpCheckerController extends ControllerBase {


  /**
   * @var array
   */
  private $ip_range;

  /**
   * @var string the last ip_rnage that save in config for phone tracking
   *   modules
   */
  private $last_ip_range;

  /**
   * @var array
   */
  private $subnet;

  /**
   * @var RequestStack
   */
  private $request_stack;

  public function __construct(RequestStack $request_stack) {
    $this->last_ip_range = PhoneTrackingConfig::getIpRange();
    $this->ip_range = '';
    $API = new IpamApi();
    $this->subnet = $API->getSubnets();
    $this->request_stack = $request_stack;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }


  public function ProcessUpdate() {
    $result = "";
    $valid_token = getenv('API_TOKEN');
    $access_token = $this->request_stack->getCurrentRequest()
      ->get('access_token');
    if (strcmp($valid_token, $access_token)) {
      try {
        $result = $this->prepareInternalSubnet($this->subnet);
        $this->ip_range = implode(",\r\n", $result);
        /**
         * @todo  as we wanna test this and in future the asterisk ip may change we add this line of code to make all ip range
         *        as a valid range as soon ans the new ip for asterisk determine we should remove this line of code
         */
        $this->ip_range .= ",\r\n";
        $this->ip_range .= "0.0.0.0/0";
        PhoneTrackingConfig::setIpRange($this->ip_range);
        $result = 'Update Internal Ip Range Complete';
      } catch (Exception $e) {
        Logger::errorEvent('Exception in  processIpRange of PhoneTrackingIpCheckerController', $e);
        $result = 'Exception in Internal Ip Range Update with This Message:' . $e->getMessage();
      }
    } else {
      $result = 'you are not Authorized for run this method';
    }
    return new JsonResponse($result);

  }

  private function prepareInternalSubnet(array $records): array {
    try {
      $result = [];
      if (isset($records) && !empty($records)) {
        foreach ($records as $record) {
          array_push($result, $record['subnet']);
        }
      } else {
        throw new Exception('Something went wrong we can not fetch subnet from Ipam Api');
      }
      return $result;
    } catch (Exception $e) {
      Logger::errorEvent('Exception in  prepareInternalSubnet of PhoneTrackingIpCheckerController', $e);
    }
  }

}