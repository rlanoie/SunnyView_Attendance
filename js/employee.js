//EMPLOYEE PAGE----------------------------------------------------------
	$(function() {
		/*OPEN MODAL TO ADD EMPLOYEE - Add new employee*/
		$('.addModalEmp').click(function(){
			resetEmployeeFilter();
			resetModalAppearance();	
			adminUnCheckable();
			
			document.getElementById("modal_Row_headerDetails").style.display = "none";
			document.getElementById("recordHistory").style.display = "none";			
			
			document.getElementById("modal-ChangesTitle").style.display = "none";
			document.getElementById("submitModal_UpdateEmp").style.display = "none";
			document.getElementById("submitModal_UpdateEmp").disabled = true;
			document.getElementById("modal-AddTitle").style.display = "inherit";
			document.getElementById("submitModal_AddEmp").style.display = "inherit";
				
			$("#modal_User").addClass('addEmployee');
			$('#modal_User').modal('show');
		})
			/*ADD NEW EMPLOYEE from modal*/
			$('#submitModal_AddEmp').click(function() {
				addEmployee();	
				return false;
			});
			$('#submitModal_UpdateEmp').click(function() {
				updateEmployee();
				return false;
			});
			/*$('#passwordUpdate').click(function() {
				alert('here');
				changePassword();
			});*/
			$('.selectEmployee').click(function() {
				var $row = $(this);
				openModal_changeEmp($row)
			});
			/*DEACTIVATE EMPLOYEE from modal*/
			$('#deactivateEmp').click(function() {
				if (confirm("Confirm that you would like to deactivate this user.")) {
 					var method = 'deactivateEmployee';
					var data = {method: method};
						
					if ($("#frm_id").val() !== "") {
						data.frm_id = $("#frm_id").val();
						data.active = 0;
						var now = new Date();
						var formatted = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDay() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
						data.deactivateDate = formatted;
  				}
          var userID = $("#userID").val();
			    if (userID === ""){
			  	  document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
				    return false;
			    }else{
	  			  if(Number.isNaN(Number(userID))){
  					  document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					    return false;
				    }else{
					    data.userID = $("#userID").val();
				    }
			    }
          var token = $("#token").val();
          if (token === ""){
					  document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					  return false;
			    }else{
					  data.token = token;
			    }
					  doAjax(data, processAddEmployee, '../includes/userChanges.php');
            submit_EmployeeFilter();
				} else {
    		  die("You pressed Cancel!");
				}
			});
      //Process the employee filter
			$('#employeeFilter').click(function() {
				submit_EmployeeFilter();
			});
		})




