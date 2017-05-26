# Control Panel Elastix for Laravel 5.*
Call control panel for elastix call center for laravel 5. *

## Installation

Require the `selkis/elastix` package in your composer.json and update your dependencies.

    $ composer require selkis/elastix

Run Artisan command for publish config and views:

    $ php artisan vendor:publish

## Configuration
   In the `config/elastix.php` file you will find the necessary configuration for the connection with the elastix server, the following table shows the configurable values:
   
   |Key      |Description                                                            | Default Value    
   --- | --- | --- 
   | host    | This value determines the "host" the serve install Elastix.           |  -             
   | port    | This value determines the "port" the serve install Elastix.           |  -            
   | user    | This value determines the "user" the serve install Elastix.           |  -         
   | password| This value determines the "password" the serve install Elastix.       |  -         
   | ext_model| This value determines the model used to drive the assignment of extensions acoording to the ip of requesting machine.       |  App\Models\ipExt::class
   | waitTime| This value determines the "waiting time" of dialing the call.       |  45
   | troncal|This value determines the "line" of elastix.      |  DAHDI/g11/
   | value_ext_in|This value determines the name of column in the table defined in attribute "ext_model" used to defined the input extension according to the request ip.     |  ext_in
   | value_ext_out|TThis value determines the name of column in the table defined in attribute "ext_model" used to defined the output extension according to the request ip.     |  ext_out

## Usage

 To get the panel view you must control invoking the `/ bar` url

## License

Released under the MIT License, see LICENSE.
