<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 11.11.16
 * Time: 15:44
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class PlayerElasticStorage
 * @package App
 */
class ProgrammerMysqlStorage extends Model implements ProgrammerStorageInterface
{
    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'programmers';
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return self::query()->count();
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        /*
         * Aggregate (facets search)
         */
        $body = [
            'size' => 0,
            'aggregations' => [
                'list' => [
                    'terms' => [
                        'field' => 'skills_list',
                        'size' => 0 // get all
                    ]
                ]
            ]
        ];
        return $this->request($body, 'list');
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
        $body = [
            'size' => $size,
            'from' => $from,
        ];
        if (!empty($sort)) {
            $body['sort'] = [
                $sort => [
                    'order' => $dir,
                ]
            ];
        }
        if (count($skills)) {
            $body['query'] = [
                'bool' => [
                    'must' => [
                        'terms' => [
                            // convert to lower case as it was stored in Elastic
                            'skills' => array_map('strtolower', $skills)
                        ]
                    ]
                ]
            ];
        }
        $startTime = microtime(true);
        $results = $this->request($body, 'search');
        $results['elapsed'] = (float) number_format(microtime(true) - $startTime, 5);
        return $results;
    }

    /**
     * @param array|null $body
     * @param string $action
     * @return mixed
     */
    protected function request($body, $action = 'search')
    {
        $params = [
            'index' => $this->getIndexName(),
            'type' => $this->getTypeName(),
        ];
        if (!empty($body)) {
            $json = json_encode($body);
            if (!empty($json)) {
                $params['body'] = $json;
            }
        }
        switch ($action) {
            case 'search':
                $result = $this->getElasticSearchClient()->search($params);
                return [
                    'total' => $result['hits']['total'],
                    'max_score' => $result['hits']['max_score'],
                    'hits' => array_map(
                        function ($item) {
                            return $item['_source'];
                        },
                        $result['hits']['hits']
                    ),
                ];
                break;
            case 'list':
                $result = $this->getElasticSearchClient()->search($params);
                $list = $result['aggregations']['list']['buckets'];
                if (count($list)) {
                    // sort by 'key' field
                    usort($list, function ($a, $b) {
                        return strcasecmp($a['key'], $b['key']);
                    });
                }
                return [
                    'list' => $list,
                ];
                break;
            case 'count':
                $result = $this->getElasticSearchClient()->count($params);
                return [
                    'count' => intval($result['count'])
                ];
                break;
            default:
                throw new BadMethodCallException('Method "' . $action . '" is unknown');
        }
    }

}