//EMPLOYEE PAGE APPEARANCE ----------------------------------------------------------
		  function resetEmployeeFilter(){
			resetFormField("#formFilterID", formFilterID);
			resetFormField("#formLastName", formLastName);
			resetFormField("#formFirstName", formFirstName);
											
			document.getElementById("formFilterID_radio_0").checked = true;
			document.getElementById("formLastName_radio_0").checked = true;
			document.getElementById("formFirstName_radio_0").checked = true;
			document.getElementById("active_radio_1").checked = true;
												
			document.getElementById("formFilterID_radio_1").checked = false;
			document.getElementById("formLastName_radio_1").checked = false;
			document.getElementById("formFirstName_radio_1").checked = false;
		}
	//--GRIDLIST UPDATE
		/*Update the attendance roster div (#gridlist) containing the residents who
			have been accounted for during the attendance with changes that have taken place to the roster.*/
		  function employeeGridlist(array_in, length){
				var node;
				var textnode;
				var wrapper, hyperlink, recordedDate;
				var count = 1;
				for (var i = 0; i < length; i++) {
					hyperlink = $('<a>', {'href':'#', 'class':'modalOpen_SelectEmployee'}).appendTo('#gridlist');
					var current = 'rowEmpID_'+array_in[i].id;
					if(array_in[i].active === '0'){
						wrapper = $('<div>',{'id':current,'class':'row rowResults inactive'}).appendTo(hyperlink);	
					}else{
						wrapper = $('<div>',{'id':current,'class':'row rowResults'}).appendTo(hyperlink);	
					}
					wrapper.on("click", function(event){selectEmployee($(this));});
					$('<div>',{'class':'col-sm-2 row_count','html':(count)}).appendTo(wrapper);
					//$('<div>',{'class':'col-sm-2 rowId','html':(array_in[i].id), 'hidden':''} ).appendTo(wrapper);
					$('<div>',{'class':'col-sm-3 row_ID','html':(array_in[i].id)}).appendTo(wrapper);
					$('<div>',{'class':'col-sm-3 row_nameLast','html':(array_in[i].userLastName)}).appendTo(wrapper);
					$('<div>',{'class':'col-sm-3 row_nameFirst','html':(array_in[i].userFirstName)}).appendTo(wrapper);
					count++;
				}
			}
			function selectEmployee($row){
				openModal_changeEmp($row);
			}
	//--MODAL INTERACTIVE CHECKBOXES
		/*Lock or unlock fieldset*/
			function checkboxClick(checkboxClick, fieldset){
				var checked = document.getElementById(checkboxClick).checked;
				if(checked===true){
					enable(fieldset);
					return true;
				}else{
					disable(fieldset);
					return false;
				}	
			}
		//PERMISSION CHECKBOXES
		/*ADMIN CHECKBOX - Process response to admin checkbox being clicked. */
			function write_AdminClick (checkbox, fieldset){
				var checkboxStatus = checkboxClick(checkbox, fieldset);
				if (checkboxStatus === true ){
					adminCheckable();
				}else if (checkboxStatus === false){
					adminUnCheckable();
				}
			}
		/*EMPLOYEE CHECKBOXES - Process emoployee permissions checkboxes*/
			function employeePermissions(permission){
				var writeUser = document.getElementById("writeUsers").checked;
				var readUser = document.getElementById("readUsers").checked;
				if (writeUser===true){
					//check if the user clicked the read checkbox and give error message if true
					if(permission==='read' && readUser===true){
						alert("To change this setting, you must disable the user's ability to make changes to Employees.");
					}
					RUsersUnCheckable();
				}else{
					WUsersCheckable()
					RUsersCheckable();
				}
			}
		/*RESIDENT CHECKBOXES - Process response to resident checkboxes*/
			function residentPermissions(permission){
				var writeRes = document.getElementById("writeResidents").checked;
				var readResident = document.getElementById("readResident").checked;
				if(writeRes === true){
					if(permission==='read' && readResident===true){
						alert("To change this setting, you must disable the user's ability to make changes to Residents.");
					}
						RResidentsUnCheckable();
				}else{ //check view residents & make in-active.  View cannot be disabled unless write is unchecked.
					WResidentsCheckable();
					RResidentsCheckable();
				}	
			}
		/*CHECKABLE - Make checkboxes checkable add checkable class; remove un-checkable class*/
			function RResidentsCheckable(){
					$("#labelRResident").removeClass('un-checkedable');
					$("#labelRResident").addClass('checkable');
				}
			function WResidentsCheckable(){
					$("#labelWResident").removeClass('un-checkedable');
					$("#labelWResident").addClass('checkable');
				}
			function adminCheckable(){
					RUsersCheckable();
					WUsersCheckable();
				}
			function RUsersCheckable(){
					$("#labelRUsers").removeClass('un-checkedable');
					$("#labelRUsers").addClass('checkable');
				}
			function WUsersCheckable(){
					$("#labelWUsers").addClass('checkable');
					$("#labelWUsers").removeClass('un-checkedable');
				}
		/*UNCHECKABLE - Make all checkboxes uncheckable add un-checkable class; remove checkable class*/
			function RResidentsUnCheckable(){
					document.getElementById("readResident").checked = false;
					$("#labelRResident").removeClass('checkable');
					$("#labelRResident").addClass('un-checkedable');
				}
			function adminUnCheckable(){
					RUsersUnCheckable();
					WUsersUnCheckable();
				}
			function RUsersUnCheckable(){
					document.getElementById("readUsers").checked = false;
					$("#labelRUsers").removeClass('checkable');
					$("#labelRUsers").addClass('un-checkedable');
				}
			function WUsersUnCheckable(){
					document.getElementById("writeUsers").checked = false;
					$("#labelWUsers").removeClass('checkable');
					$("#labelWUsers").addClass('un-checkedable');
				}
			function resetModalAppearance(){
				document.getElementById('Modal_CurrResult').innerHTML = '';
				document.getElementById('modal_Row_headerDetails').style.display = "none";
				
				document.getElementById("rowOne").innerHTML = "";
				document.getElementById("rowTwo").innerHTML = "";
				document.getElementById("rowThree").innerHTML = "";
				
				resetFormField('newFirst', newFirst);
				resetFormField('newLast', newLast);
				resetFormField('newUsername', newUsername);
				resetFormField('newPassword', newPassword);
				resetFormField('newEmail', newEmail);
				
				document.getElementById("attendance").checked = false;
				document.getElementById("readResident").checked = false;
				document.getElementById("writeResidents").checked = false;
				document.getElementById("writeadmin").checked = false;
				document.getElementById("readUsers").checked = false;
				document.getElementById("writeUsers").checked = false;
			}

 			
