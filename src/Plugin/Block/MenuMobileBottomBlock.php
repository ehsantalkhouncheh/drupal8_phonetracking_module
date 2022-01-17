<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block that show phone number in the bottom of the mobile menu.
 *
 * @Block(
 *   id = "menu_mobile_bottom_block",
 *   admin_label = @Translation("Menu Mobile Bottom"),
 * )
 */
class MenuMobileBottomBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<div class="menu_mobile_bottom">
	<div class="menu-logo">
		<a class="logo__link" href="/" rel="home" title="Secretaría.es"><img width="142" alt="Secretaría.es" src="/themes/mkt/mkt_ses/images/logo_white@2x.png"></a>
	</div>
	<div class="mobile-contact">
		<div class="mobile-menu-bottom phone-call">
			<a href="tel:' . PhoneTrackingController::getNumber() . '" title="Phone Number" class="pure-menu-link icon--call_circle">
                <span>' . PhoneTrackingController::getNumber() . '</span>
			</a>
		</div>
		<div class="mobile-menu-bottom mobile-bottom-user user-login">
			<a href="/login.html" title="User Login"  class="pure-menu-link  icon--avatar" >
			    <span>Iniciar sesión</span>
			</a>
		</div>
	</div>
</div>',
    ];
  }

}
