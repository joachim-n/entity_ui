<?php

namespace Drupal\entity_ui\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for editing and creating entity tab entities.
 */
class EntityTabForm extends EntityForm {

  /**
   * The entity type for which the entity tab is being created.
   *
   * @var string
   */
   // ADD FORM ONLY TODO!
  protected $targetEntityTypeId;

  // entity_type.bundle.info

  /**
   * {@inheritdoc}
   */
  protected function init(FormStateInterface $form_state) {
    parent::init($form_state);
    $this->entityType = $this->entityManager->getDefinition($this->entity->getEntityTypeId());
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL) {
    // ADD FORM ONLY TODO!

    $this->targetEntityTypeId = $entity_type_id;
    $form = parent::buildForm($form, $form_state);
    // Change replace_pattern to avoid undesired dots.
    //$form['id']['#machine_name']['replace_pattern'] = '[^a-z0-9_]+';
    $definition = $this->entityManager->getDefinition($this->targetEntityTypeId);
    $form['#title'] = $this->t('Add new %label @entity-type', array(
      '%label' => $definition->getLabel(),
      '@entity-type' => $this->entityType->getLowercaseLabel(),
    ));
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_tab = $this->entity;
    dsm($entity_tab);
    
    // Add only:
    $entity_tab->targetEntityType = $this->targetEntityTypeId;
    
    
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_tab->label(),
      '#description' => $this->t("Label for the Entity tab."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_tab->id(),
      '#machine_name' => [
        'exists' => '\Drupal\entity_ui\Entity\EntityTab::load',
      ],
      '#disabled' => !$entity_tab->isNew(),
    ];

    // TODO:
    // verb
    // weight

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Sets the entity type ID into the config name.
    // TODO: this only needs to happen when adding a new entity.
    // ARGH where does this come from???????
    $form_state->setValueForElement($form['id'], $this->targetEntityTypeId . '.' . $form_state->getValue('id'));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_tab = $this->entity;
    $status = $entity_tab->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Entity tab.', [
          '%label' => $entity_tab->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Entity tab.', [
          '%label' => $entity_tab->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity_tab->urlInfo('collection'));
  }

}
