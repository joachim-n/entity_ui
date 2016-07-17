<?php

namespace Drupal\entity_ui\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Entity tab entity.
 *
 * @ConfigEntityType(
 *   id = "entity_tab",
 *   label = @Translation("Entity tab"),
 *   handlers = {
 *     "list_builder" = "Drupal\entity_ui\EntityTabListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_ui\Form\EntityTabForm",
 *       "edit" = "Drupal\entity_ui\Form\EntityTabForm",
 *       "delete" = "Drupal\entity_ui\Form\EntityTabDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_ui\EntityTabHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "entity_tab",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/entity_ui/entity_tab/{entity_tab}",
 *     "add-form" = "/admin/structure/entity_ui/entity_tab/add",
 *     "edit-form" = "/admin/structure/entity_ui/entity_tab/{entity_tab}/edit",
 *     "delete-form" = "/admin/structure/entity_ui/entity_tab/{entity_tab}/delete",
 *     "collection" = "/admin/structure/entity_ui/entity_tab"
 *   }
 * )
 */
class EntityTab extends ConfigEntityBase implements EntityTabInterface {

  /**
   * The Entity tab ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Entity tab label.
   *
   * @var string
   */
  protected $label;

}
