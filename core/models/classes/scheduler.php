<?php
namespace Core\Models\Classes;

class Scheduler
{
	public $settings = [];
	public $students_schedule = [];
	public $students_schedule_xn_format = [];
	public $students_schedule_count = [];
	public $emptyScheduleFlag = false;

	public $testingMode = 0;
	
	public $teachers_schedule_xn_format = [];  
	public $teachers_schedule = [];
	public $teachers_schedule_json = '';

	private $json_main_folder_name = 'zejsonfiles';
	private $python_main_folder_name = 'python';

	private $json_settings = ['name'=>'settings'];
	private $json_students_schedule = ['name'=>'students_schedule'];
	private $json_students_schedule_count = ['name'=>'students_schedule_count_for_python'];
	private $json_teachers_schedule = ['name'=>'teachers_schedule'];
	private $json_students_schedule_imported;
	
	private $python_exec_path = 'python';
	private $python_script_path = 'solver.py';
	private $python_script_debug_path = 'solver-debug.py';

	public $days_a_week = 5;
	public $day_starting_time_default = '07:00'; // 7 AM
	public $day_ending_time_default = '17:00'; // 5 PM
	public $day_slot_time_default = 30; // 30 mins
	public $max_students_per_teacher_default = 5;
	public $fulltime_available_teachers_default = 69;
	public $parttime_available_teachers_default = 69;
	public $slot_total_count = '';
	
	public $studentsClass = '';

	public $returnData = [];
  
  function __construct(){
		// Set Paths for JSON/Python Files on server
		$this->setRealPaths();
		
		$this->getSettings();
  }// end constructor
	
	function kickstart(){
		
		if ( $_GET['debug'] == 1 ){
			$python_output = exec("$this->python_exec_path $this->python_script_debug_path");
			var_dump ( $python_output );
			exit;
		}
		
		// Calculate slots' total count
		$this->calculateSlotTotalCount();
		
		// Recalculate the schedules if user press the Generate Random Schedule button
		$this->getSchedules();
		
		// format student array to x1y1, x1y2, format to be used on front end
		$this->saveFormatStudentsSchedule();
		
		// format teachers array to x1y1, x1y2, format to be used on front end
		$this->saveFormatTeachersSchedule();
	}
	
	function useStudentClass ( $class ){
		$this->studentsClass = $class;
	}
	
	function getSettings(){
		// $settings_json = getJSONFile ( $this->settings_json_path );
		$settings_json = getJSONFile ( $this->json_settings );

		if ( $settings_json ){
			$this->settings['day_starting_time'] = ( isset ($settings_json['day_starting_time']) && !empty ($settings_json['day_starting_time'] ) ) ? $settings_json['day_starting_time'] : $this->day_starting_time_default;
			
			$this->settings['day_ending_time'] = ( isset ($settings_json['day_ending_time']) && !empty ($settings_json['day_ending_time'] ) ) ? $settings_json['day_ending_time'] : $this->day_ending_time_default;
			
			// hack to force day length to 8, 10, 12 hours
			// minutes must match
			$dayLengthInHours = ( strtotime($this->settings['day_ending_time']) - strtotime($this->settings['day_starting_time']) ) / 60 / 60;
			// 8, 10, 12 hours allowed!
			$checkDayLength = ( is_int($dayLengthInHours ) && ($dayLengthInHours == 8 || $dayLengthInHours == 10 || $dayLengthInHours == 12) ) ? 1 : 0;
			
			// you shall not pass if it's not 8, 10, or 12 hours
			// we reset values to default here
			if ( !$checkDayLength ){
				$this->settings['day_starting_time'] = $this->day_starting_time_default;
				$this->settings['day_ending_time'] = $this->day_ending_time_default;
			}
			// we are trusting the default values to be 8, 10, 12 hours!!!
			// trust the developer lol
			$this->settings['day_length'] = $this->settings['day_ending_time'] - $this->settings['day_starting_time'];
			// end hack
			
			// since Python only accepts 30 minute slots
			// this setting must be 30, 60, 90, 120, etc...
			$this->settings['day_slot_time'] = ( isset ($settings_json['day_slot_time']) && !empty ($settings_json['day_slot_time'] ) && is_numeric ($settings_json['day_slot_time']) && is_int( $settings_json['day_slot_time']/30 ) && $settings_json['day_slot_time'] > 0 ) ? $settings_json['day_slot_time'] : $this->day_slot_time_default;
			
			$this->settings['max_students_per_teacher'] = ( isset ($settings_json['max_students_per_teacher']) && !empty ($settings_json['max_students_per_teacher'] ) && is_numeric ($settings_json['max_students_per_teacher']) && $settings_json['max_students_per_teacher'] > 0 ) ? intval($settings_json['max_students_per_teacher']) : $this->max_students_per_teacher_default;
			
			$this->settings['fulltime_available_teachers'] =  ( $settings_json['fulltime_available_teachers'] && is_numeric ($settings_json['fulltime_available_teachers']) && $settings_json['fulltime_available_teachers'] > 0 ) ? intval($settings_json['fulltime_available_teachers']) : $this->fulltime_available_teachers_default;
			
			$this->settings['parttime_available_teachers'] =  ($settings_json['parttime_available_teachers'] && is_numeric ($settings_json['parttime_available_teachers']) && $settings_json['parttime_available_teachers'] > 0 ) ? intval($settings_json['parttime_available_teachers']) : $this->parttime_available_teachers_default;
		}
		else{
			// defaults
			$this->settings['day_starting_time'] = $this->day_starting_time_default;
			$this->settings['day_ending_time'] = $this->day_ending_time_default;
			$this->settings['day_slot_time'] = $this->day_slot_time_default;
			$this->settings['max_students_per_teacher'] = $this->max_students_per_teacher_default;
			$this->settings['fulltime_available_teachers'] = $this->fulltime_available_teachers_default;
			$this->settings['parttime_available_teachers'] = $this->parttime_available_teachers_default;
		}
		
		$this->saveSettingsToJSON();
	}
	
