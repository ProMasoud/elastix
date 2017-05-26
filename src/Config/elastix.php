<?php
return [
	/*
    |--------------------------------------------------------------------------
    | Host Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "host" the serve install Elastix.
    | This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
	'host' => env('HOST_ELASTIX'),

    /*
    |--------------------------------------------------------------------------
    | Port Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "port" the serve install Elastix.
    | This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
    'port' => env('POST_ELASTIX'),

	/*
    |--------------------------------------------------------------------------
    | User Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "user" the serve install Elastix.
    | This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */
	'user' => env('USER_ELASTIX'),

	/*
    |--------------------------------------------------------------------------
    | Password Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "password" the serve install Elastix.
    | This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */  
	'password' => env('PASSWORD_ELASTIX'),

    /*
    |--------------------------------------------------------------------------
    | Model Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the model used to drive the assignment of extensions
    | acoording to the ip of requesting machine. 
    |
    */
    'ext_model' => App\Models\ipExt::class,

    /*
    |--------------------------------------------------------------------------
    | Call waiting time Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "waiting time" of dialing the call. Set this in your ".env" 
    | file.
    |
    */
    'waitTime' => env('WAIT_TIME_ELASTIX', 45),

    /*
    |--------------------------------------------------------------------------
    | Line Elastix
    |--------------------------------------------------------------------------
    |
    | This value determines the "line" of elastix. Set this in your ".env" file.
    |
    */
    'troncal' => env('TRONCAL_ELASTIX', 'DAHDI/g11/'),

    /*
    |--------------------------------------------------------------------------
    | Value column input extension
    |--------------------------------------------------------------------------
    |
    | This value determines the name of column in the table defined in attribute  
    | "ext_model" used to defined the input extension according to the request ip.
    |
    */
   
    'value_ext_in' => 'ext_in',

     /*
    |--------------------------------------------------------------------------
    | Value column output extension
    |--------------------------------------------------------------------------
    |
    | This value determines the name of column in the table defined in attribute
    | "ext_model" used to defined the output extension according to the request ip.
    |
    */
   
    'value_ext_out' => 'ext_out'
];
 
?>