//EMPLOYEE MODAL ----------------------------------------------------------			
		function openModal_changeEmp($row){
			resetEmployeeFilter();
			resetModalAppearance();
			var method = 'getEmployees';
			var url = '../includes/userChanges.php';
				var data = {};
						data.method = method;
			
			var rowID = $row.find('.row_ID').text();
			var nameLast =  $row.find('.row_nameLast').text();
			var nameFirst =  $row.find('.row_nameFirst').text();
      data.formFilterID = rowID;
			data.formLastName = nameLast;
			data.formFirstName = nameFirst;
			
			data.formFilterID_radio = '=';
			data.formLastName_radio = '=';
			data.formFirstName_radio ='=';
			
			data.userPermission = true;
			$('#frm_id').val(rowID);		
			var thisAjax = doAjax(data, '', url);
			thisAjax.then(function(response_in){
				//console.log(response_in);
				var data = JSON.parse(response_in);
				var array = data.row[0];				
				//Attendance checkbox
					if(array.attendance === "write"){
						document.getElementById("attendance").checked = true;
					}
					//Resident checkboxes
				if(array.residents === "write"){
						document.getElementById("writeResidents").checked = true;
					}else if(array.residents === 'read'){
						document.getElementById("readResident").checked = true; //check view residents
					}
					residentPermissions(array.residents);
											
					//ADMIN PRIVLAGES
					if(array.admin === "write"){
						document.getElementById("writeadmin").checked = true;
					}else if (array.admin === "read"){
						document.getElementById("writeadmin").checked = false;
					}
					write_AdminClick ('writeadmin', '#fieldsetAdminChecks');	
										
					//User checkboxes
					if(array.users === "read"){
						document.getElementById("readUsers").checked = true;
						//Check if admin checkbox is true.  Make true if not and process unlocking of associated fields.
						if(document.getElementById("writeadmin").checked===false){
							document.getElementById("writeadmin").checked = true;	
							write_AdminClick ('writeadmin', '#fieldsetAdminChecks');
						}
					}else if(array.users === "write"){
						document.getElementById("writeUsers").checked = true;
						if(document.getElementById("writeadmin").checked===false){
							document.getElementById("writeadmin").checked = true;	
							write_AdminClick ('writeadmin', '#fieldsetAdminChecks');
						}
						RUsersUnCheckable();
					}
					document.getElementById("modal_Row_headerDetails").style.display = "inherit";					
					document.getElementById("modal-ChangesTitle").style.display = "inherit";
					document.getElementById("submitModal_UpdateEmp").style.display = "inherit";
					document.getElementById("submitModal_UpdateEmp").disabled = false;

				
					document.getElementById("modal-AddTitle").style.display = "none";
					document.getElementById("submitModal_AddEmp").style.display = "none";
				
					document.getElementById("recordHistory").style.display = "inherit";
				
					document.getElementById("Modal_CurrResult").innerHTML = "<br /> ID: " + array.id +
													    																		"<br />"  + array.employee +
						 																											"<br /> Email: " + array.email;
				
					/*$("#modal_User").removeClass('addEmployee');*/

					$('#modal_User').modal('show');
  	  })	
	}
//EMPLOYEE PROCESSES ------------------------------------------------------------
	//--REFRESH GRIDLIST
		function refreshGridList(data_in){
				var data = JSON.parse(data_in);
				deleteDivID('gridlist');
				employeeGridlist(data.row, data.count);
			}
