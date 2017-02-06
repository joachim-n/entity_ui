<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the interface for Entity UI admin handlers.
 *
 *
 */
interface EntityUIAdminInterface {

  /**
   *
   */
  // CAlled from subscriber
  public function getRoutes();

  // called from derivative
  public function getLocalTasks($base_plugin_definition);

  /**
   * Alter local tasks.
   *
   * @param $local_tasks
   *  The array of local tasks passed to hook_local_tasks_alter().
   *
   * @see entity_ui_local_tasks_alter()
   */
  public function localTasksAlter(&$local_tasks);

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
