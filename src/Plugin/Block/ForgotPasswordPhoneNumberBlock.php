<?php

namespace Drupal\mkt_phone_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\mkt_phone_tracking\Controller\PhoneTrackingController;

/**
 * Provides a block with a text and Phone Number Block
 *
 * @Block(
 *   id = "forgot_password_phone_number_block",
 *   admin_label = @Translation("Forgot Password Phone Number Block"),
 * )
 */
class ForgotPasswordPhoneNumberBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $phone_number = PhoneTrackingController::getNumber();
    return [
      '#markup' => '<div class="forgot-password__item padding-box">
                        <p>¿Tiene problemas al restablecer su contraseña o nombre de usuario? Desafortunadamente, solo le podremos enviar una nueva contraseña si nos facilitó una dirección de correo electrónico alternativa o un número de teléfono móvil en el portal de clientes.</p>
                        <p>De todas maneras, el servicio de atención al cliente estará encantado de ayudarle directamente en caso de que sea necesario: <strong> ' . $phone_number . '</strong></p>
                    </div>',
    ];
  }

}