//EMPLOYEE FUNCTIONS ------------------------------------------------------------
		function addEmployee(){
			var data = {};
      var method = 'addEmployee';
      data.method = method;
            
			var newFirstName = $("#newFirst").val();
			if (newFirstName === "") {
   			$("#newFirst").focus();
				document.getElementById("rowOne").innerHTML = "You must enter a First Name!";
				return false;
  		}else{
        data.newFirstName = newFirstName;
      }
            
			var newLastName = $("#newLast").val();
			if (newLastName === "") {
   		  $("#newLast").focus();
				document.getElementById("rowOne").innerHTML = "You must enter a Last Name!";
				return false;
  		}else{
        data.newLastName = newLastName;
      }
            
			var newUsername = $("#newUsername").val();
			if (newUsername === "") {
   		  $("#newUsername").focus();
				document.getElementById("rowTwo").innerHTML = "You must enter a username!";
				return false;
  		}else{
        data.newUsername = newUsername;
      }
            
			var newPassword = $("#newPassword").val();
			if (newPassword === "") {
   			$("#newPassword").focus();
				document.getElementById("rowTwo").innerHTML = "You must enter a Temporary Password!";
				return false;
			}else{
				//verify that the password meets the minimum requirements
				var passwordMinRequirements = checkPassword(newPassword);
				if (passwordMinRequirements===false){
				  document.getElementById("rowTwo").innerHTML = "Password does not meet the minimum requirements. \n Must be 8 - 12 characters long \n 1 Upppercase \n 1 lowercase \n 1 number";
					$("#newPassword").focus();
					return false;
				}else{
          data.newPassword = newPassword;
					data.tempPassword = 1;
        }
			}
            
		  var newEmail = $("#newEmail").val();
			if (newEmail === "") {
   			$("#newEmail").focus();
				document.getElementById("rowThree").innerHTML = "You must enter an E-mail Address!";
				return false;
			}else{
        data.newEmail = newEmail;
      }
            
			var count=0;
			var attendance ='';
			if(document.getElementById("attendance").checked === true){
				attendance = $("#attendance").val();
        data.attendance = attendance;
				count++;
			}
            
			var readResident = '';
			var writeResidents = '';
			if(document.getElementById("readResident").checked === true){
				readResident = $("#readResident").val();
        data.readResident = readResident;
				count++;
			}else if(document.getElementById("writeResidents").checked === true){
				writeResidents = $("#writeResidents").val();
        data.writeResidents = writeResidents;
				count++;
			}
						
			var writeadmin = '';
			if(document.getElementById("writeadmin").checked === true){
				writeadmin = $("#writeadmin").val();
        data.writeadmin = writeadmin;
				count++;
			}
						
			var readUsers = '';
			var writeUsers = '';
			if(document.getElementById("readUsers").checked === true){
				readUsers = $("#readUsers").val();
        data.readUsers = readUsers;
				count++;
			}else if (document.getElementById("writeUsers").checked === true){
			  writeUsers = $("#writeUsers").val();
        data.writeUsers = writeUsers;
			  count++;
			}
			var userID = $("#userID").val();
				if (userID === ""){
					document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
					if(Number.isNaN(Number(userID))){
						document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
						return false;
					}else{
						data.userID = $("#userID").val();
					}
				}
        var token = $("#token").val();
        if (token === ""){
					document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
					data.token = token;
				}									
			  data.active = 1;
			  data.submitModal_AddEmp = 'submit';
            
				var url = '../includes/userChanges.php';
				doAjax(data, processAddEmployee, url);
  		  return false;
			}
		/*UPDATE EMPLOYEE from modal*/
		function updateEmployee(){
			var method = 'changeEmployee';
			var data = {method: method};

			if ($("#frm_id").val() !== "") {
				data.frm_id = $("#frm_id").val();
  		}
			if ($("#newFirst").val() !== "") {
			  data.newFirst = $("#newFirst").val();
  		}
			if ($("#newLast").val() !== "") {
   			data.newLast = $("#newLast").val();
  		}						
			if ($("#newUsername").val() !== "") {
   			data.newUsername = $("#newUsername").val();
  		}
			if ($("#newPassword").val() !== "") {
   			data.newPassword = $("#newPassword").val();
				data.tempPassword = 1;
			}
			if ($("#newEmail").val() !== "") {
   			data.newEmail = $("#newEmail").val();
			}
			var count=0;
						
			if(document.getElementById("attendance").checked === true){
				data.attendance = $("#attendance").val();
				count++;
			}

			if(document.getElementById("readResident").checked === true){
				data.readResident = $("#readResident").val();
				count++;
			}else if(document.getElementById("writeResidents").checked === true){
				data.writeResidents = $("#writeResidents").val();
				count++;
			}
						
			if(document.getElementById("writeadmin").checked === true){
				data.writeadmin = $("#writeadmin").val();
				count++;
			}
						
			if(document.getElementById("readUsers").checked === true){
				data.readUsers = $("#readUsers").val();
				count++;
			}else if (document.getElementById("writeUsers").checked === true){
				data.writeUsers = $("#writeUsers").val();
				count++;
			}
			var userID = $("#userID").val();
			if (userID === ""){
				document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
				return false;
			}else{
				if(Number.isNaN(Number(userID))){
					document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
				}else{
					data.userID = $("#userID").val();
				}
			}
      var token = $("#token").val();
      if (token === ""){
					document.getElementById("addMessage").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					return false;
			}else{
					data.token = token;
			}
			data.submitModal_AddEmp = 'submit';
			var url = '../includes/userChanges.php';
			alert('The user must log out before changes will take affect.')
			doAjax(data, processAddEmployee, url);
  		return false;
		}	
		/*Process response to ADD EMPLOYEE
			CLOSE MODAL
			RESUBMIT FILTER - to refresh the results div with the new list*/
		function processAddEmployee(response_in){
      if(response_in === 'credientalError'){
          window.location.assign('../index.html');
      }else{
			  alert(response_in);
				$('#modal_User').modal('hide');
				//document.getElementById("filterEmployees").submit(); //refresh the employee list          
        submit_EmployeeFilter();
      }
		}
		/*UPDATE the employee's password*/
