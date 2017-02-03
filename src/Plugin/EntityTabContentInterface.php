<?php

namespace Drupal\entity_ui\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines an interface for Entity tab content plugins.
 */
interface EntityTabContentInterface extends PluginInspectionInterface {

  public static function appliesToEntityType(EntityTypeInterface $entity_type);

  public function access();

  /**
   * Provides a form array for the TODO plugin's settings form.
   *
   * @param array $form
   *   The form array.
   * @param FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified form array.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state);

}
