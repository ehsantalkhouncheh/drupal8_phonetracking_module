<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block with a CTA red button.
 *
 * @Block(
 *   id = "cta_button_phone_number_block",
 *   admin_label = @Translation("CTA Button Number Block"),
 * )
 */
class CtaButtonPhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $phone_number = PhoneTrackingController::getNumber();
    return [
      '#markup' => '<div class="cta-btn__item">
                            <a href="tel:' . $phone_number . '" class="cta-link cta-lphone pure-button pure-button-secondary pure-button-with-icon icon-call_circle-filled" role="button" tabindex="-1" aria-pressed="false" draggable="false">
                                ' . $phone_number . '
                            </a>
                        </div>',
    ];
  }

}
