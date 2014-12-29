<?php
class TeachersController extends Controller
{
	private $model;
	public $menuActive;
	
	public function TeachersController(){
		$this->model = $this->loadModel('teachersModel');
		$this->menuActive = 'teachers';
	}
	
	public function index()
	{
		$MAIN = $this->model->index();
		
		$WRAPPER = 'teachers';
		require 'core/views/_templates/header.php';
		require 'core/views/teachers/index.php';
		require 'core/views/_templates/footer.php';
	}
	public function add()
	{
		$MAIN = $this->model->add();
		
		$WRAPPER = 'teachers';
		require 'core/views/_templates/header.php';
		require 'core/views/teachers/add.php';
		require 'core/views/_templates/footer.php';
	}

}