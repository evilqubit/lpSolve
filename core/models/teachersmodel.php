<?php
use \Core\Models\Classes\Teachers;

include ('classes/teachers.php');

class TeachersModel
{
	private $teachersClass;
	
	function __construct($db) {
		$this->teachersClass = new Teachers;
		
		try {
			$this->db = $db;
		} catch (PDOException $e) {
			exit('Database connection could not be established.');
		}
	}

	public function index(){
		return $this->teachersClass;
	}
	public function add(){
		$this->teachersClass->addTeacher();
		return $this->teachersClass;
	}
}