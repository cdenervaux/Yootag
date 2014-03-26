// ****************
// Configuration
// ****************

// Absolute Path definition
var ABSPATH ='http://dev.yootag.com';

// Path to IMG directory
var ABSPATH_IMG = ABSPATH+'/en/img/';


// ****************
// Global variables
// ****************

// getMainframeURL variable
var mainframeURL;

// Login Form variables
var log_email_stat = false;
var log_password_stat = false;

// Registration Form variables
var reg_email_stat = false;
var reg_password_stat = false;
var reg_rpassword_stat = false;

// New Password Form variables
var new_password_stat = false;
var new_rpassword_stat = false;


// ****************
// Shared functions
// ****************

// JS URL parameter parsing function
function getParam(name) {
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
 	var regexS = "[\\?&]"+name+"=([^&#]*)";
 	var regex = new RegExp( regexS );
 	var results = regex.exec( window.location.href );
 	
	if( results == null )
  		return "";
	else
 		return results[1];
}

// Get the main frame URL
function getMainframeURL() {
	mainframeURL = getParam('add_url');
}

// Close the iFrame
function closeMarklet(message) {
	
	var url = mainframeURL + "#yootag=" + message;
	try {
		top.location.replace(url);
	} catch (e) {
		top.location = url;
	}
}


// ****************
// Events functions
// ****************


// Logreg / iFrame Login Form - Email validation function
$(document).ready(function () {
  
  $('#log_email').change(function () {	  
	  var regEx = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	
	  if(!$('#log_email').val().match(regEx)){
		  $('#msg_log_email').html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Please enter your email');
		  // Update global variable
		  log_email_stat = false;
	  }
	  else {
		// Error message reset
		$('#msg_log_email').html('');
		$('.gen_msgbox').html('');
		
		// Update global variable
		log_email_stat = true;
	  }
  })
});


// Logreg / iFrame Login Form - Password validation function
$(document).ready(function () {
  
  $('#log_password').change(function () { 
	  
    if ($('#log_password').val().length < 6) {
        $('#msg_log_password').html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Please enter your password');
		// Update global variable
		log_password_stat = false;
	  }
	  else {
		// Error message reset
		$('#msg_log_password').html('');
		$('.gen_msgbox').html('');
		
		// Update global variable
		log_password_stat = true;
	  }
  })
});


// Logreg / iFrame Login Form - Submit function
$(".log_submit_button").click(function() {
  
	// Email field check	
	if ($("#log_email").val() == "" || !log_email_stat) {
		$('#log_email').change();
		$("#log_email").focus(); 
		return false;
	}
	  	
	// Password field check
	if ($("#log_password").val() == "" || !log_password_stat) {
		$('#log_password').change();
		$("#log_password").focus();
		return false;
	}
		    
	// AJAX Server request
	$.ajax({
	type: 'POST',
	url: ABSPATH+'/en/user/login.php',
	data: 'log_email='+ $("#log_email").val() 
	+ '&log_password=' + $("#log_password").val() 
	+ '&log_remember=' + $("#log_remember").prop('checked')
	+ '&log_type=' + $("#log_type").val(),
	dataType: 'json',
	success: function(response) {
			  if(response.status) {
				  
				  // Valid username / password
				  if(response.msg != '' && response.msg === 'std')
				  	 window.location = ABSPATH+'/en/dashboard/dashboard.php';
				   
				  if(response.msg != '' && response.msg === 'frm')
				  	 window.location = ABSPATH+'/en/tagmarklet/frame.php?add_url=' + $('#add_url').val();
			      	  
			  } else {
				  // Invalid username / password
				  $('.gen_msgbox').html(response.msg);
			  }   
	},
	error: function() {
		$('.gen_msgbox').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request error. Please try again later');
	}
	}); 
    return false;
});


// Logreg / iFrame Forgot Password Link - Clear form function
$('#for_password').click(function() {
	$("#lost_email").val('');
	$("#msg_lost_email").html('');
});


