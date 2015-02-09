<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us: Open Learning Center</title>
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

    <div id="contact" class="container">
      <div class="main">
        <!-- OLC Navigation -->
        <?php include 'includes/olc-navigation.php'; ?>
        <!-- End of OLC Navigation-->

        <!-- Student Services Page Content -->
        <div class="row">
          <div class="large-12 columns">
            <h2 class="heading-background">Contact Us</h2>
          </div>
        </div>
        <div class="row">
          <!-- Start of Phone Tile -->
          <div class="large-4 medium-6 small-12 columns">
            <div class="panel radius">
              <div class="row">
                <div class="large-12 medium-3 small-3 columns">
                  <img src="images/phone.png" alt="Phone" />
                </div>
                <div class="large-12 medium-9 small-9 columns">
                  <h4>Call us at:</h4>
                  <ul>
                    <li>416-675-5049</li>
                    <li>1-877-215-6117</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Phone Tile -->

          <!-- Start of Email Tile -->
          <div class="large-4 medium-6 small-12 columns">
            <div class="panel radius">
              <div class="row">
                <div class="large-12 medium-3 small-3 columns">
                  <img src="images/mail.png" alt="Email" />
                </div>
                <div class="large-12 medium-9 small-9 columns">
                  <h4>Email us at:</h4>
                  <p class="bold"><a href="mailto:olc@humber.ca">olc@humber.ca</a><br />&nbsp;</p>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Email Tile -->


         <!-- Start of CHAT Tile -->
          <div class="large-4 medium-6 small-12 columns">
            <div class="panel radius chatus">
              <div class="row">
                <div class="large-12 medium-3 small-3 columns ">
                  <img src="images/chat.png" alt="Chat" />
                </div>
                <div class="large-12 medium-9 small-9 columns">
                  <h4><a href="chat.html" target="_blank" title="Chat with us">Chat with us</a></h4>
                  	<p>&nbsp;</p>
                  </div>
              </div>
            </div>
          </div>
          <!-- End of CHAT Tile -->

          <!-- Start of Drop In Tile -->
          <div class="large-6 medium-6 small-12 columns">
            <div class="panel radius">
              <div class="row">
                <div class="large-12 medium-3 small-3 columns">
                  <img src="images/shop.png" alt="Address" />
                </div>
                <div class="large-12 medium-9 small-9 columns">
                  <h4>Drop in at:</h4>
                  <p>Room #D-225<br />
                    205 Humber College Blvd., Toronto, ON M9W 5L7</p>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Drop In Tile -->

          <!-- Start of Hours of Operation Tile -->
          <div class="large-6 medium-6 small-12 columns end">
            <div class="panel radius">
              <div class="row">
                <div class="large-12 medium-3 small-3 columns">
                  <img src="images/clock.png" alt="Hours of Operation" />
                </div>
                <div class="large-12 medium-9 small-9 columns">
                  <h4>Our hours of operation are:</h4>
                  <ul>
                    <li>Monday to Friday from 8:30am to 8:00pm</li>
                    <li>Saturday and Sunday from 9:00am to 5:00pm</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Hours of Operation Tile -->

        </div>
      </div><!-- Main Close -->
    </div><!-- Container Close -->

    <!-- HUMBER FOOTER -->
    <?php include_once("/var/www/humber_global/footer.php"); ?>
    <!-- END OF HUMBER FOOTER -->

    <script src="bower_components/jquery/jquery.js"></script>
    <script src="bower_components/foundation/js/foundation.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/custom.js"></script>
    <script>

      function layoutCal(){
        var width = $(window).width();
        width = parseInt(width);
        if (width > 1024){//3 cols
          $(".chatus").css("min-height",246);
          $(".chatus").css("max-height",246);
        }
        else if (width > 640){//2 cols
          $(".chatus").css("min-height",157);
          $(".chatus").css("max-height",157);
        }
        else{
          $(".chatus").css("min-height","initial");
          $(".chatus").css("max-height","initial");
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
