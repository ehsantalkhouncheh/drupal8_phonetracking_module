<?php

namespace Drupal\mkt_phone_tracking\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure marketing-phone-tracking settings for this site.
 */
class IframeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mkt_phone_tracking_iframe_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mkt_phone_tracking.iframe_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['iframe_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('iframe Url'),
      '#required' => TRUE,
      '#default_value' => $this->config('mkt_phone_tracking.iframe_settings')
        ->get('iframe_url'),
    ];
    $form['iframe_api'] = [
      '#type' => 'textfield',
      '#title' => $this->t('api url'),
      '#required' => TRUE,
      '#default_value' => $this->config('mkt_phone_tracking.iframe_settings')
        ->get('iframe_api'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('iframe_url'))) {
      $form_state->setErrorByName('iframe_url', $this->t('This field is required.'));
    } elseif (empty($form_state->getValue('iframe_api'))) {
      $form_state->setErrorByName('iframe_api', $this->t('This field is required.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mkt_phone_tracking.iframe_settings')
      ->set('iframe_url', $form_state->getValue('iframe_url'))
      ->set('iframe_api', $form_state->getValue('iframe_api'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
