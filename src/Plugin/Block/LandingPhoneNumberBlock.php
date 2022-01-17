<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block with a simple phone number without style - exclusive for
 * landing page.
 *
 * @Block(
 *   id = "landing_phone_number_block",
 *   admin_label = @Translation("Landing Phone Number block"),
 * )
 */
class LandingPhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $phone_number = PhoneTrackingController::getNumber();
    return [
      '#markup' => '<a href="tel:' . $phone_number . '" class="phone-tracking lp-phone__link">' . $phone_number . '</a>',
    ];
  }

}
