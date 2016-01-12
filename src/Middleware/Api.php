<?php namespace Sakadigital\Api\Middleware;

use Closure;
use Config;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\TokenMismatchException;

class Api extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */

	public function handle($request, Closure $next)
	{
		if ($request->segment(1) === Config::get('api.prefix'))
		{
			return $next($request);
		}
		else if ($this->isReading($request) || $this->tokensMatch($request))
		{
			return $this->addCookieToResponse($request, $next($request));
		}

		throw new TokenMismatchException;
	}
}
