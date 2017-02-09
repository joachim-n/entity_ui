<?php

namespace Drupal\entity_ui\Plugin;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Entity tab content plugins.
 */
interface EntityTabContentInterface extends PluginInspectionInterface {

  /**
   * Determines whether the plugin can be used with the given entity type.
   *
   * This should purely concern itself with applicability: whether the entity
   * type supports what this plugin does.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface
   *  The entity type to check.
   *
   * @return bool
   *  TRUE if the plugin can be used with the entity type, FALSE if not.
   */
  public static function appliesToEntityType(EntityTypeInterface $entity_type);

  /**
   * Provides a form array for the plugin's settings form.
   *
   * @param array $form
   *   The form array to add elements to. Elements should be placed in the top
   *   level of this.
   * @param FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   A form array for the plugin's configuration subform.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state);

  public function access();

  /**
   * Builds the content for the entity tab.
   *
   * @param \Drupal\Core\Entity\EntityInterface $target_entity
   *  The entity the tab is on.
   *
   * @return
   *  A render array.
   */
  public function buildContent(EntityInterface $target_entity);

}
