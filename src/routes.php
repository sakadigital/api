<?php

use Sakadigital\ApiDocumentation\Documentation;

$api_segment = Config::get('apidoc.api_segment');
if( ! is_array($api_segment))
{
	$api_segment = [$api_segment];
}

foreach ($api_segment as $segment)
{
	Route::get($segment.'/'.Config::get('apidoc.doc_segment').'/{controller?}/{function?}',function($controller='', $function=''){
		$doc = new Documentation;
		$data['menu'] = $doc->createMenu($controller, $function);
		$data['content'] = $doc->createContent($controller, $function);
		$data['baseUrl'] = URL::to($doc->curret_api);
		//dd($data);
		return View('doc::view')->with($data);
	});
}
