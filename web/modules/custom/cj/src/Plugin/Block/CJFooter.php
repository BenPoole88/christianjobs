<?php

namespace Drupal\cj\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block that displays page contemt.
 *
 * @Block(
 *   id = "cj_footer",
 *   admin_label = @Translation("Christian Jobs Footer"),
 * )
 */
class CJFooter extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Creates a CJHeaderBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *  The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('config.factory'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
  }

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {
    $build = [
      '#markup' => '<div><div class="pull-center">
      <div class="footer-logo"><img src="/themes/custom/cj_material/logo.svg" width="40px" height="40px"></div>
      <div class="footer-verse"><p>Whatever you do, work at it with all your heart, as working for the Lord.</p><span class="bible-reference">Colossians 3:23</span></div>
      </div></div>
      <div>
      <div class="footer-contact footer-item pull-left">
        <a href="http://www.instagram.com/christianjobs.co.uk" class="services-icons icon-primary" data-icon="instagram"></a> | <a href="http://www.linkedin.com/company/christianjobs-co-uk" class="services-icons icon-primary" data-icon="linkedin"></a> | <a href="http://www.twitter.com/ukchristianjobs" class="services-icons icon-primary" data-icon="twitter"></a> | <a href="http://www.facebook.com/ukchristianjobs" class="services-icons icon-primary" data-icon="facebook"></a> | <a href="mailto:info@christianjobs.co.uk">info@christianjobs.co.uk</a> | <a href="tel:01619463550">0161 946 3550</a></div>
      <div class="footer-links footer-item pull-right">'.(\Drupal::currentUser()->isAuthenticated() ? '<a href="/user/logout"> Logout</a> | ' : '').'<a href="/pricing">Pricing</a> | <a href="/legal">Terms of Use</a> | &copy; Christian Jobs Ltd 2019-2020</div>
    </div>',
      '#cache' => [
        'contexts' => ['user.roles:authenticated'],
        'tags' => [],
        'max-age' => 60*60*24,
      ],
    ];

    return $build;
  }

}
