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
use CleverAge\ProcessSoapBundle\Soap\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SoapClientTask
 *
 * @package CleverAge\ProcessSoapBundle\Task
 * @author  Madeline Veyrenc <mveyrenc@clever-age.com>
 */
class SoapClientTask extends AbstractConfigurableTask
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
        try {
            $serviceName = trim($options['soap_client'], '@');
            if ($this->container->has($serviceName)) {
                /** @var ClientInterface $service */
                $service = $this->container->get($serviceName);
                $input = $state->getInput() ?: [];
                $result = $service->call($options['method'], $input);

                // Handle empty results
                if (false === $result) {

                    $logContext = $state->getLogContext();
                    $logContext['options'] = $options;
                    $logContext['last_request'] = $service->getLastRequest();
                    $logContext['last_request_headers'] = $service->getLastRequestHeaders();
                    $logContext['last_response'] = $service->getLastResponse();
                    $logContext['last_response_headers'] = $service->getLastResponseHeaders();
                    $this->logger->error('Empty resultset for query', $logContext);

                    if ($options[self::ERROR_STRATEGY] === self::STRATEGY_SKIP) {
                        $state->setSkipped(true);
                    } elseif ($options[self::ERROR_STRATEGY] === self::STRATEGY_STOP) {
                        $state->setStopped(true);
                    }

                    return;
                }

                $state->setOutput($result);

                return;
            }

            $logContext = $state->getLogContext();
            $logContext['options'] = $options;
            $this->logger->error('Soap client service not found', $logContext);

            if ($options[self::ERROR_STRATEGY] === self::STRATEGY_SKIP) {
                $state->setSkipped(true);
            } elseif ($options[self::ERROR_STRATEGY] === self::STRATEGY_STOP) {
                $state->setStopped(true);
            }
        } catch (\Exception $e) {
            $state->setError($state->getInput());
            $logContext = $state->getLogContext();
            $logContext['options'] = $options;
            $this->logger->error($e->getMessage(), $logContext);
            if ($options[self::ERROR_STRATEGY] === self::STRATEGY_SKIP) {
                $state->setSkipped(true);
            } elseif ($options[self::ERROR_STRATEGY] === self::STRATEGY_STOP) {
                $state->stop($e);
            }
        }
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
