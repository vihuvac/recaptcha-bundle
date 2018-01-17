<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("vihuvac_recaptcha");

        $rootNode
            ->children()
                ->scalarNode("site_key")->isRequired()->end()
                ->scalarNode("secret_key")->isRequired()->end()
                ->booleanNode("enabled")->defaultTrue()->end()
                ->booleanNode("verify_host")->defaultFalse()->end()
                ->booleanNode("ajax")->defaultFalse()->end()
                ->scalarNode("locale_key")->defaultValue("%kernel.default_locale%")->end()
                ->booleanNode("locale_from_request")->defaultFalse()->end()
            ->end()
        ;

        $this->addHttpClientConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * {@inheritDoc}
     */
    private function addHttpClientConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("http_proxy")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("host")->defaultValue(null)->end()
                        ->scalarNode("port")->defaultValue(null)->end()
                        ->scalarNode("auth")->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
