<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block with a simple phone number without style.
 *
 * @Block(
 *   id = "phone_number_block",
 *   admin_label = @Translation("Phone Number block"),
 * )
 */
class PhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $phone_number = PhoneTrackingController::getNumber();
    return [
      '#markup' => '<a title="' . $phone_number . '" href="tel:' . $phone_number . '" class="phone-tracking">' . $phone_number . '</a>',
    ];
  }

}
