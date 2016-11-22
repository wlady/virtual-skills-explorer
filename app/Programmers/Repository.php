<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 04.11.16
 * Time: 17:43
 */

namespace App\Programmers;

use App\Programmers\Storage\StorageInterface;
use Illuminate\Http\Request;

/**
 * Class Player
 * @package App
 */
class Repository
{
    /**
     * @var ProgrammerStorageInterface
     */
    protected $storage = null;

    /**
     * PlayerRepository constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
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
