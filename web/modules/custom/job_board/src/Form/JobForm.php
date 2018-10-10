<?php

namespace Drupal\job_board\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class JobForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $form['#attributes']['class'][] = 'card';
    $form['#attributes']['class'][] = 'card-main';

    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array|void
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['#attributes']['class'][] = 'card-item';
    $actions['#attributes']['class'][] = 'card-actions';
    $actions['#attributes']['class'][] = 'divider-top';
    return $actions;
  }

}
