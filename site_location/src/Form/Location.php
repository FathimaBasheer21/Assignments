<?php

namespace Drupal\site_location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provide settings page for adding site location configuration.
 */
class Location extends ConfigFormBase {

  /**
   * The construct.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
    $this->config = $config_factory->getEditable('site_location.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Implements FormBuilder::getFormId.
   */
  public function getFormId() {
    return 'site_location_settings';
  }

  /**
   * Implements ConfigFormBase::getEditableConfigNames.
   */
  protected function getEditableConfigNames() {
    return ['site_location.settings'];
  }

  /**
   * Implements FormBuilder::buildForm.
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $header_section = $this->config('site_location.settings')->get();

    $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#default_value' => isset($header_section['country']) ? $header_section['country'] : '',
      '#description' => $this->t('Enter country from which the site is accessed.'),
      '#required' => TRUE,
    ];

    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => isset($header_section['city']) ? $header_section['city'] : '',
      '#description' => $this->t('Enter city from which the site is accessed.'),
      '#required' => TRUE,
    ];

    $timezone = [
      ' ' => $this->t('-Any-'),
      'America/Chicago' => $this->t('America/Chicago'),
      'America/New_york' => $this->t('America/New york'),
      'Asia/Tokyo' => $this->t('Asia/Tokyo'),
      'Asia/Dubai'  => $this->t('Asia/Dubai'),
      'Asia/Kolkata' => $this->t('Asia/Kolkata'),
      'Europe/Amsterdam' => $this->t('Europe/Amsterdam'),
      'Europe/Oslo' => $this->t('Europe/Oslo'),
      'Europe/London' => $this->t('Europe/London'),
    ];

    $form['time'] = [
      '#type' => 'select',
      '#title' => $this->t('Time Zone'),
      '#options' => $timezone,
      '#default_value' => isset($header_section['time']) ? $header_section['time'] : '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements FormBuilder::submitForm().
   *
   * Serialize the user's settings and save it to the Drupal's config Table.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configFactory()
      ->getEditable('site_location.settings')
      ->set('country', $values['country'])
      ->set('city', $values['city'])
      ->set('time', $values['time'])
      ->save();

    $messenger = parent::messenger();
    $messenger->addMessage('Your Settings have been saved.', 'status');

    parent::submitForm($form, $form_state);
  }

}
