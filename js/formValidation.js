function validateOffcampus(){

var formObj = document.offcampus;
var emailAd = document.offcampus.email.value;
var proctorEmail = document.offcampus.proctorEmail.value;
var flatnumberid = /^\d+$/;

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

else if (formObj.proctorName.value == "") {
	window.alert("Please enter the proctor's full name");
	formObj.proctorName.focus();
	return false;
	}

else if (formObj.proctoredSite.value == "") {
	window.alert("Please enter the company/organization name your proctor works for.");
	formObj.proctoredSite.focus();
	return false;
	}

else if (formObj.proctorPosition.value == "") {
	window.alert("Please enter proctor's position or title");
	formObj.proctorPosition.focus();
	return false;
	}

else if (formObj.proctorEmail.value == "") {
	window.alert("Please enter your proctor's valid business email address");
	formObj.proctorEmail.focus();
	return false;
	}

else if ((proctorEmail.indexOf("@") == -1) || (proctorEmail.indexOf(".") == -1)){
	window.alert("Proctor's Email address is not valid.");
	formObj.proctor_email.focus();
	formObj.proctor_email.select();
	return false;
	}

else if (formObj.proctorPhone.value == "") {
	window.alert("Please enter proctor's business phone number along with the area code");
	formObj.proctorPhone.focus();
	return false;
	}

else if (formObj.streetNumName.value == "") {
	window.alert("Please enter your proctor's street address");
	formObj.streetNumName.focus();
	return false;
	}

else if (formObj.cityTown.value == "") {
	window.alert("Please enter the city name where your proctored site is located");
	formObj.cityTown.focus();
	return false;
	}

else if (formObj.provinceState.value == "") {
	window.alert("Please enter the province name where your proctored site is located");
	formObj.provinceState.focus();
	return false;
	}

else if (formObj.postalCode.value == "") {
	window.alert("Please enter the postal code of your proctored site");
	formObj.postalCode.focus();
	return false;
	}

else if (formObj.country.value == "") {
	window.alert("Please enter the country name where your proctored site is located");
	formObj.country.focus();
	return false;
	}

else if (formObj.comments.value == "") {
	window.alert("You must explain why you are submitting the proctor request form. Be sure to provide any further detail that may help us process your proctor request faster.");
	formObj.comments.focus();
	return false;
	}

else if (formObj.elements['campus'].selectedIndex < 1) {
	window.alert("Please choose a test centre campus.");
	formObj.campus.focus();
	return false;
	}

if(formObj.ofcWebsite.value != ""){
	window.alert("Are you a robot?");
	return false;
}

}


function validateVirtual(){

var formObj = document.virtual;
var emailAd = document.virtual.email.value;

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

else if (formObj.comments.value == "") {
	window.alert("You must explain why you are submitting the proctor request form. Be sure to provide any further detail that may help us process your proctor request faster.");
	formObj.comments.focus();
	return false;
	}

}


function validateTestCentre(){

var formObj = document.testcentre;
var emailAd = document.testcentre.email.value;

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

if (formObj.studentNum.value == "") {
	window.alert("Please enter your Humber Student Number.");
	formObj.studentNum.focus();
	return false;
	}

if (formObj.email.value == "") {
	window.alert("Please enter a valid email address.");
	formObj.email.focus();
	return false;
	}

if ((emailAd.indexOf("@") == -1) || (emailAd.indexOf(".") == -1)){
	window.alert("Email address is not valid.");
	formObj.email.focus();
	formObj.email.select();
	return false;
	}


if (formObj.elements['courseCode'].selectedIndex < 1) {
	window.alert("Please select a course.");
	formObj.courseCode.focus();
	return false;
	}

if (formObj.elements['campus'].value == "select") {
	window.alert("Please choose a test centre campus.");
	formObj.campus.focus();
	return false;
	}

if (formObj.comments.value == "") {
	window.alert("You must explain why you are submitting the proctor request form. Be sure to provide any further detail that may help us process your proctor request faster.");
	formObj.comments.focus();
	return false;
	}

if(formObj.tcWebsite.value != ""){
	window.alert("Are you a robot?");
	return false;
	}
}
