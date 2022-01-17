<?php

namespace Drupal\mkt_phone_tracking\Helper;

use Drupal;
use Drupal\Core\Config\Config;

class PhoneTrackingConfig {

  public static function getPhoneTrackingConfig(): Config {
    return Drupal::configFactory()
      ->getEditable('mkt_phone_tracking.ip_settings');
  }

  public static function getIpRange(): ?string {
    return self::getPhoneTrackingConfig()->get('ip_range');
  }

  public static function setIpRange(string $ip_range) {
    self::getPhoneTrackingConfig()->set('ip_range', $ip_range)->save();
  }

}