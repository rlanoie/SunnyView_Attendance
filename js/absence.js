$(function() {
	$('#submitAbsenceModal').click(function() {
		
	});
});

//ABSENCE FUNCTIONS-------------------------------------------------------
		//--ABSENCE FORMATTING --------------------------------------------------
			//Modal Date Selection
				/*format the absence modal form based on checkbox selected*/
				function singleDay(){
					var singleDay = document.getElementById("oneDay").checked;
					if (singleDay===true){
						document.getElementById("multiDay").checked = false;
						$("#lblLeaving").prop('hidden', true);
						$("#lblDate").prop('hidden', false);
						$("#multiDates_Title").prop('hidden', true);
						$("#multiInstructions").prop('hidden', true);														
						$("#lblReturning").prop('hidden', true);
						$("#formReturning").prop('hidden', true);
						resetFormField("#formReturning", formReturning);
					}else{
						document.getElementById("multiDay").checked = true;
						multipleDays();
					}
    		}
				function multipleDays(){								
					var multiDay = document.getElementById("multiDay").checked;
					if (multiDay===true){
						document.getElementById("oneDay").checked = false;
						$("#lblLeaving").prop('hidden', false);
						$("#lblDate").prop('hidden', true);
						$("#multiDates_Title").prop('hidden', false);
						$("#multiInstructions").prop('hidden', false);
						$("#lblReturning").prop('hidden', false);
						$("#formReturning").prop('hidden', false);
					}else{
						document.getElementById("oneDay").checked = true;
						singleDay();
					}
				}
				function resetAbsenceModal(){
					resetFormField("#residentID", residentID);
					resetFormField("#formBeginning", formBeginning);
					resetFormField("#formReturning", formReturning);
					resetFormField("#reasonID", reasonID);
					document.getElementById("rowOne").innerHTML = "";
					document.getElementById("rowTwo").innerHTML = "";
					document.getElementById("Modal_CurrResult").innerHTML = "";
					document.getElementById("formOldBeginning").value = "";
					document.getElementById("formOldReturning").value = "";	
					document.getElementById("row_UpdatedDetails").innerHTML = "";
					document.getElementById("row_CreatedDetails").innerHTML = "";
          var elementExists = document.getElementById("notification");
          if(elementExists!==null){
            $( "#notification" ).remove();
          }
					//Check if notifications exists
					if($('#notification').length !== 0) {
						$( "#notification" ).remove();
					}
				}

	//--ABSENCE MODAL----------------------------------------------------------
			/*Open Modal using AddAbsence Layout*/
				function openModal_AddAbsence() {		
					//Check if notifications exists
					if($('#notification').length !== 0) {
						$( "#notification" ).remove();
					}
					resetAbsenceModal();
					document.getElementById("modal-AddTitle").style.display = "inherit";
					document.getElementById("modal-ChangesTitle").style.display = "none";
  				document.getElementById("modalbtn_deactivateAbsence").style.display = "none";
					document.getElementById("modal_Row_headerDetails").style.display = "none";
					document.getElementById("recordHistory").style.display = "none";
					document.getElementById("oneDay").checked = true;
					document.getElementById("multiDay").checked = false;
					singleDay();
					enable(residentID);
					
					frmAbsence.residentID.classList.remove("lockandView");
					document.getElementById('frmSubmissionType').value = "add";
					$('#absences').modal('show');
				}
			/*Open Modal using Change Absence Layout - */
				function openModal_ChangeAbsence(row){
					var rowID = row.find('.rowId').text();
					var rowResident = row.find('.rowResident').text();
					var rowCode = row.find('.row_code').text();
					var rowLeaving = row.find('.row_leaving').text();
					var rowReturning = row.find('.row_Returning').text();
					var rowRecordedDate = dateTimeFormatting(row.find('.row_localTimeStamp').text());
					var rowlastUpdate = row.find('.row_lastUpdate').text();
					var rowUpdateBy = row.find('.row_updatedBy').text();
					
					if(rowlastUpdate==="0000-00-00 00:00:00"){
						rowlastUpdate = "";
					}
					if(rowUpdateBy==="0"){
						rowUpdateBy = "";
					}
					
					resetAbsenceModal();
				
				//retrieve values from current absence record;
         	document.getElementById("modalbtn_deactivateAbsence").style.display = "inherit";
					document.getElementById("modal-AddTitle").style.display = "none";
					document.getElementById("modal-ChangesTitle").style.display = "inherit";
					document.getElementById("modal_Row_headerDetails").style.display = "inherit";
					document.getElementById("recordHistory").style.display = "inherit";
					document.getElementById("reasonID").value = rowCode;
					
					
				//Modify modal to reflect current resident's absence information
					frmAbsence.residentID.value = rowID;
					disable(residentID);
					frmAbsence.residentID.classList.add("lockandView");
					document.getElementById('frmSubmissionType').value = "change";
					
					//format leaving date to change calendar input
						var LeavingDate = new Date(rowLeaving +'PST');
						document.getElementById("formBeginning").value = inputDateFormat(LeavingDate);
						document.getElementById("formOldBeginning").value = inputDateFormat(LeavingDate);
				
					//check if returning date exists and format reflect value on the calendar input
						var insert;//used in the string added to the Modal_CurrResult
						if ((rowReturning !=="") && (rowReturning !== null)){
							var ReturningDate = new Date(rowReturning +'PST');
							document.getElementById("formReturning").value = inputDateFormat(ReturningDate);	
							document.getElementById("formOldReturning").value = inputDateFormat(ReturningDate);	
							document.getElementById("oneDay").checked = false;
							document.getElementById("multiDay").checked = true;
							multipleDays();
							insert = " - ";
						}else{
							document.getElementById("oneDay").checked = true;
							document.getElementById("multiDay").checked = false;
							singleDay();
							insert = "";
						}
						
						$("#reasonID > option").each(function() {
							if(this.text === rowCode){
								frmAbsence.reasonID.value = this.value;
							}
						});
					
					var rowEmployee = row.find('.row_Employee').text();
					var strReturn = "ID: " + rowID +
													"<br>" + rowResident + 
													"<br>" + rowCode+ ": " + rowLeaving + insert + rowReturning;
					document.getElementById("Modal_CurrResult").innerHTML = strReturn;
					
					var strCreation = "Recorded By: " + rowEmployee + 
													"<br>Recorded On: " +rowRecordedDate;
					
					document.getElementById("row_CreatedDetails").innerHTML = strCreation;
					
					var strUpdate = "Updated By: " + rowUpdateBy+
													"<br> Update On: " +rowlastUpdate;
					document.getElementById("row_UpdatedDetails").innerHTML = strUpdate;
					$('#absences').modal('show');
				}
			/*Delete Record*/
				function btnDeleteRecord(){
					document.getElementById('frmSubmissionType').value = "delete";
					submitAbsence();
				}

			/*Submit Absence Modal Form using AJAX
				Check for any errors on the form and return false if errors exist*/
				function submitAbsence(){
					var url = '../includes/attendanceFunctions.php';
					var method="";
					var data = {};
					var submissionType="";
					
					/*Check for all types of submission*/
					if ($("#residentID").val() !== "") {
						data.residentID = $("#residentID").val();
						document.getElementById("rowOne").innerHTML ="";
  				}else{		
						$("#residentID").focus();
						document.getElementById("rowOne").innerHTML = "You must enter a Resident!";
						document.getElementById("rowTwo").innerHTML ="";
						return false;
					}
					if($("#formUserID").val() !== "") {
						data.formUserID = $("#formUserID").val();
  				}else{
						$("#formUserID").focus();
						alert('You are not logged in!');
						return false;
					}			

					
					/*check type of submission*
						Add New Absence, Change Absence Details, Mark Absence Inactive*/
					if ($("#frmSubmissionType").val() !== "") {
						submissionType = $("#frmSubmissionType").val();
						
						if (submissionType === "add"){
							method = 'addNewAbsence';
						}else if  (submissionType === "change"){
							method = 'getUpdateAbsence';
						}else if (submissionType === "delete"){
							method = 'getDeleteAbsence';
							data.formActive = 0;
							data.PKLeaving = $("#formOldBeginning").val();
						}else{
							return false;
						}
					}else{
						return false;
					}
					/*today is used to compare if the date being added is prior to the current date
					Set today's time to 0 to allow all new absences for the current date to be added.'*/
					var today = new Date();
					today.setHours(0,0,0,0);
					
					if((submissionType === "add") || (submissionType === "change")){
						
						//Check if form beginning date exists
						if($("#formBeginning").val() !== "") {
							var LeavingDate = new Date($("#formBeginning").val() +'PST');
							if(LeavingDate < today){
								document.getElementById("rowTwo").innerHTML ="INVALID LEAVING DATE! Date is in the past.";	
								return false;
							}
							if(submissionType === "change"){
								data.formBeginning = $("#formBeginning").val();
								data.formNewBeginning = $("#formBeginning").val();
								data.formOldBeginning = $("#formOldBeginning").val();
								data.PKLeaving = $("#formOldBeginning").val();
							}else{
								data.formBeginning = $("#formBeginning").val();
							}
							document.getElementById("rowTwo").innerHTML ="";
  					}else{
							$("#formBeginning").focus();
							document.getElementById("rowTwo").innerHTML = "MISSING START DATE! You must enter a start date."
							return false;
						} 
						
					//If multiple days are checked then get the returning date.
					if (document.getElementById("multiDay").checked === true && $("#formReturning").val() === "") {
						$("#formReturning").focus();
						document.getElementById("rowTwo").innerHTML = "MISSING RETURNING DATE! \n You've indicated the resident will be gone for multiple days.";
						return false;
					}else if($("#formReturning").val() !== ""){
						
						//Check if returning date is greater than start date
						if ($("#formReturning").val() <= $("#formBeginning").val()){
							document.getElementById("rowTwo").innerHTML = "INVALID RETURNING DATE! \n Returning date cannot be before or the same as the date Leaving.";
							return false;
						}else{
							if(submissionType === 'change'){
								data.formReturning = $("#formReturning").val();
								data.formNewReturning = $("#formReturning").val();
								data.formOldReturning = $("#formOldReturning").val();
							}else if (submissionType === "add"){
								data.formReturning = $("#formReturning").val();
								document.getElementById("rowTwo").innerHTML ="";															
							}else{
								return false;
							}
						}
					}else{
						data.formReturning =null;
					}
						
					if($("#reasonID").val() !== "") {
						data.reasonID = $("#reasonID").val();
  				}else{
						$("#reasonID").focus();
						document.getElementById("rowTwo").innerHTML = "MISSING REASON! You must enter a reason for absence.";
						return false;
					}
				}
					
					data.method = method;
					data.phpFunction = 'attendanceFunctions';
					data.submitAbsenceModal = 'submitAbsenceModal';
					
					//Check if notifications exists
						if($('#notification').length !== 0) {
							$( "#notification" ).remove();
						}
					var userID = $("#userID").val();
				  if (userID === ""){
					  //document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					  return false;
				  }else{
					  if(Number.isNaN(Number(userID))){
						  //document.getElementById("").innerHTML = "A fatal error has occurred!  Please log out and try again.";
						  return false;
					  }else{
						  data.userID = $("#userID").val();
					  }
				  }
				  var token = $("#token").val();
          if (token === ""){
					  //document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					  return false;
				  }else{
					  data.token = token;
				  }	

					doAjax(data, submitAbsence_Post_AjaxResponse, url);
 					return false;
				}
	//--PROCESS MODAL ABSENCE FORM SUBMISSION-------------------------------
			/*Add or change an absence based on sql query results
				DUPLICATE - means that their is an overlap in dates and return false
				For all other responses, add the new absence to the list of absences, close the modal*/
				function submitAbsence_Post_AjaxResponse(response_in){
          if(response_in !== 'credentialError'){
						var data = JSON.parse(response_in);
						var array = data.row;
						var rowCount = data.count;
						if(data.error === 'duplicate'){
							if($('#notification').length !== 0) {
								$( "#notification" ).remove();
							}
							/*if entries already exist for the current absence date then
							display them in the #results div*/
							modalAbsences_AppendConflict(array, rowCount);
							return false;
						}else{
							$('#absences').modal('hide');
							//delete the elements in #results div
							deleteDivID("gridlist");
						
							gridlist_AbsenceList(array, rowCount);
						}        
          }else{ 
            window.location.assign('../index.html');
					}
        }
			/*CONFLICTING ABSENCE DETAILS
				Add details of conflicting absences to the modal, bottom of the modal-bodyContent section*/
				function modalAbsences_AppendConflict(array_in, length){
					var $wrapper, $row, $section, $header;
						$section = $('<section>',{'id':'notification','class':'notification'}).insertBefore('#modalAbsenceForm');
						$wrapper = $('<div>',{'class':'container'}).appendTo($section);
						$header = $('<h2>',{'html':('Conflicting Absence')}).appendTo($wrapper);
					for (var i = 0; i < length; i++) {
						$row = $('<div>',{'class':'row no-col-padding'}).appendTo($wrapper);
						$('<div>',{'class':'col-sm-2','html':(array_in[i].resID)}).appendTo($row);
						$('<div>',{'class':'col-sm-3','html':(dateFormatting(array_in[i].Leaving))}).appendTo($row);
						if(array_in[i].Returning!==null){
								$('<div>',{'class':'col-sm-3','html':(dateFormatting(array_in[i].Returning))}).appendTo($row);
						}else{
							$('<div>',{'class':'col-sm-3','html':(array_in[i].Returning)}).appendTo($row);
						}
						$('<div>',{'class':'col-sm-3','html':((array_in[i].Code))}).appendTo($row);
					}
				}
				/*ABSENCE LIST REFRESH - Repopulates the list of absences and appends the list to the div #gridlist*/
				function gridlist_AbsenceList (array_in, length){
					var wrapper, hyperlink;
					var clickID, clickElement;
					var leavingDate, returningDate, returningRowInsert;
					for (var i = 0; i < length; i++) {
						
						var current = 'rowResID_'+array_in[i].resID;
						hyperlink = $('<a>', {'href':'#','class':'changeAbsence'}).appendTo('#gridlist');
						
							leavingDate = dateFormatting(array_in[i].Leaving);
							if (array_in[i].Returning!==null){
								returningDate = dateFormatting(array_in[i].Returning);
								returningRowInsert = $('<div>',{'class':'col-sm-2 row_Returning','html':(returningDate)}); 
							}else{
								returningRowInsert = $('<div>',{'class':'col-sm-2 row_Returning','html':(array_in[i].Returning)});
							}
							
							var $currentDate = new Date();
							var $strCurrentDate = ("0" + ($currentDate.getMonth() + 1)).slice(-2) +  "-" + ("0" +$currentDate.getDate()).slice(-2) + "-" + $currentDate.getFullYear();
						
							//Add Row and assign an onclick event
								if((leavingDate===$strCurrentDate) || (returningDate===$strCurrentDate)){
									wrapper = $('<div>',{'id':current, 'class':'row rowResults highlight'}).appendTo(hyperlink);
								}else{
									wrapper = $('<div>',{'id':current, 'class':'row rowResults'}).appendTo(hyperlink);
								}
								wrapper.on("click", function(event){openModal_ChangeAbsence($(this));});

							//Add Remaining columns
							$('<div>',{'class':'col-sm-1 rowId','html':(array_in[i].resID)}).appendTo(wrapper);
							$('<div>',{'class':'col-sm-3 rowResident','html':(array_in[i].resident)}).appendTo(wrapper);
							$('<div>',{'class':'col-sm-2 row_code','html':((array_in[i].Code))}).appendTo(wrapper);
							$('<div>',{'class':'col-sm-2 row_leaving','html':(leavingDate)}).appendTo(wrapper);
							returningRowInsert.appendTo(wrapper);//add returning date column
							$('<div>',{'class':'col-sm-2 row_Employee','html':(array_in[i].employee)}).appendTo(wrapper);
							$('<div>',{'class':'col-sm-2 row_localTimeStamp','hidden':'','html':(array_in[i].createdOn)}).appendTo(wrapper);
							$('<div>',{'class':'col-sm-2 row_lastUpdate','hidden':'','html':(array_in[i].lastUpdate)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-2 row_updatedBy','hidden':'','html':(array_in[i].updator)}).appendTo(wrapper);
						}
				}


				$(document).ready(function(){	
					$('.changeAbsence').click(function(){
						var row = $(this);
						openModal_ChangeAbsence(row);
					})
				})


