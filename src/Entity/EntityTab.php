<?php

namespace Drupal\entity_ui\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Entity tab entity.
 *
 * @ConfigEntityType(
 *   id = "entity_tab",
 *   label = @Translation("Entity tab"),
 *   label_singular = @Translation("Entity tab"),
 *   label_plural = @Translation("Entity tabs"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Entity tab",
 *     plural = "@count Entity tabs",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\entity_ui\EntityHandler\EntityTabListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entity_ui\Form\EntityTabForm",
 *       "edit" = "Drupal\entity_ui\Form\EntityTabForm",
 *       "delete" = "Drupal\entity_ui\Form\EntityTabDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity_ui\Routing\AdminRouteProvider",
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
 *     "add-form" = "/admin/structure/entity_ui/entity_tab/add/{entity_type_id}",
 *     "edit-form" = "/admin/structure/entity_ui/entity_tab/{entity_tab}/edit",
 *     "delete-form" = "/admin/structure/entity_ui/entity_tab/{entity_tab}/delete",
 *     "Xcollection" = "/admin/structure/entity_ui/entity_tab"
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

  /**
   * The target entity type ID.
   *
   * @var string
   */
  protected $targetEntityType;

  /**
   * The ID of the Entity Tab Content plugin
   *
   * @var string
   */
  protected $content_plugin = 'entity_view';

  /**
   * Gets the target entity type ID.
   *
   * @return string
   *  The entity type ID that this entity tab is on.
   */
  public function getTargetEntityTypeID() {
    return $this->targetEntityType;
  }

  /**
   * Gets the path component for this tab.
   *
   * @return string
   *  The path component that is appended to the target entity's canonical URL.
   */
  public function getPathComponent() {
    // TODO: safer to store this as a separate piece of schema config!
    list(, $path) = explode('.', $this->id());
    return $path;
  }

  public function getPluginID() {
    return $this->content_plugin;
  }

}
