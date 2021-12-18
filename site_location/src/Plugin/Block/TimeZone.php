<?php

namespace Drupal\site_location\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\site_location\LocationManager;
use Drupal\Core\Cache\Cache;

/**
 * Provides a block for displaying time that are fetched from the time zone.
 *
 * @Block(
 *   id = "time_zone_block",
 *   admin_label = @Translation("Time Zone"),
 * )
 */
class TimeZone extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * LocationManager service.
   *
   * @var \Drupal\site_location\LocationManager
   */
  protected $locationServices;


  /**
   * The construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\site_location\LocationManager $location_manager
   *   The news services.
   */
  public function __construct(array $configuration,
    $plugin_id,
    array $plugin_definition,
    DateFormatterInterface $date_formatter,
    ConfigFactoryInterface $config_factory,
    LocationManager $location_manager) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory);
    $this->dateFormatter = $date_formatter;
    $this->config = $config_factory->get('site_location.settings');
    $this->locationServices = $location_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('date.formatter'),
      $container->get('config.factory'),
      $container->get('site_location.location_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data = [];
    $city = $this->config->get('city');
    $country = $this->config->get('country');
    $timezone = $this->locationServices->updateTimezone();

    $data = [
      'time' => $timezone,
      'country' => $country,
      'city' =>  $city,
    ];

    return [
      "#theme" => 'site_location',
      "#data" => $data,
     ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
