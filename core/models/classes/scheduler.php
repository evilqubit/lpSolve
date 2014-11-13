<?php
class Scheduler
{
  public $kids = array (
   1 => array ('id' => 1, 'name' => 'Patrick' ),
   2 => array ('id' => 2, 'name' => 'Joe' ),
   3 => array ('id' => 3, 'name' => 'John' ),
   4 => array ('id' => 4, 'name' => 'Jenny' ),
   5 => array ('id' => 5, 'name' => 'Carla' ),
   6 => array ('id' => 6, 'name' => 'Patricia' ),
   7 => array ('id' => 7, 'name' => 'Jonny' ),
   8 => array ('id' => 8, 'name' => 'Rabih' ),
   9 => array ('id' => 9, 'name' => 'Carl' ),
   10 => array ('id' => 10, 'name' => 'Mike' ),
   11 => array ('id' => 11, 'name' => 'Lisa' ),
   12 => array ('id' => 12, 'name' => 'Elie' ),
   13 => array ('id' => 13, 'name' => 'Carlo' ),
   14 => array ('id' => 14, 'name' => 'Jenna' ),
   15 => array ('id' => 15, 'name' => 'Mary' ),
   16 => array ('id' => 16, 'name' => 'Roy' ),
   17 => array ('id' => 17, 'name' => 'Jamil' ),
   18 => array ('id' => 18, 'name' => 'Lizz' ),
   19 => array ('id' => 19, 'name' => 'Catherine' ),
   20 => array ('id' => 20, 'name' => 'Catherina' ),
   21 => array ('id' => 21, 'name' => 'Ramona' ),
   22 => array ('id' => 22, 'name' => 'Jason' ),
   23 => array ('id' => 23, 'name' => 'Yves' ),
   24 => array ('id' => 24, 'name' => 'Arthur' ),
   25 => array ('id' => 25, 'name' => 'Ross' )
  );
  
	public $settings = array();
  public $students_schedule = array();
  public $students_schedule_xn_format = array();
  public $students_schedule_count = array();
  public $emptyScheduleFlag = false;
  
  public $teachers_schedule_xn_format = array();  
  public $teachers_schedule = array();
  public $teachers_schedule_json = '';
  
  private $json_main_folder_name = 'zejsonfiles';
  private $python_main_folder_name = 'python';
  
	private $settings_json_path = 'settings.json';
	
  private $students_schedule_json_path = 'students_schedule.json';
  private $students_schedule_count_json_path = 'students_schedule_count_for_python.json';
  private $teachers_schedule_json_path = 'teachers_schedule.json';
  // private $python_exec_path = 'C:\PYTHON33\python.exe';
  private $python_exec_path = 'python';
  private $python_script_path = 'solver.py';
  
  public $day_starting_time_default = '07:00'; // 9 AM
  public $day_ending_time_default = '19:00'; // 7 PM
  public $day_slot_time_default = 30; // 30 mins
	public $max_kids_per_teacher_default = 5;
	public $fulltime_available_teachers_default = 69;
	public $parttime_available_teachers_default = 69;
  public $slot_total_count = '';
	
	public $returnData = array();
  
  function __construct(){
    
    // Set Paths for JSON/Python Files on server
    $this->setRealPaths();
		
		$this->getSettings();
  }// end constructor
	
	function kickstart(){
    // Calculate slots' total count
    $this->calculateSlotTotalCount();
    
    // Recalculate the schedules if user press the Generate Random Schedule button
    $this->getSchedules();
    
    // format student array to x1y1, x1y2, format to be used on front end
    $this->saveFormatStudentsSchedule();
    
    // format teachers araay to x1y1, x1y2, format to be used on front end
    $this->saveFormatTeachersSchedule();
	}
	
