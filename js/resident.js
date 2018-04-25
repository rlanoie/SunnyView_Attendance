
//RESIDENT PAGE---------------------------------------------------------
$(function() {
	//MODAL OPEN
	  /*SET FOCUS - When Resident Modal is opened set focus to the first textbox (newFirst)*/
  $('#modalResident').on('shown.bs.modal', function () {
    $('#newFirst').focus();
  })
	/*ADD NEW LAYOUT - Open Modal using the layout for Adding a new Resident*/
	$('#modalOpen_AddResident').click(function(){
		reset_ResidentFilter();  //Reset the form to filter the resident list	
		reset_ModalAppearance();
		
		document.getElementById("modal_Row_headerDetails").style.display = "none";
    document.getElementById("recordHistory").style.display = "none";
		
		document.getElementById("modal-ChangesTitle").style.display = "none";
		document.getElementById("modalBtn_UpdateResident").style.display = "none";
		document.getElementById("modalBtn_UpdateResident").disabled = true;
		document.getElementById("modal-AddTitle").style.display = "inherit";
		document.getElementById("rowOne").innerHTML = "";
		document.getElementById("rowTwo").innerHTML = "";
		document.getElementById("modalBtn_AddResident").style.display = "inherit";
    
		//	$("#modal_AddResident").addClass('addEmployee');
						
	  $('#modalResident').modal('show');
						
	});
  $('#modalBtn_AddResident').click(function() {
    addResident();
  });
  $('.selectResident').click(function(){
    var $row = $(this);
    openModal_Change($row);
	});
	$('#modalBtn_UpdateResident').click(function() {
		updateResident();
	});
	$('#modalbtn_deactivateRes').click(function() {
		deleteResident();
	});
  $('#residentfilter').click(function() {
		submit_ResidentFilter();
	});
  
  
})

