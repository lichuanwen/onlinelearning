function validate(){

var formObj = document.proctor;
var emailAd = document.proctor.email.value;

if (formObj.firstName.value == "") {
	window.alert("Please enter your first name.");
	formObj.firstName.focus();
	return false;
	}
	
if (formObj.lastName.value == "") {
	window.alert("Please enter your last name.");
	formObj.lastName.focus();
	return false;
	}

else if (formObj.studentNum.value == "") {
	window.alert("Please enter your Humber Student Number.");
	formObj.studentNum.focus();
	return false;
	}

else if (formObj.studentNum.value.search(/\d{3}\-\d{3}\-\d{3}/) == -1) {
	window.alert("Please enter a valid 9 digit Humber student number with dashes. For example: xxx-xxx-xxx");
	formObj.studentNum.focus();
	return false;
	}

else if (formObj.email.value == "") {
	window.alert("Please enter a valid email address.");
	formObj.email.focus();
	return false;
	}

else if ((emailAd.indexOf("@") == -1) || (emailAd.indexOf(".") == -1)){
	window.alert("Email address is not valid.");
	formObj.email.focus();
	formObj.email.select();
	return false;
	}


else if (formObj.elements['courseCode'].selectedIndex < 1) {
	window.alert("Please select a course.");
	formObj.courseCode.focus();
	return false;
	}

else if (formObj.elements['courseSection'].selectedIndex < 1) {
	window.alert("Please select your online course's section number.");
	formObj.courseSection.focus();
	return false;
	}

else if (formObj.elements['campus'].selectedIndex < 1) {
	window.alert("Please choose a test centre campus.");
	formObj.campus.focus();
	return false;
	}

else if (formObj.comments.value == "") {
	window.alert("You must explain why you are submitting the proctor request form. Be sure to provide any further detail that may help us process your proctor request faster.");
	formObj.comments.focus();
	return false;
	}

}
