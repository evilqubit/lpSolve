<?php
use \Core\Models\Classes\Scheduler;

class HomeModel
{
	private $schedulerClass;
	
	function __construct($db) {
		
		include ('classes/scheduler.php');
		$this->schedulerClass = new Scheduler;
		
		try {
			$this->db = $db;
		} catch (PDOException $e) {
			exit('Database connection could not be established.');
		}
	}

	public function index($args = []){
		
		if ( isset ($args['studentsClass']) )
			$this->schedulerClass->useStudentClass( $args['studentsClass'] );
		
		// testing mode
		if ( isset($_GET['test']) && $_GET['test'] == 1 ){
			$this->schedulerClass->enableTesting();
		}
		
		$this->schedulerClass->kickstart();
		
		return $this->schedulerClass;
	}
}