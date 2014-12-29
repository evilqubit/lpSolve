<section class="block">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<h3>Settings</h3>
				<?php
				if ( isset ($MAIN->returnData['message']) ){
					$alert_status = $MAIN->getMessageStatus();
					$alert_color = ($alert_status == 1) ? 'alert-success' : 'alert-warning';
					?>
					<div class="alert <?php echo $alert_color;?>"><?php echo $MAIN->returnData['message'];?></div>
					<?php
				}?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<form id="hidden_form" method="POST" action="" enctype="multipart/form-data">
					<br>
					<button class="btn btn-success" type="submit" id="save_settings">Save Settings</button>
					<br><br>
					<div class="form-group">
						<label>Import Schedule (JSON format only)</label>
						<input class="form-control" name="json_schedule_import" type="file" value="">
					</div>
					<div class="form-group">
						<label>Starting Hour</label>
						<input class="form-control timeSlotPicker" name="day_starting_time" type="text" value="<?php echo $MAIN->settings['day_starting_time'];?>">
					</div>
					<div class="form-group">
						<label>Ending Hour</label>
						<input class="form-control timeSlotPicker" name="day_ending_time" type="text" value="<?php echo $MAIN->settings['day_ending_time'];?>">
					</div>
					<div class="form-group">
						<label>Slot Time (in minutes)</label>
						<input class="form-control" name="day_slot_time" type="text" value="<?php echo $MAIN->settings['day_slot_time'];?>">
					</div>
					<div class="form-group">
						<label>Max kids per teacher</label>
						<input class="form-control" name="max_kids_per_teacher" type="text" value="<?php echo $MAIN->settings['max_kids_per_teacher'];?>">
					</div>
					<div class="form-group">
						<label>Fulltime available teachers</label>
						<input class="form-control" name="fulltime_available_teachers" type="text" value="<?php echo $MAIN->settings['fulltime_available_teachers'];?>">
					</div>
					<div class="form-group">
						<label>Parttime available teachers</label>
						<input class="form-control" name="parttime_available_teachers" type="text" value="<?php echo $MAIN->settings['parttime_available_teachers'];?>">
					</div>
				</form>
					
			</div><!-- col-md-12-->
		</div>
	</div>
</section>