// Logreg / iFrame Reset Password Form - Submit function
$(".lost_submit_button").click(function() {

	// Email field check	
	var regEx = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	
	  if(!$('#lost_email').attr("value").match(regEx)){
		  $('#msg_lost_email').html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Please enter a valid email');
		  $('#lost_email').focus();
		  return false;
	  }
	
	// Process request
	$('#msg_lost_email').html(' <img src="'+ABSPATH_IMG+'ajax-loader.gif" /> Please wait...');
		    
	// AJAX Server request
	$.ajax({
	type: 'POST',
	url: ABSPATH+'/en/user/checkreset.php',
	data: 'lost_email='+ $("#lost_email").val(),
	dataType: 'json',
	success: function(response) {
		$('#msg_lost_email').html(response.msg);
	},
	error: function() {
		$('#msg_lost_email').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request error. Please try again later');
	}
	});
    return false;
});


// Registration Form - Email validation function
$(document).ready(function () {
  
  $('#reg_email').change(function () {
	var regEx = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

	if(!$('#reg_email').val().match(regEx)){
  		$('#msg_reg_email').html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Please enter a valid email address');
		
		// Update global variable
		reg_email_stat = false;  
		return false;
	}
    else {
	  // Error message reset
	  $('#msg_reg_email').html('');
	  $('.gen_msgbox').html('');
	  
	  // AJAX Server request
		$.ajax({
		  type: 'POST',
		  url: 'checkreg.php',
		  data: 'reg_email=' + $('#reg_email').val(),
		  dataType: 'json',
		  success: function (response) {
			  // Update global variable
			  if (response.status) {
				  reg_email_stat = true;	  
			  } else {
				  reg_email_stat = false;
				  $('#msg_reg_email').html(response.msg);
			  }
		  },
		  error: function() {
		      $('#msg_reg_email').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request error. Please try again later');
		  }
		});
    }
  });
});


// Registration Form - Password validation function
$(document).ready(function () {
   
  $('#reg_password').change(function () {
	
	// Validation rules: 6 characters min
    if ($('#reg_password').val().length < 6) {
        $('#msg_reg_password').html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Password must be at least 6 characters long');
		
		// Update global variable
		reg_password_stat = false;
    
	} else {
		// Error message reset
		$('#msg_reg_password').html('');
	    $('.gen_msgbox').html('');
		
		// Update global variable
		reg_password_stat = true;
		
		if (!$('#reg_matchpassword').attr("value") == "") {
			$('#reg_matchpassword').change();
		}
	}
	
  });
});


// Registration Form - Password matching function
$(document).ready(function () {
  
	$('#reg_matchpassword').change(function () {

		if ($('#reg_password').val() != $('#reg_matchpassword').val()) {
		  $('#msg_reg_matchpassword').html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Your password does not match');
		  
		  // Update global variable
		  reg_rpassword_stat = false;
		
		} else {
		  // Error message reset
		  $('#msg_reg_matchpassword').html('');
		  $('.gen_msgbox').html('');
		  	
		  // Update global variable
		  reg_rpassword_stat = true;	
		}
	});
});


// Registration Form - Submit function
$(".reg_submit_button").click(function() {
  
    // Email field check	
	if ($('#reg_email').val() == "" || !reg_email_stat) {
		$("#reg_email").change();
		$("#reg_email").focus();
		return false;
	}

    // Password field check	
	if ($("#reg_password").val() == "" || !reg_password_stat) {
		$("#reg_password").change();
		$("#reg_password").focus();
		return false;
	}
	
	// Retype Password field check	
	if ($('#reg_matchpassword').val() == "" || !reg_rpassword_stat) {
		$("#reg_matchpassword").change();
		$("#reg_matchpassword").focus();
		return false;
	}
	
	// Process request
	$('.gen_msgbox').html(' <img src="'+ABSPATH_IMG+'ajax-loader.gif" /> Please wait...');
				 
	// AJAX Server request
	$.ajax({
	  type: 'POST',
	  url:  ABSPATH+'/en/user/reguser.php',
	  data: 'reg_email=' + $('#reg_email').val() + '&reg_password=' + $("#reg_password").val(),
	  dataType: 'json',
	  success: function (response) {
	  	 $('.gen_msgbox').html(response.msg);
	  },
	  error: function() {
		 $('.gen_msgbox').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request Error. Please try again later');
	  }		
	});
	return false;
});


