<section class="block">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<h3>Students</h3>
				<a href="students/add"><div class="btn btn-primary">Add</div></a>
				<?php
				if ( isset ($MAIN->returnData['message']) ){
					$alert_status = $MAIN->getMessageStatus();
					$alert_color = ($alert_status) ? 'alert-success' : 'alert-warning';
					?>
					<div class="alert <?php echo $alert_color;?>"><?php echo $MAIN->returnData['message'];?></div>
					<?php
				}?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped">
					<thead>
						<th>#</th>
						<th>Name</th>
					</thead>
					<?php
					$students = $MAIN->getStudents();
					
					$c = 0;
					if ( $students ){
						foreach ($students as $student){
							$c++;
							?>
							<tr>
								<td><?php echo $c;?></td>
								<td><?php echo $student['name'];?></td>
							</tr>
							<?php
						}
					}
					?>
				</table>
			</div><!-- col-->
		</div>
	</div>
</section>