<?php namespace App\Http\Controllers;

use App\Programmers\Repository;
use App\Programmers\Storage\Elastic;
use App\Programmers\Storage\MysqlMemcached;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

    protected $programmers = null;

    public function __construct()
    {
        $this->programmers = new Repository(new MysqlMemcached());
    }
}
