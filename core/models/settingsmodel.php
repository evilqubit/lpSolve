<?php
class SettingsModel
{
	function __construct($db) {
		try {
			$this->db = $db;
		} catch (PDOException $e) {
			exit('Database connection could not be established.');
		}
	}

	public function index(){
		include ('classes/scheduler.php');
		$scheduler = new Scheduler();
		$scheduler->saveSettings();
		return $scheduler;
	}
}