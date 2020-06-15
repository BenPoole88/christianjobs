<?php

namespace Drupal\job_board\Form;

use CommerceGuys\Intl\Formatter\CurrencyFormatterInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\organization\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RecruiterBuyCreditForm extends FormBase {

  /**
   * @var \Drupal\organization\Entity\Organization
   */
  protected $organization;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * @var \Drupal\commerce_cart\CartManagerInterface
   */
  protected $cartManager;

  /**
   * @var \CommerceGuys\Intl\Formatter\CurrencyFormatterInterface
   */
  protected $currencyFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('commerce_cart.cart_provider'),
      $container->get('commerce_cart.cart_manager'),
      $container->get('commerce_price.currency_formatter')
    );
  }

  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    CartProviderInterface $cart_provider,
    CartManagerInterface $cart_manager,
    CurrencyFormatterInterface $currency_formatter
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cartProvider = $cart_provider;
    $this->cartManager = $cart_manager;
    $this->currencyFormatter = $currency_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'recruiter_buy_credit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Organization $organization = NULL) {
    $this->organization = $organization;

    // @todo: Get products
    $product_variation_storage = $this->entityTypeManager->getStorage('commerce_product_variation');
    $query = $product_variation_storage->getQuery();
    $query->condition('type', 'credit_bundle');
    $options = [];
    foreach ($product_variation_storage->loadMultiple($query->execute()) as $variation) {
      /** @var \Drupal\commerce_price\Price $price */
      $price = $variation->price->get(0)->toPrice();
      $options[$variation->id()] = $this->t(
          '@num @label',
          [
            '@num' => $variation->credit_count->value,
            '@label' => $variation->credit_count->value == 1 ?
              $this->t('Job Credit') : $this->t('Job Credits')
          ]
        )." - ".$this->currencyFormatter->format($price->getNumber(), $price->getCurrencyCode());
    }

    $form['bundle'] = [
      '#type' => 'radios',
      '#options' => $options,
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Add to Cart'),
        '#submit' => [
          '::submitForm',
        ]
      ]
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $variation_id = $form_state->getValue('bundle');
    $storage = $this->entityTypeManager->getStorage('commerce_product_variation');
    /** @var \Drupal\commerce_product\Entity\ProductVariation $variation */
    $variation = $storage->load($variation_id);

    $cart = $this->cartProvider->getCart('default');
    if (!$cart) {
      $cart = $this->cartProvider->createCart('default');
      $cart->organization = $this->organization;
    }

    $this->cartManager->addEntity($cart, $variation);

    $form_state->setRedirect('commerce_checkout.form', [
      'commerce_order' => $this->cartProvider->getCart('default')->id(),
    ]);
  }
}
