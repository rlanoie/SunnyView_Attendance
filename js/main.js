jQuery(document).ready(function($){
	//if you change this breakpoint in the style.css file (or _layout.scss if you use SASS), don't forget to update this value as well
	var MqL = 1170;
	//move nav element position according to window width
	moveNavigation();
	$(window).on('resize', function(){
		(!window.requestAnimationFrame) ? setTimeout(moveNavigation, 300) : window.requestAnimationFrame(moveNavigation);
	});

	//mobile - open lateral menu clicking on the menu icon
	$('.cd-nav-trigger').on('click', function(event){
		event.preventDefault();
		if( $('.cd-main-content').hasClass('nav-is-visible') ) {
			closeNav();
			$('.cd-overlay').removeClass('is-visible');
		} else {
			$(this).addClass('nav-is-visible');
			$('.cd-main-header').addClass('nav-is-visible');
			$('.cd-main-content').addClass('nav-is-visible').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				$('body').addClass('overflow-hidden');
			});
			toggleSearch('close');
			$('.cd-overlay').addClass('is-visible');
		}
	});

	//open search form
	$('.cd-search-trigger').on('click', function(event){
		event.preventDefault();
		toggleSearch();
		closeNav();
	});

	//submenu items - go back link
	$('.go-back').on('click', function(){
		$(this).parent('ul').addClass('is-hidden').parent('.has-children').parent('ul').removeClass('moves-out');
	});

	function closeNav() {
		$('.cd-nav-trigger').removeClass('nav-is-visible');
		$('.cd-main-header').removeClass('nav-is-visible');
		$('.cd-primary-nav').removeClass('nav-is-visible');
		$('.has-children ul').addClass('is-hidden');
		$('.has-children a').removeClass('selected');
		$('.moves-out').removeClass('moves-out');
		$('.cd-main-content').removeClass('nav-is-visible').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
			$('body').removeClass('overflow-hidden');
		});
	}

	function toggleSearch(type) {
		if(type=="close") {
			//close serach 
			$('.cd-search').removeClass('is-visible');
			$('.cd-search-trigger').removeClass('search-is-visible');
			$('.cd-overlay').removeClass('search-is-visible');
		} else {
			//toggle search visibility
			$('.cd-search').toggleClass('is-visible');
			$('.cd-search-trigger').toggleClass('search-is-visible');
			$('.cd-overlay').toggleClass('search-is-visible');
			if($(window).width() > MqL && $('.cd-search').hasClass('is-visible')) $('.cd-search').find('input[type="search"]').focus();
			($('.cd-search').hasClass('is-visible')) ? $('.cd-overlay').addClass('is-visible') : $('.cd-overlay').removeClass('is-visible') ;
		}
	}

	function checkWindowWidth() {
		//check window width (scrollbar included)
		var e = window, 
            a = 'inner';
        if (!('innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        if ( e[ a+'Width' ] >= MqL ) {
			return true;
		} else {
			return false;
		}
	}

	function moveNavigation(){
		var navigation = $('.cd-nav');
  		var desktop = checkWindowWidth();
        if ( desktop ) {
			navigation.detach();
			navigation.insertBefore('.cd-header-buttons');
		} else {
			navigation.detach();
			navigation.insertAfter('.cd-main-content');
		}
	}
});

