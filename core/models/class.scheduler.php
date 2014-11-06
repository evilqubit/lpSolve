<?php
class Scheduler
{
  public $kids = array (
   1 => array ('id' => 1, 'name' => 'Patrick' ),
   2 => array ('id' => 2, 'name' => 'Joe' ),
   3 => array ('id' => 3, 'name' => 'John' ),
   4 => array ('id' => 4, 'name' => 'Jenny' ),
   5 => array ('id' => 5, 'name' => 'Carla' )
  );
  
  public $students_schedule = array();
  public $students_schedule_xn_format = array();
  public $students_schedule_count = array();
  public $emptyScheduleFlag = false;
  
  public $teachers_schedule_xn_format = array();  
  public $teachers_schedule = array();
  public $teachers_schedule_json = '';
  
  private $json_main_folder_name = 'json';
  private $python_main_folder_name = 'python';
  
  private $students_schedule_json_path = 'students_schedule.json';
  private $students_schedule_count_json_path = 'students_schedule_count.json';
  private $teachers_schedule_json_path = 'schedule-teachers.json';
  private $python_exec_path = 'C:\PYTHON33\python.exe';
  private $python_script_path = 'python-script.py';
  
  public $day_starting_time = '09:00'; // 9 AM
  public $day_end_time = 17; // 5 PM
  public $slot_time = 30; // 30 mins
  public $slot_total_count = '';
  
  function __construct(){
    
    // Set Paths for JSON/Python Files on server
    $this->setRealPaths();
    
    // Calculate slots' total count
    $this->calculateSlotTotalCount();
    
    // Recalculate the schedules if user press the Generate Random Schedule button
    $this->getSchedules();
    
    // format student array to x1y1, x1y2, format to be used on front end
    $this->saveFormatStudentsSchedule();
    
    // format teachers araay to x1y1, x1y2, format to be used on front end
    $this->saveFormatTeachersSchedule();
    
  }// end constructor
  /**
  * Set Paths for JSON/Python Files on server
  * 
  */
  function setRealPaths (){
    $this->students_schedule_json_path = realpath(dirname(__FILE__)).'/'.$this->json_main_folder_name.'/'.$this->students_schedule_json_path;
    
    $this->students_schedule_count_json_path = realpath(dirname(__FILE__)).'/'.$this->json_main_folder_name.'/'.$this->students_schedule_count_json_path;
    
    $this->teachers_schedule_json_path = realpath(dirname(__FILE__)).'/'.$this->json_main_folder_name.'/'.$this->teachers_schedule_json_path;
    
    $this->python_script_path = realpath(dirname(__FILE__)).'/'.$this->python_main_folder_name.'/'.$this->python_script_path;
  }// end function
  
  function getDayStartingTime(){
    return $this->day_starting_time;
  }
  
  function getDayEndTime(){
    return $this->day_end_time;
  }
  
  function getSlotTime(){
    return $this->slot_time;
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
    $this->slot_total_count = ($this->day_end_time - $this->day_starting_time) * 60 / $this->slot_time;
  }// end function
  
  /**
  * Creates empty teachers schedule and saves it in the teachers schedule JSON file
  * this is only used if the existing teachers was badly formatted or empty
  * 
  */
  function createEmptyTeachersSchedule (){
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
        $kidsInSlot = rand (0, 3);
        
        $this->students_schedule_count['Days'][($x-1)]['Slots'][] = array(
          'NumberOfStudents' => $kidsInSlot
        );
        
        $this->students_schedule['Days'][($x-1)]['Slots'][] = array(
          'Students' => array()
        );
        
        // save kids in this slot
        for ( $r = 0; $r < $kidsInSlot; $r++ ){
          $kid_id = rand (1, 5); // which kids
          if ( $this->getKid ($kid_id) != '' ){
            $this->students_schedule['Days'][($x-1)]['Slots'][($y-1)]['Students'][] = $this->getKid($kid_id);
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
    if (!file_exists($this->python_exec_path)) {
      exit("The python executable '$this->python_exec_path' does not exist");
    }
    if (!is_executable($this->python_exec_path)) {
      exit(("The python executable '$this->python_exec_path' is not executable"));
    }
    if (!file_exists($this->python_script_path)) {
      exit("The python script file '$this->python_script_path' does not exist");
    }
    
    // Send the Schedule Students Count as parameter to Python script
    $pythonParameter = json_encode($this->students_schedule_count);
    
    // send command with params
    $python_output = exec("$this->python_exec_path $this->python_script_path $pythonParameter");
    
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
      fwrite ($fp, json_encode($this->students_schedule) );
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
      $slot_number = 0;
      
      // if teachers json file is corrupt, we default to 0 values for all time slots
      $bad_json_format = 0;
      
      $this->teachers_schedule = json_decode( $this->teachers_schedule_json, true);
      
      if ( isset ($this->teachers_schedule['Days']) && !empty ($this->teachers_schedule['Days']) ){
        foreach ( $this->teachers_schedule['Days'] as $dayData ){
          $day_number++;
          
          $slots = ( isset($dayData['Slots']) && count($dayDaya['Slots']) == $this->slot_total_count ) ? $dayData['Slots'] : '';
          if ( $slots ){
            foreach ( $slots as $slot ){
              $slot_number++;
              
              $this->teachers_schedule_xn_format['x'.$day_number.'y'.$slot_number]['FullTime'] = ( isset($slot['FullTime']) ) ? $slot['FullTime'] : 0;
              $this->teachers_schedule_xn_format['x'.$day_number.'y'.$slot_number]['PartTime'] = ( isset($slot['PartTime']) ) ? $slot['PartTime'] : 0;
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
    if ( isset ($_POST['create_schedule']) && $_POST['create_schedule'] == 1 ){
      
      // create new random students schedule alongwith the respective students schedule count
      $this->createRandomStudentsSchedule();
      
      // Calculate new teachers schedule via python script
      $this->pythonDoTeachersSchedule();
    }
    else{
      
      // get existing students schedule and students schedule count data
      $this->getStudentsSchedule();
      
      // Attempt to find existing teachers schedule
      $json_result = $this->getJSONFile ( $this->teachers_schedule_json_path );
      if ( $json_result ){
        $this->teachers_schedule = $json_result;
      }
      else{
        // no existing teachers schedule found
        // Force a new calculation via python script
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
    return date('H:i', mktime(0, ($this->day_starting_time*60)+ ($this->slot_time*$iterator)));
  }
  
}// end class
?>