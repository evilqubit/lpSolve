<?php
class Settings extends Controller
{
  public function index()
  {
    $settingsModel = $this->loadModel('settingsModel');
    $settings_data = $settingsModel->index();
    
		// for menus
		$WRAPPER = 'settings';
		
    require 'core/views/_templates/header.php';
    require 'core/views/settings/index.php';
    require 'core/views/_templates/footer.php';
  }
}