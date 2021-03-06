<?php

namespace Drupal\entity_ui\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Entity tab content item annotation object.
 *
 * @see \Drupal\entity_ui\Plugin\EntityTabContentManager
 * @see plugin_api
 *
 * @Annotation
 */
class EntityTabContent extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
