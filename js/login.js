$(function() {
  //Displays the form frm_restUsername and message to user.
  $('#forgotUsername').click(function(){
		resetLoginCredentials_Layout();
		$('#frm_resetUsername :input').prop('disabled', false);
		$('#frm_resetUsername').prop('hidden', false);
		document.getElementById("loginMessage").innerHTML = "FORGOT YOUR USERNAME! <br/>Enter your email address.";
	});
  
  /*$('#passwordReset').click(function(){
		$( "#forgotPassword" ).trigger( "click" );
	})*/
	$('#forgotPassword').click(function(){
		resetLoginCredentials_Layout();
		$('#frm_resetPassword :input').prop('disabled', false);
		$('#frm_resetPassword').prop('hidden', false);
		document.getElementById("loginMessage").innerHTML = "TO RESET YOUR PASSWORD: <br/>Enter your username and email address.";
	});
})

//LOGIN PAGE APPEARANCE
function resetPasswordOnClick(){
	$( "#forgotPassword" ).trigger( "click" );
	$('#passwordReset').prop('hidden', true);
	$('#passwordReset').prop('disabled', true);
	$('#lockedPassword').prop('hidden', false);
	$('#lockedPassword').prop('disabled', false);
}

//Navigates to the login page with the login form displayed.
function Navigate_loginPage(){
 window.location.replace('login');
}
function hideLoginForm(){
	document.getElementById("frm_loginform").style.display = "none";
	$('#frm_loginform :input').prop('disabled', true);
  
}
function hidePasswordForm(){
  $('#lockedPassword').prop('hidden', true);
	$('#lockedPassword').prop('disabled', true);
}
function hideUsernameForm(){
  $('#frm_resetUsername').prop('hidden', true);
	$('#frm_resetUsername').prop('disabled', true);
}

	/*Changes the login and hides all of the forms, messages, and buttons*/
  function resetLoginCredentials_Layout(){
    hideLoginForm();
		hidePasswordForm();	
    hideUsernameForm();
    //messages & links on the form
		document.getElementById("loginMessage").innerHTML = "";
		document.getElementById("forgotUsername").style.display = "none";
		document.getElementById("forgotPassword").style.display = "none";
  }
//AJAX PROCESSES
	/*Find employee username*/
	  function findUsername(){
		  var url = '../includes/userChanges.php';
			var data = {}
			  data.method = 'getUsername';
			var email = $("#email3").val();
			if(email !==''){
				data.email = email;
			}else{
				$("#email3").focus();
			}
      document.getElementById("loginMessage").innerHTML = "An email is being sent.";
			doAjax(data, emailResponse, url);
			return false;
		}
	/*resets a user's password*/
		function resetPassword(action_in){
			var url = '../includes/userChanges.php';
			var data = {}
					data.method = 'resetPassword';
					data.action = action_in;
			
			var username = $("#username2").val();
			if (username !==''){
				data.username = username;
			}else{
				$("#username2").focus();
        return false;
			}
			var email = $("#email2").val();
			if(email !==''){
				data.email = email;
			}else{
				$("#email2").focus();
        return false;
			}
      document.getElementById("loginMessage").innerHTML = "An email is being sent.";
			var thisAjax = doAjax(data, emailResponse, url);	
			return false;
		}
  /*Response to username & email reset, provides navigation back to the original login form.*/
		function emailResponse(response_in){
			console.log(response_in);
			if(response_in === "Success"){
				document.getElementById("loginMessage").innerHTML = 
				"Message has been sent!<br />Didn't receive an email?  Please check your spam folder or check with your account administrator to see if you have access to this site.";
         var hyperlink = $('<a>', {'href':'#', 'class':'passwordReset', 'html':' Return to Login'}).appendTo('#loginMessage');
         hyperlink.on("click", function(event){Navigate_loginPage();});
			}else{
				document.getElementById("loginMessage").innerHTML = "Message could not be sent!";
			}		
		}

