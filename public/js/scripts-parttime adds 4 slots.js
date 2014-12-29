$(function() {
  
	$("#toggle_students").click(function(){
		// Toggle All students in all slots
		var mode = ( $(this).hasClass("hide_me") ) ? 1 : 0;
		
		if (mode==1) $(this).removeClass("hide_me");
		else $(this).addClass("hide_me");
		
		var form = $("#homepage-form"),
		studentsInForm = form.find (".student-entry");
		
		if ( mode == 1 ) studentsInForm.show();
		else studentsInForm.hide();
			
		return false;
	});
	
	$('.timeSlotPicker').datetimepicker({
		 timepicker: true,
		 datepicker: false,
		 format:'H:i'
	});
	
  $("#save_schedule").click (function(){
    $("input[name='save_schedule']").val("1");
    $("#homepage-form").submit();
  });
	
	function removeEntryFromTable(object){
		
		var currentEntry = object.parent('.draggable'),
		currentTD = object.parents('.droppable'),
		currentEntryTeacherMode = currentEntry.attr("data-teacher-mode"),
		currentEntryID = currentEntry.attr('data-id'),
		currentEntryType = currentEntry.attr('data-type'),
		tableSchedule = $("#table-schedule"),
		tableRows = tableSchedule.find("tbody tr"),
		currentColumnIndex = currentEntry.parents("td").index() + 1,
		currentColumnHead = tableSchedule.find("thead tr th:nth-child("+currentColumnIndex+")");
		parttimeIDsArray = currentColumnHead.attr("data-parttime-ids").split(',');
		
		// student case
		if ( currentEntryType == 'student' )
		{
			if ( !$("#save_schedule").hasClass("btn-warning") )
				$("#save_schedule").removeClass("btn-success").addClass("btn-warning");
			
			// remove the ID from the input field and the data-student-ids attribute
			// update the student counter text
				var ids = currentTD.attr( 'data-students-ids' ).split(','),
				idsInputField = currentTD.find('input'),
				student_listing_count = currentTD.find(".student-count"),
				student_count = (student_listing_count.html()) ? parseInt(student_listing_count.html()) : 0;
				
				ids = jQuery.grep(ids, function(value) {
					return value != currentEntryID;
				});
				currentTD.attr('data-students-ids', ids.toString());
				idsInputField.val ( ids.toString() );
				student_listing_count.html( student_count-1);
			//
			
			// remove the student entries box if no students are in it anymore
			if ( ids.length == 0){
				var studentsEntriesBox = currentTD.find(".students-entries");
				studentsEntriesBox.remove();
			}
			
			// remove the entry
			currentEntry.remove();
		}
		
		// teacher case
		else if (currentEntryType == 'teacher'){
			if ( currentEntryTeacherMode == 'fulltime' || currentEntryTeacherMode == 'vacation' || currentEntryTeacherMode == 'parttime' ){

				$.each (tableRows, function(a,b){
					var currentLoopTD = $(this).find ("td:nth-child("+currentColumnIndex+")"),
					teachersWithID = currentLoopTD.find (".teacher-entry[data-id='"+currentEntryID+"']"),
					teachersCount = '',
					teachersStatsDiv = '';

					if ( currentEntryTeacherMode == 'fulltime'){
						teachersStatsDiv = currentLoopTD.find(".teachers-fulltime-count");
						teachersCount = parseInt(teachersStatsDiv.html());
						teachersStatsDiv.html ( teachersCount + 1 );
					}
					
					var teacherIDs = currentLoopTD.attr('data-teachers-ids').split(',');
					
					if ( currentEntryTeacherMode == 'parttime' ){
						parttimeIDsArray = jQuery.grep(parttimeIDsArray, function(value){
							return value != currentEntryID;
						});
						currentColumnHead.attr("data-parttime-ids", parttimeIDsArray.toString());
						
						if ( $.inArray( currentEntryID, teacherIDs) != -1 ){
							teachersStatsDiv = currentLoopTD.find(".teachers-parttime-count");
							teachersCount = parseInt(teachersStatsDiv.html());
							teachersStatsDiv.html ( teachersCount + 1 );
							
							// update total teacher hour count
							var totalHoursObj = $(".all-teachers .teacher-entry[data-id='" +currentEntryID + "'] .total-hours span"),
							oldHours = parseInt(totalHoursObj.html());
							totalHoursObj.html (oldHours-1);
						}
					}
					else{
						if ( currentEntryTeacherMode == 'fulltime' ){
							// update total teacher hour count
							var totalHoursObj = $(".all-teachers .teacher-entry[data-id='" +currentEntryID + "'] .total-hours span"),
							oldHours = parseInt(totalHoursObj.html());
							totalHoursObj.html (oldHours-1);
						}
					}
					// remove the teacher entry
					teachersWithID.remove();
					
					var teacherIDs = jQuery.grep(teacherIDs, function(value) {
						return value != currentEntryID;
					});
					currentLoopTD.attr('data-teachers-ids', teacherIDs.toString());
					if ( teacherIDs.length == 0){
						var teachersEntriesBox = currentLoopTD.find(".teachers-entries");
						teachersEntriesBox.remove();
					}
				});
			}
		}
	}
  $(".draggable").draggable({
    prependTo: "body",
    helper: "clone",
		revert: 'invalid'
  })
  .on('click', '.close-me', function(){
		var draggable = $(this);
		removeEntryFromTable (draggable);
  });
 
  $( ".droppable" ).droppable({
    hoverClass: "ui-state-active",
    drop: function( event, ui ) {
      
      var currentTD = $(this),
			currentColumnIndex = currentTD.index() + 1, // +1 because the first td is for Slot hours > 7:00-8:00, etc...
			dropabbleRow = currentTD.parents("tr"),
			droppableRowIndex = dropabbleRow.index() + 1, // +1 to be able to user nth-child(1), nth-child(2)
			tableSchedule = $("#table-schedule"),
			tableRows = tableSchedule.find("tbody tr"),
			totalSlots = tableRows.length,
			tableHead = $("#table-schedule thead tr"),
			currentColumnHead = tableHead.find("th:nth-child("+currentColumnIndex+")"),
			parttimeIDs = currentColumnHead.attr("data-parttime-ids"),
			currentEntry = ui.draggable,
      currentEntryID = currentEntry.attr('data-id'),
			currentEntryType = currentEntry.attr('data-type'),
			teacherModePicker = currentEntry.find(".teacher-mode-picker"),
			teacherModePickerSelected = teacherModePicker.find("option:selected"),
      studentsIDs = ( $(this).attr('data-students-ids') != '' ) ? $(this).attr('data-students-ids') : 'empty';

			if ( currentEntryType == "teacher" ){
				var teacher_mode = (teacherModePickerSelected) ? teacherModePickerSelected.val() : '';
				if ( teacher_mode == '' )
					return false;
				
				if ( teacher_mode == 'fulltime' || teacher_mode == 'vacation'){
					// when added, push item to each slot in the entire day where the slot is dropped
					$.each (tableRows, function(a,b){
						var currentLoopTD = $(this).find ("td:nth-child("+currentColumnIndex+")"),
						entries_listing_div = ( currentLoopTD.find('.teachers-entries') != '' ) ? currentLoopTD.find('.teachers-entries') : '',
						teacher_ids = currentLoopTD.attr('data-teachers-ids'),
						teacher_ids_array = teacher_ids.split(','),
						parttimeIDsArray = parttimeIDs.split(',');
							
						if ( $.inArray( currentEntryID, parttimeIDsArray) == -1 && $.inArray (currentEntryID, teacher_ids_array) == -1 )
						{
							if ( entries_listing_div.length == 0 )
								currentLoopTD.prepend("<div class='teachers-entries'><span class='teachers-head'>Teachers</span></div>");
							
							entries_listing_div = currentLoopTD.find('.teachers-entries');
							
							// important - add a clone of the same element so we click its X again
							var clone = currentEntry.clone();
							clone.append("<span class='teacher-mode'>("+teacher_mode+")</span");
							clone.addClass("teacher-mode-"+teacher_mode);
							clone.attr("data-teacher-mode",teacher_mode);
							entries_listing_div.append(clone);
							entries_listing_div.find("select").remove();
							entries_listing_div.find(".total-hours").remove();
							clone.draggable({
								prependTo: "body",
								helper: "clone",
								revert: 'invalid'
							})
							.on('click', '.close-me', function(){
								var draggable = $(this);
								removeEntryFromTable (draggable);
							});
							
							if ( teacher_mode == 'fulltime') {
								// update total hours count for original label
								var totalHoursObj = $(".all-teachers .teacher-entry[data-id='" +currentEntryID + "'] .total-hours span"),
								oldHours = parseInt(totalHoursObj.html());
								console.log (oldHours);
								totalHoursObj.html (oldHours+1);
								// update teacher needed count
								// no stats for vacation, so we check if this is a fulltime teacher
								var fulltimeStatsDiv = currentLoopTD.find(".teachers-fulltime-count"),
								fulltimeStats = parseInt(fulltimeStatsDiv.html());
								fulltimeStatsDiv.html (fulltimeStats-1);
							}
							
							var extra = ( teacher_ids != '' ) ? ',' : '';
							currentLoopTD.attr('data-teachers-ids', teacher_ids + extra + currentEntryID);
						}
						else{ 
							currentLoopTD.addClass('ui-state-error');
							setTimeout( function(){
								currentLoopTD.removeClass('ui-state-error');
							},1000);
						}
					});
				}
				if ( teacher_mode == 'parttime'){
						
					// when added, push item to each slot in the entire day where the slot is dropped
					var rowCounter = 0,
					parttimeCounter = 4;
					
					// in parttime, we check all tds once for the ids (to prevent adding the same parttime teachers in other slots
					// but we only add 4 entries (parttime) at a time
					var parttimeDuplicateCheck = 0;
						
					$.each (tableRows, function(a,b){
						
						rowCounter++;
						// minimum 4 slots have to be available to add parttime student
						if ( parttimeCounter > 0 && rowCounter >= droppableRowIndex && droppableRowIndex <= (totalSlots-3) )
						{
							parttimeCounter--;
							
							var currentLoopTD = $(this).find ("td:nth-child("+currentColumnIndex+")"),
							entries_listing_div = ( currentLoopTD.find('.teachers-entries') != '' ) ? currentLoopTD.find('.teachers-entries') : '',
							teacher_ids = currentLoopTD.attr('data-teachers-ids'),
							teacher_ids_array = teacher_ids.split(','),
							// we check all IDs, special case for parttime teachers
							parttimeIDsArray = parttimeIDs.split(',');
							
							if ( ( $.inArray( currentEntryID, parttimeIDsArray) == -1 && $.inArray (currentEntryID, teacher_ids_array) == -1 ) || parttimeDuplicateCheck == 1 )
							{
								parttimeDuplicateCheck = 1;
								if ( entries_listing_div.length == 0 )
									currentLoopTD.prepend("<div class='teachers-entries'><span class='teachers-head'>Teachers</span></div>");
								
								entries_listing_div = currentLoopTD.find('.teachers-entries');
								
								// important - add a clone of the same element so we click its X again
								var clone = currentEntry.clone();
								clone.append("<span class='teacher-mode'>("+teacher_mode+")</span");
								clone.addClass("teacher-mode-"+teacher_mode);
								clone.attr("data-teacher-mode",teacher_mode);
								entries_listing_div.append(clone);
								entries_listing_div.find("select").remove();
								entries_listing_div.find(".total-hours").remove();
								
								clone.draggable({
									prependTo: "body",
									helper: "clone",
									revert: 'invalid'
								})
								.on('click', '.close-me', function(){
									var draggable = $(this);
									removeEntryFromTable (draggable);
								});
								
								// update total hours count for original label
								var totalHoursObj = $(".all-teachers .teacher-entry[data-id='" +currentEntryID + "'] .total-hours span"),
								oldHours = parseInt(totalHoursObj.html());
								console.log (oldHours);
								totalHoursObj.html (oldHours+1);
								
								var fulltimeStatsDiv = currentLoopTD.find(".teachers-parttime-count"),
								fulltimeStats = parseInt(fulltimeStatsDiv.html());
								fulltimeStatsDiv.html (fulltimeStats-1);
								
								var extra = ( teacher_ids != '' ) ? ',' : '',
								newTeachersIDs = teacher_ids + extra + currentEntryID;
								currentLoopTD.attr('data-teachers-ids', newTeachersIDs);
								
								var extra = ( parttimeIDs != '' ) ? ',' : '';
								if ( $.inArray( currentEntryID, parttimeIDsArray) == -1 ){
									parttimeIDs += extra + currentEntryID;
									currentColumnHead.attr("data-parttime-ids", parttimeIDs);
								}
							}
							else{ 
								currentLoopTD.addClass('ui-state-error');
								setTimeout( function(){
									currentLoopTD.removeClass('ui-state-error');
								},1000);
							}
						}
						else{
							parttimeDuplicateCheck = 0;
						}
					});
				}
			}
			// student case
			else{
				
				if ( !$("#save_schedule").hasClass("btn-warning") )
					$("#save_schedule").removeClass("btn-success").addClass("btn-warning");
				
				// check if this student is already in this time slot
				if ( studentsIDs || studentsIDs == 'empty' )
				{
					studentsIDs = ( studentsIDs == 'empty' ) ? '' : studentsIDs;
					var myIdsArray = studentsIDs.split(',');
					if ( $.inArray( currentEntryID, myIdsArray) == -1 ){
						
						var entries_listing_div = ( currentTD.find(".students-entries") != '' ) ? currentTD.find(".students-entries") : '';
						
						if ( entries_listing_div.length == 0 ){
							currentTD.prepend("<div class='students-entries'><span class='students-head'>Students (<span class='student-count'></span>)</span></div>");
						}
						entries_listing_div = currentTD.find('.students-entries');
						
						var student_listing_count = currentTD.find(".student-count"),
						student_count = (student_listing_count.html()) ? parseInt(student_listing_count.html()) : 0;
						student_listing_count.html( student_count+1);
						
						// important - add a clone of the same element so we click its X again
						var clone = currentEntry.clone();
						entries_listing_div.append(clone);
						clone.draggable({
							prependTo: "body",
							helper: "clone",
							revert: 'invalid'
						})
						.on('click', '.close-me', function(){
							var draggable = $(this);
							removeEntryFromTable (draggable);
						});
						
						entries_listing_div.find(".student-entry").show();
						
						var extra = ( studentsIDs != '' ) ? ',' : '';
						currentTD.attr('data-students-ids', studentsIDs + extra + currentEntryID);
						
						var input_object = currentTD.find('input');
						extra = ( input_object.val() != '' ) ? ',' : '';
						input_object.val ( input_object.val() + extra + currentEntryID );
					}
					else{ 
						currentTD.addClass('ui-state-error');
						setTimeout( function(){
							currentTD.removeClass('ui-state-error');
						},1000);

					}
				}
			}
    }
  });
  
});

$(window).scroll(function(){
	var offset = $("body").scrollTop();
	if ( offset >= 50 )
		$("#left-panel").css({top:"10px", height: "95%"});
	else
		$("#left-panel").css({top:"auto"});
});