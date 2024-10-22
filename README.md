CleverAge/ProcessSoapBundle
===========================

This bundle is deprecated, use [CleverAge/SoapProcessBundle](https://github.com/cleverage/soap-process-bundle) instead


This bundle extends the CleverAge/ProcessBundle to make Soap calls.

## Configuration reference

### Defining your Soap client

You can use the default client :

```yml
clever_age_soap_process:
    clients:
        <client_name>:
            wsdl: ~ # See SoapClient documentation : http://php.net/manual/en/soapclient.soapclient.php
            options: ~ # See SoapClient documentation : http://php.net/manual/en/soapclient.soapclient.php
            service_alias: ~ # An alias to your client service. The default name of the generated service is 'clever_age_process_soap.soap_client.<client_name>'
            class: ~ # The class implementing the service. By default '%clever_age_process_soap.soap_client.class%' 
```        

Or you can declare your own Soap client service by implementing the `CleverAge\ProcessSoapBundle\Soap\Client\ClientInterface`.

### Existing tasks

#### SoapClientTask

Call a method on your Soap client :

```yml
<task_code>:
    service: '@clever_age_soap_process.task.soap_client'
    options:
        # Required options
        soap_client: <service name> # your Soap client service
        method: <method name> # the method to call
    outputs: [<task_code>] # Array of tasks to pass the output to
```