//EMPLOYEE PASSWORD -------------------------------------------------------------
			/*Submit changes for a user's password*/
			function changePassword(user_in, userID_in){
						var method = 'updateAccount';
						var url = '../includes/userChanges.php';
						var data = {method: method};
						if(user_in !==''){
							data.username = user_in;
						}else{
							return false
						}
						if(userID_in!==''){
							data.userID = userID_in;
							data.frm_id = userID_in;
						}else{
							return false
						}
            var token = $("#token").val();
            if (token === ""){
					    //document.getElementById("rowOne").innerHTML = "A fatal error has occurred!  Please log out and try again.";
					    return false;
				    }else{
					    data.token = token;
				    }	
						var oldPassword = $("#oldPassword").val();
						if (oldPassword !== "") {
   						data.password = oldPassword;
						}else{
							document.getElementById("errorMsg").innerHTML = "Missing current password."
							return false
						}
				    
						var newPassword = $("#newPassword").val();
						if (newPassword !== "") {
							
							var passwordMinRequirements = checkPassword(newPassword);
							if (passwordMinRequirements===false){
								document.getElementById("errorMsg").innerHTML = "Password does not meet the minimum requirements. \n Must be 8 - 12 characters long \n 1 Upppercase \n 1 lowercase \n 1 number";
								$("#newPassword").focus();
								return false;
							}else{
                data.newPassword = newPassword;
								data.tempPassword = 0;
              }
						}else{
							document.getElementById("errorMsg").innerHTML = "You must enter a new password."
							return false
						}
						var verifyPassword = $("#newPassword").val();
						if(verifyPassword !==""){
							if(verifyPassword !== newPassword){
								document.getElementById("errorMsg").innerHTML = "Passwords do not match!"
								return false;
							}	
						}else{
							document.getElementById("errorMsg").innerHTML = "You must verify your new password!";
							return false;
						}
						data.locked = 0;
					  
						var thisAjax = doAjax(data, '', url);
						thisAjax.then(function(response_in){
							if(response_in !== '1'){
								return false;
							}else{
								window.location.replace('../dashboard');
							}
						});
						return false;
					}

//EMPLOYEE LIST ------------------------------------------------------------------
			function submit_EmployeeFilter(){
				var url = '../includes/userChanges.php';
				var data = {}
						data.method = 'getEmployees';
						data.modalAddResident = 'submit';
						//data.phpFunction = 'residentFunctions';
				
				if ($("#formFilterID").val() !==''){
					data.formFilterID = $("#formFilterID").val()
					data.formFilterID_radio = $("input[name=formFilterID_radio]:checked").val();
					
				}
				if ($("#formLastName").val() !==''){
					data.formLastName = $("#formLastName").val();
					data.formLastName_radio = $("input[name=formLastName_radio]:checked").val()
				}
				if ($("#formFirstName").val() !==''){
					data.formFirstName = $("#formFirstName").val();
					data.formFirstName_radio = $("input[name=formFirstName_radio]:checked").val()
				}
				if ($("#active").val() !==''){
					data.active = $("input[name=active]:checked").val();
				}
				if($("#frmsort").val() !==''){
					data.frmSort = $("input[name=frmSort]:checked").val();
				}
				var thisAjax = doAjax(data, '', url);
				thisAjax.then(function(response_in){refreshGridList(response_in);})
				
			}


				
