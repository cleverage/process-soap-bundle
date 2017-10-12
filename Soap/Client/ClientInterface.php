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

/**
 * Interface ClientInterface
 *
 * @package CleverAge\ProcessSoapBundle\Soap\Client
 */
interface ClientInterface
{
    /**
     * Return the URI of the WSDL file or NULL if working in non-WSDL mode.
     *
     * @return string
     */
    public function getWsdl();

    /**
     * Set the URI of the WSDL file or NULL if working in non-WSDL mode.
     *
     * @param string|null $wsdl
     * @return void
     */
    public function setWsdl($wsdl);

    /**
     * Return the Soap client options
     * @see http://php.net/manual/en/soapclient.soapclient.php
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set the Soap client options
     * @see http://php.net/manual/en/soapclient.soapclient.php
     *
     * @param array $options
     * @return void
     */
    public function setOptions($options);
}