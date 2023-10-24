<?php
namespace App\Helpers;

class Utils{
    /**
	 * Generate an asset path for the application.
	 *
	 * @param  string  $path
	 * @param  bool    $secure
	 * @return string
	 */
	static public function asset($path, $secure = null)
	{
		return app('url')->asset($path, $secure);
	}

}
