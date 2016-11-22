<?php namespace App\Http\Controllers;

use App\Programmers\Repository;
use App\Programmers\Storage\Elastic;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
//use App\ProgrammerElasticStorage;
//use App\ProgrammerMysqlStorage;
//use App\ProgrammerMysqlDenormalizedStorage;
//use App\ProgrammerRepository;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

    protected $programmers = null;

    public function __construct()
    {
        $this->programmers = new Repository(new Elastic());
    }
}
