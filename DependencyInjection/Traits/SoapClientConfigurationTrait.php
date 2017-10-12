<?php
/*
 *    CleverAge/ProcessBundle
 *    Copyright (C) 2017 Clever-Age
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace CleverAge\ProcessSoapBundle\DependencyInjection\Traits;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Trait SoapClientConfigurationTrait
 *
 * @package CleverAge\ProcessSoapBundle\DependencyInjection\Traits
 * @author  Madeline Veyrenc <mveyrenc@clever-age.com>
 */
trait SoapClientConfigurationTrait
{
    /**
     * @return ArrayNodeDefinition
     */
    protected function getSoapClientConfiguration()
    {
        $node = new ArrayNodeDefinition('options');

        // @formatter:off
        $node
            ->children()
                ->scalarNode('location')->end()
                ->scalarNode('uri')->end()
                ->enumNode('soap_version')->values([SOAP_1_1, SOAP_1_2])->end()
                ->scalarNode('login')->end()
                ->scalarNode('password')->end()
                ->scalarNode('proxy_host')->end()
                ->scalarNode('proxy_port')->end()
                ->scalarNode('proxy_login')->end()
                ->scalarNode('proxy_password')->end()
                ->scalarNode('local_cert')->end()
                ->scalarNode('passphrase')->end()
                ->enumNode('authentication')->values([SOAP_AUTHENTICATION_BASIC, SOAP_AUTHENTICATION_DIGEST])->end()
                ->scalarNode('compression')->end()
                ->scalarNode('encoding')->end()
                ->scalarNode('trace')->end()
                ->scalarNode('classmap')->end()
                ->scalarNode('exceptions')->end()
                ->scalarNode('connection_timeout')->end()
                ->arrayNode('typemap')
                    ->children()
                        ->scalarNode('type_name')->end()
                        ->scalarNode('type_ns')->end()
                        ->scalarNode('from_xml')->end()
                        ->scalarNode('to_xml')->end()
                    ->end()
                ->end()
                ->enumNode('cache_wsdl')->values([WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY, WSDL_CACHE_BOTH])->end()
                ->scalarNode('user_agent')->end()
                ->scalarNode('stream_context')->end()
                ->enumNode('features')->values([SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS])->end()
                ->booleanNode('keep_alive')->end()
                ->enumNode('ssl_method')->values([SOAP_SSL_METHOD_TLS, SOAP_SSL_METHOD_SSLv2, SOAP_SSL_METHOD_SSLv3, SOAP_SSL_METHOD_SSLv23])->end()
            ->end();
        // @formatter:on

        return $node;
    }
}