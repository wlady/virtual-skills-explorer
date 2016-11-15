<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\ProgrammerElasticStorage;
use App\ProgrammerRepository;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

    protected $person = null;

    public function __construct()
    {
        $this->person = new ProgrammerRepository(new ProgrammerElasticStorage());
    }
}