	function getSettings(){
		$settings_json = $this->getJSONFile ( $this->settings_json_path );
		
		if ( $settings_json ){
			$this->settings['day_starting_time'] = ( isset ($settings_json['day_starting_time']) && !empty ($settings_json['day_starting_time'] ) ) ? $settings_json['day_starting_time'] : $this->day_starting_time_default;
			$this->settings['day_ending_time'] = ( isset ($settings_json['day_ending_time']) && !empty ($settings_json['day_ending_time'] ) ) ? $settings_json['day_ending_time'] : $this->day_ending_time_default;
			$this->settings['day_slot_time'] = ( isset ($settings_json['day_slot_time']) && !empty ($settings_json['day_slot_time'] ) && is_numeric ($settings_json['day_slot_time']) && $settings_json['day_slot_time'] >= 30 ) ? $settings_json['day_slot_time'] : $this->day_slot_time_default;
			$this->settings['max_kids_per_teacher'] = ( isset ($settings_json['max_kids_per_teacher']) && !empty ($settings_json['max_kids_per_teacher'] ) && is_numeric ($settings_json['max_kids_per_teacher']) && $settings_json['max_kids_per_teacher'] > 0 ) ? $settings_json['max_kids_per_teacher'] : $this->max_kids_per_teacher_default;
			
			$this->settings['fulltime_available_teachers'] =  ( $settings_json['fulltime_available_teachers'] && is_numeric ($settings_json['fulltime_available_teachers']) && $settings_json['fulltime_available_teachers'] > 0 ) ? $settings_json['fulltime_available_teachers'] : $this->fulltime_available_teachers_default;
			
			$this->settings['parttime_available_teachers'] =  ($settings_json['parttime_available_teachers'] && is_numeric ($settings_json['parttime_available_teachers']) && $settings_json['parttime_available_teachers'] > 0 ) ? $settings_json['parttime_available_teachers'] : $this->parttime_available_teachers_default;
		}
		else{
			// defaults
			$this->settings['day_starting_time'] = $this->day_starting_time_default;
			$this->settings['day_ending_time'] = $this->day_ending_time_default;
			$this->settings['day_slot_time'] = $this->day_slot_time_default;
			$this->settings['max_kids_per_teacher'] = $this->max_kids_per_teacher_default;
			$this->settings['fulltime_available_teachers'] = $this->fulltime_available_teachers_default;
			$this->settings['parttime_available_teachers'] = $this->parttime_available_teachers_default;
			
			$this->saveSettingsToJSON();
		}
	}
	
	function saveSettings(){
		if ( isset ($_POST) && !empty ($_POST) ){
			
			$this->settings['day_starting_time'] = ( isset ($_POST['day_starting_time']) && !empty ($_POST['day_starting_time'] ) ) ? $_POST['day_starting_time'] : $this->day_starting_time_default;
			$this->settings['day_ending_time'] = ( isset ($_POST['day_ending_time']) && !empty ($_POST['day_ending_time'] ) ) ? $_POST['day_ending_time'] : $this->day_ending_time_default;
			$this->settings['day_slot_time'] = ( isset ($_POST['day_slot_time']) && !empty ($_POST['day_slot_time'] ) && is_numeric ($_POST['day_slot_time']) && $_POST['day_slot_time'] >= 30 ) ? $_POST['day_slot_time'] : $this->day_slot_time_default;
			$this->settings['max_kids_per_teacher'] = ( isset ($_POST['max_kids_per_teacher']) && !empty ($_POST['max_kids_per_teacher'] ) && is_numeric ($_POST['max_kids_per_teacher']) && $_POST['max_kids_per_teacher'] > 0 ) ? $_POST['max_kids_per_teacher'] : $this->max_kids_per_teacher_default;
			
			$this->settings['fulltime_available_teachers'] = ( isset ($_POST['fulltime_available_teachers']) && !empty ($_POST['fulltime_available_teachers'] ) && is_numeric ($_POST['fulltime_available_teachers']) && $_POST['fulltime_available_teachers'] > 0 ) ? $_POST['fulltime_available_teachers'] : $this->fulltime_available_teachers_default;
			
			$this->settings['parttime_available_teachers'] = ( isset ($_POST['parttime_available_teachers']) && !empty ($_POST['parttime_available_teachers'] ) && is_numeric ($_POST['parttime_available_teachers']) && $_POST['parttime_available_teachers'] > 0 ) ? $_POST['parttime_available_teachers'] : $this->parttime_available_teachers_default;
			
			$this->returnData['message'] = 'Settings saved.';
			
			$this->saveSettingsToJSON();
		}
	}
	
