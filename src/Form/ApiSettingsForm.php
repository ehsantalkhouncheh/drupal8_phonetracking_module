<?php

namespace Drupal\mkt_phone_tracking\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure mkt_phone_tracking settings for this site.
 */
class ApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mkt_phone_tracking_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mkt_phone_tracking.api_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['dev_api'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Dev Api'),
      '#default_value' => $this->config('mkt_phone_tracking.api_settings')
        ->get('dev_api'),
    ];
    $form['test_api'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Stage Api'),
      '#default_value' => $this->config('mkt_phone_tracking.api_settings')
        ->get('test_api'),
    ];
    $form['live_api'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Live Api'),
      '#default_value' => $this->config('mkt_phone_tracking.api_settings')
        ->get('live_api'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mkt_phone_tracking.api_settings')
      ->set('dev_api', $form_state->getValue('dev_api'))
      ->set('test_api', $form_state->getValue('test_api'))
      ->set('live_api', $form_state->getValue('live_api'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
