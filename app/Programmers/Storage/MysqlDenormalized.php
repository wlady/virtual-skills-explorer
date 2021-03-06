<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 11.11.16
 * Time: 15:44
 */

namespace App\Programmers\Storage;

use App\Programmers\Skill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class PlayerElasticStorage
 * @package App
 */
class MysqlDenormalized extends Model implements StorageInterface
{
    protected $table = 'programmers_denormalized';

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return [
            'count' => intval($this->query()->count())
        ];
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return [
            'list' => Skill::getCounters()
        ];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getFiltered(Request $request)
    {
        $startTime = microtime(true);
        $skills = $request->get('skills');
        $from = intval($request->get('from'));
        $size = intval($request->get('size'));
        $sort = filter_var($request->get('sort'), FILTER_SANITIZE_STRING);
        $dir = filter_var($request->get('dir'), FILTER_SANITIZE_STRING);
        $where = [];
        if (is_array($skills)) {
            foreach ($skills as $skill) {
                $where[] = 'skills LIKE \'%"' . $skill . '"%\'';
            }
        }
        $sql =
            'SELECT DISTINCT SQL_CALC_FOUND_ROWS *
            FROM programmers_denormalized ' .
            (count($where) ? ' WHERE ' . implode(' OR ', $where) . ' ' : ' ') .
            (!empty($sort) ? ' ORDER BY ' . $sort . ' ' . $dir : ' ') .
            ' LIMIT ' . $from . ', ' . $size;
        $results = self::hydrateRaw($sql)->toArray();
        $numRows = self::hydrateRaw('SELECT FOUND_ROWS() total');
        array_walk($results, function (&$item) {
            $item['registered'] = date('Y-m-d', $item['registered']);
            $item['skills'] = explode(',', str_replace('"', '', $item['skills']));
            $item['location'] = [
                'lat' => $item['latitude'],
                'lon' => $item['longitude'],
            ];
            unset($item['latitude']);
            unset($item['longitude']);
        });
        return [
            'max_score' => 1,
            'total' => $numRows[0]['total'],
            'hits' => $results,
            'elapsed' => (float)number_format(microtime(true) - $startTime, 5),
        ];
    }
}