  /**
  * Set Paths for JSON/Python Files on server
  * 
  */
  function setRealPaths (){
    $this->students_schedule_json_path = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->students_schedule_json_path;
    
    $this->students_schedule_count_json_path = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->students_schedule_count_json_path;
    
    $this->teachers_schedule_json_path = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->teachers_schedule_json_path;
    
		$this->settings_json_path = realpath(dirname(dirname(dirname(dirname(__FILE__))))).'/'.$this->json_main_folder_name.'/'.$this->settings_json_path;
		
    $this->python_script_path = realpath(dirname(dirname(__FILE__))).'/'.$this->python_main_folder_name.'/'.$this->python_script_path;
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
  
  function getKid($id){
    return ( isset($this->kids[$id]) ) ? $this->kids[$id] : '';
  }
  
  /**
  * Calculate slots' total count
  * 
  * @uses string $day_starting_time
  * @uses string $day_end_time
  * @uses string $slot_time
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
    $this->teachers_schedule = array();
    
    for ($x = 1; $x <= 7; $x++ ){
      $this->teachers_schedule['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      for ($y = 1; $y <= $this->slot_total_count; $y++){
        $this->teachers_schedule['Days'][($x-1)]['Slots'][] = array(
          'FullTime' => 0,
          'PartTime' => 0
        );
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
    for ($x = 1; $x <= 7; $x++ ){
      $this->students_schedule_count['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      $this->students_schedule['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      for ($y = 1; $y <= $this->slot_total_count; $y++){
        $this->students_schedule_count['Days'][($x-1)]['Slots'][] = array(
          'NumberOfStudents' => 0
        );
        
        $this->students_schedule['Days'][($x-1)]['Slots'][] = array(
          'Students' => array()
        );
      }// end for y
      
    }// end for x
    
    // Save students schedule
    $this->saveStudentsScheduleToJSON();
    
    // Save students schedule count
    $this->saveStudentsScheduleCountToJSON();
  }// end function
  
  /**
  * Creates a random student schedule and its approprite student schedule count
  * 
  */
  function createRandomStudentsSchedule (){
    for ($x = 1; $x <= 7; $x++ ){
      $this->students_schedule_count['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      $this->students_schedule['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      for ($y = 1; $y <= $this->slot_total_count; $y++){
        $kidsInSlot = rand (10, 20);
        
        $this->students_schedule_count['Days'][($x-1)]['Slots'][] = array(
          'NumberOfStudents' => $kidsInSlot
        );
        
        $this->students_schedule['Days'][($x-1)]['Slots'][] = array(
          'Students' => array()
        );
        
        // shuffle kids
        shuffle ($this->kids);
        
        // save kids in this slot
        for ( $r = 1; $r <= $kidsInSlot; $r++ ){
          if ( $this->getKid ($r) != '' ){
            $this->students_schedule['Days'][($x-1)]['Slots'][($y-1)]['Students'][] = $this->getKid($r);
          }
        }// end for r
        
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
		$kids = array();
		foreach ($_POST['slots'] as $k=>$v){
      $array = explode(',',$v);
			if ( !empty ($array) && $array[0] != '' ){
				foreach ($array as $val){
					$kids[$k] = $array;
				}
			}
		}
		
		for ($x = 1; $x <= 7; $x++ ){
      $this->students_schedule_count['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      $this->students_schedule['Days'][($x-1)] = array(
        'Slots' => array()
      );
      
      for ($y = 1; $y <= $this->slot_total_count; $y++){
        $kidsInSlot = count ( $kids['x'.$x.'y'.$y] );
				
        
        $this->students_schedule_count['Days'][($x-1)]['Slots'][] = array(
          'NumberOfStudents' => $kidsInSlot
        );
        
        $this->students_schedule['Days'][($x-1)]['Slots'][] = array(
          'Students' => array()
        );
        
        // save kids in this slot
        for ( $r = 0; $r < $kidsInSlot; $r++ ){
          if ( $this->getKid ( $kids['x'.$x.'y'.$y][$r] ) != '' ){
            $this->students_schedule['Days'][($x-1)]['Slots'][($y-1)]['Students'][] = $this->getKid( $kids['x'.$x.'y'.$y][$r] );
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
  * Get schedule students from json file
  * ALSO calculates the schedule students count
  * 
  * @uses $students_schedule_json_path, $students_schedule_count_json_path
  *
  * @param string $input
  * @return array|string array or empty string
  */
  function getStudentsSchedule(){
    
    // flag to force an empty student schedule count if the student schedule is false
    $this->emptyScheduleFlag = false;
    
    // Try to read Students Schedule JSON File
    $getJSONFileStudentsSchedule = $this->getJSONFile ( $this->students_schedule_json_path );
    
    // Students Schedule JSON not found or empty
    if ( !$getJSONFileStudentsSchedule ){
      // Students schedule not found or empty
      // Create empty one
      $this->createEmptyStudentsSchedule();
      
      // flag to create an empty student schedule count
       $this->emptyScheduleFlag = true;
    }// end if
    
    // Students Schedule JSON Found
    else{
      
      // Save Students Schedule
      $this->students_schedule = $getJSONFileStudentsSchedule;
      
      // Try to read the Students Schedule Count JSON file
      $getJSONFileStudentsScheduleCount = $this->getJSONFile ( $this->students_schedule_count_json_path );
      
      // Students Schedule Count JSON Found
      if ( $getJSONFileStudentsScheduleCount ){
        
        // if we just created an empty student schedule (check $this->emptyScheduleFlag above)
        // we have to create an empty student schedule count
        // even if there was a student schedule count JSON file with data in it (possibly from previous calculation where the student schedule JSON file being deleted or empty)
        if (  $this->emptyScheduleFlag ) {
          $this->students_schedule_count = json_encode ( $this->students_schedule_count );
          $this->saveStudentsCountScheduleToJSON ();
        }
        else{
          // get previously saved students schedule count
          $this->students_schedule_count = $getJSONFileStudentsScheduleCount;
        }
      }
      // Students Schedule JSON Count not found or empty
      else{
        // Create new one from Students Schedule
        $this->students_schedule_count = json_encode ( $this->students_schedule_count );
        $this->saveStudentsCountScheduleToJSON ();
      }// end else
    }// end else
    
  }// end function
  
  /**
  * Not implemented yet
  * Will update students schedules from user input (frontend) and recalculates everything
  */
  function recalculateSchedule(){
    /* foreach ($_POST['slots'] as $k=>$v)
    {
      $values = array(); 
      $array = explode(',',$v);     
      foreach ($array as $val){
        $values[$val] = $val;
      }
      $kid_index = 0;
      
      unset ( $this->schedule[$k]['kids']);
    
      if ( !empty ($values) )
      {
        foreach ($values as $new_kids_ids)
        {
          $this->schedule[$k]['kids'][$kid_index] = $this->kids[ ($new_kids_ids) ];
          $kid_index++;
        }
      }
    } */
  }// end function
  
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
    
    // send command with params
    $python_output = exec("$this->python_exec_path $this->python_script_path");
    
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
      $fp = fopen( $this->students_schedule_json_path, 'w');
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
      $fp = fopen( $this->settings_json_path, 'w');
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
      $fp = fopen( $this->students_schedule_count_json_path, 'w');
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
      $fp = fopen( $this->teachers_schedule_json_path, 'w');
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
      for ($x=1; $x<=7; $x++){
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
                $this->students_schedule_xn_format['x'.$day_number.'y'.$slot_number]['Students'] = array();
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
      for ($x=1; $x<=7; $x++){
        for ($y=1; $y <= $this->slot_total_count; $y++){
          $this->students_schedule_xn_format['x'.$x.'y'.$y]['Students'] = array();
        }
      }
      // resave the students schedule JSON file
      // because it had bad formatting
      $this->createEmptyStudentsSchedule();
    }
    
  }// end function
  
  /**
  * Read JSON File content
  *
  * @param string $json_file_path
  * @return array|string array from JSON data or empty string
  */
  function getJSONFile ( $json_file_path ){
    $output = '';
    
    clearstatcache();
    $fp = '';
    $created_file = 0;
    // Open JSON File (create if not exists 'w')
    if (!file_exists($json_file_path)) {
      $fp = fopen( $json_file_path, 'w');
      $created_file = 1;
    }
    // Get JSON file contents
    $json_file_content = file_get_contents ($json_file_path);
    if ( $created_file) 
      fclose($fp);
    
    if ( isset ($json_file_content) && !empty ($json_file_content) ){
      // fetch JSON content as array
      $json_result = json_decode ($json_file_content, true);
      if ( isset ($json_result) ){
        $output = $json_result;
      }
    }
    
    return $output;
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
      $this->createRandomStudentsSchedule();

      // Calculate new teachers schedule via python script
      $this->pythonDoTeachersSchedule();
    }
		// if user presses the Save Schedule button in frontend
    // we force recalculation of the teachers schedule via python
    elseif ( isset ($_POST['save_schedule']) && $_POST['save_schedule'] == 1 ){
      
      // create new random students schedule alongwith the respective students schedule count
      $this->saveNewStudentsSchedule();

      // Calculate new teachers schedule via python script
      $this->pythonDoTeachersSchedule();
    }
    else{
      
      // get existing students schedule and students schedule count data
      $this->getStudentsSchedule();
      
      // Attempt to find existing teachers schedule
      $json_result = $this->getJSONFile ( $this->teachers_schedule_json_path );
      if ( $json_result ){
        $this->teachers_schedule_json = json_encode($json_result);
      }
      else{
        // no existing teachers schedule found
        // Force a new calculation via python script
        
        if ( !$this->emptyScheduleFlag ) 
          $this->pythonDoTeachersSchedule();
      }
    }
    
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