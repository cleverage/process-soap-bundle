<?php
/*
 *    CleverAge/ProcessSoapBundle
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

namespace CleverAge\ProcessSoapBundle\Transformer;

use CleverAge\ProcessBundle\Transformer\ConfigurableTransformerInterface;
use CleverAge\ProcessSoapBundle\Soap\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SoapClientTransformer
 *
 * @package CleverAge\ProcessSoapBundle\Transformer
 * @author  Madeline Veyrenc <mveyrenc@clever-age.com>
 */
class SoapClientTransformer implements ConfigurableTransformerInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     */
    public function transform($value, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        $result = null;
        $serviceName = trim($options['soap_client'], '@');
        if ($this->container->has($serviceName)) {
            /** @var ClientInterface $service */
            $service = $this->container->get($serviceName);

            return $service->call($options['method'], $value);
        }

        throw new \Exception('Soap client service not found');
    }

    /**
     * Returns the unique code to identify the transformer
     *
     * @return string
     */
    public function getCode()
    {
        return 'soap_client';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'soap_client',
                'method',
            ]
        );
        $resolver->setAllowedTypes('soap_client', ['string']);
        $resolver->setAllowedTypes('method', ['string']);
    }
}
