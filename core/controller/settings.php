<?php
class SettingsController extends Controller
{
	private $model;
	public $menuActive;
	
	public function SettingsController(){
		$this->model = $this->loadModel('settingsModel');
		$this->menuActive = 'settings';
	}
	
	public function index()
	{
		$MAIN = $this->model->index();
		
		$WRAPPER = 'settings';
		require 'core/views/_templates/header.php';
		require 'core/views/settings/index.php';
		require 'core/views/_templates/footer.php';
	}
}