	function saveSettings(){
		if ( isset ($_POST) && !empty ($_POST) ){
			
			// if json student schedule import
			// save it
			$json_students_schedule_imported = isset ($_FILES['json_schedule_import']) && !empty ($_FILES['json_schedule_import'] ) ? $_FILES['json_schedule_import'] : '';
			if ( isset($json_students_schedule_imported) && $json_students_schedule_imported['error'] == 0 ){
				$tmp_name = $_FILES["json_schedule_import"]["tmp_name"];
				$zejsonfilesPath = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_students_schedule['name'].'.json';
				@move_uploaded_file($tmp_name, $zejsonfilesPath);
			}
			
			$this->settings['day_starting_time'] = ( isset ($_POST['day_starting_time']) && !empty ($_POST['day_starting_time'] ) ) ? $_POST['day_starting_time'] : $this->day_starting_time_default;
			$this->settings['day_ending_time'] = ( isset ($_POST['day_ending_time']) && !empty ($_POST['day_ending_time'] ) ) ? $_POST['day_ending_time'] : $this->day_ending_time_default;
			
			// hack to force day length to 8, 10, 12 hours
			// minutes must match
			$dayLengthInHours = ( strtotime($this->settings['day_ending_time']) - strtotime($this->settings['day_starting_time']) ) / 60 / 60;
			// 8, 10, 12 hours allowed!
			$checkDayLength = ( is_int($dayLengthInHours ) && ($dayLengthInHours == 8 || $dayLengthInHours == 10 || $dayLengthInHours == 12) ) ? 1 : 0;
			
			// you shall not pass if it's not 8, 10, or 12 hours
			if ( !$checkDayLength ){
				$this->returnData['message_status'] = 0;
				$this->returnData['message'] = 'Only 8, 10 and 12 hour day lengths is allowed. Please make sure the minutes match as well. 7:45 to 5:40 won\'t work';
				return '';
			}
			$this->settings['day_length'] = $dayLengthInHours;
			// end hack
			
			// since Python only accepts 30 minute slots
			// this setting must be 30, 60, 90, 120, etc...
			$slotTimeCheck = ( isset ($_POST['day_slot_time']) && !empty ($_POST['day_slot_time'] ) && is_numeric ($_POST['day_slot_time']) && is_int( $_POST['day_slot_time']/30 ) && $_POST['day_slot_time'] > 0 ) ? 1 : 0;
			$this->settings['day_slot_time'] = ($slotTimeCheck) ? $_POST['day_slot_time'] : $this->day_slot_time_default;
			
			// you shall not pass if it's not 8, 10, or 12 hours
			if ( !$slotTimeCheck ){
				$this->returnData['message_status'] = 0;
				$this->returnData['message'] = 'Only 30-minute based slot times are currently allowed';
				return '';
			}
			
			$this->settings['max_students_per_teacher'] = ( isset ($_POST['max_students_per_teacher']) && !empty ($_POST['max_students_per_teacher'] ) && is_numeric ($_POST['max_students_per_teacher']) && $_POST['max_students_per_teacher'] > 0 ) ? intval($_POST['max_students_per_teacher']) : $this->max_students_per_teacher_default;
			
			$this->settings['fulltime_available_teachers'] = ( isset ($_POST['fulltime_available_teachers']) && !empty ($_POST['fulltime_available_teachers'] ) && is_numeric ($_POST['fulltime_available_teachers']) && $_POST['fulltime_available_teachers'] > 0 ) ? intval($_POST['fulltime_available_teachers']) : $this->fulltime_available_teachers_default;
			
			$this->settings['parttime_available_teachers'] = ( isset ($_POST['parttime_available_teachers']) && !empty ($_POST['parttime_available_teachers'] ) && is_numeric ($_POST['parttime_available_teachers']) && $_POST['parttime_available_teachers'] > 0 ) ? intval($_POST['parttime_available_teachers']) : $this->parttime_available_teachers_default;
			
			$this->returnData['message'] = 'Settings saved.';
			
			$this->saveSettingsToJSON();
		}
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
		$this->json_students_schedule['path'] = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_students_schedule['name'].'.json';
		$this->json_students_schedule['url'] = URL.$this->json_main_folder_name.'/'.$this->json_students_schedule['name'].'.json';
		
		$this->json_students_schedule_count['path'] = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_students_schedule_count['name'].'.json';
		$this->json_students_schedule_count['url'] = URL.$this->json_main_folder_name.'/'.$this->json_students_schedule_count['name'].'.json';
		
		$this->json_teachers_schedule['path'] = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_teachers_schedule['name'].'.json';
		$this->json_teachers_schedule['url'] = URL.$this->json_main_folder_name.'/'.$this->json_teachers_schedule['name'].'.json';

		$this->json_settings['path'] = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->json_settings['name'].'.json';
		$this->json_settings['url'] = URL.$this->json_main_folder_name.'/'.$this->json_settings['name'].'.json';
		
		$this->python_script_path = realpath(dirname(dirname(__FILE__))).'/'.$this->python_main_folder_name.'/'.$this->python_script_path;
		
		$this->python_script_debug_path = realpath(dirname(dirname(__FILE__))).'/'.$this->python_main_folder_name.'/'.$this->python_script_debug_path;
		
  }// end function
  
