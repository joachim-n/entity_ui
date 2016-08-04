<?php

namespace Drupal\entity_ui\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EntityTabForm.
 *
 * @package Drupal\entity_ui\Form
 */
class EntityTabForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_tab = $this->entity;
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

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Sets the entity type ID into the config name.
    // TODO: this only needs to happen when adding a new entity.
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
