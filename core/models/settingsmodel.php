<?php
use \Core\Models\Classes\Scheduler;

include ('classes/scheduler.php');

class SettingsModel
{
	private $schedulerClass;
	
	function __construct($db) {
		$this->schedulerClass = new Scheduler;
		
		try {
			$this->db = $db;
		} catch (PDOException $e) {
			exit('Database connection could not be established.');
		}
	}

	public function index(){
		$this->schedulerClass->saveSettings();
		return $this->schedulerClass;
	}
}