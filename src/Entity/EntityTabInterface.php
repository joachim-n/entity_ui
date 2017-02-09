<?php

namespace Drupal\entity_ui\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Entity tab entities.
 */
interface EntityTabInterface extends ConfigEntityInterface {

  /**
   * Returns the settings for the content plugin.
   *
   * @return array
   *  The plugin settings.
   */
  public function getPluginConfiguration();

  // TODO
}
