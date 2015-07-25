API DOC
=======

The api url looks like `api/index.php?r={controller_name}/{action_name}/{server_name}/{database_name}/{collection_name}`

## MongoDB Server

### Get server list

- Request Method:
GET

- Request Endpoint:
http://{server-domain}/api/index.php?r=server/index

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |

- Request Example

```
http://localhost/phpMongoAdmin/api/index.php?r=server/index
```

- Response Example

```
[
    {
        "name": "server1",
        "dsn": "localhost:27017"
    }
]
```

### Update server config

- Request Method:
PUT

- Request Endpoint:
http://{server-domain}/api/index.php?r=server/update

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
| config | array | yes |  |  |

- Request Parameters Example
```
{
    "config": [{
        "name": "server0",
        "dsn": "localhost:27017"
    }, {
        "name": "server1",
        "dsn": "localhost:27017"
    }]
}
```
- Request Example

```
http://localhost/phpMongoAdmin/api/index.php?r=server/update
```

- Response Example

```
{
    "ok": "1"
}

```

## MongoDB Database

### Get database list

- Request Method:
GET

- Request Endpoint:
http://{server-domain}/api/index.php?r=database/index/{server_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=database/index/server1
```

- Response Example
```
[
    "local",
    "harry",
    "test"
]
```

### Execute a mongo shell

- Request Method:
POST

- Request Endpoint:
http://{server-domain}/api/index.php?r=database/execute/{server_name}/{database_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
|code|string|yes|"db.table1.find()"||

- Request Parameters Example
```
{
    "code": "db.test.find().limit(1);db.test.find().skip(1).limit(1);"
}
```

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=database/execute/server1/harry
```

- Response Example
```
[
    {
        "retval": [
            {
                "_id": "ObjectId(\"554edaf79d02041d08db34ee\")",
                "name": "test2",
                "email": "test2@126.com",
                "createAt": "ISODate(\"2015-05-10T12:13:43.181Z\")"
            }],
        "ok": 1
    }, {
        "retval": [{
                "_id": "ObjectId(\"554edaf79d02041d08db34ef\")",
                "name": "test3",
                "email": "test3@126.com",
                "createAt": "ISODate(\"2015-05-10T12:13:43.182Z\")"
            }],
        "ok": 1
    }
]
```

## MongoDB Collection

### Get collection list

- Request Method:
GET

- Request Endpoint:
http://{server-domain}/api/index.php?r=collection/index/{server_name}/{database_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=collection/index/server1/harry
```

- Response Example
```
[
    "test",
    "table1"
]
```

### Create a collection

- Request Method:
POST

- Request Endpoint:
http://{server-domain}/api/index.php?r=collection/create/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=collection/create/server1/harry/test/table2
```

- Response Example
```
{
    "w": 1,
    "wtimeout": 10000
}
```

### Drop a collection

- Request Method:
DELETE

- Request Endpoint:
http://{server-domain}/api/index.php?r=collection/delete/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=collection/delete/server1/harry/test
```

- Response Example
```
{
    "ns": "harry.test",
    "nIndexesWas": 1,
    "ok": 1
}
```

## MongoDB Indexes

### Get all indexes

- Request Method:
GET

- Request Endpoint:
http://{server-domain}/api/index.php?r=index/index/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=index/index/server1/harry/test
```

- Response Example
```
[
    {
        "v": 1,
        "key": {
            "_id": 1
        },
        "name": "_id_",
        "ns": "harry.test"
    }
]
```

### Create a index

- Request Method:
POST

- Request Endpoint:
http://{server-domain}/api/index.php?r=index/create/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
|keys|string or array|yes|"key1" or {"key1":1, "key2":-1}||
|options|array|no|{"background":true}|Default is {"background":true}|

- Request Parameters Example:
```
{
    "keys": "a",
    "options": {
        "background": true
    }
}
```

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=index/create/server1/harry/test
```

- Response Example
```
{
    "createdCollectionAutomatically": false,
    "numIndexesBefore": 1,
    "numIndexesAfter": 2,
    "ok": 1
}
```

### Delete a index

- Request Method:
DELETE

- Request Endpoint:
http://{server-domain}/api/index.php?r=index/delete/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
|keys|string or array|yes|"key1" or {"key1":1, "key2":-1}||

- Request Parameters Example:
```
{
    "keys": "a"
}
```

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=index/delete/server1/harry/test
```

- Response Example
```
{
    "nIndexesWas": 2,
    "ok": 1
}
```

## MongoDB Document

### Get documents

- Request Method:
GET

- Request Endpoint:
http://{server-domain}/api/index.php?r=doc/index/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
|page|int|no|1|Page number, default is 1|
|per-page|int|no|20|Page size, default is 20|

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=doc/index/server1/harry/test&page=1&per-page=5
```

- Response Example
```
{
    "total": 1,
    "page": 1,
    "per-page": 5,
    "items": [{
            "_id": "ObjectId(\"55ab8ba3cbd9d14d1bcac753\")",
            "a": 1
        }]
}
```

### Create or update a document

- Request Method:
POST

- Request Endpoint:
http://{server-domain}/api/index.php?r=doc/create/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
|any|any|yes||A document, if there is "_id", it will be updating a document|

- Request Parameters Example:
```
{
    "_id" : "ObjectId(\"559791e3377d472649641bd1\")",
    "i" : 0,
    "username" : "user0",
    "age" : 84,
    "created" : "ISODate(\"2015-07-04T07:57:23.908Z\")"
}
```

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=doc/create/server1/harry/test
```

- Response Example
```
{
    "ok": 1,
    "nModified": 0,
    "n": 1,
    "err": null,
    "errmsg": null,
    "upserted": "ObjectId(\"559791e3377d472649641bd1\")",
    "updatedExisting": false
}
```

### Delete a document

- Request Method:
DELETE

- Request Endpoint:
http://{server-domain}/api/index.php?r=doc/delete/{server_name}/{database_name}/{collection_name}

- Request Parameters:

| Name | Type | Required | Example | description |
| ---- | ---- | -------- | ------- | ----------- |
|id|string|yes|"559791e3377d472649641bd1" or "ObjectId(\"559791e3377d472649641bd1\")"||

- Request Parameters Example:
```
{
    "id": "ObjectId(\"55ab8ba3cbd9d14d1bcac753\")"
}
```

- Request Example
```
http://localhost/phpMongoAdmin/api/index.php?r=doc/delete/server1/harry/test
```

- Response Example
```
{
    "ok": 1,
    "n": 1,
    "err": null,
    "errmsg": null
}
```
