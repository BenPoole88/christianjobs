<?php

namespace Drupal\job_board\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\organization\Entity\Organization;
use Drupal\organization\Plugin\Field\FieldType\OrganizationMetadataReferenceItem;
use Drupal\user\UserInterface;

class RecruiterApproveMemberRequestForm extends RecruiterMemberFormBase {

  /**
   * {@inheritdoc}
   *
   * This form only applies to member requests.
   */
  protected $permittedStatus = [
    OrganizationMetadataReferenceItem::STATUS_REQUESTED
  ];

  /**
   * [@inheritdoc}
   */
  public function getFormId() {
    return 'recruiter_approve_member_request_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Organization $organization = NULL, UserInterface $user = NULL) {
    $form = parent::buildForm($form, $form_state, $organization, $user);

    $form['message'] = $this->t('Are you sure you wish to confirm @user as a @role of @organization?', [
      '@user' => $user->label(),
      '@organization' => $organization->label(),
      '@role' => ucfirst($this->item->role),
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->item->status = OrganizationMetadataReferenceItem::STATUS_ACTIVE;
    $this->item->getEntity()->save();
  }
}
