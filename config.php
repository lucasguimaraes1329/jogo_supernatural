<?php
$dbHost='127.0.0.1'; $dbName='scooby_rpg'; $dbUser='root'; $dbPass='';
function db(): PDO {
 global $dbHost,$dbName,$dbUser,$dbPass; static $pdo=null;
 if(!$pdo){$pdo=new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",$dbUser,$dbPass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
 return $pdo;
}