  function getDayStartingTime(){
    return $this->settings['day_starting_time'];
  }
  
  function getDayEndTime(){
    return $this->settings['day_ending_time'];
  }
  
  function getSlotTime(){
    return $this->settings['day_slot_time'];
  }
  
  /**
  * Calculate slots' total count
  * 
  * @uses string $day_starting_time
  * @uses string $day_end_time
  * @uses string $day_slot_time_default
  */
  function calculateSlotTotalCount(){
    $this->slot_total_count = ($this->settings['day_ending_time'] - $this->settings['day_starting_time']) * 60 / $this->settings['day_slot_time'];
  }// end function
  
  /**
  * Creates empty teachers schedule and saves it in the teachers schedule JSON file
  * this is only used if the existing teachers was badly formatted or empty
  * 
  */
  function createEmptyTeachersSchedule (){
    // empty teachers schedule first
    $this->teachers_schedule = [];
    
    for ($x = 1; $x <= $this->days_a_week; $x++ ){
      $this->teachers_schedule['Days'][($x-1)] = [ 'Slots' => [] ];
      
      for ($y = 1; $y <= $this->slot_total_count; $y++){
        $this->teachers_schedule['Days'][($x-1)]['Slots'][] = [
          'FullTime' => 0,
          'PartTime' => 0
        ];
      }// end for y
      
    }// end for x
    
    // save JSON version of the schedule
    $this->teachers_schedule_json = json_encode ( $this->teachers_schedule );
    
    // Save teachers schedule
    $this->saveTeachersScheduleToJSON();
  }// end function
  
