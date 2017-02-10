<?php

namespace Drupal\entity_ui\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Base class for Entity tab content plugins.
 */
abstract class EntityTabContentBase extends PluginBase implements EntityTabContentInterface {

  /**
   * Default configuration values for this plugin.
   *
   * TODO: is there a proper way of doing this?
   */
  protected $defaults = [];

  /**
   * The target entity type ID for this plugin instance.
   */
  protected $targetEntityTypeId;

  /**
   * {@inheritdoc}
   */
  public function __construct($configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    // Our plugin manager has already checked this exists as expected.
    $entity_tab = $configuration['entity_tab'];

    $this->targetEntityTypeId = $entity_tab->getTargetEntityTypeID();
    // Zap the configuration the parent method set, replace with the actual
    // configuration from the Entity Tab.
    $this->configuration = $entity_tab->getPluginConfiguration();
    $this->configuration += $this->defaults;
  }

  /**
   * {@inheritdoc}
   */
  public static function appliesToEntityType(EntityTypeInterface $entity_type) {
    return TRUE;
  }

  public function access() {

  }

  protected function isAvailable() {

  }

  protected function hasAccess() {

  }

}
