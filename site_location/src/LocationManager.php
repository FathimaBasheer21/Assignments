<?php

namespace Drupal\site_location;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Component\Datetime;

/**
 * Provides a block for displaying time zone that are fetched from the site location configuration.
 *
 * @Block(
 *   id = "site_location_block",
 *   admin_label = @Translation("Site Location"),
 * )
 */
class LocationManager {

  /**
   * Contains the configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The construct.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(ConfigFactoryInterface $config_factory,
    DateFormatterInterface $date_formatter) {

    $this->config = $config_factory->get('site_location.settings');
    $this->dateFormatter = $date_formatter;
  }


  /**
   * Function to fetch time based on the provided time zone.
   */
  public function updateTimeZone() {
    $time = $this->config->get('time');
    $date = date('m/d/Y h:i:s a', time());
    $formatted = $this->dateFormatter->format(strtotime($date),'custom', 'jS M Y h:i A ',$time);
    return $formatted;

  }

}