  /**
  * Creates empty student schedule and empty student schedule count
  * and saves both to seperate JSON files on server
  * 
  */
  function createEmptyStudentsSchedule (){
    for ($x = 1; $x <= $this->days_a_week; $x++ ){
			
			$this->students_schedule_count['Days'][($x-1)] = [ 'Slots' => [] ];
			$this->students_schedule['Days'][($x-1)] = [ 'Slots' => [] ];
			
			for ($y = 1; $y <= $this->slot_total_count; $y++){
				// start hack
				// explained in the saveNewStudentsSchedule() function
				// hack for python slot times (based 30 mins)
				// default to one times in case the slot time is flawed
				$howManyTimes = ( ( $this->settings['day_slot_time'] / 30 ) > 0 ) ? intval($this->settings['day_slot_time'] / 30) : 1;
				
				for ( $t = 0; $t < $howManyTimes ; $t++){
					$this->students_schedule_count['Days'][($x-1)]['Slots'][] = ['NumberOfStudents' => 0];
				}
				// end hack
				
				$this->students_schedule['Days'][($x-1)]['Slots'][] = [ 'Students' => [] ];
			}// end for y
			
		}// end for x
		
		// Save students schedule
		$this->saveStudentsScheduleToJSON();
		
		// Save students schedule count
		$this->saveStudentsScheduleCountToJSON();
  }// end function
	
	/**
	* Save new students schedule
	* 
	*/
	function saveNewStudentsSchedule(){
		$students = [];
		foreach ($_POST['slots'] as $k=>$v){
      $array = explode(',',$v);
			if ( !empty ($array) && $array[0] != '' ){
				foreach ($array as $val){
					$students[$k] = $array;
				}
			}
		}
		
		for ($x = 1; $x <= $this->days_a_week; $x++ ){
			$this->students_schedule_count['Days'][($x-1)] = [ 'Slots' => [] ];
			$this->students_schedule['Days'][($x-1)] = [ 'Slots' => [] ];
			
			for ($y = 1; $y <= $this->slot_total_count; $y++){
				$kidsInSlot = count ( $students['x'.$x.'y'.$y] );
				
				// hack for Python
				// since the python script only accepts 30 minute slots
				// we send him the data depending on the slot duration in minutes (with a base of 30)
				// ex: if the slot time is 60mins, we send him each slot data twice
				// if the slot time is 90mins, thrice
				// if it's 30, once
				/*
				{
					"NumberOfStudents": 15
				},
				{
					"NumberOfStudents": 15
				},
				{
					"NumberOfStudents": 20
				},
				{
					"NumberOfStudents": 20
				}
				*/
				
				// default to one times in case the slot time is flawed
				$howManyTimes = ( ( $this->settings['day_slot_time'] / 30 ) > 0 ) ? intval($this->settings['day_slot_time'] / 30) : 1;
				for ( $t = 0; $t < $howManyTimes ; $t++){
					$this->students_schedule_count['Days'][($x-1)]['Slots'][] = [ 'NumberOfStudents' => $kidsInSlot ];
				}
				$this->students_schedule['Days'][($x-1)]['Slots'][] = [ 'Students' => [] ];
				
				// save students in this slot
				for ( $r = 0; $r < $kidsInSlot; $r++ ){
					
					$kidID = $students['x'.$x.'y'.$y][$r];
					$student = $this->studentsClass->getStudentByID ( $kidID );
					
					if ( isset($student['id']) ){
						$this->students_schedule['Days'][($x-1)]['Slots'][($y-1)]['Students'][] = $student;
					}
				}// end for r
				
			}// end for y
		}// end for x
		
		// Save students schedule
		$this->saveStudentsScheduleToJSON();
		
		// Save students schedule count
		$this->saveStudentsScheduleCountToJSON();
	}
	
