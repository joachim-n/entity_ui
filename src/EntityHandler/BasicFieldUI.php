<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Provides a TODO.
 */
 // TODO: add our own interafce
class BasicFieldUI extends EntityUIAdminBase {

  /**
   * {@inheritdoc}
   */
  // CAlled from subscriber
  public function getRoutes() {

  }

  // called from derivative
  public function getLocalTasks() {

  }

  /**
   * {@inheritdoc}
   */
  public function getLocalActions($base_plugin_definition) {
    return [];
  }

}