//RESIDENT.php APPEARANCE -------------------------------------------------
		/*Reset the form used to filter the resident list*/
			function clearForm(){
				
			}
			function reset_ResidentFilter(){
			resetFormField("#formFilterID", formFilterID);
			resetFormField("#formFilterHID", formFilterHID);
			resetFormField("#formLastName", formLastName);
			resetFormField("#formFirstName", formFirstName);
												
			document.getElementById("formFilterHID_radio_0").checked = true;
			document.getElementById("formFilterID_radio_0").checked = true;
			document.getElementById("formLastName_radio_0").checked = true;
			document.getElementById("formFirstName_radio_0").checked = true;
													
			document.getElementById("formFilterHID_radio_1").checked = false;
			document.getElementById("formFilterID_radio_1").checked = false;
			document.getElementById("formLastName_radio_1").checked = false;
			document.getElementById("formFirstName_radio_1").checked = false;
		}
		/*Reset the Modal*/
			function reset_ModalAppearance(){
			  $('#frm_id').val('');
			  $('#newFirst').val('');	
		  	$('#newLast').val('');	
	  		$('#newRFID').val('');	
  			$('#newRoom').val('');
        enable(disableResidentChange);
        document.getElementById("modalbtn_deactivateRes").style.display = "inherit";
		  }
		/*Refresh Gridlist*/
			function refreshGridList(data_in){
				var data = JSON.parse(data_in);
				deleteDivID('gridlist');
				residentGridList(data.row, data.count);
				
				document.getElementById("qryResults").innerHTML = "Showing results for <br />" + data.count + " record(s).";
			}
		/*Update the attendance roster div (#gridlist) containing the residents who
			have been accounted for during the attendance with changes that have taken place to the roster.*/
			function residentGridList(array_in, length){
					var node;
					var textnode;
					var wrapper, hyperlink, recordedDate;
					var count = 0;
					for (var i = 0; i < length; i++) {
						hyperlink = $('<a>', {'href':'#', 'class':'modalOpen_SelectResident'}).appendTo('#gridlist');
						hyperlink.on("click", function(event){selectResident($(this));});
						
						var current = 'rowResID_'+array_in[i].id;
						if(array_in[i].active === '0'){
							wrapper = $('<div>',{'id':current,'class':'row rowResults inactive'}).appendTo(hyperlink);	
						}else{
							wrapper = $('<div>',{'id':current,'class':'row rowResults'}).appendTo(hyperlink);	
						}
						
						count++;
						$('<div>',{'class':'col-sm-2 rowCount','html':(count)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-2 rowId','html':(array_in[i].id), 'hidden':''} ).appendTo(wrapper);
						$('<div>',{'class':'col-sm-3 row_nameLast','html':(array_in[i].ResLName)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-3 row_nameFirst','html':(array_in[i].ResFName)}).appendTo(wrapper);
						$('<div>',{'class':'col-sm-3 row_RoomNumber','html':(array_in[i].roomNumber)}).appendTo(wrapper);
					}
				}
		
	//MODAL OPEN APPEARANCE
			function selectResident($row){
    		openModal_Change($row);
			}
		/*Open modal using the change view */		
  	  function openModal_Change($row){
				reset_ModalAppearance();
				var method = 'getResident';
				var url = '../includes/residentFunctions.php';
				var data = {};
						data.method = method;
						data.phpFunction = 'residentFunctions';						
						
				var rowID = $row.find('.rowId').text();
				var nameLast =  $row.find('.row_nameLast').text();
				var nameFirst =  $row.find('.row_nameFirst').text();
						
				data.formFilterID = rowID;
				data.formLastName = nameLast;
				data.formFirstName = nameFirst;
				data.formFilterID_radio = '=';
				data.formLastName_radio = '=';
				data.formFirstName_radio ='=';
					
				$('#frm_id').val(rowID);	
				var thisAjax = getResident(data, url)
				/*Process response to Change Employee
				OPEN MODAL to view details of current resident*/
				thisAjax.then(function(response_in){
           						var data = JSON.parse(response_in);
											reset_ResidentFilter();
											document.getElementById("modal_Row_headerDetails").style.display = "inherit";
											document.getElementById("modal-ChangesTitle").style.display = "inherit";
											document.getElementById("modalBtn_UpdateResident").style.display = "inherit";
											document.getElementById("modalBtn_UpdateResident").disabled = false;
											document.getElementById("modal-AddTitle").style.display = "none";
											document.getElementById("rowOne").innerHTML = "";
											document.getElementById("rowTwo").innerHTML = "";
											document.getElementById("modalBtn_AddResident").style.display = "none";
											document.getElementById("recordHistory").style.display = "inherit";
											document.getElementById("Modal_CurrResult").innerHTML = "<br /> ID: " + data.row[0].id +
													    																								"<br />"  + data.row[0].resident +
																																							"<br /> Room #: "  + data.row[0].roomNumber;
											var strCreation = "Created On: " + dateTimeFormatting(data.row[0].CreatedOn) + 
																				"<br /> Created By: " + data.row[0].creator;
											document.getElementById('row_CreatedDetails').innerHTML = strCreation;
											var strUpdate = "Last Update: " + dateTimeFormatting(data.row[0].lastUpdate) +
																			"<br /> Updated By: " + data.row[0].updator;
											document.getElementById('row_UpdatedDetails').innerHTML = strUpdate;
											if(data.row[0].deactivateDate !== "0000-00-00 00:00:00"){
												var strDelete = "Deleted On:" + dateTimeFormatting(data.row[0].deactivateDate) + 
																				"<br /> Deleted By: " + data.row[0].deletor;
												document.getElementById('row_DeletedDetails').innerHTML = strDelete;                              
												disable(disableResidentChange);
												document.getElementById("modalbtn_deactivateRes").style.display = "none";
											}
											$('#modalResident').modal('show');
											$("#newFirst").focus();
          });
					return false;          
        }

//RESIDENT FUNCTIONS ----------------------------------------------------
		/*Get a single resident*/
			function getResident(data_in, url){
				var thisAjax = doAjax(data_in, '', url);	
				return thisAjax;
			}
		/*Get all residents*/
			function getResidentsActive(active){
				var url = '../includes/residentFunctions.php';
				var data = {};
						data.method = 'getResident';
						data.modalAddResident = 'submit';
						data.active = active;
						data.phpFunction = 'residentFunctions';
				var thisAjax = doAjax(data, '', url);	
				return thisAjax;
			}
		/*Filter resident list*/
			function submit_ResidentFilter(){
				var url = '../includes/residentFunctions.php';
				var data = {}
						data.method = 'getResident';
						data.modalAddResident = 'submit';
						data.phpFunction = 'residentFunctions';
				
				if ($("#formFilterHID").val() !==''){
					data.formFilterHID = $("#formFilterHID").val()
					data.formFilterHID_radio = $("input[name=formFilterHID_radio]:checked").val();
				}
				if ($("#formLastName").val() !==''){
					data.formLastName = $("#formLastName").val();
					data.formLastName_radio = $("input[name=formLastName_radio]:checked").val()
					
				}
				if ($("#formFirstName").val() !==''){
					data.formFirstName = $("#formFirstName").val();
					data.formFirstName_radio = $("input[name=formFirstName_radio]:checked").val()
					//console.log = (data.formFirstName_radio);
				}
				if ($("#active").val() !==''){
					data.active = $("input[name=active]:checked").val();
				}
				if($("#frmsort").val() !==''){
					data.frmSort = $("input[name=frmSort]:checked").val();
				}
				
				var thisAjax = doAjax(data, '', url);
				thisAjax.then(function(response_in){refreshGridList(response_in)});
				return false;
			}

		/*ADD Resident*/		
			function addResident(){
				var method = 'addResident';
				var data = {method: method};
				var url = '../includes/residentFunctions.php';
				var newFirst = $("#newFirst").val();
				if (newFirst === "") {
  	 			$("#newFirst").focus();
					document.getElementById("rowOne").innerHTML = "You must enter a First Name!";
				  return false;
	  		}else{
					data.newFirst = newFirst;
				}
				var newLast = $("#newLast").val();
				if (newLast === "") {
  	 			$("#newLast").focus();
					document.getElementById("rowOne").innerHTML = "You must enter a Last Name!";
					return false;
	  		}else{
					data.newLast = newLast;
				}						
				var newRFID = $("#newRFID").val();
				if (newRFID === "") {
  	 			$("#newRFID").focus();
					document.getElementById("rowTwo").innerHTML = "You must assign a key fob # to this resident!";
					return false;
				}else{
					if(Number.isNaN(Number(newRFID))){
						document.getElementById("rowTwo").innerHTML = "Key Fob # cannot contain letters or symbols!";
						$("#newRFID").focus();
						return false;
					}else{
						data.newRFID = newRFID;	
					}
				}
				var newRoom = $("#newRoom").val();
				if(newRoom !== ""){
					data.newRoom = newRoom;
				}
				var userID = $("#userID").val();
				if (userID === ""){
					document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
					if(Number.isNaN(Number(userID))){
						document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
						return false;
					}else{
						data.userID = $("#userID").val();
					}
				}
				var token = $("#token").val();
        if (token === ""){
					document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
						data.token = token;
				}		
				data.active = 1;
				data.modalAddResident = 'submit';
				data.phpFunction = 'residentFunctions';
				var thisAjax = doAjax(data, '', url);
				
      	ajaxChain(thisAjax);
			return false;
		}	
		/*Update Resident*/
			function updateResident(){
				var method = 'updateResident';
				var data = {method: method};
						data.phpFunction = 'residentFunctions';
						
				if ($("#frm_id").val() !== "") {
					data.frm_id = $("#frm_id").val();
  			}
				if ($("#newFirst").val() !== "") {
					data.newFirst = $("#newFirst").val();
  			}
				if ($("#newLast").val() !== "") {
					data.newLast = $("#newLast").val();
	  		}
				if ($("#newRFID").val() !== "") {
					var newRFID = $("#newRFID").val();
					if(Number.isNaN(Number(newRFID))){
						document.getElementById("rowTwo").innerHTML = "Key Fob # cannot contain letters or symbols!";
						$("#newRFID").focus();
						return false;
					}else{
						data.newRFID = newRFID;	
					}
  			}
				var newRoom = $("#newRoom").val();
				if(newRoom !== ""){
					data.newRoom = newRoom;
				}
        var userID = $("#userID").val();
				if (userID === ""){
					document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
					if(Number.isNaN(Number(userID))){
						document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
						return false;
					}else{
						data.userID = $("#userID").val();
					}
				}
        var token = $("#token").val();
        if (token === ""){
					document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
						data.token = token;
				}
				data.active = 1;
					
				var thisAjax = doAjax(data, '', '../includes/residentFunctions.php');
				ajaxChain(thisAjax);
				return false;
			}
		/*Delete Resident*/
			function deleteResident(){
				if (confirm("Confirm that you would like to deactivate this resident.")) {
 					var method = 'deactivateResident';
					var data = {method: method};
							data.phpFunction = 'residentFunctions';			
					if ($("#frm_id").val() !== "") {		
						data.frm_id = $("#frm_id").val();
						data.active = 0;
  				}
					if ($("#userID").val() !== "") {
						data.deactivatedBy = $("#userID").val();			
					}else{
						alert('Uh Oh! Something went wrong.')
					}
          
          var userID = $("#userID").val();
				  if (userID === ""){
  					document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
	  				return false;
		  		}else{
			  		if(Number.isNaN(Number(userID))){
				  		document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					  	return false;
  					}else{
	  					data.userID = $("#userID").val();
		  			}
			  	}
          var token = $("#token").val();
          if (token === ""){
					  document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
  					return false;
	  			}else{
		  			data.token = token;
			  	}
          
					var thisAjax = doAjax(data, '', '../includes/residentFunctions.php');
					ajaxChain(thisAjax);
					return false;
				} else {
					return false;
				}
			}
//RESIDENT PROCESSES ----------------------------------------------------
		/*CHAINBREAK - Used to break out of the ajax chain*/	
			function chainBreak(data, varFunction, varVariable){
				if(data === true)
				{
					if(varVariable !== ''){
						
						return varFunction(varVariable);							
					}else{
						return varFunction();							
					}
				}else if (data===false){
					return Promise.reject('cancelled');
				}
			}
		/*Changes to residents are processed.*/
			function  processResidentChange(response_in){
        if(response_in === 'credientalError'){
          window.location.assign('../index.html');
				}else if(response_in === 'duplicate'){
				  document.getElementById("rowTwo").innerHTML = "DUPLICATE ERROR! <br \> This key fob number is already assigned to a current resident.";
					return false;
				}else{
          alert(response_in);
				  $('#modalResident').modal('hide');
					reset_ResidentFilter();
					return true;
        }
			}
		/*Chain of Events for all modal submits*/
			function ajaxChain (data_in){
			data_in.then(function(response_in){return processResidentChange(response_in)})
      	.then(function(data1){return chainBreak(data1, getResidentsActive, 1)})
				.then(function(data2){refreshGridList(data2)})
				.catch(function(e){console.log(e)} );
		}





		