// New Password Form - Password validation function
$(document).ready(function () {
   
  $('#new_password').change(function () {
	  
	  // Validation rules: 6 characters min
	  if ($("#new_password").val().length < 6) {
		  $("#msg_new_password").html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Password must be at least 6 characters long');
		  
		  // Update global variable
		  new_password_stat = false; 
	  
	  } else {
		  // Error message reset
		  $("#msg_new_password").html('');
		  $('.gen_msgbox').html('');
		  
		  // Update global variable
		  new_password_stat = true;
		  
		  if (!$('#new_matchpassword').val() == "") {
			  $('#new_matchpassword').change();
			  }
	  }
  });
  
});


// New Password Form - Password matching function
$(document).ready(function () {
  
	$('#new_matchpassword').change(function () {

		if ($("#new_password").val() != $("#new_matchpassword").val()) {
		  $("#msg_new_matchpassword").html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Your password does not match');
		  
		  // Update global variable
		  new_rpassword_stat = false;
		
		} else {
		  // Error message reset
		  $("#msg_new_matchpassword").html('');
		  $('.gen_msgbox').html('');
		  	
		  // Update global variable
		  new_rpassword_stat = true;	
		}
	});
});


// New Password Form - Submit function
$(".new_submit_button").click(function() {

	// Password field check	
	if ($("#new_password").val() == "" || !new_password_stat) {
		$("#new_password").change();
		$("#new_password").focus();
		return false;
	}
	
	// Retype Password field check	
	if ($("#new_matchpassword").val() == "" || !new_rpassword_stat) {
		$("#new_matchpassword").change();
		$("#new_matchpassword").focus();
		return false;
	}
		    
	// AJAX Server request
	$.ajax({
	type: 'POST',
	url: ABSPATH+'/en/user/setpass.php',
	data: 'token=' + getParam( 'token' ) 
	+ '&new_pass=' + $("#new_password").val(),
	dataType: 'json',
	success: function(response) {
		$('.gen_msgbox').html(response.msg);
		$('#new_pass').html('');
	},
	error: function() {
		$('.gen_msgbox').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request error. Please try again later<br/>');
	}
	}); 
    return false;
});

	
// Profile Form - Submit function
$('.pro_submit_button').click(function() {
	  
	  // Error message reset
	  $('.gen_msgbox').html('');
	  
	  // AJAX Server request
	  $.ajax({
		type: 'POST',
		url: 'savepro.php',
		data: 'pro_firstname='+ $("#pro_firstname").val() 
		+ '&pro_lastname=' + $("#pro_lastname").val(),
		dataType: 'json',
		success: function(response) {
			  
			  if(!response.status && response.msg == 'redirect') {
				  window.location = ABSPATH+"/en/user/logreg.php";
			  
			  } else {
				  $(".gen_msgbox").html(response.msg);
			  }
		},
		error: function() {
			$(".gen_msgbox").html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request Error. Please try again later');
	    }					
	   });
	 return false;
});


// Contact Form - Email validation function
$(document).ready(function () {
  var msg_con_email = $('#msg_con_email');
  
  $('#con_email').change(function () {
	var regEx = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    con_email_val = $('#con_email').attr("value");

	if(!con_email_val.match(regEx)){
  		msg_con_email.html('<img src="'+ABSPATH_IMG+'cross_circle_frame.png" /> Please enter a valid email address');
		// Update global variable
		con_email_stat = false;  
		return false;
	}
    else {
	   	// Update global variable
		con_email_stat = true;
    }
  });
});


