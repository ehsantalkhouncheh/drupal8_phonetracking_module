<?php

namespace Drupal\mkt_phone_tracking\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure marketing-phone-tracking settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mkt_phone_tracking_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mkt_phone_tracking.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['default_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Number'),
      '#required' => TRUE,
      '#default_value' => $this->config('mkt_phone_tracking.settings')
        ->get('default_number'),
    ];
    $form['domain'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Domain'),
      '#default_value' => $this->config('mkt_phone_tracking.settings')
        ->get('domain'),
    ];
    $form['destination'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Destination'),
      '#default_value' => $this->config('mkt_phone_tracking.settings')
        ->get('destination'),
    ];
    $form['use_option'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => $this->t('Options'),
      '#options' => [
        'use_api' => t('Use api'),
        'use_iframe' => t('Use iframe'),
      ],
      '#default_value' => $this->config('mkt_phone_tracking.settings')
        ->get('use_option'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('use_option'))) {
      $form_state->setError($form, 'you should select one radio button');
    } elseif (empty($form_state->getValue('default_number'))) {
      $form_state->setError($form, 'This field is required');
    } elseif (empty($form_state->getValue('domain'))) {
      $form_state->setError($form, 'This field is required');
    } elseif (empty($form_state->getValue('destination'))) {
      $form_state->setError($form, 'This field is required');
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mkt_phone_tracking.settings')
      ->set('use_option', $form_state->getValue('use_option'))
      ->set('default_number', $form_state->getValue('default_number'))
      ->set('destination', $form_state->getValue('destination'))
      ->set('domain', $form_state->getValue('domain'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
