<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * A field for entering a recaptcha text.
 */
class RecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_API_SERVER        = "http://www.google.com/recaptcha/api";
    const RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api";
    const RECAPTCHA_API_JS_SERVER     = "http://www.google.com/recaptcha/api/js/recaptcha_ajax.js";

    /**
     * The public key
     *
     * @var string
     */
    protected $siteKey;

    /**
     * Use secure url?
     *
     * @var Boolean
     */
    protected $secure;

    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Language
     *
     * @var string
     */
    protected $language;


    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->siteKey  = $container->getParameter("vihuvac_recaptcha.site_key");
        $this->secure   = $container->getParameter("vihuvac_recaptcha.secure");
        $this->enabled  = $container->getParameter("vihuvac_recaptcha.enabled");
        $this->language = $container->getParameter($container->getParameter("vihuvac_recaptcha.locale_key"));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars,
            array(
                "vihuvac_recaptcha_enabled" => $this->enabled
            )
        );

        if (!$this->enabled) {
            return;
        }

        if ($this->secure) {
            $server = self::RECAPTCHA_API_SECURE_SERVER;
        } else {
            $server = self::RECAPTCHA_API_SERVER;
        }

        $view->vars = array_replace($view->vars,
            array(
                "url_challenge" => sprintf("%s/challenge?k=%s", $server, $this->siteKey),
                "url_noscript"  => sprintf("%s/noscript?k=%s", $server, $this->siteKey),
                "site_key"      => $this->siteKey
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                "compound"      => false,
                "site_key"      => null,
                "url_challenge" => null,
                "url_noscript"  => null,
                "attr"          => array(
                    "options" => array(
                        "theme" => "clean",
                        "lang"  => $this->language
        	        )
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return "form";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "vihuvac_recaptcha";
    }

    /**
     * Gets the Javascript source URLs.
     *
     * @param string $key The script name
     *
     * @return string The javascript source URL
     */
    public function getScriptURL($key)
    {
        return isset($this->scripts[$key]) ? $this->scripts[$key] : null;
    }

    /**
     * Gets the public key.
     *
     * @return string The javascript source URL
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }
}