//GENERAL FUNCTIONS --------------------------------------------------
		//--USER PERMISSIONS
			/*Access Page - check if the user has permission to access this page.
				if permission is granted unlock the form fields*/
				function getUserPermission(page, permission){
					var thisPermission = permission;
          	if(thisPermission == 'none'){
               window.location = "../dashboard";
            }
              
          var thisPage = page;
					  switch (page) {
							case 'resident':  //redirect to the admin page
							  if(thisPermission === 'read'){
                  disable('#disableResidentChange');
                  disable('#modalbtn_deactivateRes');
                  disable('#colDeleteRes');
                }else if(thisPermission === 'write'){
							    enable('#disableResidentChange');
                  enable('#modalbtn_deactivateRes');
                  enable('#colDeleteRes');
							  }else{
							    window.location = "../dashboard";
							  }
							  break;
							case 'employee':
							  if(thisPermission === 'read'){
							    disable('#disableUserChange');
							    disable('#deactivateEmp');   
                  disable('#colDeleteUser');
							  }else if(thisPermission === 'write'){
							    enable('#disableUserChange');                  
                  enable('#deactivateEmp');
                  enable('#colDeleteUser');
                } else{
                  window.location = "../dashboard";
                } 
							  break;
               case 'attendance':
                if(thisPermission === 'read'){
                  disable('#disableAbsenceChange');
                  disable('#disableFilter');
                }else if(thisPermission === 'write'){
                  enable('#disableAbsenceChange');
                  enable('#disableFilter');
                }else{
                  window.location = "../dashboard";
                }
                break;
              case 'absence':
               if(thisPermission === 'read'){
                  disable('#disableFilter');
                }else if(thisPermission === 'write'){
                  enable('#disableFilter');
                }else{
                  window.location = "../dashboard";
                }
                break; 
            }
          
					}
				// at least one number, one lowercase and one uppercase letter
    		// at least six characters that are letters, numbers or the underscore
 				function checkPassword(str){
   				var re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\w{6,}$/;
    			return re.test(str);
  			}
			/*Used to distinguish fields and page features that are UNLOCKED to the user.*/
				function removeGrey(id, css){
					var element = document.getElementById(id);
    			element.classList.remove(css);
				}
			/*Used to distinguish fields and page features that are LOCKED to the user.*/
				function addGrey(id, css){
					document.getElementById(id).className = css
				}
			/*Disable the current portion of the form
				ADDS property DISABLE
				ADDS class disabled*/
				function disable(valuein){
					var value = valuein;
					$(value).prop('disabled', true);
					$(value).addClass('disabled');
				}
			/*Enable the current portion of the form
				REMOVES  property DISABLE
				REMOVES class disabled*/
				function enable(valuein){
					var value = valuein;
					$(value).prop('disabled', false);
					$(value).removeClass('disabled');
				}
			/*Move focus to form field & resets value*/
				function resetFormField(fieldID, field){
					$(fieldID).focus();
					field.value = "";
				}
			
			function inputDateFormat(date_input){
				var formattedDate = new Date(date_input +'PST');
				var d = ("0" + (formattedDate.getDate())).slice(-2);
				var m =  ("0" + (formattedDate.getMonth() + 1)).slice(-2);
				var y = formattedDate.getFullYear();
				return y + "-" + m + "-" + d;
			}

			/*Date formatting MM-DD-YYYY*/
				function dateFormatting(date_input){
					var formattedDate = new Date(date_input + 'PST');
					var d = ("0" + (formattedDate.getDate())).slice(-2);
					var m =  ("0" + (formattedDate.getMonth() + 1)).slice(-2);
					var y = formattedDate.getFullYear();
					return m + "-" + d + "-" + y;
				}
			/*Date & Time formatting MM-DD-YYYY h:m:s am/pm*/
				function dateTimeFormatting(date_input){
					var formattedDate = new Date(date_input);
					var d = ("0" + (formattedDate.getDate())).slice(-2);
					var m =  ("0" + (formattedDate.getMonth() + 1)).slice(-2);
					var y = formattedDate.getFullYear();
					var hr = formattedDate.getHours();
					var ampm = "am";
							if( hr > 12 ) {
    						hr -= 12;
    						ampm = "pm";
							}
					var min = formattedDate.getMinutes();
							if (min < 10) {
    						min = "0" + min;
							}
					var seconds =formattedDate.getSeconds();
							if (seconds < 10) {
    						seconds = "0" + seconds;
							}
					return m + "-" + d + "-" + y + " "+ hr + ":"+ min + ":" + seconds + ampm;
				}
			/*DELETE DIV Remove all the elements in the specified div*/
				function deleteDivID(div){
					var myNode = document.getElementById(div);
					while (myNode.firstChild) {
    				myNode.removeChild(myNode.firstChild);
					}
				}
//PRINT FUNCTION---------------------------------------------------------
	//PRINT BY IDENTIFICATION OR CLASS
		//print a portion of the page by id or class
				function printByTag(id){
	  			var htmlBody=document.getElementById(id).innerHTML;	
					var mywindow = window.open('', '', 'height=1000,width=1000,scrollbars=0');
	
  				mywindow.document.write('<html><head>');
		
					mywindow.document.write('</head>');
	
					var $linkBoot = mywindow.document.createElement('link');
	  			$linkBoot.id = 'id3';
  				$linkBoot.rel = 'stylesheet';
  				$linkBoot.href = '../css/bootstrap.min.css';
					$linkBoot.media = 'all';
  				mywindow.document.head.appendChild($linkBoot);
	
					var $linkCSS = mywindow.document.createElement('link');
	  			$linkCSS.id = 'id2';
  				$linkCSS.rel = 'stylesheet';
  				$linkCSS.href = '../css/pdf.css';
					$linkCSS.media = 'all';
  				mywindow.document.head.appendChild($linkCSS);
	
					mywindow.document.write('<body onload="window.print();window.close()">');
  				mywindow.document.write(htmlBody);
			
				  mywindow.document.write('</body></html>');
					mywindow.document.close();
					mywindow.focus();
				}



