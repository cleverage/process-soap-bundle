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

namespace CleverAge\ProcessSoapBundle\Task;

use CleverAge\ProcessBundle\Model\AbstractConfigurableTask;
use CleverAge\ProcessBundle\Model\ProcessState;
use CleverAge\ProcessBundle\Model\TaskInterface;
use CleverAge\ProcessSoapBundle\Soap\Client\ClientInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Fetch entities from doctrine
 *
 * @author Valentin Clavreul <vclavreul@clever-age.com>
 * @author Vincent Chalnot <vchalnot@clever-age.com>
 */
class SoapClientTask extends AbstractConfigurableTask implements TaskInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     */
    public function execute(ProcessState $state)
    {
        $options = $this->getOptions($state);

        $serviceName = trim($options['soap_client'], '@');
        if ($this->container->has($serviceName)) {
            /** @var ClientInterface $service */
            $service = $this->container->get($serviceName);
            $result = $service->call($options['method'], $state->getInput());

            // Handle empty results
            if (false === $result) {
                $state->log('Empty resultset for query', LogLevel::WARNING, $options['class_name'], $options);
                $state->setStopped(true);

                return;
            }

            $state->setOutput($result->result);

            return;
        }

        $state->log('Soap client service not found', LogLevel::EMERGENCY, $options['soap_client'], $options);
        $state->setStopped(true);

        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
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