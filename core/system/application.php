<?php
class Application
{
	private $url_controller = null;
	private $url_action = null;
	private $url_parameter_1 = null;
	private $url_parameter_2 = null;
	private $url_parameter_3 = null;

	/**
	* "Start" the application:
	* Analyze the URL elements and calls the according controller/method or the fallback
	*/
	public function __construct()
	{
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// create array with URL parts in $url
		$this->splitUrl();

		// check for controller: does such a controller exist ?
		if (file_exists('./core/controller/' . $this->url_controller . '.php')) {

			// if so, then load this file and create this controller
			// example: if controller would be "car", then this line would translate into: $this->car = new car();
			require './core/controller/' . $this->url_controller . '.php';
			// homeController, settingsController, etc...
			$controllerName = ucwords($this->url_controller.'Controller');
			$this->url_controller = new $controllerName();
			
			// check for method: does such a method exist in the controller ?
			if (method_exists($this->url_controller, $this->url_action)) {
				// call the method and pass the arguments to it
				if (isset($this->url_parameter_3)) {
						// will translate to something like $this->home->method($param_1, $param_2, $param_3);
						$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);
				} elseif (isset($this->url_parameter_2)) {
						// will translate to something like $this->home->method($param_1, $param_2);
						$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);
				} elseif (isset($this->url_parameter_1)) {
						// will translate to something like $this->home->method($param_1);
						$this->url_controller->{$this->url_action}($this->url_parameter_1);
				} else {
						// if no parameters given, just call the method without parameters, like $this->home->method();
						$this->url_controller->{$this->url_action}();
				}
			} else {
				// default/fallback: call the index() method of a selected controller
				$this->url_controller->index();
			}
		}
		else {
			// invalid URL, so simply show home/index
			require './core/controller/home.php';
			$home = new HomeController();
			$home->index();
		}
	}

	/**
	 * Get and split the URL
	 */
	private function splitUrl(){
		if (isset($_GET['url'])) {

			// split URL
			$url = rtrim($_GET['url'], '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);

			// Put URL parts into according properties
			$this->url_controller = (isset($url[0]) ? $url[0] : null);
			$this->url_action = (isset($url[1]) ? $url[1] : null);
			$this->url_parameter_1 = (isset($url[2]) ? $url[2] : null);
			$this->url_parameter_2 = (isset($url[3]) ? $url[3] : null);
			$this->url_parameter_3 = (isset($url[4]) ? $url[4] : null);
		}
	}
}