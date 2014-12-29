<?php
use \Core\Models\Classes\Students;

include ('classes/students.php');

class StudentsModel
{
	public $studentsClass;
	
	function __construct($db) {
		$this->studentsClass = new Students;
		
		try {
			$this->db = $db;
		} catch (PDOException $e) {
			exit('Database connection could not be established.');
		}
	}
	public function getClass(){
		return $this->studentsClass;
	}

	public function index(){
		return $this->studentsClass;
	}
	public function add(){
		$this->studentsClass->addStudent();
		return $this->studentsClass;
	}
}