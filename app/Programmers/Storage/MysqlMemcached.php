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
use Illuminate\Support\Facades\Cache;

/**
 * Class PlayerElasticStorage
 * @package App
 */
class MysqlMemcached extends Model implements StorageInterface
{
    protected $table = 'programmers_normalized';

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
        $skills = $request->get('skills');
        $from = intval($request->get('from'));
        $size = intval($request->get('size'));
        $sort = filter_var($request->get('sort'), FILTER_SANITIZE_STRING);
        $dir = filter_var($request->get('dir'), FILTER_SANITIZE_STRING);
        $skillsArray = Cache::get('skills', function() {
            $records = Skill::all()->toArray();
            return array_combine(
                array_column($records, 'id'),
                array_column($records, 'skill')
            );
        });
        $sql =
            'SELECT DISTINCT SQL_CALC_FOUND_ROWS
                p.name, p.city, p.ip, p.registered, p.latitude, p.longitude, p.timezone 
            FROM skills_relations sr 
            JOIN programmers_normalized p ON p.id=sr.person 
            JOIN skills s ON s.id=sr.skill ' .
            (is_array($skills) ? ' WHERE s.skill IN ("' . implode('","', $skills) . '") ' : ' ') .
            (!empty($sort) ? ' ORDER BY ' . $sort . ' ' . $dir : ' ') .
            ' LIMIT ' . $from . ', ' . $size;
        $startTime = microtime(true);
        $results = self::hydrateRaw($sql)->toArray();
        $numRows = self::hydrateRaw('SELECT FOUND_ROWS() total');
        array_walk($results, function (&$item) {
            $item['registered'] = date('Y-m-d', $item['registered']);
            $item['skills'] = explode(',', $item['skills']);
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