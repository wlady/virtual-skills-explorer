## ElasticSearch vs MySQL vs MongoDB

Сравнение времени выполнения запросов к разным типам баз данных.

Общее количество записей - 100000.

### Структура (mapping) ElasticSearch:

```sh
GET /skills/programmers/_mapping?pretty

{
  "skills": {
    "mappings": {
      "programmers": {
        "properties": {
          "city": {
            "type": "string"
          },
          "ip": {
            "type": "string",
            "index": "not_analyzed"
          },
          "location": {
            "type": "geo_point"
          },
          "name": {
            "type": "string"
          },
          "registered": {
            "type": "date",
            "format": "strict_date_optional_time||epoch_millis"
          },
          "skills": {
            "type": "string",
            "copy_to": [
              "skills_list"
            ]
          },
          "skills_list": {
            "type": "string",
            "index": "not_analyzed"
          },
          "timezone": {
            "type": "string"
          }
        }
      }
    }
  }
}
```

Структура MySQL таблицы для денормализованных данных:

```sh
CREATE TABLE `programmers_denormalized` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
 `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 `registered` bigint(20) NOT NULL,
 `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
 `latitude` double NOT NULL,
 `longitude` double NOT NULL,
 `timezone` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
 `skills` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`),
 KEY `registered` (`registered`) USING BTREE,
 FULLTEXT KEY `skills` (`skills`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

Структура MySQL таблиц для нормализованных данных:

```sh
CREATE TABLE `programmers_normalized` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
 `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 `registered` bigint(20) NOT NULL,
 `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
 `latitude` double NOT NULL,
 `longitude` double NOT NULL,
 `timezone` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`),
 KEY `registered` (`registered`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `skills` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `skill` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`),
 KEY `skill` (`skill`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `skills_relations` (
 `person` int(11) NOT NULL,
 `skill` int(11) NOT NULL,
 KEY `person_constraints1` (`person`),
 KEY `skill_constraints1` (`skill`),
 CONSTRAINT `person_constraints1` FOREIGN KEY (`person`) REFERENCES `programmers_normalized` (`id`) ON DELETE CASCADE,
 CONSTRAINT `skill_constraints1` FOREIGN KEY (`skill`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```

### Пример тестовых данных

ElasticSearch

```sh
GET /skills/programmers/_search?pretty -d
{
  "size": 3
}
```

Двнные, отсортированные по релевантности

```sh
{
  "took": 3,
  "timed_out": false,
  "_shards": {
    "total": 5,
    "successful": 5,
    "failed": 0
  },
  "hits": {
    "total": 99999,
    "max_score": 1,
    "hits": [
      {
        "_index": "skills",
        "_type": "programmers",
        "_id": "AVhnegLRWNTWw7qAKLEL",
        "_score": 1,
        "_source": {
          "name": "Vince Kreiger",
          "city": "Tremembe",
          "location": {
            "lat": -22.95833,
            "lon": -45.54944
          },
          "timezone": "America/Sao_Paulo",
          "registered": "2016-10-08",
          "ip": "83.215.48.253",
          "skills": [
            "REST",
            "JQuery",
            "Linux",
            "ReactJS",
            "Java",
            "SOAP"
          ]
        }
      },
      {
        "_index": "skills",
        "_type": "programmers",
        "_id": "AVhnegLRWNTWw7qAKLEO",
        "_score": 1,
        "_source": {
          "name": "Johnathan Schuster",
          "city": "Tipasa",
          "location": {
            "lat": 36.58972,
            "lon": 2.4475
          },
          "timezone": "Africa/Algiers",
          "registered": "2016-10-05",
          "ip": "147.165.190.2",
          "skills": [
            "SOAP",
            "JQuery",
            "Perl",
            "REST",
            "Linux"
          ]
        }
      },
      {
        "_index": "skills",
        "_type": "programmers",
        "_id": "AVhnegLRWNTWw7qAKLEQ",
        "_score": 1,
        "_source": {
          "name": "Loyal Bins",
          "city": "Boldumsaz",
          "location": {
            "lat": 42.12824,
            "lon": 59.67101
          },
          "timezone": "Asia/Ashgabat",
          "registered": "2016-07-05",
          "ip": "233.251.138.80",
          "skills": [
            "Java",
            "NGinx",
            "Perl",
            "SOAP",
            "NodeJS",
            "C"
          ]
        }
      }
    ]
  }
}
```

MySQL (денормализованные данные)

```sh
SELECT * FROM `programmers_denormalized` LIMIT 3
```

![Query Results](programmers_table.png)

MySQL (нормализованные данные)

```sh
SELECT DISTINCT p.*, (SELECT GROUP_CONCAT(s1.skill) FROM skills_relations sr1 JOIN skills s1 ON sr1.skill=s1.id WHERE sr1.person=p.id) skills FROM skills_relations sr JOIN `programmers_normalized` p ON p.id=sr.person JOIN skills s ON s.id=sr.skill LIMIT 3
```

![Query Results](programmers_table.png)



### Скриншоты

![Hits](hits.png)

![Hits](skills.png)

![Hits](places.png)