// Contact Form - Submit function
var con_email_stat = false;

$(".con_submit_button").click(function() {
  
  	// NEEDS UPDATE
  	if ($('#con_name').val() == '' || !con_email_stat || $('#con_message').val() == '') {
      $('#msg_con_sendmsg').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" /> Please complete and review all fields before submitting the form');
    
	} else {		
	  // Processing request
	  $('#msg_con_sendmsg').html(' <img src="'+ABSPATH_IMG+'ajax-loader.gif" /> Please wait...');
	
	  // AJAX Server request
	  $.ajax({
		type: 'POST',
		url: ABSPATH+'/en/contact/sendmsg.php',
		data: 'con_name='+ $('#con_name').val() 
		+ '&con_email=' + $('#con_email').val() 
		+ '&con_enquiry=' + $('#con_enquiry').val() 
		+ '&con_message=' + $('#con_message').val(),
		dataType: 'json',
		success: function (response) {
			$('#msg_con_sendmsg').html(response.msg);
		},
	  	error: function() {
		 	$('#msg_con_sendmsg').html('<img src="'+ABSPATH_IMG+'exclamation_red_frame.png" />&nbsp;Request Error. Please try again later');
	  }		
	  });
    }
	return false;
});


// iFrame Add Product Form - Submit function
$(".add_submit_button").click(function() {
			 
	// AJAX Server request
	$.ajax({
	  type: 'POST',
	  url: 'addprod.php',
	  data: 'add_url=' + $('#add_url').val() 
	  + '&site_uid=' + $('#site_uid').val() 
	  + '&add_title=' + $('#add_title').val() 
	  + '&add_boxshot=' + $('#add_boxshot').val() 
	  + '&add_currency=' + $('#add_currency').val() 
	  + '&add_amount=' + $('#add_amount').val()
	  + '&add_alert=' + $('#add_alert').val() 
	  + '&add_listname=' + document.getElementById('add_listname').value,
	  
	  dataType: 'json',
	  success: function(response) {
			  
		  // If user is not logged in, redirect to iFrame login
		  if(!response.status && response.msg == 'redirect') {
			  window.location = ABSPATH+"/en/tagmarklet/logframe.php?add_url=" + $('#add_url').val();

		  } else {
			  $("#frame_product").hide();
			  $("#frame_product_result").show();
			  $("#frame_product_result_msg").html(response.msg);	  
		  }
	  },
		  error: function() {
			  $("#frame_product").hide();
			  $("#frame_product_result").show();
			  $("#frame_product_result_msg").html('Oops! There was an error adding this product to your dashboard. Sorry for the inconvenience.<br><br>Please try again later.');	  	  
		  }					
   });
	 return false;
});


// Dashboard - Filter by function
$(document).ready(function () {
  
    $("#prod_list").change(function() {
		
		 if ($(this).find(':selected').val() === '0') {
             window.location = ABSPATH+'/en/dashboard/dashboard.php'; 
        } else {
		 	 window.location = ABSPATH+'/en/dashboard/dashboard.php?filter=' + $(this).find(':selected').val(); 
		}
		
    });
});


// Dashboard - Product properties display function
$(document).on("click", "a.gen_modal_edit", function() {
	$(this).parent().find('.dashboard_modal_editprod').show();
	return false;
});


// Dashboard - Product properties alert function
$(document).ready(function () {
    $(document).on("change", ".mod_alert", function () {
		
	  // AJAX Server request
	  $.ajax({
		type: 'POST',
		url: 'modalert.php',
		data: 'prod_uid=' + $(this).parent().parent().find('.prod_uid').val()
		   + '&mod_alert=' + $(this).find(':selected').val(), 
		dataType: 'json',
		success: function(response) {  	
			},
		error: function() {
			}					
	  });
	  return false;
   });
   
});


