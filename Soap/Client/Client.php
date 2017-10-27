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

namespace CleverAge\ProcessSoapBundle\Soap\Client;

use Psr\Log\LoggerInterface;

/**
 * Class Client
 *
 * @package CleverAge\ProcessSoapBundle\Soap\Client
 */
class Client implements ClientInterface
{
    /**
     * @var string
     */
    protected $wsdl;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \SoapClient
     */
    protected $soapClient;

    /**
     * Client constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }

    /**
     * {@inheritdoc}
     */
    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function call($method, array $input = [])
    {
        $this->initializeSoapClient();

        $callMethod = sprintf('soapCall%s', ucfirst($method));
        if (method_exists($this, $callMethod)) {
            return $this->$callMethod($input);
        }

        $this->logger->notice(
            sprintf("Soap call '%s' on '%s'", $method, $this->wsdl)
        );

        $result = null;

        try {
            $result = $this->soapClient->__soapCall($method, [$input]);
        } catch (\SoapFault $e) {
            $this->logger->alert(sprintf("Soap call '%s' on '%s' failed", $method, $this->wsdl));
        }
        if (array_key_exists('trace', $this->options) && $this->options['trace']) {
            $trace = [
                'LastRequest' => $this->soapClient->__getLastRequest(),
                'LastRequestHeaders' => $this->soapClient->__getLastRequestHeaders(),
                'LastResponse' => $this->soapClient->__getLastResponse(),
                'LastResponseHeaders' => $this->soapClient->__getLastResponseHeaders(),
            ];
            $this->logger->notice(
                sprintf("Trace of soap call '%s' on '%s'", $method, $this->wsdl),
                $trace
            );
        }

        return $result;
    }

    /**
     * Initialize \SoapClient object
     *
     * @return void
     */
    protected function initializeSoapClient()
    {
        if (!$this->soapClient) {
            $this->soapClient = new \SoapClient($this->getWsdl(), $this->getOptions());
        }
    }

}