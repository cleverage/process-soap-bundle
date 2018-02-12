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
    protected $options = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \SoapClient
     */
    protected $soapClient;

    /**
     * @var string
     */
    protected $lastRequest;

    /**
     * @var string
     */
    protected $lastRequestHeaders;

    /**
     * @var string
     */
    protected $lastResponse;

    /**
     * @var string
     */
    protected $lastResponseHeaders;

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
        return array_merge($this->options, ['trace' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return string
     */
    public function getLastRequestHeaders()
    {
        return $this->lastRequestHeaders;
    }

    /**
     * @return string
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @return string
     */
    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
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

        return $this->doSoapCall($method, $input);
    }

    /**
     * @param string $method
     * @param array  $input
     * @return bool|mixed
     */
    protected function doSoapCall($method, array $input = [])
    {
        $result = false;

        try {
            $result = $this->soapClient->__soapCall($method, [$input]);
        } catch (\SoapFault $e) {
            $this->getLastRequestTrace();
            $this->logger->alert(
                sprintf("Soap call '%s' on '%s' failed : %s", $method, $this->wsdl, $e->getMessage()),
                $this->getLastRequestTraceArray()
            );

            return;
        }

        $this->getLastRequestTrace();

        if (array_key_exists('trace', $this->getOptions()) && $this->getOptions()['trace']) {
            $this->logger->notice(
                sprintf("Trace of soap call '%s' on '%s'", $method, $this->wsdl),
                $this->getLastRequestTraceArray()
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

    /**
     * @return array
     */
    protected function getLastRequestTrace()
    {
        $this->lastRequest = $this->soapClient->__getLastRequest();
        $this->lastRequestHeaders = $this->soapClient->__getLastRequestHeaders();
        $this->lastResponse = $this->soapClient->__getLastResponse();
        $this->lastResponseHeaders = $this->soapClient->__getLastResponseHeaders();
    }

    /**
     * @return array
     */
    protected function getLastRequestTraceArray()
    {
        return [
            'LastRequest' => $this->lastRequest,
            'LastRequestHeaders' => $this->lastRequestHeaders,
            'LastResponse' => $this->lastResponse,
            'LastResponseHeaders' => $this->lastResponseHeaders,
        ];
    }

}