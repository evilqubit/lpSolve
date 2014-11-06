<?php
function createKidLabel ($data){
  $output = "<div class='label label-primary ui-widget-header draggable' data-name='".strtolower($data['name'])."' data-id='$data[id]'>$data[name] #$data[id]";
  $output .= "<span class='close-me'>X</span></div>";
  
  if ( isset ($data['__return_']) )
    return $output;
  else
    echo $output;
}
?>
<section class="block">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        
        <!--<h3>Kids</h3>-->
        
        <div class="all-kids">
          <?php
            /* foreach ($home_data->kids as $kid ){
              createKidLabel ( $kid );
            } */
          ?>
        </div>
                              
        <form id="hidden_form" method="POST" action="">
          <br>
          <button class="btn btn-success" type="submit" id="random_schedule">Create Random Schedule</button>
          <input type="hidden" name="create_schedule" value="1">
          
          <table class="table table-bordered">
            <thead>
             <tr>
                <th>Slot</th>
                <th class="col-md-1">Monday</th>
                <th class="col-md-1">Tuesday</th>
                <th class="col-md-1">Wednesday</th>
                <th class="col-md-1">Thursday</th>
                <th class="col-md-1">Friday</th>
                <th class="col-md-1">Saturday</th>
                <th class="col-md-1">Sunday</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // starting slot time
              $old_slot_time = $home_data->day_starting_time;
              
              // iterate through all time Slots
              for ( $i = 1; $i <= $home_data->slot_total_count; $i++){
                // next slot time
                $new_slot_time = $home_data->getSlotTimeInIteration($i);
                ?>
                <tr>
                  <td class="col-md-1"><?php echo $old_slot_time.' - '.$new_slot_time;?></td>
                  <?php
                  $teachers_text = ' teachers';
                  
                  // iterate through 7 days
                  for ( $y = 1; $y <= 7; $y++){
                    $slot_key = 'x'.$y.'y'.$i;
                    
                    if ( isset ($home_data->students_schedule_xn_format[$slot_key]) && !empty($home_data->students_schedule_xn_format[$slot_key]['Students']) ){
                      $student_text = ''; $csv_ids = '';
                      $students_ids = array();
                      
                      $home_data->students_schedule_xn_format[$slot_key]['Students'] = ( isset($home_data->students_schedule_xn_format[$slot_key]['Students'][0]) && !empty($home_data->students_schedule_xn_format[$slot_key]['Students'][0] ) ) ? $home_data->students_schedule_xn_format[$slot_key]['Students'] : array();
                      
                      foreach ($home_data->students_schedule_xn_format[$slot_key]['Students'] as $student_data)
                      {
                        $csv_ids .= $student_data['id'].',';
                        // cant add same student to the same slot
                        // if by mistake we add the student twice to the same time slot
                        if ( !isset ( $students_ids[ $student_data['id'] ] ) )
                        {
                          $student_data['__return_'] = 1;
                          $student_text .= createKidLabel ($student_data, 'return');
                          $students_ids[$student_data['id']] = $student_data['id'];
                        }
                      }
                      $csv_ids = rtrim ($csv_ids, ',');
                      ?>
                      <td data-slot="<?php echo $slot_key;?>" data-ids="<?php echo $csv_ids;?>" class="col-md-1 ui-widget-header droppable"><?php echo $student_text;?>
                      
                      <span class="teacher_count label label-info"><?php echo $home_data->teachers_schedule_xn_format[$slot_key]['FullTime'].' fulltime '.$teachers_text;?></span>
                      <span class="teacher_count label label-info"><?php echo $home_data->teachers_schedule_xn_format[$slot_key]['PartTime'].' partime '.$teachers_text;?></span>
                      
                      <input type="hidden" name="slots[<?php echo $slot_key;?>]" value="<?php echo implode( ',', $students_ids);?>" />
                      </td>
                      <?php
                    }
                    else
                    {?>
                      <td data-ids="<?php echo $slot_key;?>" class="col-md-1 ui-widget-header droppable">
                        <span class="teacher_count label label-info">0 fulltime <?php echo $teachers_text;?></span>
                        <span class="teacher_count label label-info">0 partime <?php echo $teachers_text;?></span>
                        <input type="hidden" name="slots[<?php echo $slot_key;?>]" value="" />
                      </td>
                      <?php
                    }
                  }
                  ?>
                </tr>
                <?php
                $old_slot_time = $new_slot_time;
              }?>
          </table>
        </form>
          
      </div><!-- col-md-12-->
    </div>
  </div>
</section>