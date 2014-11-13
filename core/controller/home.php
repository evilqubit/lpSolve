<?php
class Home extends Controller
{
  public function index()
  {
    $homeModel = $this->loadModel('homeModel');
    $home_data = $homeModel->index();
    
		// for menus
		$WRAPPER = 'home';
		
    require 'core/views/_templates/header.php';
    require 'core/views/home/index.php';
    require 'core/views/_templates/footer.php';
  }
}