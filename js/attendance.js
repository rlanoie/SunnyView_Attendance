$(function() {
	/*ADD NEW ATTENDEE --------------------------------------------------------------------*/
		/*Gather input from the attendance form and submit to attendance php addAttendee function*/
  	$("#btn_AddAttendee").click(function() {
			 insertAttendee();
			return false;
		});
	
	/*VIEW ATTENDEE DETAILS --------------------------------------------------------------------*/
		$(".modalOpen_Attendee").click(function() {
			//alert('test');
		});
		$(".ignoreAbsence").click(function() {
			//alert('test');
		})
	//OPEN ABSENCE APPROVAL MODAL*/
		/*Calls the modal absence open function*/
		$('#modalOpen_VerifyAbsence').click(function(e) {
			e.preventDefault();
					e.stopImmediatePropagation();
			var message = {};
			message.type = 'open';
			message.text = 'There are no pending absences to verify at this time.'
			var ajax = modal_AbsenceOpen(message);
			return false;
		});
});
//PAGE FUNCTIONS ----------------------------------------------------

			function clearHID(){
				$('#formResHID').val('');	
			}
		//--FORM DATE CHANGE --------------------------------------------------
		  /*When the date is changed call AJAX to update the gridlist attendance
			  roster to show the recorded residents for the new date.*/
	  		function dateHandler(e){	
  	 				var filterDate = e.target.value;
			  		var thisDate = JSON.stringify(filterDate);
				  	var method = "dateChange";
					  var data = {method: method, frmDate: thisDate};
							data.phpFunction = 'attendanceFunctions';
					
					  var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
					
						/*Based on the current status of the attendance. If the attendance is not open lock the form fields to prevent changes
         			 update the gridlist to displaythe attendees recorded for the new date.*/
						thisAjax.done(function(response_in){ 
														var data = JSON.parse(response_in);
														/*if the attendance status is closed or has not been started 
															do not check for user permissions and lock the form.*/
															var statusResults = data.status;
															resetFormField("#formResHID", formResHID);
															formatChanges(statusResults);
															document.getElementById("headerDate").innerHTML = "Attendance Date: " + dateFormatting(filterDate);
															//get the size of the returned data.query
															var array = data.query.row;
															var rowCount = data.query.count;
											
															//delete the elements in #results div
															deleteDivID('gridlist');
															/*if entries already exist for the current attendance date then
															display them in the #results div*/
															attendanceGridList(array, rowCount, '');
						});
						var missingAjax = getMissing();
						missingAjax.then(function(response_in){
							var data = JSON.parse(response_in);
							var array = data.row;
							var length = data.count;
							document.takeAttendance.residentID.options.length=0;
							var master = document.takeAttendance.residentID;
							for (var x=0; x<length; x++){
    						master.options[residentID.options.length]=new Option(array[x].resident, array[x].id);
							}
							
						})
						return false;
				  }
		//ATTENDANCE ROSTER LIST REFRESH - 
			/*Update the attendance roster div (#gridlist) containing the residents who
				have been accounted for during the attendance with changes that have taken place to the roster.*/
				function attendanceGridList(array_in, length, count_in){
					var node;
					var textnode;
					var wrapper, hyperlink, recordedDate;
					var count=count_in;
					for (var i = 0; i < length; i++) {
						count++;
						hyperlink = $('<a>', {'href':'#', 'class':'modalOpen_Attendee', 'data-toggle':'modal'}).appendTo('#gridlist');
						wrapper = $('<div>',{'class':'row rowResults'}).appendTo(hyperlink);
						$('<div>',{'class':'col-sm-1 rowCount','html':(count)}).appendTo(wrapper);
						//console.log(array_in[i].resident + " - " + (count));
						$('<div>',{'class':'col-sm-3 rowId','hidden':'','html':(array_in[i].ResID)}).appendTo(wrapper);
						recordedDate = dateFormatting((array_in[i].timestamp).substring(0, 11));
						$('<div>',{'class':'col-sm-4 row_recorded pdf-print-only','html':recordedDate + "&emsp;  " + (array_in[i].timestamp).substring(11, 19)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-2 timestamp pdf-noPrint','html':(array_in[i].timestamp).substring(11, 19)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-3 row_resident','html':(array_in[i].resident)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-2 row_status','html':(array_in[i].code)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-3 row_employee','html':(array_in[i].employee)}).appendTo(wrapper);
					}
				}
		//ATTENDANCE MISSING FROM ROSTER LIST REFRESH - 
			/*Updates the list of unaccounted residents
				Unaccounted for residents includes anyone who has not been reported as present, vacation, doctor etc.*/
				function missingGridlist(array_in, length){
					var hyperlink, row, rowCount, colID, colResident;
					var count = 0;
					for (var x = 0; x < length; x++){
						count++;
						hyperlink = $('<a>',{'href':'#'}).appendTo('#gridlist_Missing');
						row = $('<div>',{'class':'row rowResults'}).appendTo(hyperlink);
						rowCount = $('<div>',{'class':'col-sm-2 rowCount','html':(count)}).appendTo(row);
						//colID = $('<div>',{'class':'col-sm-3','html':(array_in[x].id)}).appendTo(row);	
						colResident = $('<div>',{'class':'col-sm-4','html':(array_in[x].resident)}).appendTo(row);	
					}
				}
		//ATTENDANCE.php APPEARANCE -------------------------------------------------
			/*Alter the appearance of attendance.php page based on the status of the
				attendance if it started, not started, or closed AND the user's permission's for 
				processing the attendance.*/	
				function formatChanges(changeFactor){
					switch (changeFactor) {
					case 'started':  
						getUserPermission('attendance', permission());	//redirect to the admin page
						addGrey('startClick', 'closed');
						removeGrey('closeClick', 'closed');
						removeGrey('gridlist', 'closed');
						removeGrey('modalOpen_VerifyAbsence', 'closed');
						document.getElementById("displayStatus").innerHTML = "In Progress";
						document.getElementById("headerStatus").innerHTML = "Status: In Progress";
						break;
					case 'no': //lock all fieldsets to make the form readonly
						disable('#disableFilter');
						removeGrey('startClick', 'closed');
						addGrey('closeClick', 'closed');
						addGrey('gridlist', 'team-row sectionResults closed');
						addGrey('modalOpen_VerifyAbsence', 'closed');
						document.getElementById("displayStatus").innerHTML = "Not Started";
						document.getElementById("headerStatus").innerHTML = "Not Started";
						break;
					case 'closed'://unlock all fieldsets to allow user to write to the page.  
						disable('#disableFilter');
						addGrey('startClick', 'closed');
						addGrey('closeClick', 'closed');
						addGrey('gridlist', 'team-row sectionResults closed');	
						addGrey('modalOpen_VerifyAbsence', 'closed');
						document.getElementById("displayStatus").innerHTML = "Completed";
						document.getElementById("headerStatus").innerHTML = "Status: Completed";
						break;
					}
			}
		//GNERATE REPORT
				/*Creates a new page with the attendance details in print mode*/
				function generateReport_absence(){
					var ajaxmissing = getMissing();
					var ajax = getAttendanceList();
					$.when(ajaxmissing, ajax).then(function(){ printByTag('sectionBody');});
					return false;
				}
	//MODAL FORMATTING
			//OPEN ABSENCE MODAL
			/*	Refreshes the absence list and then opens the modal absence */
				function modal_AbsenceOpen(message){
					var attendanceStatus = document.getElementById("displayStatus").innerHTML;
						if (attendanceStatus === ''){
							alert ('Unable to verify Absences! Attendance has not been started!')
							return false;
						} else if (attendanceStatus === 'Completed'){
							alert ('Unable to make changes! Attendance is Closed.')
							return false;
						}else{
							var ajaxresults = getPendingAbsences();
							ajaxresults.then(function(response_in){
												var data = JSON.parse(response_in);
												var array = data.row;
												var length = data.count;
												if(length === 0){
													if(message.type === 'open'){
														alert(message.text);	
													}else{
														return false;
													}
												}else{
													//delete current contents of missing residents div
													deleteDivID('gridlist_Modal');
													//add list of missing residents to the attendance page
													gridlist_ModalAbsence(data.row, length);
													$('#modal_AbsenceVerification').modal('show');													
												}
											});
							return ajaxresults;
						}
					return false;
				}
			/*Process response to opening the absence modal from the attendance page.*/
				function modalAbsenceOpen_Post_AjaxResponse(response_in){
					var data = JSON.parse(response_in);
					var length = data.count;
					deleteDivID('gridlist_Modal');
					gridlist_ModalAbsence(data.row, length);
					$('#modal_AbsenceVerification').modal('show');
				}
			/*ABSENCE MODAL LIST REFRESH - 
				Update the appearance of the absence modal on the attendance.php page.				
				Adds new absences to the list of absences on the absence modal by appending them to the 
				#gridlist_Modal in the absence Modal.*/
				function gridlist_ModalAbsence (array_in, length){
					var wrapper, element;
					var next, column1, square, checkbox, checkboxLabel, columRowID, columnResident, ignore, columnReasonID, columnCode, columnLeaving, columnReturning;
					var columnDateRange;
					var clickID, clickElement;
					element = 1;
					for (var i = 0; i < length; i++) {
						wrapper = $('<div>', {'data-toggle':'modal'}).appendTo('#gridlist_Modal');
						
						next = $('<div>', {'class':'row rowResults rowResID_' + array_in[i].resID}).appendTo(wrapper);
						column1 = $('<div>', {'class':'col-sm-1 selected'}).appendTo(next);
						square = $('<div>', {'class':'square'}).appendTo(column1);
						checkbox = $('<input>', {'type':'checkbox','class':'absenceConfirmation', 'name':'absenceConfirm', 'id':'resCheckbox_'+array_in[i].resID, 'value':array_in[i].resID}).appendTo(square);
						checkboxLabel = $('<label>', {'for':'resCheckbox_'+array_in[i].resID, 'id':'labelRResident', 'class':'checkable' }).appendTo(square);
						columRowID = $('<div>', {'class':'col-sm-1 rowId', 'hidden':'', 'html':array_in[i].resID }).appendTo(next);
						columnResident = $('<div>', {'class':'col-sm-9 row_resident bold', 'html':array_in[i].resident}).appendTo(next);
						ignore = $('<a>', {'href':'#','id':'ignoreAbsence'+array_in[i].resID,'class':'col-sm-2 row_Ignore italic', 'hidden':'','html':"Ignore"}).appendTo(next);
							//Add click event to the ignore hyperlink
								clickID = 'ignoreAbsence'+array_in[i].resID;
								clickElement = document.getElementById(clickID);
								clickElement.addEventListener("click",test,false);
						
						columnReasonID = $('<div>',{'class':'col-sm-3 row_reasonID','html':array_in[i].reasonID, 'hidden':''}).appendTo(next);
						columnCode = $('<div>', {'class':'col-sm-3 italic row_reason', 'html':array_in[i].Code}).appendTo(next);
						columnLeaving = $('<div>', {'class':'col-sm-7 row_leaving','hidden':'' ,'html':array_in[i].Leaving}).appendTo(next);
						columnReturning = $('<div>', {'class':'col-sm-7 row_returning','hidden':'' ,'html':array_in[i].Returning}).appendTo(next);
						
						/*If returning is null then only display the leaving date.*/
						columnDateRange;
							if(array_in[i].Returning===null){
								columnDateRange= $('<div>', {'class':'col-sm-7 row_dates italic','html':array_in[i].Leaving}).appendTo(next);	
							}else{
								columnDateRange= $('<div>', {'class':'col-sm-7 row_dates italic','html':array_in[i].Leaving + " - " + array_in[i].Returning}).appendTo(next);
							}
						}
				}

			/*VERIFY ABSENCES FORMATTING - 
				Takes in an array of residents and checks if any of those residents match
				the absences on the attendance page to avoid duplicate entry.  
				
				If a resident is on the attendance roster and an absence is recorded for the same
				date the staff will be unable to approve the absence and the record is highlighted on the absence modal with an error message.
				
				Residents that do not have a duplicate entry will be added to the attendance roster and removed from the modal.
				using an AJAX call.*/
				function verifyAbsence(array_in){
					var arrayCount = array_in.count;
					var array = array_in.row;
					var errorlist = [];
					var absenceApprovedList = [];
					
					/*Collect the information from all absence confirmation checkboxes from the absence confirmation modal */
					var inputs = document.getElementsByClassName("absenceConfirmation");
          
          var elementID, checked;
          var reasonID, leaving, returning, userID;
					/*For each input get the checkbox ID and verify the box has been checked.*/
					for (var i = 0; i < inputs.length; i++) {
						elementID = 'resCheckbox_'+inputs[i].value;
						checked = document.getElementById(elementID).checked;
						if(checked ===true){
							//$( "#resCheckbox_"+inputs[i].value ).parent().parent().siblings(".row_code").css( "background", "yellow" );
							reasonID = $( "#resCheckbox_"+inputs[i].value ).parent().parent().siblings(".row_reasonID").html();
							leaving = $( "#resCheckbox_"+inputs[i].value ).parent().parent().siblings(".row_leaving").html();
							returning = $( "#resCheckbox_"+inputs[i].value ).parent().parent().siblings(".row_returning").html();
							userID = $( "#formUserID").val();
							//check if any residents have been recorded for the current roster.
							if(arrayCount > 0){
								/*Compare the marked checkboxes to the residents who have been marked as present
								during roll call.*/
								let obj = array.find(o => o.ResID === inputs[i].value);
								/*Build two arrays.
								ErrorList Array - List of residents who have been accounted for during roll call.
								absenceApprovedList Array - List of residents who's absences have been confirmed.'*/
								if(obj !== undefined){
									errorlist.push(obj);
								}else{
									absenceApprovedList.push({residentID: inputs[i].value,
																					formBeginning: leaving,
																					formReturning: returning,
																					reasonID:  reasonID,
																					formUserID: userID});
								}
							}else{
							  //add's a resident to approved array if no entries for the attendance have been recorded yet.
								absenceApprovedList.push({residentID: inputs[i].value,
																					formBeginning: leaving,
																					formReturning: returning,
																					reasonID:  reasonID,
																					formUserID: userID});
							}
							
						}	
					}
					var data = {};
					data.phpFunction = 'attendanceFunctions';
					
          var frmUserID = $("#formUserID").val();
				  if (userID === ""){
					  return false;
				  }else{
					  if(Number.isNaN(Number(userID))){
						  return false;
					  }else{
						  data.userID = frmUserID;
					  }
				  }
          
          
          var token = $("#token").val();
          if (token === ""){
					  return false;
				  }else{
					  data.token = token;
				  }	
          
          
					if(absenceApprovedList.length > 0){
						if($("#frmDate").val()!==''){
							data.frmDate = $("#frmDate").val();	
						}
						data.method = 'absenceApproval';
						data.approved = absenceApprovedList;
            
						var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
						/*Get approved absences and update the absence page. Remove the absence from the Attendance Absence Approval Modal
						Add absence to the gridlist Absence div that holds all residents recorded on the attendance roster.*/
						thisAjax.then(function(response_in){
                            if (response_in === 'Error'){
                              alert('An unknown error has occurred.  Please log out and try again.');
                            }else if(response_in !== 'credentialError'){
															var data = JSON.parse(response_in);
															var arrayappend = data.row;
															var row;
							
															//get count from the last child of gridlist used to keep sequential numbering
															var seqCount = $( "#gridlist").children("a:last-child").children().children(".rowCount").html();
															if(seqCount === undefined){
																seqCount = 0;
															}
															//add absences to the bottom of the grid.
							 								attendanceGridList(arrayappend, arrayappend.length, seqCount);	
															for (var x = 0; x < data.length; x++) {
					  										row = data[x].row;
																for (var y = 0; y < row.length; y++) {
																	//Remove the abence approval for this resident from the modal.
																	$( "div" ).remove( ".rowResID_" + row[y].ResID); 
																}
															}
                }else{
                  window.location.assign('../index.html');
                }
						});
					}
					
					if(errorlist.length > 0){
						document.getElementById("modalAbsenceAlert").innerHTML = "DUPLICATE RECORDINGS! <br /> The highlighted residents have already been recorded on the attendance roster.";
						for (var x = 0; x < errorlist.length; x++) {
							$( ".rowResID_"+errorlist[x].ResID).css( "background", "yellow" );
						}	
					//if no errors exist, close the modal.
					}else{
						$('#modal_AbsenceVerification').modal('hide');	
					}
					return false;					
				}
