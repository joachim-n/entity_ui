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
