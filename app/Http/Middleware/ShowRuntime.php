<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class ShowRuntime {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$response = $next($request);

        if (config('SHOW_RUNTIME') && is_array($response)) {
            $response['runtime'] = 1;
        }

        return $response;
	}

}
