<?php namespace Sakadigital\Api;

use URL;
use Route;
use Config;
use Request;

class Documentation {

	protected $_routes;
	protected $_api_url;
	protected $_doc_url;
	public $current_api;

	public function __construct()
	{
		if ( ! Config::get('api.version'))
		{
			$this->_api_url = URL::to(Config::get('api.prefix')).'/';
			$this->current_api = Config::get('api.prefix');
			$this->_doc_url = URL::to(Config::get('api.prefix').'/'.Config::get('api.documentation_prefix')).'/';
		}
		else
		{
			foreach (Config::get('api.version') as $key => $value)
			{
				if ( ! Config::get('api.version.'.$key.'.enabled')) continue;

				if (Request::segment(2) === Config::get('api.version.'.$key.'.prefix')) 
				{
					$this->_api_url = URL::to(Config::get('api.prefix').'/'.Config::get('api.version.'.$key.'.prefix')).'/';
					$this->current_api = Config::get('api.prefix').'/'.Config::get('api.version.'.$key.'.prefix');
					$this->_doc_url = URL::to(Config::get('api.prefix').'/'.Config::get('api.version.'.$key.'.prefix').'/'.Config::get('api.documentation_prefix')).'/';
				}
			}
		}

		$this->_routes = $this->_getRoutes();
	}

	/**
	 * Get Data route with prefix
	 */
	protected function _getRoutes()
	{
		$routes = [];
		foreach (Route::getRoutes() as $key=>$route)
		{
			$prefix = trim($this->current_api,'/').'/';
			$uri = $route->getUri();
			$uris = explode('/', str_replace($prefix,'',$uri));

			if (strpos($uri, $prefix) === false) continue;
			if (in_array($uris[1], ['{_missing}'])) continue;

			if ($uris[0] !== Config::get('api.documentation_prefix'))
			{
				$action = $route->getAction();

				if (isset($action['controller']))
				{
					$controller = explode('@', $action['controller']);
					$parts = explode('\\', $controller[0]);
					$objectName = strtolower($this->_getObjectName(end($parts)));
					$routes[$objectName][$uris[1]]['name'] = $this->_titleCase($uris[1]);
					$routes[$objectName][$uris[1]]['object'] = $objectName;
					$routes[$objectName][$uris[1]]['object_uri'] = $this->_doc_url.$objectName;
					$routes[$objectName][$uris[1]]['uri'] = $uris[0].'/'.$uris[1];
					$routes[$objectName][$uris[1]]['api_uri'] = $this->_api_url.$uris[0].'/'.$uris[1];
					$routes[$objectName][$uris[1]]['doc_uri'] = $this->_doc_url.$uris[0].'/'.$uris[1];
					$routes[$objectName][$uris[1]]['class'] = $controller[0];
					$routes[$objectName][$uris[1]]['function'] = $controller[1];
					$routes[$objectName][$uris[1]]['method'] = $route->getMethods()[0];
					$routes[$objectName][$uris[1]]['middleware'] = $action['middleware'];
				}

				if (isset($action['namespace']))
				{
					$routes[$objectName]['namespace'] = $action['namespace'];
				}
			}	
			
		}

		return $routes;
	}

	protected function _titleCase($string)
	{
		$string = explode('-', $string);
		$string = array_map('ucwords', $string);
		return implode(' ', $string);
	}

	protected function _getObjectName($controller)
	{
		$controller = str_replace(Config::get('api.controller_prefix'), '', $controller);
		$controller = str_replace(Config::get('api.controller_suffix'), '', $controller);
		return $controller;
	}

	public function createMenu($activeController='', $activeFunction='')
	{
		$menu = array();
		foreach ($this->_routes as $key => $object)
		{
			$menu[ucwords($key)]['active'] = $key == $activeController ? true : false;
			$menu[ucwords($key)]['object_url'] = $this->_doc_url.$key;
			$menu[ucwords($key)]['functions'] = $this->_routes[$key];
			foreach ($object as $k => $val)
			{
				if ($k === 'namespace') continue;

				if ($k == $activeFunction)
					$menu[ucwords($key)]['functions'][$k]['active'] = true;
				else 
					$menu[ucwords($key)]['functions'][$k]['active'] = false;
			}
		}

		return $menu;
	}

