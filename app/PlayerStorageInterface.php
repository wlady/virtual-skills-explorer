<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 11.11.16
 * Time: 15:41
 */

namespace App;

use Illuminate\Http\Request;

interface PlayerStorageInterface
{
    public function getSkills();
    public function getTotal();
    public function getFiltered(Request $request);
}