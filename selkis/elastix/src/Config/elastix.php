<?php
return [
	/*
    |--------------------------------------------------------------------------
    | Host Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
	'host' => env('HOST_ELASTIX', '192.168.10.118'),

    /*
    |--------------------------------------------------------------------------
    | Host Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
    'port' => env('POST_ELASTIX', '5038'),

	/*
    |--------------------------------------------------------------------------
    | User Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
	'user' => env('USER_ELASTIX', 'gareapi'),

    /*
    |--------------------------------------------------------------------------
    | User Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
    'waitTime' => env('WAIT_TIME_ELASTIX', 45),

    /*
    |--------------------------------------------------------------------------
    | User Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
    'troncal' => env('TRONCAL_ELASTIX', 'DAHDI/g11/'),

	/*
    |--------------------------------------------------------------------------
    | password Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
	'password' => env('PASSWORD_ELASTIX', 'gare2014.'),

    /*
    |--------------------------------------------------------------------------
    | Passaword Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
   
    'ext_model' => App\Models\ipExt::class,

    /*
    |--------------------------------------------------------------------------
    | Passaword Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
   
    'value_ext_in' => 'ext_in',

    /*
    |--------------------------------------------------------------------------
    | Passaword Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
   
    'value_ext_out' => 'ext_out'
];
 
?>