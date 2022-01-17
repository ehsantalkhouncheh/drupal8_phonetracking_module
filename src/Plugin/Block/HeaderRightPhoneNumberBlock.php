<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block that show phone number in header right area.
 *
 * @Block(
 *   id = "header_right_phone_number_block",
 *   admin_label = @Translation("Header Right Phone Number"),
 * )
 */
class HeaderRightPhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<div class="hidden-xs">
   <div class="flex-nav-left">

      <a href="/recepcion-de-llamadas/prueba-en-directo.html" title="Prueba gratuita">
         <span class="pure-button pure-button-primary header-right-items header-right-btn">Prueba gratuita</span>
      </a>

      <div class="contact_popup">
          <div class="popover__wrapper">
            <a href="#" title="Phone Number">
             <span class="header-right-items phone-call pure-button pure-button-secondary popover__title">
                <span>' . PhoneTrackingController::getNumber() . '</span>
             </span>
            </a>
            <div class="popover__content">
                <div class="popover__message">
                    <h5>Contacto<span>Asesoramiento a clientes</span></h5>
                    <p>Nacional gratuito <span>900 423 686 </span>
                    <br>Internacional <span>+34 91 123 7707 </span>
                    </p>
                    <p>Atención al cliente <span>900 423 633 </span>
                    <br>Configure su servicio de<br>telesecretariado <span>900 809 725 </span>
                    </p>
                </div>
            </div>
          </div>
      </div>
      <div class="login_popup">
        <a href="#"  data-toggle="modal" data-target="#user-login" title="User Login" class="pure-menu-link customer_portal__link float-left">
         <span class="user-icon">
         </span>
        </a>
      </div>


   </div>
</div>

<div class="visible-xs">
   <div class="mobile-header-right">
      <div class="contact_popup float-left">
        <div class="popover__wrapper">
          <a href="#" title="Phone Number">
             <span class="phone-call icon icon--call_circle"></span>
          </a>
          <div class="popover__content popover__content_small">
                <div class="popover__message">
                    <h5>Contacto<span>Asesoramiento a clientes</span></h5>
                    <p>Nacional gratuito <span>900 423 686 </span>
                    <br>Internacional <span>+34 91 123 7707 </span>
                    </p>
                    <p>Atención al cliente <span>900 423 633 </span>
                    <br>Configure su servicio de<br>telesecretariado <span>900 809 725 </span>
                    </p>
                </div>
            </div>
      </div>
      </div>
      
      <div class="login_popup float-left">
          <a href="#"  data-toggle="modal" data-target="#user-login"  title="User Login" >
             <span class="user_login icon icon--avatar"></span>
             </p>
          </a>
      </div>

   </div>
</div>',
    ];
  }

}
