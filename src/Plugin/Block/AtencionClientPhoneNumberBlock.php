<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block that show phone number in footer area.
 *
 * @Block(
 *   id = "atencion_client_phone_number_block",
 *   admin_label = @Translation("Atencion Client Phone Number"),
 * )
 */
class AtencionClientPhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $phone_number = PhoneTrackingController::getNumber();

    return [
      '#markup' => '<div class="extra-footer">
    <p>Atención al cliente <a href="tel:900838524">900 838 524</a></p>
    <p>Configure su servicio de telesecretariado <a href="tel:' . $phone_number . '" class="phone-tracking">' . $phone_number . '</a></p>
</div>',
    ];
  }

}