// Dashboard - Product properties list function
$(document).ready(function () {
    $(document).on("change", ".mod_list", function () {
		
	  // AJAX Server request
	  $.ajax({
		type: 'POST',
		url: 'modlist.php',
		data: 'prod_uid=' + $(this).parent().parent().find('.prod_uid').val()
		   + '&mod_list=' + $(this).find(':selected').val(), 
		dataType: 'json',
		success: function(response) {  	
			},
		error: function() {
			}					
	  });
	  return false;
   });
   
});


// Dashboard - Product properties hide function
$(document).on("click", "a.gen_modal_back", function() {
	$(this).parent().hide();
	return false;
});


// Dashboard - Product delete display function
$(document).on("click", "a.gen_modal_remove", function() {
	$(this).parent().find('.dashboard_modal_delprod').show();
	return false;
});


// Dashboard - Product delete Yes function
$(document).ready(function () {
    $(document).on("click", "a.dashboard_modal_delprod_yes", function () {
		
	  // AJAX Server request
	  $.ajax({
		type: 'POST',
		url: 'delprod.php',
		data: 'prod_uid=' + $(this).parent().parent().find('.prod_uid').val(), 
		dataType: 'json',
		success: function(response) {
			},
		error: function() {
			}					
	  });
	  $(this).parent().hide();
	  $(this).parent().parent().hide();
	  return false;
   });
   
});


// Dashboard - Product delete No function
$(document).on("click", "a.dashboard_modal_delprod_no", function() {
	$(this).parent().hide();
	return false;
});


// Generic - Modal Close button function
$(".gen_modal_close").click(function(){
	$(this).parent().hide();
	return true;
});


// Dashboard - Manage List display function
$("#dashboard_editlist").click(function(){
	
	 var posX = $(this).offset().left,
         posY = $(this).offset().top;
	
	/*var alink = $(this),
		alinkPos = alink.position(),
		popupTop = alinkPos.top+20,
		popupLeft = alinkPos.left-100;*/

	$('#dashboard_modal_editlist').css({left:posX+140, top:posY+25});
	$('#dashboard_modal_editlist').show();
	return false;
});


// Dashboard - Manage List Rename click function
$(document).on("click", "a.dashboard_modal_editlist_rename", function() {
			
        var textBlock = $(this).parent().find('.dashboard_modal_editlist_name');
		//var textBlock = 'div.dashboard_modal_editlist_name';
        // Create a new input to allow editing text on double click
        var textBox = $('<input/>');
        textBox.insertAfter(textBlock).val('test');
		
		/*
        // Hiding the input and showing the original div
        textBox.blur(function() {
            toggleVisiblity(false);
        });

        toggleVisiblity = function(editMode) {
            if (editMode == true) {
                textBlock.hide();
                textBox.show().focus();
                // workaround, to move the cursor at the end in input box.
                textBox[0].value = textBox[0].value;
            }
            else {
                textBlock.show();
                textBox.hide();
                textBlock.html(textBox.val());
            }
		}
		toggleVisiblity(true);
		*/
});


// Dashboard - Manage List Edit click function
$(document).on("click", "a.dashboard_modal_editlist_edit_click", function() {
	$('#dashboard_modal_editlist_name').val($(this).parent().parent().find('.dashboard_modal_editlist_name').html());
	$('#dashboard_modal_editlist_edit').show();
	return false;
});


// Dashboard - Manage List Delete click function
$(".dashboard_modal_editlist_delete_click").click(function(){
	$('#dashboard_modal_editlist_delete').show();
	return false;
});


// Dashboard - Manage List Create click function
$(".dashboard_modal_editlist_create_click").click(function(){
	$('#dashboard_modal_editlist_create').show();
	return false;
});