	/**
	* Check schedule, if the students in it are not in our students.json file, delete them
	* Recalculate the schedule after checking
	* 
	* @param json data from the students_schedule json file
	*/
	function getStudentsSchedule(){
		// every student in the json file must have a valid ID and name that exist in the students.json file
		// otherwise we remove the student from the file
		// plus, if the file itself is not in the good format (slot total must be equal to the slot total which is calculated from the values in our settings page), we do not accept the file, we create an empty one to be used on the frontend
		$bad_format = 0;
		
		// Try to read Students Schedule JSON File
		$json_data = getJSONFile ( $this->json_students_schedule );
		
		$students_schedule_count = [];
		
		$howManyTimes = ( $this->settings['day_slot_time'] / 30 ) > 0 ? intval($this->settings['day_slot_time'] / 30) : 1;
		
		if ( isset ($json_data['Days']) && !empty ($json_data['Days']) ){
			// these are used to know where to unset the students if they are not valid
			$x = -1;
			foreach ( $json_data['Days'] as $dayData ){
				$y = -1;
				$x++;
				
				$students_schedule_count['Days'][$x] = [ 'Slots' => [] ];
				$slots = ( isset($dayData['Slots']) && count($dayData['Slots']) == $this->slot_total_count ) ? $dayData['Slots'] : '';
				if ( $slots != '' ){
					foreach ( $slots as $slot ){
						$studentsDuplicateChecker = [];
						$y++;
						
						if ( isset ($slot['Students']) ){
							if ( !empty ($slot['Students']) ){
								
								$studentC = -1;
								$validStudents = 0;
								foreach ( $slot['Students'] as $student ){
									$studentC++;
									
									if ( isset ($student['id']) && isset ($student['name']) && !in_array($student['id'],$studentsDuplicateChecker) ){
										$checkStudent = $this->studentsClass->getStudentByID ( $student['id'] );
										if ( !$checkStudent || ($checkStudent && $checkStudent['name'] != $student['name']) ){
											unset ($json_data['Days'][$x]['Slots'][$y]['Students'][$studentC]);
										}
										else{
											// student is valid
											$validStudents++;
											// save him in the duplicates array
											// to check in the loop - cant add the same student in the same slot
											$studentsDuplicateChecker[] = $student['id'];
										}
									}
									else {
										unset ($json_data['Days'][$x]['Slots'][$y]['Students'][$studentC]);
									}
								}
								for ( $t = 0; $t < $howManyTimes ; $t++){
									$students_schedule_count['Days'][$x]['Slots'][] = [ 'NumberOfStudents' => $validStudents ];
								}
							}
							else{
								for ( $t = 0; $t < $howManyTimes; $t++){
									$students_schedule_count['Days'][$x]['Slots'][] = [ 'NumberOfStudents' => 0 ];
								}
							}
						}
						else $bad_format = 1;
					}
				}
				else $bad_format = 1;
			}
		}
		else $bad_format = 1;
		
		// bad format on json file
		// we don't accept it.
		if ( $bad_format){
			$this->createEmptyStudentsSchedule();
		}
		else{
			$this->students_schedule = $json_data;
			$this->students_schedule_count = $students_schedule_count;
			
			// Save students schedule
			$this->saveStudentsScheduleToJSON();
			
			// Save students schedule count
			$this->saveStudentsScheduleCountToJSON();
		}
	}
  
