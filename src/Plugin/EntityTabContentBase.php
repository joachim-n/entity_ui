<?php

namespace Drupal\entity_ui\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity_ui\Entity\EntityTabInterface;

/**
 * Base class for Entity tab content plugins.
 */
abstract class EntityTabContentBase extends PluginBase implements EntityTabContentInterface {

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
    // Zap the configuration the parent method set.
    $this->configuration = $entity_tab->getPluginConfiguration();
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
