<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 04.11.16
 * Time: 17:43
 */

namespace App;

use Illuminate\Http\Request;

/**
 * Class Player
 * @package App
 */
class PlayerRepository
{
    /**
     * @var PlayerStorageInterface
     */
    protected $storage = null;

    /**
     * PlayerRepository constructor.
     * @param PlayerStorageInterface $storage
     */
    public function __construct(PlayerStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return $this->storage->getSkills();
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->storage->getTotal();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getFiltered(Request $request)
    {
        return $this->storage->getFiltered($request);
    }
}
