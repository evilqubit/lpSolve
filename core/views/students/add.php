<section class="block">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<h3>Add Student</h3>
				<?php
				if ( isset ($MAIN->returnData['message']) ){
					$alert_status = $MAIN->getMessageStatus();
					$alert_color = ($alert_status==1) ? 'alert-success' : 'alert-warning';
					?>
					<div class="alert <?php echo $alert_color;?>"><?php echo $MAIN->returnData['message'];?></div>
					<?php
				}?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<form id="hidden_form" method="POST" action="">
					<input type="hidden" name="save_students" value="1">
					<br>
					<a href="students"><div class="btn btn-primary">Back to all Students</div></a>
					<br><br>
					<div class="form-group">
						<label>Name</label>
						<input class="form-control" name="name" type="text" value="">
					</div>
					<button class="btn btn-success" type="submit" id="save_settings">Add Student</button>
				</form>
					
			</div><!-- col-md-12-->
		</div>
	</div>
</section>