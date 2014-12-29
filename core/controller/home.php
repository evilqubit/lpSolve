<?php
class HomeController extends Controller
{
	public $model;
	public $students_model;
	public $teachers_model;
	
	public function HomeController(){
		$this->model = $this->loadModel('homeModel');
		$this->students_model = $this->loadModel('studentsModel');
		$this->teachers_model = $this->loadModel('teachersModel');
	}
	
	public function index()
	{
		$MAIN = $this->model->index( ['studentsClass'=>$this->students_model->getClass()] );
		
		$STUDENTS = $this->students_model->index();
		$TEACHERS = $this->teachers_model->index();
		
		$WRAPPER = 'home';
		require 'core/views/_templates/header.php';
		require 'core/views/home/index.php';
		require 'core/views/_templates/footer.php';
	}
}