<?php

define("DBHOST","localhost");
define("DBUSER","admin");
define("DBPASS","admin");
define("DBNAME","appgabut");


class Database
{
	public function __construct($config = null)
	{
		if($config == null){
			$this->CONN = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);			
		}else{
			$this->CONN = mysqli_connect($config['host'],$config['user'], $config['pass'], $config['name']);
		}
	}
	public function query($q)
	{
		$res = new DBQuery;
		$res->conn = $this->CONN;
		$res->queryres = mysqli_query($this->CONN, $q);
		return $res;
	}
}
class DBQuery
{
	var $conn;
	var $queryres;
	public function getResult()
	{
		$result = [];
		while($r = mysqli_fetch_assoc($this->queryres)){
			$result[] = $r;
		}
		return $result;
	}
	public function getRow()
	{
		return mysqli_fetch_assoc($this->queryres);
	}
	public function getNumRows()
	{
		return mysqli_num_rows($this->queryres);
	}
	public function AffectedRows()
	{
		return mysqli_affected_rows($this->conn);
	}
}
