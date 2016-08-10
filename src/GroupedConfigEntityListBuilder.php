<?php

namespace Drupal\entity_ui;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of TODO.
 */
abstract class GroupedConfigEntityListBuilder extends ConfigEntityListBuilder {

  /**
   * All entity types.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface[]
   */
  protected $entityTypes;

  /**
   * Load all the entities and group them by a property.
   *
   * @return
   *  An array grouped by some property of the entities. Each key is a grouping
   *  value, and each value is a further array of entities keyed by ID.
   */
  protected function loadGrouped() {
    $entities = array();
    foreach ($this->load() as $id => $entity) {
      $entities[$this->getGrouping($entity)][$id] = $entity;
    }
    return $entities;
  }

  /**
   * Gets the entity property to use to group an entity.
   *
   * @param $entity
   *  The entity to get the property for.
   *
   * @return
   *  A value with which to group this entity.
   */
  abstract protected function getGrouping($entity);

  abstract protected function getGroups();

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = array();
    $grouped_entities = $this->loadGrouped();
    foreach ($this->getGroups() as $grouping => $grouping_label) {
      if (isset($grouped_entities[$grouping])) {
        $entities = $grouped_entities[$grouping];
      }
      else {
        $entities = [];
      }

      $table = array(
        '#prefix' => '<h2>' . $grouping_label . '</h2>',
        '#type' => 'table',
        '#header' => $this->buildHeader(),
        '#rows' => array(),
      );
      foreach ($entities as $entity) {
        if ($row = $this->buildRow($entity)) {
          $table['#rows'][$entity->id()] = $row;
        }
      }

      // Move content at the top.
      /*
      // TODO!
      if ($entity_type == 'node') {
        $table['#weight'] = -10;
      }
      */

      $table['#rows']['_add_new'][] = array(
        'data' => array(
          '#type' => 'link',
          '#url' => $this->getGroupedAddURL($grouping),
          '#title' => $this->t('Add new %grouping @entity-type', array(
            '%grouping' => $grouping_label,
            '@entity-type' => $this->entityType->getLowercaseLabel(),
          )),
        ),
        'colspan' => count($table['#header']),
      );
      $build[$grouping] = $table;
    }
    return $build;
  }

  /**
   * Gets the human-readable label for a grouping.
   *
   * @param string $grouping
   *   A grouping value. This is the same as the values returned by
   *   getGrouping().
   *
   * @return string
   *   The label for the grouping.
   */
  protected function getGroupingLabel($grouping) {
    return ucfirst($grouping);
  }

  abstract protected function getGroupedAddURL($grouping);

}

