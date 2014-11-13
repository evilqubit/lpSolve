<section class="block">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				
				<h3>Settings</h3>
				
				<?php
				if ( isset ($settings_data->returnData['message']) ){
					$alert_status = ( isset ($settings_data->returnData['message_status']) ) ? $settings_data->returnData['message_status'] : 1;
					$alert_color = ($alert_status) ? 'alert-success' : 'alert-warning';
					?>
					<div class="alert <?php echo $alert_color;?>"><?php echo $settings_data->returnData['message'];?></div>
					<?php
				}
				?>
									
				<form id="hidden_form" method="POST" action="">
					<br>
					<button class="btn btn-success" type="submit" id="save_settings">Save Settings</button>
					<br><br>
					<div class="form-group">
						<label>Starting Hour</label>
						<input class="form-control timeSlotPicker" name="day_starting_time" type="text" value="<?php echo $settings_data->settings['day_starting_time'];?>">
					</div>
					<div class="form-group">
						<label>Ending Hour</label>
						<input class="form-control timeSlotPicker" name="day_ending_time" type="text" value="<?php echo $settings_data->settings['day_ending_time'];?>">
					</div>
					<div class="form-group">
						<label>Slot Time (in minutes)</label>
						<input class="form-control" name="day_slot_time" type="text" value="<?php echo $settings_data->settings['day_slot_time'];?>">
					</div>
					<div class="form-group">
						<label>Max kids per teacher</label>
						<input class="form-control" name="max_kids_per_teacher" type="text" value="<?php echo $settings_data->settings['max_kids_per_teacher'];?>">
					</div>
					<div class="form-group">
						<label>Fulltime available teachers</label>
						<input class="form-control" name="fulltime_available_teachers" type="text" value="<?php echo $settings_data->settings['fulltime_available_teachers'];?>">
					</div>
					<div class="form-group">
						<label>Parttime available teachers</label>
						<input class="form-control" name="parttime_available_teachers" type="text" value="<?php echo $settings_data->settings['parttime_available_teachers'];?>">
					</div>
				</form>
					
			</div><!-- col-md-12-->
		</div>
	</div>
</section>