//ATTENDANCE FUNCTIONS ----------------------------------------------------
		//ATTENDANCE STATUS -------------------------------------------------
			/*Get an update on the status of the Attendance process? - check if the attendance has been started for 
				the day using php function attendanceStatus_Update*/
				function getAttendanceStatus(date_in){
					var method = "returnAttendanceStatus";
					var data = {method: method};
					data.attendanceDate = date_in;
					data.phpFunction = 'attendanceFunctions';
					var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
					/*lock all form fields if attendance has not been started*/
					thisAjax.done(function(response_in){
													var data = response_in;
													formatChanges(data);
					});
					return thisAjax;
				}
			/*Gets the list of people recorded on the attendance roster for the date on the attendance form.*/
				function getAttendanceList(){
					var dataAttendance = {};
					if($('#frmDate').val()!==''){
						dataAttendance.attendanceDate = $('#frmDate').val();
					}
						dataAttendance.method = "getAttendanceList";
						dataAttendance.phpFunction = 'attendanceFunctions';
						var thisAjax = doAjax(dataAttendance, ' ', '../includes/attendanceFunctions.php');
						thisAjax.then(function(response_in, status){
							
												var data = JSON.parse(response_in);
												var array = data.row;
												var length = data.count;
												var count = 0;
												//delete current contents of missing residents div
												deleteDivID('gridlist');
												//add list of missing residents to the attendance page
												attendanceGridList(array, length, count);
												document.getElementById("attendanceCount").innerHTML = "Total Residents: " + length;
											});
					
					return thisAjax;
				}
			/*Queries the list of absences for the date entered into the frmDate input and displays them in the modal when the modal opens
				AJAX Response varies by process requestion the function*/
				function getPendingAbsences (){
					var method = 'getAbsenceByDate';
					var data = {method: method};
							data.phpFunction = 'attendanceFunctions';
							data.frmDate = $("#frmDate").val();
					document.getElementById("modalAbsenceAlert").innerHTML = "";
					var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
					return thisAjax;
				}
			/*Retrieves a list of missing residents*/
				function getMissing(){
					var data = {};
						if($('#frmDate').val()!==''){
							data.frmDate = $('#frmDate').val();
						}
						//update missing list
						data.phpFunction = 'attendanceFunctions';
						data.method = 'getMissingResidents';
					 	var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
						thisAjax.then(function(response_in){
												var data = JSON.parse(response_in);
												var array = data.row;
												var length = data.count;
												//delete current contents of missing residents div
												deleteDivID('gridlist_Missing');
												//add list of missing residents to the attendance page
												missingGridlist(array, length);
												document.getElementById("missingDetails").innerHTML = "Total Missing Residents: " + length;
											});
					return thisAjax;
				}
		//UPDATE ATTENDANCEHEADER
			/*adds a closing date to the attendance header table*/
				function updateAttendanceHeader(date_in){
						//var closeThisDate = $('#frmDate').val();
						var thisDate = date_in;
						var method = "closeThisDay";
						
						var data = {method: method, frmDate: thisDate};
								data.phpFunction = 'attendanceFunctions';
							if($('#formUserID').val()!==''){
								data.userid  = $('#formUserID').val();
							}else{
								return false;
							}
						var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
								thisAjax.then(function(response_in){
												 				if(response_in === 'closed'){
					  						 					formatChanges(response_in);
				  							 				}else{
            							 				alert('Attendance is already closed!')
            							 				return false;
          						 					}
							});
						return thisAjax;
					}	
		//INSERT ATTENDEE	
			/*Collects data from the attendance form and posts, formats the attendance page based on post success.*/
				function insertAttendee(){
			
      		var method = "addAttendee";
					var data = {method: method};
							data.phpFunction = 'attendanceFunctions';
					
					var formDate = $("#frmDate").val();
					if (formDate === "") {
   					$("#frmDate").focus();
						return false;
  				}else{
						if (formDate < returnCurrentDate()){
							if(confirm('You are attempting enter a new record into a past attendance roster.  \n\nThe date of the new record will not match the date of the attendance. \n\nDo you want to proceed?')){
								data.frmDate = formDate;
								data.filterActiveDate = formDate;
							}else{
								return false;
							}
						}else{
							data.frmDate = formDate;
							data.filterActiveDate = formDate;
						}
					}
					
					var formResHID = $("#formResHID").val();
					var formResidentID = $("#residentID").val();
					if ((formResHID === "") && (formResidentID === "")){
							$("#formResHID").focus();
							return false;	
  				}else if (formResHID !== ""){
						data.formResHID = formResHID;
						//set for residentFunctions.php filteredQuery($db)
						data.formFilterHID = formResHID; 
						data.formFilterHID_radio = '=';
					}else if (formResidentID !== ""){
						//set for residentFunctions.php filteredQuery($db)
						data.formFilterID = formResidentID;
						data.formFilterID_radio =  '=';
					}
					var userID = $("#formUserID").val();
					if (userID === "") {
						return false;
  				}else{
						data.userID = userID; 
					}
					var token = $("#token").val();
          if (token === ""){
					  document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
				  	return false;
				  }else{
						data.token = token;
				  }	
          
					var formCode = $("#formCode").val();
					if (formCode === "") {
						return false;
  				}else{
						data.formCode = formCode;
					}
					
						data.formSubmit = 'formSubmit';
					var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
					/*Process response for adding a new attendee
						-Format the page based on the results
						-If an error message does not exist add the object information for to the html page.*/
					thisAjax.done(function(response_in){
							          if(response_in === 'credentialError'){
                          window.location.assign('../index.html');
                        }else{        
													resetFormField("#formResHID", formResHID);
													resetFormField("#residentID", residentID);
													var data = JSON.parse(response_in);
												//Check the message for the type of response received.
													/*If success = add the data to the attendance page.
														If failure do nothing*/	
													if(data.message === 'success'){
														var rowCount = data.array.count;
														var array = data.array.row; 
														var num ='';
														
														//get last child of gridlist
														var seqCount = $( "#gridlist").children("a:last-child").children().children(".rowCount").html();
														if(seqCount === undefined){
															seqCount = 0;
														}
														//var seqCount = $( "#gridlist:last-child").children().children(".rowCount").html();
														console.log(seqCount);
														attendanceGridList(array, rowCount, seqCount);
														//delete the resident from the dropdown list
														for(var x = 0; x < rowCount; x++){
															num = data.array.row[x].ResID;
															$('#residentID option[value=' + num + ']').remove();
														}
														
														var objDiv = document.getElementById("gridlist");
     												objDiv.scrollTop = objDiv.scrollHeight;
													}
													
													//Move focus back to the formResHID input field
													$("#formResHID").focus();
                          $("#formResHID").val("");
												}
					});
					return false;
    		}