	public function createContent($activeController='', $activeFunction)
	{
		if ( ! $this->_hasController($activeController)) $activeController = '';

		$content = array();
		
		if ( ! Config::get('api.version'))
		{
			$content['current_version'] = false;
		}
		else
		{
			$segment = explode('/', $this->current_api);
			$dataVersion = Config::get('api.version');
			if (is_array($dataVersion) AND count($dataVersion) > 0)
			{
				foreach ($dataVersion as $key => $value)
				{
					if (isset($value['prefix']) AND $value['prefix'] === end($segment))
					{
						$content['current_version'] = $key;
					}
				}
			}
			
		}

		if ($activeController === '')
		{
			$content['type'] = 'home';
			$content['body_title'] = Config::get('api.project_name').' API Documentation';
			$content['page_title'] = Config::get('api.project_name').' API Documentation';
			$content['description'] = Config::get('api.project_description');
			$content['data'] = null;
		}
		else
		{
			if ( ! $this->hasFunctionName($activeController, $activeFunction))
			{
				$activeFunction = '';
			}

			$className = $this->_routes[$activeController]['namespace'].'\\'.Config::get('api.controller_prefix').ucwords($activeController).Config::get('api.controller_suffix');
			$content['type'] = 'class';
			$content['page_title'] = 'Object '.ucwords($activeController);
			$content['body_title'] = 'Object '.ucwords($activeController);
			$content['description'] = $this->_getClassDescription($className);
			$content['data'] = $this->_routes[$activeController];

			if ($activeFunction !== '')
			{
				$className = $this->_routes[$activeController][$activeFunction]['class'];
				$functionName = $this->_routes[$activeController][$activeFunction]['function'];
				
				$content['type'] = 'function';
				$content['page_title'] = ucwords($this->_routes[$activeController][$activeFunction]['method']).' '.$activeController.'/'.$activeFunction;
				$content['body_title'] = $activeController.'/'.$activeFunction;
				$content['description'] = $this->_getFunctionDescription($className, $functionName);
				$content['data'] = $this->_routes[$activeController][$activeFunction];
				$content['data']['property'] = $this->_getFunctionProperty($className,  $functionName);
			}
		}

		return $content;
	}

	protected function _hasController($object)
	{
		return array_key_exists($object, $this->_routes);
	}

	protected function hasFunctionName($object='', $function='')
	{
		if ( ! $this->_hasController($object)) return false;
		return array_key_exists($function, $this->_routes[$object]);
	}

	protected function _getClassDescription($className)
	{
		$reflector = new \ReflectionClass($className);
		$comment = $reflector->getDocComment();
		$comment = $this->_parseComment($comment);
		return $comment['description'];
	}

	protected function _getFunctionDescription($className, $function)
	{
		return $this->_getFunctionProperty($className, $function, 'description');
	}

	protected function _getFunctionProperty($className, $function, $type='')
	{
		$reflector = new \ReflectionClass($className);
		$comment = $reflector->getMethod($function)->getDocComment();
		$comment = $this->_parseComment($comment);
		if ($type !== '' AND array_key_exists($type, $comment)) return $comment[$type];
		else
		{
			$comments['param'] = array_key_exists('param', $comment) ?  $comment['param'] : null;
			$comments['return'] = array_key_exists('return', $comment) ?  $comment['return'] : null;
			$comments['error'] = array_key_exists('error', $comment) ?  $comment['error'] : null;
			return $comments;
		}
	}

	protected function _parseComment($comment)
	{
		if (empty($comment)) return null;
		//Get the comment
		preg_match('#^/\*\*(.*)\*/#s', $comment, $comments);
		$comment = trim($comments[1]);
		 
		//Get all the lines and strip the * from the first character
		preg_match_all('#^\s*\*(.*)#m', $comment, $lines);
		foreach ($lines[1] as $key => $line)
		{
			$line = trim($line);
	 
			if(strpos($line, '@') === 0) {
				$param = substr($line, 1, strpos($line, ' ') - 1); //Get the parameter name
				$value = substr($line, strlen($param) + 2); //Get the value
				$value = explode(Config::get('api.description_sparator'), $value);
				$comments[$param][] = [$param=>str_replace(' ','',$value[0]), 'description'=>(isset($value[1]) ? $value[1] : 'No Description'), 'role'=>(isset($value[2]) ? $value[2] : '')];
			}
			else if($line !== '') $comments['description'][] = $line;
		}

		$comments['description'] = implode("<br>", $comments['description']);
		return $comments;
	}
}
