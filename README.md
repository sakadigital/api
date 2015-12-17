# Rest Api Structure for Laravel

Build Rest Api with laravel Php Framework. This package also  genrate automaticly documentation for your API based Api controller docblock and test api function.

## Features
* Versioning API
* Enabled/Disabled API or Some version
* Enabled/Disabed Documentation
* Bypass csrf token laravel for api prefix
* Auto-generation of API documentation based on docblock
* Simulate API call & view API return data

## Installation

Require this package with composer:
```
composer require sakadigital/api
```
After updating composer, add the ApiServiceProvider to the providers array in config/app.php
```
Sakadigital\Api\ApiServiceProvider::class,
```
Copy the package resource to your application with the publish command:
```
php artisan vendor:publish
```
by run command vendor:publish we will copy folder and file as following:
* Config file `api.php` to `/config` folder
* Asset folder `api` to `/public` folder, this folder contain css,js, etc.
* Api structur `Api` folder to `/App`, this folder contain `Controllers` folder as a place for your api controller, and `routes.php` for api route.

And you are ready to build your API.

## Using The Package
If you finish installation process above, by default your api is active and now you can checkout your api by GET http://yourdomain.com/api, and also documentation by GET http://yourdomain.com/api/doc.
### creating controller
Your controller is your documentation, so we sugest  you to follow some our standard to make controller file for documentation page.
##### Class Name
* use prefix and suffix for contoller `Api`{{Object}}`Controller`, for example if you will use Auth you should use file and class name `ApiAuthContoller`,
* Add description to your calss.
```
/**
 * This is description from file ApiAuthController
*/
class ApiAuthController extends Controller {
    //...
}
```
##### Function
each function must have a description, param, return, and error. example:
```
    /**
	 * Login user from api
	 * This process will generate new token of loginned user
	 *
	 * @param email | registered user email with role | required email
	 * @param password | user password that created with role | required
	 * @return user_id | id of user
	 * @return user_token | user token for access data via api
	 * @return providers | list of connected providers
	 * @error Email or Password not match | no email and password registered in database
	 */
	public function login(Request $request)
	{
	    //...
	}
```
in `@param` contain information about paramter, description and validation rule that sparated by `|`, and in `@return` also contain return type or name and description of function return. `@error` is special error that function made, you can give the information to the error.
* Place your all controllers inside Api controllers folder.
### using versioning
if you will make your api with many version create folder inside you api namespace :
```
App
|_ Api
    |_ v1 //your first version here
        |_ Controllers
        |_ route.php
    |_ v2 //your second version
       |...
```
place your `routes.php` in each version and create `Controllers` folder in each version and place your contorller and register your api version bellow as following:
```
'version'=>[
    'v1'=> ['enabled'=>true, 'namespace'=>'App\Api\v1', 'prefix'=>'v1'],
    'v2'=> ['enabled'=>true, 'namespace'=>'App\Api\v2', 'prefix'=>'v2'],
],
```
your version prefix will placed in second url prefix
http://yourdomain.com/{api-prefix}/{version-prefix}/
### Routing
this package will automaticly append prefix to routes as api config file
```
Route::get('test', function(){
    return response()->toJson('Hallo word!');
});
```
test by GET http://yourdomian.com/api/`test',
if you use version v1, GET http://yourdoumain/api/v1/test
