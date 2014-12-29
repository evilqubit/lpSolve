<section class="block">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div id="left-panel">
					<h4 class="head">Students</h4>
					<div class="all-students">
						<?php $STUDENTS->createStudentsEntries();?>
					</div>
					<h4 class="head head-teachers">Teachers</h4>
					<div class="all-teachers">
						<?php $TEACHERS->createTeachersEntries();?>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<form id="homepage-form" method="POST" action="">
					<button class="btn btn-success" type="submit" id="save_schedule">Compute Schedule</button>
					<input type="hidden" name="save_schedule" value="0">
					<button class="btn btn-success hide_me" type="submit" id="toggle_students">Toggle Students</button>
					
					<table class="fixed table-bordered" id="table-schedule">
						<thead>
						 <tr>
								<th colspan="1" class="col-slot-head">Time</th>
								<th colspan="3" data-parttime-ids="">Monday</th>
								<th colspan="3" data-parttime-ids="">Tuesday</th>
								<th colspan="3" data-parttime-ids="">Wednesday</th>
								<th colspan="3" data-parttime-ids="">Thursday</th>
								<th colspan="3" data-parttime-ids="">Friday</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// starting time
							$old_starting_time = $MAIN->settings['day_starting_time'];
							$teachers_text = ' teachers';
							
							// iterate through all time Slots
							for ( $i = 1; $i <= $MAIN->slot_total_count; $i++){
								// next slot time
								$new_starting_time = $MAIN->getSlotTimeInIteration($i);
								?>
								<tr>
									<td colspan="1" class="col-slot-time"><?php echo $old_starting_time.'<br>'.$new_starting_time;?></td>
									<?php
									// iterate through the opening days
									for ( $y = 1; $y <= $MAIN->days_a_week; $y++){
										$slot_key = 'x'.$y.'y'.$i;
										
										if ( isset ($MAIN->students_schedule_xn_format[$slot_key]) && !empty($MAIN->students_schedule_xn_format[$slot_key]['Students']) ){
											$student_entries = ''; $csv_ids = '';
											$students_ids = [];
											
											$MAIN->students_schedule_xn_format[$slot_key]['Students'] = ( isset($MAIN->students_schedule_xn_format[$slot_key]['Students'][0]) && !empty($MAIN->students_schedule_xn_format[$slot_key]['Students'][0] ) ) ? $MAIN->students_schedule_xn_format[$slot_key]['Students'] : [];
											
											$slot_student_count = 0;
											foreach ($MAIN->students_schedule_xn_format[$slot_key]['Students'] as $student_data)
											{
												$csv_ids .= $student_data['id'].',';
												// cant add same student to the same slot
												// if by mistake we add the student twice to the same time slot
												$student_entries .= $STUDENTS->createStudentLabel ($student_data);
												$students_ids[$student_data['id']] = $student_data['id'];
												
												$slot_student_count++;
											}
											$csv_ids = rtrim ($csv_ids, ',');
											?>
											<td colspan="3" data-slot="<?php echo $slot_key;?>" data-teachers-ids="" data-students-ids="<?php echo $csv_ids;?>" class="ui-widget-header droppable">
												<div class="students-entries">
													<span class="students-head">Students (<span class="student-count"><?php echo $slot_student_count;?></span>)</span>
													<?php echo $student_entries;?>
												</div>
												<span class="teachers-stats label">
													<?php
													echo "<span class='teachers-fulltime-count'>".$MAIN->teachers_schedule_xn_format[$slot_key]['FullTime'].'</span> fulltime '.$teachers_text;
													echo "<br><br><span class='teachers-parttime-count'>".$MAIN->teachers_schedule_xn_format[$slot_key]['PartTime'].'</span> partime '.$teachers_text;
													?>
												</span>
												<input type="hidden" name="slots[<?php echo $slot_key;?>]" value="<?php echo implode( ',', $students_ids);?>" />
											</td>
											<?php
										}
										else
										{?>
											<td colspan="3" data-teachers-ids="" data-students-ids="" class="ui-widget-header droppable">
												<span class="teachers-stats label">
													<span class="teachers-fulltime-count">0</span> fulltime <?php echo $teachers_text;?>
													<br><br><span class="teachers-parttime-count">0</span> partime <?php echo $teachers_text;?>
												</span>
												<input type="hidden" name="slots[<?php echo $slot_key;?>]" value="" />
											</td>
											<?php
										}
									}
									?>
								</tr>
								<?php
								$old_starting_time = $new_starting_time;
							}?>
					</table>
				</form>
					
			</div><!-- col-md-12-->
		</div>
	</div>
</section>