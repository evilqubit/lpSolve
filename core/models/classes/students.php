<?php
namespace Core\Models\Classes;

class Students
{
	public $testingMode = 0;
	private $json_main_folder_name = 'zejsonfiles';
	private $json_students = ['name'=>'students'];
	public $students = [];
	public $returnData = [];
  
  function __construct(){
		// Set Paths for JSON Files on server
		$this->setRealPaths();
		
		$this->kickstart();
		
  }// end constructor
	
	function kickstart(){
		// get existing students
		$this->getExistingStudents();
	}
	
	/**
  * Set Paths for JSON/Python Files on server
  * 
  */
  function setRealPaths (){
    $this->json_students['path'] = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_students['name'].'.json';
		$this->json_students['url'] = URL.$this->json_main_folder_name.'/'.$this->json_students['name'].'.json';
  }// end function
	
	/**
	* Get existing students from JSON file
	* 
	*/
	function getExistingStudents(){
		$get_students_from_json = getJSONFile ( $this->json_students );
		$this->students = ($get_students_from_json) ? $get_students_from_json : $this->students;
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
  
  public function getStudentByID($id){
		$output = '';
		
		foreach ( $this->students['Students'] as $i=>$student ){
			if ($student['id'] == $id ){
				$output = $student;
				break;
			}
		}
		
		return $output;
  }
	function getStudents(){
    return ( isset($this->students['Students']) ) ? $this->students['Students'] : [];
  }
	
  /**
	* Add Student
	* 
	*/
	function addStudent(){
		
		if ( isset ($_POST['save_students']) && $_POST['save_students'] == 1 ){
			$student_name = ( isset ($_POST['name']) && !empty ($_POST['name']) ) ? $_POST['name'] : '';
			if ( $student_name ){
				// add student to existing students
				$currentCount = count ( $this->students['Students'] );
				$this->students['Students'][] = ['id'=>($currentCount+1), 'name'=>$student_name];
				
				$this->returnData['message'] = 'Student successfully added.';
				
				// Save students to JSON
				$this->saveStudentsToJSON();	
			}
		}
  }// end function
  
  /**
  * Save students to JSON file
  * 
  * @uses students json file path
  * @uses students array
  */
	function saveStudentsToJSON(){
		try{
			$fp = fopen( $this->json_students['path'], 'w');
			$res = fwrite ($fp, json_encode($this->students) );
			fclose ($fp);
		}
		catch (Exception $e) {}
	}// end function

	function createStudentLabel($entry){
		$output = "<div class='student-entry label ui-widget-header draggable' data-id='{$entry[id]}' data-type='student'>$entry[name]";
		$output .= "<div class='close-me'><span>X</span></div>";
		$output .= "</div>";
		return $output;
	}
	function createStudentsEntries (){
		$output = '';
		$entries = $this->getStudents();
		foreach ($entries as $entry ){
			$output .= $this->createStudentLabel ($entry);
		}
		echo $output;
	}
  
}// end class
?>