  /**
  * Use Python to calculate Teachers Schedule
  * 
  * @uses $students_schedule_count as parameter for the python script
  * @returns json data which is saved in the $teachers_schedule variable and in a JSON file on server
  */
  function pythonDoTeachersSchedule(){
    // clear cache
    clearstatcache();
    
    // check if python executable and python scipts exist on server
    // if (!file_exists($this->python_exec_path)) {
      // exit("The python executable '$this->python_exec_path' does not exist");
    // }
    // if (!is_executable($this->python_exec_path)) {
      // exit(("The python executable '$this->python_exec_path' is not executable"));
    // }
    if (!file_exists($this->python_script_path)) {
      exit("The python script file '$this->python_script_path' does not exist");
    }
    
    // Send the Schedule Students Count as parameter to Python script
    $pythonParameter = json_encode($this->students_schedule_count);
    
		// testing mode, output data to check
		if ( $this->testingMode ){
			echo "<b>Testing Mode</b><br><br>";
			echo "<br><b>Parameters for Python: </b><br><br>";
			print_r ($pythonParameter);
		}
    // send command with params
    $python_output = exec("$this->python_exec_path $this->python_script_path");
		
		// testing mode, output data to check
		if ( $this->testingMode )
		{
			echo "<br><br><b>Print dumps from Python: </b><br><br>";
			var_dump ( $python_output );
			echo "<br><br><b>Response from Python: </b><br><br>";
			// print_r ( json_decode($python_output, true) );
			// echo "<b>End Response from Python</b><br><br>";
		}
    
    // Process python result
    if (isset($python_output) && !empty($python_output)){
      $python_output_decoded = json_decode($python_output, true);
      
      // if python output is proper json
      if ( isset ($python_output_decoded) ){
        // Save python output to Teachers Schedule array
        $this->teachers_schedule_json = $python_output;
        
        // Save Teachers Schedule to JSON file
        $this->saveTeachersScheduleToJSON();
      }// end if
    }// end if
  }// end function
  
  /**
  * Save students schedule to JSON file
  * 
  * @uses students schedule json file path
  * @uses students schedule array
  */
  function saveStudentsScheduleToJSON(){
    try{
      $fp = fopen( $this->json_students_schedule['path'], 'w');
      $res = fwrite ($fp, json_encode($this->students_schedule) );
      fclose ($fp);
    }
    catch (Exception $e) {}
  }// end function
	
	/**
  * Save settings to JSON file
  * 
  * @uses settings json file path
  * @uses settings array
  */
  function saveSettingsToJSON(){
		try{
			$fp = fopen( $this->json_settings['path'], 'w');
			$res = fwrite ($fp, json_encode($this->settings) );
			fclose ($fp);
		}
		catch (Exception $e) {}
  }// end function
  
  /**
  * Save students count schedule to JSON file
  * 
  * @uses students schedule count json file path
  * @uses students schedule count array
  */
  function saveStudentsScheduleCountToJSON(){
    try{
      $fp = fopen( $this->json_students_schedule_count['path'], 'w');
      fwrite ($fp, json_encode($this->students_schedule_count) );
      fclose ($fp);
    }
    catch (Exception $e) {}
  }// end function
  
  /**
  * Save Teachers Schedule to JSON file
  * 
  * @uses schedule teachers json file path
  * @uses schedule teachers array calculated via python
  */
  function saveTeachersScheduleToJSON(){
    try{
      $fp = fopen( $this->json_teachers_schedule['path'], 'w');
      fwrite ($fp, $this->teachers_schedule_json );
      fclose ($fp);
    }
    catch (Exception $e) {}
  }// end function
  
  /**
  * Format Teachers Schedule array
  * Save to a simpler format x1y1 where x = day and y = slot
  * 
  */
  function saveFormatTeachersSchedule (){
    
    if ( !empty ( $this->teachers_schedule_json ) ){
      $day_number = 0;
      
      // if teachers json file is corrupt, we default to 0 values for all time slots
      $bad_json_format = 0;
      
      $this->teachers_schedule = json_decode( $this->teachers_schedule_json, true);

      if ( isset ($this->teachers_schedule['Days']) && !empty ($this->teachers_schedule['Days']) ){
      
        foreach ( $this->teachers_schedule['Days'] as $dayData ){
        
          $slot_number = 0;
          $day_number++;
          
          $slots = ( isset($dayData['Slots']) && count($dayData['Slots']) == $this->slot_total_count ) ? $dayData['Slots'] : '';
          
          if ( $slots ){
            foreach ( $slots as $slot ){
              $slot_number++;
              
              $this->teachers_schedule_xn_format['x'.$day_number.'y'.$slot_number]['FullTime'] = ( isset($slot['FullTime']) ) ? $slot['FullTime'] : 0;
              $this->teachers_schedule_xn_format['x'.$day_number.'y'.$slot_number]['PartTime'] = ( isset($slot['PartTime']) ) ? $slot['PartTime'] : 0;
            }
          }
          else { $error = 'foreach slots<br>'; $bad_json_format = 1; }
        }
      }
      else { $error = 'isset days<br>'; $bad_json_format = 1; }
    }
    else { $error = 'empty teachers json<br>'; $bad_json_format = 1; }
    
    if ( $bad_json_format ){
      for ($x=1; $x<=$this->days_a_week; $x++){
        for ($y=1; $y <= $this->slot_total_count; $y++){
          $this->teachers_schedule_xn_format['x'.$x.'y'.$y]['FullTime'] = 0;
          $this->teachers_schedule_xn_format['x'.$x.'y'.$y]['PartTime'] = 0;
        }
      }
      // resave the teachers schedule JSON file
      // because it had bad formatting
      $this->createEmptyTeachersSchedule();
    }
    
  }// end function
  
