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

if (formObj.userName.value == "") {
	window.alert("Please enter your user name.");
	formObj.userName.focus();
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

if (formObj.school.value == "") {
	window.alert("Enter your school or department's name.");
	formObj.school.focus();
	return false;
	}

}
