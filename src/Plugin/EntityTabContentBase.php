<?php

namespace Drupal\entity_ui\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Base class for Entity tab content plugins.
 */
abstract class EntityTabContentBase extends PluginBase implements EntityTabContentInterface {


  // TODO: we need the actual Tab this is on to come into the config
  // or at least elements of it, so we know the entity type we're on!

  /**
   * The target entity type for this plugin instance.
   */
  protected $targetEntityType;


  public function __construct($configuration, $plugin_id, $plugin_definition) {
    // TODO: throw exception if
    if (!isset($configuration['target_entity_type']) || !($configuration['target_entity_type'] instanceof EntityTypeInterface)) {
      throw new \Exception("Entity tab plugin configuration must contain a target entity type.");
    }

    $this->targetEntityType = $configuration['target_entity_type'];
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
