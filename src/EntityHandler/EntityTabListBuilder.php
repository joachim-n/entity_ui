<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\entity_ui\Plugin\EntityTabContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Entity tab entities for a single target entity type.
 */
class EntityTabListBuilder extends DraggableListBuilder {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Entity Tab content plugin manager
   *
   * @var \Drupal\entity_ui\Plugin\EntityTabContentManager
   */
  protected $entityTabContentPluginManager;

  /**
   * The currently active route match object.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * The target entity type ID.
   */
  protected $target_entity_type_id;

  /**
   * Constructs a new EntityTabListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\entity_ui\Plugin\EntityTabContentManager
   *   The entity tab plugin manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   The currently active route match object.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage,
      EntityTypeManagerInterface $entity_type_manager, EntityTabContentManager $entity_tab_content_manager,
      RouteMatchInterface $current_route_match) {
    parent::__construct($entity_type, $storage);

    $this->entityTypeManager = $entity_type_manager;
    $this->entityTabContentPluginManager = $entity_tab_content_manager;
    $this->currentRouteMatch = $current_route_match;

    $this->target_entity_type_id = $current_route_match->getRouteObject()->getOption('_target_entity_type_id');
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.entity_tab_content'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_ui_collection';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    // No need to sort; load() does that.
    $query = $this->storage->getQuery();
    $query->condition('target_entity_type', $this->target_entity_type_id);

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];

    $header['label'] = $this->t('Entity tab name');
    $header['plugin_label'] = $this->t('Content provider');
    $header['operations'] = $this->t('Operations');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];

    // Parent class tries to be too helpful and stupidly attempts to turn this
    // cell into a markup element...
    $row['label'] = $entity->label();

    $plugin_definition = $this->entityTabContentPluginManager->getDefinition($entity->getPluginID());
    $row['plugin_label'] = [
      '#markup' => $plugin_definition['label'],
    ];

    $row['operations']['data'] = $this->buildOperations($entity);

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Tweak the empty text.
    $form[$this->entitiesKey]['#empty'] = $this->t('There is no @label for @target_type_label entities yet.', [
      '@label' => $this->entityType->getLabel(),
      '@target_type_label' => $this->entityTypeManager->getDefinition($this->target_entity_type_id)->getLabel(),
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Tab weights have changed: clear local task caches.
    // TODO: inject this service.
    \Drupal::service('plugin.manager.menu.local_task')->clearCachedDefinitions();
  }

}
