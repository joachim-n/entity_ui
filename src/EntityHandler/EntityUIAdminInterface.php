<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a TODO.
 */
interface EntityUIAdminInterface {

  /**
   *
   */
  // CAlled from subscriber
  public function getRoutes() {

  }

  // called from derivative
  public function getLocalTasks() {

  }

  /**
   * Gets the definition of all derivatives of a base plugin. TODO
   *
   * @param array $base_plugin_definition
   *   The definition array of the base plugin.
   *
   * @return
   *  An array of plugin derivatives for the local actions for the entity type.
   */
  public function getLocalActions($base_plugin_definition);

}
