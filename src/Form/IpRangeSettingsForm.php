<?php

namespace Drupal\mkt_phone_tracking\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure marketing-phone-tracking settings for this site.
 */
class IpRangeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mkt_phone_tracking_ip_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mkt_phone_tracking.ip_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['ip_range'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Ip Range'),
      '#required' => TRUE,
      '#default_value' => $this->config('mkt_phone_tracking.ip_settings')
        ->get('ip_range'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('ip_range'))) {
      $form_state->setErrorByName('ip_range', $this->t('This field is required.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mkt_phone_tracking.ip_settings')
      ->set('ip_range', $form_state->getValue('ip_range'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
