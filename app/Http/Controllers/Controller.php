<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\PlayerElasticStorage;
use App\PlayerRepository;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

    protected $player = null;

    public function __construct()
    {
        $this->player = new PlayerRepository(new PlayerElasticStorage());
    }
}
