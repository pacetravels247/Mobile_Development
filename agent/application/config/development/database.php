<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//die('78978');
/*
 | -------------------------------------------------------------------
 | DATABASE CONNECTIVITY SETTINGS
 | -------------------------------------------------------------------
 | This file will contain the settings needed to access your database.
 |
 | For complete instructions please consult the 'Database Connection'
 | page of the User Guide.
 |
 | -------------------------------------------------------------------
 | EXPLANATION OF VARIABLES 
 | -------------------------------------------------------------------
 |
 |	['hostname'] The hostname of your database server.
 |	['username'] The username used to connect to the database
 |	['password'] The password used to connect to the database
 |	['database'] The name of the database you want to connect to
 |	['dbdriver'] The database type. ie: mysql.  Currently supported:
 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
 |	['dbprefix'] You can add an optional prefix, which will be added
 |				 to the table name when using the  Active Record class
 |	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
 |	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
 |	['cache_on'] TRUE/FALSE - Enables/disables query caching
 |	['cachedir'] The path to the folder where cache files should be stored
 |	['char_set'] The character set used in communicating with the database
 |	['dbcollat'] The character collation used in communicating with the database
 |				 NOTE: For MySQL and MySQLi databases, this setting is only used
 | 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
 |				 (and in table creation queries made with DB Forge).
 | 				 There is an incompatibility in PHP with mysql_real_escape_string() which
 | 				 can make your site vulnerable to SQL injection if you are using a
 | 				 multi-byte character set and are running versions lower than these.
 | 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
 |	['swap_pre'] A default table prefix that should be swapped with the dbprefix
 |	['autoinit'] Whether or not to automatically initialize the database.
 |	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
 |							- good for ensuring strict SQL while developing
 |
 | The $active_group variable lets you choose which connection group to
 | make active.  By default there is only one group (the 'default' group).
 |
 | The $active_record variables lets you determine whether or not to load
 | the active record class
 */

$active_group = 'default';
$active_record = TRUE;
/*$db['default']['hostname'] = 'mysql:host=mydbinstance.cz4wp7w9anzn.ap-southeast-1.rds.amazonaws.com;dbname=pace_travel_1';
$db['default']['username'] = 'stagpacetravels';
$db['default']['password'] = 'ProductionNewRDS'; */
$db['default']['hostname'] = 'mysql:host=localhost;dbname=pace_travel_1';
$db['default']['username'] = 'provab';
$db['default']['password'] = 'jEW5%3pH';
$db['default']['database'] = 'pace_travel_1';
$db['default']['db_debug'] = FALSE;

//$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbdriver'] = 'pdo';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */

//Second DB
//$db['second_db']['hostname'] = 'travelb2b.cf3sceyrxcki.us-east-1.rds.amazonaws.com';
$db['second_db']['hostname'] = '182.18.139.221,2456';
$db['second_db']['username'] = 'PORTd@Pace$300420$';
$db['second_db']['password'] = 'I200430$ecapTR!nd@';
$db['second_db']['database'] = 'PaceTravel-Portal';
$db['second_db']['db_debug'] = TRUE;
$db['second_db']['dbdriver'] = 'sqlsrv';
$db['second_db']['dbprefix'] = ''; 
$db['second_db']['pconnect'] = FALSE;
$db['second_db']['cache_on'] = FALSE;
$db['second_db']['cachedir'] = '';
$db['second_db']['char_set'] = 'utf8';
$db['second_db']['dbcollat'] = 'utf8_general_ci';
$db['second_db']['swap_pre'] = '';
$db['second_db']['autoinit'] = TRUE;
$db['second_db']['stricton'] = FALSE;
