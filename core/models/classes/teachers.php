<?php
namespace Core\Models\Classes;

class Teachers
{
	public $testingMode = 0;
	private $json_main_folder_name = 'zejsonfiles';
	private $json_teachers = ['name'=>'teachers'];
	public $teachers = [];
	public $returnData = [];
  
  function __construct(){
		// Set Paths for JSON Files on server
		$this->setRealPaths();
		
		$this->kickstart();
		
  }// end constructor
	
	function kickstart(){
		// get existing teachers
		$this->getExistingTeachers();
	}
	
	/**
	* Get existing teachers from JSON file
	* 
	*/
	function getExistingTeachers(){
		$get_teachers_from_json = getJSONFile ( $this->json_teachers );
		$this->teachers = ($get_teachers_from_json) ? $get_teachers_from_json : $this->teachers;
	}
	
	/**
	* Enable testing mode
	* add ?test=1 in the URL to debug useful data
	* 
	*/
	function enableTesting(){
		$this->testingMode = 1;
	}
	
	/**
	* Check Message Status for Settings page
	* 
	*/
	function hasMessageStatus(){
		return ( (isset($this->returnData['message_status']) && $this->returnData['message_status'] == 1) || !isset($this->returnData['message_status']) ) ? 1 : 0;
	}
	
	/**
	* Get Message Status for Settings page
	* 
	* @param echo or return
	*/
	function getMessageStatus ($echo = 0){
		$val = ( (isset($this->returnData['message_status']) && $this->returnData['message_status'] == 1) || !isset($this->returnData['message_status']) ) ? 1 : 0;
		
		if ( $echo ) echo $val;
		else return $val;
	}
	
  /**
  * Set Paths for JSON/Python Files on server
  * 
  */
  function setRealPaths (){
    $this->json_teachers['path'] = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_teachers['name'].'.json';
		$this->json_teachers['url'] = URL.'/'.$this->json_main_folder_name.'/'.$this->json_teachers['name'].'.json';
  }// end function
  
  function getTeacher($id){
    return ( isset($this->teachers['Teachers'][$id]) ) ? $this->teachers['Teachers'][$id] : '';
  }
	function getTeachers(){
    return ( isset($this->teachers['Teachers']) ) ? $this->teachers['Teachers'] : [];
  }
	
  /**
	* Add Teacher
	* 
	*/
	function addTeacher(){
		
		if ( isset ($_POST['save_teachers']) && $_POST['save_teachers'] == 1 ){
			$name = ( isset ($_POST['name']) && !empty ($_POST['name']) ) ? $_POST['name'] : '';
			if ( $name ){
				// add teacher to existing teachers
				$currentCount = isset($this->teachers['Teachers']) ? count ( $this->teachers['Teachers'] ) : 0;
				$this->teachers['Teachers'][] = ['id'=>($currentCount+1), 'name'=>$name];
				
				// Save teachers to JSON
				$this->saveTeachersToJSON();
				
				$this->returnData['message'] = 'Teacher successfully added.';
			}
		}
  }// end function
  
  /**
  * Save teachers to JSON file
  * 
  * @uses teachers json file path
  * @uses teachers array
  */
  function saveTeachersToJSON(){
    try{
      $fp = fopen( $this->json_teachers['path'], 'w');
      $res = fwrite ($fp, json_encode($this->teachers) );
      fclose ($fp);
    }
    catch (Exception $e) {}
  }// end function
	
	function createTeacherLabel($entry){
		$output = "<div class='teacher-entry label ui-widget-header draggable' data-id='{$entry[id]}' data-type='teacher'>$entry[name]";
		$output .= "<div class='total-hours'>Hours: <span>0</span></div>";
		$output .= "
		<select class='form-control teacher-mode-picker'>
			<option value='parttime'>Part-time</option>
			<option value='fulltime'>Full-time</option>
			<option value='vacation'>Vacation</option>
		</select>";
		$output .= "<div class='close-me'><span>X</span></div>";
		$output .= "</div>";
		return $output;
	}
	function createTeachersEntries (){
		$output = '';
		
		$entries = $this->getTeachers();
		foreach ($entries as $entry ){
			$output .= $this->createTeacherLabel ($entry);
		}
		echo $output;
	}
  
}// end class
?>