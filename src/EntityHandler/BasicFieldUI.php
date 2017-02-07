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
  public function getRoutes() {
    return [];
  }

  // called from derivative
  public function getLocalTasks($base_plugin_definition) {
    return [];
  }

  public function localTasksAlter(&$local_tasks) {

  }

  /**
   * {@inheritdoc}
   */
  public function getLocalActions($base_plugin_definition) {
    return [];
  }

}
