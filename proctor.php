<?php
  /* Required files */
  session_start();
  require_once 'SupportFiles/opendb.php';
  
  /* Connect to Database */
  connection();
?>
<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Proctor Request: Open Learning Center</title>
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
      var recaptcha1,recaptcha2,recaptcha3;
      var myCallBack = function() {
        //Render the recaptcha1 on the element with ID "recaptcha1"
        recaptcha1 = grecaptcha.render('recaptcha1', {
          'sitekey' : '6LeVIQETAAAAAB9IxTmCS8nBwERZjkOU8y686to4', //Replace this with your Site key
          'theme' : 'light'
        });
        
        //Render the recaptcha2 on the element with ID "recaptcha2"
        recaptcha2 = grecaptcha.render('recaptcha2', {
          'sitekey' : '6LeVIQETAAAAAB9IxTmCS8nBwERZjkOU8y686to4', //Replace this with your Site key
          'theme' : 'light'
        });

        //Render the recaptcha2 on the element with ID "recaptcha2"
        recaptcha3 = grecaptcha.render('recaptcha3', {
          'sitekey' : '6LeVIQETAAAAAB9IxTmCS8nBwERZjkOU8y686to4', //Replace this with your Site key
          'theme' : 'light'
        });
      };



  ga('create', 'UA-52617827-1', 'auto');
  ga('send', 'pageview');

	</script>
  <script src='https://www.google.com/recaptcha/api.js?onload=myCallBack&render=explicit' async defer></script>
    <!-- GOOGLE ANALYTICS CODE ENDS -->
    <style type="text/css">
      form .hidden-captcha {
          display: none;
      }
    </style>
    
  </head>
  <body>
    <!-- HUMBER NAVIGATION -->
    <?php include_once("/var/www/humber_global/navigation.php"); ?>
    <?php include_once("/var/www/humber_global/header.php"); ?>
    <!-- END OF HUMBER NAVIGATION -->

    <div id="proctor" class="container">
      <div class="main">
        <!-- OLC Navigation -->
        <?php include 'includes/olc-navigation.php'; ?>
        <!-- End of OLC Navigation -->

        <!-- Proctor Request Page Content -->
        <div class="row">
          <div class="large-12 columns">
            <h2 class="heading-background">Proctoring Information</h2>
            <p>Humber College OLC offers three proctoring options to those online students who are unable to write the final exam under their instructors' direct supervision.</p>

          </div>

          <!-- Start of Off-Campus Proctor Tile -->
          <div class="large-4 small-12 columns cells">
            <div class="panel radius">
              <h3>Off-Campus Proctor</h3>
              <ul>
                <li>Submit this form if you live more than 100 km from Humber College's North campus.</li>
                <li>Please ensure that you have secured a proctor and have collected all of the necessary proctor contact information.</li>
                <li>Late proctor requests will be accepted but time and date options will be potentially severely restricted.</li>
                <li>The off-campus proctor MUST be a registered college/university and MUST have a business email address.</li>
                <li>Off-Campus Proctoring FEE will apply.</li>
              </ul>
              
              <a href="#" class="button radius small" data-reveal-id="form-offcampus">Off-Campus Proctor Request Form</a>
            </div>
          </div>
          <!-- End of Off-Campus Proctor Tile -->

          <!-- Start of Virtual Proctor Tile -->
          <div class="large-4 small-12 columns cells">
            <div class="panel radius">
              <h3>Virtual Proctor</h3>
              <ul>
                <li>Submit this form if you live more than 100 km from Humber College's North campus.</li>
                <li>With Virtual Proctoring, you can take the online exam from the comfort of your home.</li>
                <li>Virtual Proctoring FEE will apply.</li>
                <li>You MUST have a high speed Internet connection and a good quality webcam to be eligible for Virtual Proctoring.</li>
                <li>For specific virtual proctoring requirements, go to <a href="http://onlineproctornow.com/techreq.php" target="_blank">http://onlineproctornow.com/techreq.php</a></li>
                <li>To see how Virtual Proctoring works, <a href="#" data-reveal-id="virtual-proctoring">click here</a> to watch this video.</li>
                
              </ul>
             
             
             <a href="#" class="button radius small" data-reveal-id="form-virtual">Virtual Proctor Request Form</a>
            </div>
          </div>
          <!-- End of Virtual Proctor Tile -->

          <!-- Start of Test Centre Tile -->
          <div class="large-4 small-12 columns cells">
            <div class="panel radius">
              <h3>Test Centre</h3>
              <ul>
                <li>Submit this form if you want to write the exam at a Humber Test Centre due to a scheduling conflict with your online exam.</li>
                <li>Late proctor requests will be accepted but time and date options will be potentially severely restricted.</li>
              </ul>
              
              <a href="#" class="button radius small" data-reveal-id="form-testcentre">Test Centre Request Form</a>
            </div>
          </div>
          <!-- End of Test Centre Tile -->

        </div>
      </div><!-- Main Close -->
    </div><!-- Container Close -->

    <!-- HUMBER FOOTER -->
    <?php include_once("/var/www/humber_global/footer.php"); ?>
    <!-- END OF HUMBER FOOTER -->


    <!-- POPUP MODALS -->
    <!-- Start of Virtual Proctoring Video Modal -->
    <div id="virtual-proctoring" class="reveal-modal medium" data-reveal>
      <h3>Virtual Proctoring</h3>
      <div class="flex-video-embedded">
        <iframe width="900" height="675" src="http://www.youtube.com/embed/gUlPg9xaCew?rel=0" frameborder="0" allowfullscreen=""></iframe>
      </div>
      <a class="close-reveal-modal">&#215;</a>
    </div>
    <!-- End of Virtual Proctoring Video Modal -->

    <!-- Start of Off-Campus Proctor Form -->
    <div id="form-offcampus" class="reveal-modal medium proctor-modal" data-reveal>
      <div class="row">
        <div class="large-12 columns">
          <h3 class="heading-background">Off-Campus Proctor Request Form for Online Students</h3>
          <p class="italic">&#42;All fields are mandatory.</p>
        </div>
      </div>
      <form id="offcampus" name="offcampus" method="post" action="SupportFiles/proctor-success.php" onsubmit="return validateOffcampus();">
        <input type="hidden" value="OCF" name="formid" />
        <div class="row">
          <div class="large-12 columns">
            <h4>Student Information</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>First Name</label>
            <input name="firstName" id="firstName" type="text" placeholder="First Name" />
          </div>
          <div class="large-6 columns">
            <label>Last Name</label>
            <input name="lastName" id="lastName" type="text" placeholder="Last Name" />
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>Student Number</label>
            <input name="studentNum" id="studentNum" type="text" placeholder="Student Number" />
          </div>
          <div class="large-6 columns">
            <label>Email</label>
            <input name="email" id="email" type="email" placeholder="Email" />
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>Select Your Online Course</label>
          </div>
          <div class="large-12 columns">
            <select name="courseCode">
              <option value="select" selected>Select Course</option>
              <?php  
                $query = "SELECT DISTINCT CName, CNum, CSec FROM `courses` ORDER BY `CName`, `CNum`, `CSec` ASC";
                $results = sendQuery($query);
                
                if ($results){
                    while ($row = mysqli_fetch_object($results)) {
                      printf('<option value="'.$row->CName.$row->CNum.$row->CSec.'">'.$row->CName.' '.$row->CNum.' '.$row->CSec.'</option>'); 

                  }  // while-loop {ends}
                } // if .. results  {ends}

              ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <h4>Proctor Information</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>Proctor Name</label>
            <input name="proctorName" type="text" placeholder="Proctor Name" />
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>Academic Institution/Proctored Site</label>
            <input name="proctoredSite" type="text" placeholder="Academic Institution/Proctored Site" />
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>Proctor Position/Title</label>
            <input name="proctorPosition" type="text" placeholder="Proctor Position/Title" />
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>Proctor's Email</label>
            <input name="proctorEmail" type="text" placeholder="Proctor's Email" />
          </div>
          <div class="large-6 columns">
            <label>Proctor's Phone Number</label>
            <input name="proctorPhone" type="text" placeholder="Proctor's Phone Number" />
          </div>
        </div>

        <div class="row">
          <div class="large-12 columns">
            <h4>Proctor's Mailing Address</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-8 columns">
            <label>Street No. &amp; Name</label>
            <input name="streetNumName" id="streetNumName" type="text" placeholder="Street No. &amp; Name" />
          </div>
          <div class="large-4 columns">
            <label>City/Town</label>
            <input name="cityTown" id="cityTown" type="text" placeholder="City/Town" />
          </div>
        </div>
        <div class="row">
          <div class="large-4 columns">
            <label>Province/State</label>
            <input name="provinceState" id="provinceState" type="text" placeholder="Province/State" />
          </div>
          <div class="large-4 columns">
            <label>Postal/Zip Code</label>
            <input name="postalCode" id="postalCode" type="text" placeholder="Postal/Zip Code" />
          </div>
          <div class="large-4 columns">
            <label>Country</label>
            <input name="country" id="country" type="text" placeholder="Country" />
          </div>
        </div>

        <div class="row">
          <div class="large-12 columns">
            <h4>Comments/Reasons</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-4 columns">
            <p>Describe in detail why you are submitting the Off-Campus Proctor request form. Be sure to provide any further detail that may help us process your proctor request faster.</p>
          </div>
          <div class="large-8 columns">
            <textarea name="comments" id="comments"></textarea>
            <input id="ofcWebsite" class="hidden-captcha" type="text" name="website" value="" />
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns firstrecaptcha" id="recaptcha1">
          </div>
          <div class="large-6 columns buttons">
            <input name="submit" type="submit" value="Submit" class="button radius btn-submit" title="Click the Submit button once you have filled in all form fields" />
            <a class="close-reveal-modal button radius btn-close">Cancel</a>
          </div>
        </div>
      </form>
    </div>
    <!-- End of Off-Campus Proctor Form -->

    <!-- Start of Virtual Proctor Form -->
    <div id="form-virtual" class="reveal-modal medium proctor-modal" data-reveal>
      <div class="row">
        <div class="large-12 columns">
          <h3 class="heading-background">Virtual Proctor Request Form for Online Students</h3>
          <p class="italic">&#42;All fields are mandatory.</p>

          <h4>Virtual Proctoring Eligibility and Fee</h4>
          <p>With Virtual Proctoring, you can take the online exam from the comfort of your home provided that you live more than 100 km from Humber College's North campus, have a high speed Internet connection, and a good quality web cam. For specific virtual proctoring requirements, go to <a href="http://onlineproctornow.com/techreq.php" target="_blank">http://onlineproctornow.com/techreq.php</a></p>
          <p>The virtual proctoring fee for exams that are up to two hours in length is $60.00.
            <br />The virtual proctoring fee for exams that exceed two hours in length is $80.00. 
            <br />Please confirm with your instructor the length of your final exam.
          </p>
          <p>To see how Virtual Proctoring works, <a href="#" data-reveal-id="virtual-proctoring">click here</a> to watch this video.</p>
        </div>
      </div>
      <form id="virtual" name="virtual" method="post" action="SupportFiles/proctor-success.php" onsubmit="return validateVirtual();">
        <input type="hidden" value="VPF" name="formid" />
        <div class="row">
          <div class="large-12 columns">
            <h4>Student Information</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>First Name</label>
            <input name="firstName" id="firstName" type="text" placeholder="First Name" />
          </div>
          <div class="large-6 columns">
            <label>Last Name</label>
            <input name="lastName" id="lastName" type="text" placeholder="Last Name" />
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>Student Number</label>
            <input name="studentNum" id="studentNum" type="text" placeholder="Student Number" />
          </div>
          <div class="large-6 columns">
            <label>Email</label>
            <input name="email" id="email" type="text" placeholder="Email" />
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>Select Your Online Course</label>
          </div>
          <div class="large-12 columns">
            <select name="courseCode">
              <option value="select" selected>Select Course</option>
              <?php  
                $query = "SELECT DISTINCT CName, CNum, CSec FROM `courses` ORDER BY `CName`, `CNum`, `CSec` ASC";
                $results = sendQuery($query);
                
                if ($results){
                    while ($row = mysqli_fetch_object($results)) {
                      printf('<option value="'.$row->CName.$row->CNum.$row->CSec.'">'.$row->CName.' '.$row->CNum.' '.$row->CSec.'</option>'); 

                  }  // while-loop {ends}
                } // if .. results  {ends}

              ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <h4>Comments/Reasons</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-4 columns">
            <p>Describe in detail why you are submitting the Virtual Proctor request form. Be sure to provide any further detail that may help us process your proctor request faster.</p>
          </div>
          <div class="large-8 columns">
            <textarea name="comments" id="comments"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns recaptchaclone" id="recaptcha2">

          </div>
          <div class="large-6 columns buttons">
            <input name="submit" type="submit" value="Submit" class="button radius btn-submit" title="Click the Submit button once you have filled in all form fields" />
            <a class="close-reveal-modal button radius btn-close">Cancel</a>
          </div>
        </div>
      </form>
    </div>
    <!-- End of Virtual Proctor Form -->

    <!-- Start of Test Centre Form -->
    <div id="form-testcentre" class="reveal-modal medium proctor-modal" data-reveal>
      <div class="row">
        <div class="large-12 columns">
          <h3 class="heading-background">Test Centre Form for Online Students</h3>
          <p class="italic">&#42;All fields are mandatory.</p>
        </div>
      </div>
      <form id="testcentre" name="testcentre" method="post" action="SupportFiles/proctor-success.php" onsubmit="return validateTestCentre();">
        <input type="hidden" value="TCF" name="formid" />
        <div class="row">
          <div class="large-12 columns">
            <h4>Student Information</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>First Name</label>
            <input name="firstName" id="firstName" type="text" placeholder="First Name" />
          </div>
          <div class="large-6 columns">
            <label>Last Name</label>
            <input name="lastName" id="lastName" type="text" placeholder="Last Name" />
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns">
            <label>Student Number</label>
            <input name="studentNum" id="studentNum" type="text" placeholder="Student Number" />
          </div>
          <div class="large-6 columns">
            <label>Email</label>
            <input name="email" id="email" type="text" placeholder="Email" />
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>Select Your Online Course</label>
          </div>
          <div class="large-12 columns">
            <select name="courseCode">
              <option value="select">Select Course</option>
              <?php  
                $query = "SELECT DISTINCT CName, CNum, CSec FROM `courses` ORDER BY `CName`, `CNum`, `CSec` ASC";
                $results = sendQuery($query);
                
                if ($results){
                    while ($row = mysqli_fetch_object($results)) {
                      printf('<option value="'.$row->CName.$row->CNum.$row->CSec.'">'.$row->CName.' '.$row->CNum.' '.$row->CSec.'</option>'); 

                  }  // while-loop {ends}
                } // if .. results  {ends}

              ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <h4>Test Centre Information</h4>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
           <!-- <label>Choose a Test Centre Campus</label>-->
           <label>* The Test Centre at Lakeshore campus is no longer an option for proctor requests.</label>
            <select name="campus">
              <option value="select">Choose a Test Centre campus</option>
              <option value="north">North Campus</option>
              <!--<option value="lakeshore">Lakeshore Campus</option>-->
              <option value="orangeville">Orangeville Campus</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="large-4 columns">
            <p>Describe in detail why you are submitting the Test Centre request form. Be sure to provide any further detail that may help us process your proctor request faster.</p>
          </div>
          <div class="large-8 columns">
            <textarea name="comments" id="comments"></textarea>
            <input id="tcWebsite" class="hidden-captcha" type="text" name="website" value="" />
          </div>
        </div>
        <div class="row">
          <div class="large-6 columns recaptchaclone" id="recaptcha3">

          </div>
          <div class="large-6 columns buttons">
            <input name="submit" type="submit" value="Submit" class="button radius btn-submit" title="Click the Submit button once you have filled in all form fields" />
            <a class="close-reveal-modal button radius btn-close">Cancel</a>
          </div>
        </div>
      </form>
      <?php
        /*if($_SERVER['REQUEST_METHOD'] == 'POST'){
          var_dump("success");
        }*/
      ?>
    </div>
    <!-- End of Test Centre Form -->
    <script src="bower_components/jquery/jquery.js"></script>
    <script src="bower_components/foundation/js/foundation.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/formValidation.js"></script>
    <script src="js/custom.js"></script>

    <script>

      function layoutCal(){
        var width = $(".row").find(".cells").css("width");
        width = parseInt(width);
        if (width > 350){
          $(".row").find(".cells").find(".panel").css("height","auto"); 
          $(".row").find(".cells").find(".panel").find(".button ").css("top",0);
          $(".row").find(".cells").find(".panel").find(".button ").eq(1).css("margin-left",0);
          $(".row").find(".cells").find(".panel").find(".button ").eq(2).css("margin-left",0);
        }
        else{
          height = $(".row").find(".cells").eq(1).css("height");
          height = parseInt(height);
          $(".row").find(".cells").find(".panel").css("height",height);
          $(".row").find(".cells").find(".panel").find(".button ").eq(0).css("top",60);
          $(".row").find(".cells").find(".panel").find(".button ").eq(1).css("top",20);
          $(".row").find(".cells").find(".panel").find(".button ").eq(2).css("top",180);
          $(".row").find(".cells").find(".panel").find(".button ").eq(1).css("margin-left",20);
          $(".row").find(".cells").find(".panel").find(".button ").eq(2).css("margin-left",30);
        }
        console.log(width);
      }

      $(window).resize(function(){
            layoutCal();
      });

      $(document).ready(function() {
          // Duplicate our reCapcha 
          layoutCal();
      });


    </script>

    </body>
</html>
