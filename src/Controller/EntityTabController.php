<?php

namespace Drupal\entity_ui\Controller;

/**
 * Provides TODO Entity tab content plugin manager.
 */
class EntityTabController {

  // TODO: inject route match and plugin manager.

  public function content() {
    $rm = \Drupal::service('current_route_match');
    $id = $rm->getRouteObject()->getDefault('_content_plugin');
    return [
      '#markup' => 'op!' . $id,
    ];
  }

  public function title() {
    return 'title!';
  }

  // TODO: add a title() method too?

}
