<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Online Courses: Open Learning Center</title>
    <link href="/navbar/navbar.css" type="text/css" rel="stylesheet" />
    <script src="/navbar/navbar.js" type="text/javascript"></script>

    <link rel="stylesheet" href="css/app.css" />
    <script src="bower_components/modernizr/modernizr.js"></script>
    
     <!-- GOOGLE ANALYTICS CODE BEGINS -->
	<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-52617827-1', 'auto');
  ga('send', 'pageview');

	</script>
    <!-- GOOGLE ANALYTICS CODE ENDS -->
    
  </head>
  <body>
    <!-- HUMBER NAVIGATION -->
    <?php include_once("/var/www/humber_global/navigation.php"); ?>
    <?php include_once("/var/www/humber_global/header.php"); ?>
    <!-- END OF HUMBER NAVIGATION -->

    <div id="courses" class="container">
      <div class="main">
        <!-- OLC Navigation -->
        <?php include 'includes/olc-navigation.php'; ?>
        <!-- End of OLC Navigation -->

        <!-- Courses Page Content -->
        <div class="row">
          <div class="large-12 columns">
            <h2 class="heading-background">Online Courses</h2>
          </div>
        </div>


        <div class="row">
          <div class="large-12 columns">
            <table class="courseSort">
              <thead>
                <tr>
                  <th class="sortable"><h3><span class="icon-sort"></span> Course Number</h3></th>
                  <th class="sortable"><h3><span class="icon-sort"></span> Course Name</h3></th>
                  <th class="sortable"><h3><span class="icon-sort"></span> Department</h3></th>
                  <th class="sortable"><h3><span class="icon-sort"></span> LMS</h3></th>
                </tr>
              </thead>

              <tbody>
                <?php
                  /* Required files */
                  require_once 'SupportFiles/opendb.php';
                  require_once 'SupportFiles/Messages.php';

                  /* Connect to Database */
                  connection(); 

                  $query = "SELECT C.`CName`, C.`CNum`, C.`CSec`, C.`Title`, S.`School`, L.`LMS`  FROM `courses` C,`schools` S, `lms_systems` L 
                   WHERE C.`school_fk` = S.`school_id` AND C.`lms_fk` = L.`lms_system_id` ORDER BY C.`CName`, C.`CNum` ASC";
                

                  $results = sendQuery($query);
                                    
                  if ($results){

                      $invalid_char = array(".", " ");
                      $nospaces = array("", "");
                      $underscore = array("_", "_");

                      while ($row = mysqli_fetch_object($results)) {
                        printf('
                            <tr>
                              <td>
                                <a class="course-link" data-reveal-id="course-info-modal" title="Click for login instructions">'.$row->CName.' '.$row->CNum.' '.$row->CSec.'</a>
                              </td>
                              <td class="course-title">'.$row->Title.'</td>
                              <td>'.$row->School.'</td>
                              <td class="lms">'.$row->LMS.'</td>
                            </tr>
                          '); 

                        
                      }  // while-loop {ends}
                  } // if .. results  {ends}
                ?>
              </tbody>
            </table>
          </div>

        </div>
      </div><!-- Main Close -->

  </div><!--Container-->

  <!-- HUMBER FOOTER -->
  <?php include_once("/var/www/humber_global/footer.php"); ?>
  <!-- END OF HUMBER FOOTER -->

  <!-- Course Info Modal Container -->
  <div id="course-info-modal" class="reveal-modal medium course-instructions" data-reveal>
    <a class="close-reveal-modal">&#215;</a>
  </div>


  <!-- DYNAMIC MODAL CONTENT -->

  <!-- Start of Blackboard Modal Content -->
  <div id="inner-blackboard">
    <h3 class="course-heading"></h3>
    <h5>User Name and Password</h5>
    <p>
      Your User Name will consist of 4 letters and 4 numbers (such as bcdf1234) found on your Admit-to-Class form. 
      <br />For password, please go to <a href="http://its.humber.ca/password" target="_blank">http://its.humber.ca/password</a> to create/reset the password.
    </p>
    <p class="warning-text"><span class="bold">ATTENTION</span>: User Names issued after February 19, 2013 may look different and start with the letter N followed by 8 random digits such as N12345678.</p>
    
    <h5>How to Log In</h5>
    <p>This online course is delivered through <a href="https://learn.humber.ca/" target="_blank">Blackboard Learn 9.1</a>. Once you have determined your User Name and created/reset password, login at <a href="https://learn.humber.ca/" target="_blank">https://learn.humber.ca</a></p>
    
    <h5>Textbook(s) and System Requirements</h5>
    <p>
      To order textbook(s) online, please visit <a href="https://www.efollett.com/" target="_blank">Humber's Online Bookstore</a> or call: 416-675-5066<br />
      See the <a href="#" data-reveal-id="system-requirements">System Requirements</a> to prepare your computer for online learning
    </p>

    <h5>Help/Support</h5>
    <p>
      <a href="contact.php">Contact</a> the OLC for help and support.
      <br />Check out several help tutorials on <a href="http://www.humber.ca/bb91help/student.php" target="_blank">Blackboard Learn 9.1 Student Help Website</a>
      <br /><a href="chat.html" target="_blank">Live chat</a> with a Student Support Advisor.
    </p>

    <h5>Prerequisites and Course Availability</h5>
    <p>Prerequisites are listed in the course description found in the Course Availability link below. It is the students' responsibility to meet the prerequisite requirements prior to registering in an online course. Students who register without meeting the prerequisites will NOT be given access to their online courses.</p>
    
    <p>
      This course has a set start and end date. Please click on the link below to view a course description and a list of availability dates: 
      <br /><a class="course-availability" href="http://www.humber.ca/continuingeducation/courses/online" target="_blank">See course availability details</a>
    </p>
    <a class="close-reveal-modal button radius btn-close">Close</a>
    <a class="close-reveal-modal">&#215;</a>
  </div>
  <!-- End of Blackboard Modal Content -->

  <!-- Start of Ontario Learn Modal Content -->
  <div id="inner-ontariolearn">
    <h3 class="course-heading"></h3>
    <h5>User Name and Password</h5>
    <p>To determine your OntarioLearn User ID, please go to <a href="https://embanet.frontlinesvc.com/app/answers/detail/a_id/662/p/3" target="_blank">What's my OntarioLearn User ID</a> web page?<br />
 	For your OntarioLearn Password, please go to <a href="http://www.ontariolearn.com/password.htm" target="_blank">What's my OntarioLearn Password</a> web page?</p>

    <h5>How to Login</h5>
    <p>This online course is delivered through <a href="http://www.ontariolearn.com" target="_blank">OntarioLearn</a>. Once you have determined your user name and password, login at <a href="http://www.ontariolearn.com" target="_blank">http://www.ontariolearn.com</a></p>
    
    <h5>Textbook(s) and System Requirements</h5>
    <p>
      To order textbook(s) online, please visit <a href="https://www.efollett.com/" target="_blank">Humber's Online Bookstore</a> or call: 416-675-5066<br />
      See the <a href="#" data-reveal-id="system-requirements">System Requirements</a> to prepare your computer for online learning
    </p>

    <h5>Help/Support</h5>
    <p>
      Contact the OLC for help and support.
      <br />Watch video tutorials on the <a href="http://www.youtube.com/channel/UCqZrKChz7tZU4E1jyPVWhaA/videos" target="_blank">Humber OLC YouTube channel</a>
      <br /><a href="chat.html" target="_blank">Live chat</a> with a Student Support Advisor.
    </p>

    <h5>Prerequisites and Course Availability</h5>
    <p>Prerequisites are listed in the course description found in the Course Availability link below. It is the students' responsibility to meet the prerequisite requirements prior to registering in an online course. Students who register without meeting the prerequisites will NOT be given access to their online courses.</p>
    
    <p>
      This course has a set start and end date. Please click on the link below to view a course description and a list of availability dates: 
      <br /><a class="course-availability" href="#" target="_blank">See course availability details</a>
    </p>
    <a class="close-reveal-modal button radius btn-close">Close</a>
    <a class="close-reveal-modal">&#215;</a>
  </div>
  <!-- End of Ontario Learn Modal Content -->

  <!-- Start of eHotel Modal Content -->
  <div id="inner-ehotel">
    <h3 class="course-heading"></h3>
    <h5>User Name and Password</h5>
    <p>Your user name and password will be emailed to you within three business days from the day we receive your <a href="_source/consent.docx" target="_blank">Consent Letter</a>.</p>
    <p class="italic small-text">
      Once you have registered for this course at the registrar's office or online, download the <a href="_source/consent.docx" target="_blank">CONSENT LETTER</a>, fill it out, and send it back to us via email or fax. 
      <br />You will receive your user name and password information in the email within three days from the day we receive your signed consent.
    </p>

    <h5>How to Login</h5>
    <p>Login to <a href="http://www.onlineschoolinc.com/students/index.cfm?s=22" target="_blank">Online School Inc.</a> once you have received your User name and Password information in the email.</p>
    
    <h5>Textbook(s) and System Requirements</h5>
    <p>
      To order textbook(s) online, please visit <a href="https://www.efollett.com/" target="_blank">Humber's Online Bookstore</a> or call: 416-675-5066<br />
      See the <a href="#" data-reveal-id="system-requirements">System Requirements</a> to prepare your computer for online learning
    </p>

    <h5>Help/Support</h5>
    <p>
      Contact the OLC for help and support.
      <br />Watch video tutorials on the <a href="http://www.youtube.com/channel/UCqZrKChz7tZU4E1jyPVWhaA/videos" target="_blank">Humber OLC YouTube channel</a>
      <br /><a href="chat.html" target="_blank">Live chat</a> with a Student Support Advisor.
    </p>

    <h5>Prerequisites and Course Availability</h5>
    <p>Prerequisites are listed in the course description found in the Course Availability link below. It is the students' responsibility to meet the prerequisite requirements prior to registering in an online course. Students who register without meeting the prerequisites will NOT be given access to their online courses.</p>
    
    <p>
      This course has a set start and end date. Please click on the link below to view a course description and a list of availability dates: 
      <br /><a class="course-availability" href="#" target="_blank">See course availability details</a>
    </p>
    <a class="close-reveal-modal button radius btn-close">Close</a>
    <a class="close-reveal-modal">&#215;</a>
  </div>
  <!-- End of eHotel Modal Content -->

  <!-- Start of Education System Modal Content -->
  <div id="inner-edusys">
    <h3 class="course-heading"></h3>
    <h5>User Name and Password</h5>
    <p>Your user name and password will be emailed to you within three business days from the day we receive your <a href="_source/consent.docx" target="_blank">Consent Letter</a>.</p>
    <p class="italic small-text">
      Once you have registered for this course at the registrar's office or online, download the <a href="_source/consent.docx" target="_blank">CONSENT LETTER</a>, fill it out, and send it back to us via email or fax. 
      <br />You will receive your user name and password information in the email within three days from the day we receive your signed consent.
    </p>

    <h5>How to Login</h5>
    <p>Login to <a href="http://www.education-web.net/humbercollege/" target="_blank">EDUCATION SYSTEM</a> once you have received your User name and Password information in the email.</p>

    <h5>Textbook(s) and System Requirements</h5>
    <p>
      To order textbook(s) online, please visit <a href="https://www.efollett.com/" target="_blank">Humber's Online Bookstore</a> or call: 416-675-5066<br />
      See the <a href="#" data-reveal-id="system-requirements">System Requirements</a> to prepare your computer for online learning
    </p>

    <h5>Help/Support</h5>
    <p>
      Contact the OLC for help and support.
      <br />Watch video tutorials on the <a href="http://www.youtube.com/channel/UCqZrKChz7tZU4E1jyPVWhaA/videos" target="_blank">Humber OLC YouTube channel</a>
      <br /><a href="chat.html" target="_blank">Live chat</a> with a Student Support Advisor.
    </p>

    <h5>Prerequisites and Course Availability</h5>
    <p>Prerequisites are listed in the course description found in the Course Availability link below. It is the students' responsibility to meet the prerequisite requirements prior to registering in an online course. Students who register without meeting the prerequisites will NOT be given access to their online courses.</p>
    
    <p>
      This course has a set start and end date. Please click on the link below to view a course description and a list of availability dates: 
      <br /><a class="course-availability" href="#" target="_blank">See course availability details</a>
    </p>
    <a class="close-reveal-modal button radius btn-close">Close</a>
    <a class="close-reveal-modal">&#215;</a>
  </div>
  <!-- End of Education System Modal Content -->


  <!-- Start of System Requirements Modal -->
  <div id="system-requirements" class="reveal-modal medium" data-reveal>
    <div class="row">
      <div class="large-12 columns">
        <h3>System Requirements</h3>
        <dl class="tabs" data-tab>
          <dd class="active"><a href="#windows-tab">Windows</a></dd>
          <dd><a href="#macintosh-tab">Macintosh</a></dd>
        </dl>
        <div class="tabs-content">
          <!-- Start of Windows Tab Content -->
          <div class="content active" id="windows-tab">
            <div class="large-6 small-12 columns">
              <h5>Operating System</h5>
              <ul>
                <li>Minimum: Windows XP</li>
                <li>Recommended: Windows 7</li>
              </ul>

              <h5>Processor</h5>
              <ul>
                <li>Minimum: 1 GHz or faster 32-bit or 64-bit</li>
                <li>Recommended: 2 GHz or faster 32-bit or 64-bit</li>
              </ul>

              <h5>Memory (RAM)</h5>
              <ul>
                <li>Minimum: 1 GB RAM</li>
                <li>Recommended: 3 GB RAM or greater</li>
              </ul>

              <h5>Internet Browser</h5>
              <ul>
                <li>Minimum: Internet Explorer 7 or Chrome 14</li>
                <li>Recommended: Internet Explorer 9 or Chrome 16</li>
              </ul>
            </div>
            <div class="large-6 small-12 columns">
              <h5>Office Suite</h5>
              <ul>
                <li>Minimum: Office 2007</li>
                <li>Recommended: Office 2010</li>
              </ul>

              <h5>Software Plug-ins</h5>
              <ul>
                <li><a href="#">Java Plug-in (Version 7 Update 6)</a></li>
                <li><a href="#">Adobe Flash</a></li>
                <li><a href="#">PDF Reader</a></li>
              </ul>

              <h5>Hardware Peripherals</h5>
              <ul>
                <li>Headset, Microphone</li>
              </ul>
            </div>
          </div>
          <!-- End of Windows Tab Content -->

          <!-- Start of Macintosh Tab Content -->
          <div class="content" id="macintosh-tab">
            <div class="large-6 small-12 columns">
              <h5>Operating System</h5>
              <ul>
                <li>Minimum: MAC OS 10.3</li>
                <li>Recommended: MAC OS X 10.4 or above</li>
              </ul>

              <h5>Processor</h5>
              <ul>
                <li>Minimum: G3 Processor</li>
                <li>Recommended: G4 or higher</li>
              </ul>

              <h5>Memory (RAM)</h5>
              <ul>
                <li>Minimum: 1 GB RAM</li>
                <li>Recommended: 3 GB RAM or greater</li>
              </ul>

              <h5>Internet Browser</h5>
              <ul>
                <li>Minimum: Safari 3.2</li>
                <li>Recommended: Safari 4 or above</li>
              </ul>
            </div>
            <div class="large-6 small-12 columns">
              <h5>Office Suite</h5>
              <ul>
                <li>Minimum: Office for Mac 2008</li>
                <li>Recommended: Office for Mac 2011</li>
              </ul>

              <h5>Software Plug-ins</h5>
              <ul>
                <li><a href="#">Java Software</a></li>
                <li><a href="#">Adobe Flash</a></li>
                <li><a href="#">PDF Reader</a></li>
              </ul>

              <h5>Hardware Peripherals</h5>
              <ul>
                <li>Headset, Microphone</li>
              </ul>
            </div>
          </div>
          <!-- End of Macintosh Tab Content -->

        </div><!-- tabs-content wrapper close-->
        <a class="close-reveal-modal button radius btn-close">Close</a>
        <div class="clearfix"></div>
      </div><!-- 12 Columns Close -->
    </div><!-- Row Close -->
    <a class="close-reveal-modal">&#215;</a>
  </div>
  <!-- End of System Requirements Modal -->

  <script src="bower_components/jquery/jquery.js"></script>
  <script src="bower_components/foundation/js/foundation.min.js"></script>
  <script src="js/app.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/custom.js"></script>
  <script type="text/javascript">
    $('.courseSort').dataTable();
  </script>
</body>
</html>