//ATTENDANCE PROCESSES ----------------------------------------------------

		//--TAKE ATTENDANCE --------------------------------------------------
			/*When the start link is clicked on the attendance page check if the 
				user has permission to take attendance.  If the user has permission
				call the attendStart function to process the request otherwise give 
				the user an error msg				
				Call ajax to insert a new record into the attendance header.
				If the returned status of the attendance is:
        Duplicate then alert the user that the attendance has already been started
        Closed then alert the user that the attendance is closed and locked
        Else set focus on the HID input on the attendance form*/
				function startAttendance(){
					var current = permission();
					if(current==='write'){
						var todaysDate = returnCurrentDate();
						var startDate='';
						//Check that a date is posted in the attendance form.
						if($('#frmDate').val()!==''){
							startDate = $('#frmDate').val();
						}else{
							return false;
						}
						if (startDate !== todaysDate) {
							alert('Unable to Start Attendance! \nYou cannot begin attendance for a past or future date.')
							return false;
						}else{
							var data = {};
							var method = "startAttendance";
							data.method = method;
							data.frmDate = startDate;
							data.phpFunction = 'attendanceFunctions';
							var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');						
							thisAjax.then(function(response_in){
														switch(response_in) {
   														case "duplicate":
      	  											alert ('Attendance has already been started!');
																$("#formResHID").focus();
        												break;
    													case "closed":
        												alert('Unable to make changes!  Attendance has been closed.');
        												break;
															case "started":
																formatChanges(response_in);
																$("#formResHID").focus();
																break;
    													default:
        												alert(response_in);
															}
								
													});
							return false;
						}
					}else{
						alert("You do not have permission for that!")
            return false;
					}
					return false;
				}
			
		//--CLOSE ATTENDANCE --------------------------------------------------
			/*When the close link is clicked on the attendance page check if the
				user has permission to take attendance
				Verify the user wishes to close the attendance
				On verification - process closing.
				 - verify absences - verify missing residents, update attendanceheader table, generate report*/
				function closeAttendance(){
					var current = permission();
					var length
					var filterDate;
					if($('#frmDate').val()!==''){
						filterDate = $('#frmDate').val();
					}else{
						return false;
					}
					if(current === 'write'){
						//Verify that the user wishes to continue
						if (confirm("Closing the day will prevent future changes.  \nDo you want to continue?")) {	
							ajaxchain('');	
  					}else{
							return false;
						}
					}else{
						alert("You do not have permission for that!");
						return false;
					}
					
				/*AJAX CHAIN FUNCTIONS used in the closing process-----------------------------------------*/
					/*EVENT LISTENER
					removes the event listener added to the submit button if the user chooses 
					to close the modal during the closing process and exit the closing process*/
					function remove_closing_SubmitModal(){
						document.getElementById("submitModal_VerifyAbsence").removeEventListener("click", closing_submitModal);
					}
					
					/*Returns true or false, used to determine if the ajaxchain should continue or break*/
					function getProcessContinuation(response_in, message, varConfrim){
						var pendingResponse = false;
						var data = JSON.parse(response_in);
						var array = data.row;
						var length = data.count;
						//if absences still exist ask the user to confirm continuation of the closing process otherwise continue without verification.
						if(Number(length) >= 1){
							if(varConfrim === true){
								if (confirm(message)) {
									pendingResponse = true;
									return pendingResponse;
								}else{
									pendingResponse = false;
									return pendingResponse;
								}
							}else{
								pendingResponse = false;
								return pendingResponse;
							}
						}else{//count is 0
							pendingResponse = true;
							return pendingResponse;
						}
						return pendingResponse;
					}
					
					//CHAIN 1 - Check status of the attendance before proceeding with closing
					function closing_AttendanceStatus(data){
						var pendingResponse = false;
						var status = JSON.parse(data);
						//console.log('status ' + status);
						var returnValue = '';
						switch(status) {
   						case "started":
      	  			returnValue = 'continue';
								break;
							case "Completed":
								alert ('Unable to proceed! Attendance is Closed.')
								returnValue = 'stop';
								break;
							case "no":
								alert ('Unable to proceed! Attendance has not been started!');
								returnValue = 'stop';
								break;
							default:
								alert(data + 'Unable to proceed!');
								returnValue = 'stop';
						}
						return returnValue;
					}
					/*CHAIN 2 - Closing Pending absences process.
					If absences exist add an event listener to the absence modal and open the modal to allow users to verify absences before making the final changes.
					If there are no pending absences then return continue*/
					function closing_PendingAbsences(data){
						var message ="";
						var continuation = "";
						var varConfirm = true;
						message = "";
						continuation = getProcessContinuation(data, message, '');
						if (continuation === false){//more absences then add event listener
							document.getElementById("submitModal_VerifyAbsence").addEventListener("click", closing_submitModal);
							document.getElementById("modalClose").addEventListener("click", remove_closing_SubmitModal);
							return 'stop';
						}else if(continuation === true){//no more absences continue
							return 'continue';
						}
						return 'stop';
					}
					/*CHAIN 2 PART 2 - EVENT LISTENER ON MODAL
					event added to the modal submit button to capture user actions during the closing process.*/
					function closing_submitModal(){
						var message ="";
						var continuation = "";
						var varConfirm = true;
						//remove event listener from modal absence submit button.
						document.getElementById("submitModal_VerifyAbsence").removeEventListener("click", closing_submitModal);
						
						/*check for remaining absences before closing the user.
						If remaining ask the user to confirm continuation of the closing process.*/
						var p = new Promise(function(resolve, reject) {resolve('Success');});
						p.then(function(){return getPendingAbsences()})
							.then(function(data, textStatus, jqXHR){
								return closing_RemainingAbsences(data)})
							.then(function(data2){return chainBreak(data2, ajaxchain, 'p2');})
							.then(function(){$('#modal_AbsenceVerification').modal('hide');})
							.catch(function(e){console.log(e)} );
						return false;
					}
					
					/*CHAIN 2 PART 3 - Closing Remaining Absences
					When the modal event captures the user clicking the submit button, check if remaining users still exist.
					This occurs after asking opening the modal for the user to verify pending absences.
					Request that the user confirm continuation with the closing process.*/
					function closing_RemainingAbsences(data){
						var message = "Pending Absences!  \nThere are absences still pending. \nDo you want to continue closing the attendance for this day?";
						var continuation = "";
						var varConfirm = true;
						continuation = getProcessContinuation(data, message, varConfirm);
						
						if(continuation === true){//continue with process
							return 'continue';
						}else if (continuation === false){
							return 'stop';
						}
						return 'stop';
					}
					
					/*CHAIN 3 - Closing Missing People
					During the closing process prompt the user if their are people missing from the attendance.
					If there are missing people ask the user to confirm continuation with the closing process.*/
					function closing_MissingPeople(data){
						var message = "Missing Residents!  \nThere are residents missing from the attendance. \nDo you want to continue closing the attendance for this day?";
						var continuation = "";
						var varConfirm = true;
						continuation = getProcessContinuation(data, message, varConfirm);
						
						if(continuation === true){//continue with process
							return 'continue';
						}else if (continuation === false){
							return 'stop';
						}
						return 'stop';
					}
					
					
					/*CHAINBREAK - Used to break out of the ajax chain*/	
					function chainBreak(data, varFunction, functionVar){
						if(data === 'continue')
						{
							if(functionVar!==''){
								return varFunction(functionVar);	
							}else{
								return varFunction();
							}
						}else{
							return Promise.reject('cancelled');
						}
					}
					/*SKIP A PROMISE Used to skip the first promise the the function ajaxchain*/
					function promiseSkip(skip, pFrom, pTo){
						if(skip === 'p2')
						{
							return pTo;
						}else{
							return pFrom;
						}
					}
					/*CLOSING DAY PROCESS - order of operations*/
					function ajaxchain(skip){
					var p1 = new Promise(function(resolve, reject) {resolve('Success')});
						
						p1.then(function(){return getAttendanceStatus(filterDate)})
							.then(function(data1){return closing_AttendanceStatus(data1)})
							.then(function(data2){return chainBreak(data2, promiseSkip(skip, parttwo, partthree), '')})
							.catch(function(e){console.log(e)} );
						
						function parttwo (){
						var p2 = new Promise(function(resolve, reject) {resolve('Success')});
							
						p2.then(function(){return modal_AbsenceOpen("")})
							.then(function(data, textStatus, jqXHR){return closing_PendingAbsences(data)})
							.then(function(data3){return chainBreak(data3, partthree, '')})
							.catch(function(e){console.log(e)} );
						}
						
						function partthree(){
						var p3 = new Promise(function(resolve, reject) {resolve('Success')});
							
						p3.then(function(){return getMissing()})
							.then(function(data, textStatus, jqXHR){return closing_MissingPeople(data)})
							.then(function(data4){return chainBreak(data4, updateAttendanceHeader, filterDate)})
							.then(function(){generateReport_absence()})
							.catch(function(e){console.log(e)} );
						}
					
					}
					return false;
				}
		//VERIFY ABSENCES
			/*Call AJAX to regenerate a new list of residents who have been 
				recorded on the attendance roster for the date inputed on the form*/
				function absenceVerificationProcess(){
				var data = {};
				/*Get the status of the attendance from the html page. This element
					is updated when the attendance is started, closed, and date changes.*/
					var attendanceStatus = document.getElementById("displayStatus").innerHTML;
					if ((attendanceStatus ==='')|| (attendanceStatus ==='Completed'))
						{
							return false;
						}
					if ($("#frmDate").val() !== "") {
							data.attendanceDate = $("#frmDate").val();
  				}else{
						return false;
					}
					data.phpFunction = 'attendanceFunctions';
					data.method = 'getAttendanceList';
					var thisAjax = doAjax(data, '', '../includes/attendanceFunctions.php');
					//Generate New list of attendees and compare the list to the absence list.
					thisAjax.done(function(response_in){
												var data = JSON.parse(response_in);
												verifyAbsence(data);
					});
					return false;
			}		
			
     
			

			function test(){
				alert('true');
			}


			
				

			



				

			 


