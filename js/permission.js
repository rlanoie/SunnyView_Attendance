//check user permissions
//get id of onclick event and permission parameter
						function permission_click(obj, x)
						{
						  var id = obj.id;
              
              var permissionType = x;
							
              var newLocation="";
              
              
              switch(id){
                case 'btn_attendancePage':
                  newLocation = "attendance-page";
                  break;									
                case 'btn_attendance':
                  newLocation = "attendance-taking";
                  break;
                case 'btn_resident':
                  newLocation = "resident";
                  break;
                case 'btn_admin':
                  newLocation = "admin-page";
                  break;
                 case 'btn_users':
                  newLocation = "users";
									break;
								case 'btn_absences':
									newLocation = "absences";
									break;
              }
           
								switch (permissionType) {
    							case 'none':
                    alert("You do not have permission to access this page!");
  	  							break;
    							case 'read':
										window.location = newLocation;
    								break;
	    						case 'write':
										window.location = newLocation;
    							}
						}