  /**
  * Format Students Schedule array
  * Save to a simpler format x1y1 where x = day and y = slot
  * 
  */
  function saveFormatStudentsSchedule(){
    if ( !empty ( $this->students_schedule ) && !$this->emptyScheduleFlag ){

      $day_number = 0;
      
      // if students schedule json file is corrupt, we default to 0 values for all time slots
      $bad_json_format = 0;
      
      if ( isset ($this->students_schedule['Days']) && !empty ($this->students_schedule['Days']) ){
        
        foreach ( $this->students_schedule['Days'] as $dayData ){
          
          $slot_number = 0;
          $day_number++;
					
          $slots = ( isset($dayData['Slots']) && count($dayData['Slots']) == $this->slot_total_count ) ? $dayData['Slots'] : '';
          
          if ( $slots != '' ){
            foreach ( $slots as $slot ){
              $slot_number++;
              
              if ( isset ($slot['Students']) && !empty ($slot['Students']) ){
                foreach ( $slot['Students'] as $student ){
                  $this->students_schedule_xn_format['x'.$day_number.'y'.$slot_number]['Students'][] = $student;
                }
              }
              else {
                $this->students_schedule_xn_format['x'.$day_number.'y'.$slot_number]['Students'] = [];
              }
            }
          }
          else { $bad_json_format = 1; }
        }
      }
      else { $bad_json_format = 1; }
    }
    else { $bad_json_format = 1; }
    
    if ( $bad_json_format ){
      for ($x=1; $x <= $this->days_a_week; $x++){
        for ($y=1; $y <= $this->slot_total_count; $y++){
          $this->students_schedule_xn_format['x'.$x.'y'.$y]['Students'] = [];
        }
      }
      // resave the students schedule JSON file
      // because it had bad formatting
      $this->createEmptyStudentsSchedule();
    }
    
  }// end function

  /**
  * This main function will call other class functions 
  * to get the student schedule, the student schedule count
  * and to calculate the teachers schedule
  * 
  */
  function getSchedules (){
		
		// if user presses the Generate Schedule button in frontend
		// we force recalculation of the teachers schedule via python
		if ( isset ($_POST['random_schedule']) && $_POST['random_schedule'] == 1 ){
			
			// create new random students schedule alongwith the respective students schedule count
			// $this->createRandomStudentsSchedule();

			// Calculate new teachers schedule via python script
			// $this->pythonDoTeachersSchedule();
		}
		// if user presses the Save Schedule button in frontend
		// we force recalculation of the teachers schedule via python
		elseif ( isset ($_POST['save_schedule']) && $_POST['save_schedule'] == 1 ){
			// saave students schedule alongwith the respective students schedule count
			$this->saveNewStudentsSchedule();
		}
		else{
			// get existing students schedule and students schedule count data
			$this->getStudentsSchedule();
			
			// Attempt to find existing teachers schedule
			$json_result = getJSONFile ( $this->json_teachers_schedule );
			if ( $json_result ){
				$this->teachers_schedule_json = json_encode($json_result);
			}
		}
		// always force new calculation
		// Calculate new teachers schedule via python script
		$this->pythonDoTeachersSchedule();
		
  }// end function
  
  /**
  * Returns slot times (used in iteration to display all the slot times)
  * 
  * @param string $iterator
  * @return string slot time | ex: 9:30
  */
  function getSlotTimeInIteration ($iterator=0){
    return date('H:i', mktime(0, ($this->settings['day_starting_time']*60)+ ($this->settings['day_slot_time']*$iterator)));
  }
  
}// end class
?>