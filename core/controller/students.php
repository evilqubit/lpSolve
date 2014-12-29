<?php
class StudentsController extends Controller
{
	private $model;
	public $menuActive;
	
	public function StudentsController(){
		$this->model = $this->loadModel('studentsModel');
		$this->menuActive = 'students';
	}
	
	public function index()
	{
		$MAIN = $this->model->index();
		
		$WRAPPER = 'students';
		require 'core/views/_templates/header.php';
		require 'core/views/students/index.php';
		require 'core/views/_templates/footer.php';
	}
	public function add()
	{
		$MAIN = $this->model->add();
		
		$WRAPPER = 'students';
		require 'core/views/_templates/header.php';
		require 'core/views/students/add.php';
		require 'core/views/_templates/footer.php';
	}

}