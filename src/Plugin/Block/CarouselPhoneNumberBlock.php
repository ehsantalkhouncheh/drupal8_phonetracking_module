<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block with a simple phone number without style.
 *
 * @Block(
 *   id = "carousel_phone_number_block",
 *   admin_label = @Translation("Carousel Phone Number block"),
 * )
 */
class CarouselPhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $phone_number = PhoneTrackingController::getNumber();
    return [
      '#markup' => '<a href="tel:' . $phone_number . '" class="phone-tracking home-carousel__link pure-button pure-button-transparent" role="button" tabindex="0" aria-pressed="false">Llámanos ' . $phone_number . '</a>',
    ];
  }

}
