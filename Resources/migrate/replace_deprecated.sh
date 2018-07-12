#!/usr/bin/env bash

find . -type f -exec sed -i 's/clever_age_soap_process.task.soap_client/CleverAge\\ProcessSoapBundle\\Task\\SoapClientTask/g' {} \;
