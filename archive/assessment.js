  var yes=new Array();
       yes[0]='Good. This will allow you to regularly participate in the various learning activities in an online course.';
       yes[1]='Great! Email is one of your main communication tools when taking an online course.';
       yes[2]='The flexibility of online learning allows you to work through the course material at a time that’s convenient for you; however, it is important to note that some online courses require students to participate in online discussions, chat sessions and/or group projects.';
       yes[3]='Good.  This shows that you are willing to create a schedule that will allow for reading, review, reflection, and to respond to the content and activities in an online course.';
       yes[4]='Great! In an online environment you may not have immediate access to your instructor therefore it is important that you can work through potential issues on your own or seek out additional resources to help.';
  var no=new Array();
       no[0]='This may present some challenges, however, students are welcome to use the computers in the Open Learning Centre’s computer lab.';
       no[1]='Most email systems are easy to learn.  It should not be a deterrent for taking an online course.   The OLC staff will be happy to provide you an introduction to email if you would like.';
       no[2]='An effective learning environment creates a sense of community for its students.  While learning online you will have the opportunity to interact electronically with your instructor and classmates through the use of email, discussion and chat.';
       no[3]='As a rule of thumb, we encourage students to allot at least the same time they would spend in a face to face class while studying online. Typically for an on-campus course a student will spend 3 hours/week in class and an additional 3-5 hours in preparation and completing assignments.  This equates to 6-8 hours.';
       no[4]='It is important that an online learner can motivate him/herself to stay on task and find additional resources to help understand the content.';

function selfAss(m,n) {
   obj=document.getElementById('assess'+m);

if(n==0) {
   obj.innerHTML=yes[m-1];
   obj.style.borderColor='#000';
   obj.style.backgroundColor='#BBD6F4';
 }
else {
   obj.innerHTML=no[m-1];  
   obj.style.borderColor='#000';
   obj.style.backgroundColor='#BBD6F4'; 
  }
 }
