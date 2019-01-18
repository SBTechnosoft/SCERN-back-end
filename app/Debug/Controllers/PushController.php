<?php
namespace ValuePad\Debug\Controllers;

use Illuminate\Http\Request;
use ValuePad\Debug\Support\BaseController;

/**
 * The controller is used to catch notifications and log 'em into the file for later verification in tests and etc.
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PushController extends BaseController
{
	/**
	 * @return string
	 */
	public function index()
	{
		$file = storage_path('debug/push.json');

		if (!file_exists($file)){
			return [];
		}

		return json_decode(file_get_contents($file), true);
	}

	public function destroy()
	{
		$file = storage_path('debug/push.json');

		if (file_exists($file)){
			unlink($file);
		}
	}

	public function store(Request $request)
	{
		$data = [];

		$dir = storage_path('debug');
		$file = $dir.'/push.json';

		if (!file_exists($dir)){
			mkdir($dir, 0755, true);
		}

		if (file_exists($file)){
			$data = json_decode(file_get_contents($file, true));
		}

		$data[] = json_decode($request->getContent(), true);

		file_put_contents($file, json_encode($data));
	}
}