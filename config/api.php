<?php

return [

    /*
    |--------------------------------------------------------------------------
    | api activate
    |--------------------------------------------------------------------------
    | you can active or inactive your api with change the value to true or false
    */
    
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | api namespace
    |--------------------------------------------------------------------------
    | namespace for your api location, we recomend use App/Api
    */
    
    'namespace' => 'App\Api',

    /*
    |--------------------------------------------------------------------------
    | api prefix
    |--------------------------------------------------------------------------
    | prefix url for your api, example http://yourdomain.com/{prefix}/
    */

    'prefix' => 'api',

    /*
    |--------------------------------------------------------------------------
    | controller prefix and suffix class and file name
    |--------------------------------------------------------------------------
    | set your controller prefix and suffix
    */

    'controller_prefix' => 'Api',
    'controller_suffix' => 'Controller',

    /*
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    | middleware group
    |--------------------------------------------------------------------------
    | if you change the name of api group in your HTTP Kernel you should update
    | this too
    */

    'middleware' => 'api',
=======
    | skip laravel csrf token
    |--------------------------------------------------------------------------
    | by default we bypass csrf token for segment api prefix as you set above
    */

    'bypass_scrf_token' => true,
>>>>>>> 8e1db83822a787b91ffbbd7d1240c2be1b8e8fd7

    /*
    |--------------------------------------------------------------------------
    | Versioning of api
    |--------------------------------------------------------------------------
    | set version to false if your api not use versioning
    | if your api want to use versioning follow this step:
    | create folder inside you api namespace (by default namespace is App\Api)
    |
    | App
    | |_ Api
    |    |_ v1 //your first version here
    |       |_ Controllers
    |       |_ route.php
    |    |_ v2 //your second version
    |       |...
    |
    | place your route in each version
    | and create controllers folder in each version and place your contorller
    | and register your api version bellow as following:
    |
    | 'version'=>[
    |   'v1'=> ['enabled'=>true, 'namespace'=>'App\Api\v1', 'prefix'=>'v1'],
    |   'v2'=> ['enabled'=>true, 'namespace'=>'App\Api\v2', 'prefix'=>'v2'],
    | ]
    |
    | your version prefix will placed in second url prefix
    | http://yourdomain.com/{api-prefix}/{version-prefix}/
    |
    */

    'version' => false,
    /*
    |--------------------------------------------------------------------------
    | enabled or disabled documentation
    |--------------------------------------------------------------------------
    | if you want to use api documentation for your api set to true
    | and give name and description to your api project
    */

    'documentation' => true,
    'project_name' => 'Sakadigital Project',
    'project_description' => 'this is project sakadigital',

    /*
    |--------------------------------------------------------------------------
    | documentation prefix
    |--------------------------------------------------------------------------
    | prefix url for your api documentation example
    | http://yourdomain.com/{api-prefix or api-prefix/version-prefix}/{doc-prefix}/
    */

    'documentation_prefix' => 'doc',

    /*
    |--------------------------------------------------------------------------
    | description sparator
    |--------------------------------------------------------------------------
    | this documentation is form your controller file
    | we use php reflaction class to get your api method description, param, return, and error description
    | more information about reflaction class http://php.net/manual/en/class.reflectionclass.php
    | so we need sparator to sparate between parameter name and description and validation rule
    |
    | example method description :
    | /**
    |  * Get list data article that sugested dependency to user_id
    |  *
    |  * @param user_token |-| required
    |  * @param user_id |-| required
    |  * @param page | latest or oldest default latest
    |  * @param start_id | article_id of start article list default 0
    |  * @return json
    |  *
    |  * @error Data Topic Empty | No data topic yet on the server
    |  *_/
    |  public function getList() ...
    */

    'description_sparator' => '|'

];