<?php

namespace App\Programmers;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model {

    protected $table = 'skills';

    public static function getCounters()
    {
        $sql =
            'SELECT 
                s.skill `key` ,COUNT(sr.skill) `doc_count` 
            FROM skills_relations sr 
            JOIN skills s ON s.id=sr.skill 
            GROUP BY s.id  
            ORDER BY s.skill';
        return self::hydrateRaw($sql)->toArray();
    }
}
