API DOC
=======

The api url looks like 'api/index.php?r={controller_name}/{action_name}/{server_name}/{database_name}/{collection_name}'

The api list:
* api/index.php?r=server/index - Get all mongo servers
* api/index.php?r=database/index/{server_name} - Get all database list
* api/index.php?r=collection/index/{server_name}/{database_name} - Get all mongo collection name
* api/index.php?r=collection/create/{server_name}/{database_name}/{collection_name} - Create a mongo collection
* api/index.php?r=collection/delete/{server_name}/{database_name}/{collection_name} - Drop a mongo collection
* api/index.php?r=doc/index/{server_name}/{database_name}/{collection_name} - Get mongo collection document. Default page is 1, page size is 50
