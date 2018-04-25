	//--AJAX DATA COLLECTION & SETUP------------------------------------------------------
			function doAjax(data, process, url){
				ajax = ajaxPosting(url, data);
  			ajax.done(process);
				ajax.fail(function(){
					alert("Failure");});
				return ajax;
			}
	//--AJAX POST------------------------------------------------------
					//generic ajax
					function ajaxPosting(url, data){
						return $.ajax({
							url: url,
							type: 'POST',
							data: data,
						})
					}

		//POST METHOD AND VALUE-----------------------------------------------------------
					function theAjax(method, postDate){
						return $.ajax({
							url: '../includes/attendanceUpdate.php',
							type: 'POST',
							data: {method: method, postDate: postDate},
						});
					}
		//POST METHOD AND URL----------------------------------------------------------------
					function ajaxPost(method, urlFile){
							return $.ajax({
							url: urlFile,
							type: 'POST',
							data: {method: method},
						});
					}
	//--AJAX RESPONSE--------------------------------------------------------------------------
					//ATTENDANCE PAGE ----------------------------------------------------------


					//ABSENCE PAGE - PROCESS ADD ABSENCE -------------------------------
										/*Add a new resident absence*/
											function processAddAbsence(response_in){
												var data = JSON.parse(response_in);
												var array = data.row;
												var rowCount = data.count;
												if(data.error === 'duplicate'){
													alert('This resident has a conflicting absence!')
													if($('#notification').length !== 0) {
														$( "#notification" ).remove();
													}
													modalAbsences_AppendConflict(array, rowCount);
													return false;
												}else{
													$('#absences').modal('hide');
													//delete the elements in #results div
													deleteDivID("gridlist");
													/*if entries already exist for the current attendance date then
													display them in the #results div*/
													gridlist_Absence(array, rowCount);
												}
												
											}
