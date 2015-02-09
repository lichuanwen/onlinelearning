<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Open Learning Center</title>
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

  <div id="home" class="container">
    <div class="main">
      <!-- OLC Navigation -->
      <?php include 'includes/olc-navigation.php'; ?>
      <!-- End of OLC Navigation-->

      <!-- Home Page Content -->
      <div class="row">

        <!-- Main Left Content -->
        <div class="large-9 columns">
          <div class="row">
            <div class="large-12 columns">

              <!-- Featured Items Slider -->
              <ul class="featured-items" data-orbit>
                <li>
                  <img src="images/home/banner-welcome.jpg" alt="slide 1" />
                </li>
                <li>
                  <a href="proctor.php"><img src="images/home/banner-proctor.jpg" alt="slide 2" /></a>
                </li>
                <li>
                  <a href="#" data-reveal-id="virtual-proctoring"><img src="images/home/banner-virtual.jpg" alt="slide 4" /></a>
                </li>
                <li>
                  <a href="courses.php"><img src="images/home/banner-login.jpg" alt="slide 5" /></a>
                </li>
              </ul>
            </div>
          </div>

          <!-- Bottom Tile Links -->
          <div class="row">
            <!-- Start of phpLive! Tile -->
            <div class="large-4 medium-4 small-12 columns">
              <div class="panel home-panel-1">
                <div class="row">
                  <div class="large-6 small-8 columns small-centered large-centered">
                    <h5 class="hide-for-large-up"><a href="chat.html" target="_blank">Chat with a Student Support Advisor</a></h5>
                    <a href="chat.html" target="_blank"><img src="images/home/chat.png" alt="Chat with a Student Support Advisor" /></a>
                  </div>
                  <div class="large-12 small-8 columns small-centered large-uncentered">
                    <h5 class="hide-for-small-only hide-for-medium-only"><a href="chat.html" target="_blank">Chat with a Student Support Advisor</a></h5>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of phpLive! Tile -->

            <!-- Start of Blackboard Tile -->
            <div class="large-4 medium-4 small-12 columns">
              <div class="panel home-panel-2">
                <div class="row">
                  <div class="large-6 small-8 columns small-centered large-centered">
                    <h5 class="hide-for-large-up">Student Blackboard 9.1 Workshops</h5>
                    <img src="images/blackboard_logo.png" alt="Blackboard Logo" />
                  </div>
                  <div class="large-12 small-8 columns small-centered large-uncentered">
                    <h5 class="hide-for-small-only hide-for-medium-only">Student Blackboard 9.1 Workshops</h5>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of Blackboard Tile -->

            <!-- Start of System Requirements Tile -->
            <div class="large-4 medium-4 small-12 columns">
              <div class="panel home-panel-3" data-reveal-id="system-requirements">
                <div class="row">
                  <div class="large-6 small-8 columns small-centered large-centered">
                    <h5 class="hide-for-large-up">System Requirements for Online Courses</h5>
                    <img src="images/home/sys_req.png" alt="System Requirements" />
                  </div>
                  <div class="large-12 small-8 columns small-centered large-uncentered">
                    <h5 class="hide-for-small-only hide-for-medium-only">System Requirements for Online Courses</h5>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of System Requirements Tile -->

          </div>
        </div>

        <!-- Side Bar -->
        <div class="side-bar large-3 columns">
          <div class="panel">
            <h5>Announcements</h5>
            <hr />
            <div class="status-box status-normal radius">
              <h5>OLC Status</h5>
              <p><a href="contact.php">Regular OLC office and lab hours (D225) are in effect. We are open!</a></p>
             <!-- <p><a href="contact.php">Humber College will be CLOSED on Dec 24, 2014 at noon.  The college will re-open on Monday January 5th, 2015. </a></p>-->
            </div>
          </div>
          <a class="twitter-timeline" href="https://twitter.com/HumberOLC" data-widget-id="436241714290774016">Tweets by @HumberOLC</a>
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>
      </div>
      
    </div><!-- Main Close -->

  </div><!-- Container Close -->

  <!-- HUMBER FOOTER -->
  <?php include_once("/var/www/humber_global/footer.php"); ?>
  <!-- END OF HUMBER FOOTER -->


  <!-- POPUP MODALS -->
  <!-- Start of Virtual Proctor Video Modal -->
  <div id="virtual-proctoring" class="reveal-modal medium" data-reveal>
    <h3>Virtual Proctoring</h3>
    <div class="flex-video-embedded">
      <iframe width="900" height="675" src="http://www.youtube.com/embed/gUlPg9xaCew?rel=0" frameborder="0" allowfullscreen=""></iframe>
    </div>
    <a href="proctor.php" class="button radius small">Submit a Proctor Request Form</a>
    <a class="close-reveal-modal">&#215;</a>
  </div>
  <!-- End of Virtual Proctor Video Modal -->

  <!-- Start of System Requirements Modal -->
  <div id="system-requirements" class="reveal-modal medium" data-reveal>
    <div class="row">
      <div class="large-12 columns">
        <h2>System Requirements</h2>
        <dl class="tabs" data-tab>
          <dd class="active"><a href="#windows-tab">Windows</a></dd>
          <dd><a href="#macintosh-tab">Macintosh</a></dd>
        </dl>
        <div class="tabs-content">
          <!-- Windows Tab Content -->
          <div class="content active" id="windows-tab">
              <div class="large-6 small-12 columns">
                <h4>Operating System</h4>
                <ul>
                  <li>Minimum: Windows Vista</li>
                  <li>Recommended: Windows 7 or Windows 8.1</li>
                </ul>

                <h4>Processor</h4>
                <ul>
                  <li>Minimum: 1 GHz or faster</li>
                  <li>Recommended: 2 GHz or faster</li>
                </ul>

                <h4>Memory (RAM)</h4>
                <ul>
                  <li>Minimum: 2 GB RAM</li>
                  <li>Recommended: 4 GB RAM or larger</li>
                </ul>

                <h4>Internet Browser</h4>
                <ul>
                  <li>Minimum: Internet Explorer 10 or Chrome 30</li>
                  <li>Recommended: Internet Explorer 11 or Chrome 37</li>
                </ul>
              </div>
              <div class="large-6 small-12 columns">
                <h4>Office Suite</h4>
                <ul>
                  <li>Minimum: Office 2010</li>
                  <li>Recommended: Office 2013</li>
                </ul>

                <h4>Software Plug-ins</h4>
                <ul>
                  <li><a href="https://www.java.com/en/download/manual.jsp" target="_blank">Java Plug-in</a></li>
                  <li><a href="http://get.adobe.com/flashplayer/" target="_blank">Adobe Flash</a></li>
                  <li><a href="http://get.adobe.com/reader/" target="_blank">PDF Reader</a></li>
                </ul>

                <h4>Hardware Peripherals</h4>
                <ul>
                  <li>Headset, Microphone, Webcam (recommended)</li>
                </ul>
              </div>
            </div>

            <!-- Macintosh Tab Content -->
            <div class="content" id="macintosh-tab">
              <div class="large-6 small-12 columns">
                <h4>Operating System</h4>
                <ul>
                  <li>Minimum: MAC OS X 10.8</li>
                  <li>Recommended: MAC OS X 10.9 or above</li>
                </ul>

                <h4>Processor</h4>
                <ul>
                  <li>Minimum: 1.6 GHz or faster</li>
                  <li>Recommended: 2.0 GHz for faster</li>
                </ul>

                <h4>Memory (RAM)</h4>
                <ul>
                  <li>Minimum: 2 GB RAM</li>
                  <li>Recommended: 4 GB RAM or larger</li>
                </ul>

                <h4>Internet Browser</h4>
                <ul>
                  <li>Minimum: Safari 6</li>
                  <li>Recommended: Safari 7 or above</li>
                </ul>
              </div>
              <div class="large-6 small-12 columns">
                <h4>Office Suite</h4>
                <ul>
                  <li>Minimum: Office for Mac 2011</li>
                  <li>Recommended: Office for Mac 2011</li>
                </ul>

                <h4>Software Plug-ins</h4>
                <ul>
                  <li><a href="https://www.java.com/en/download/manual.jsp" target="_blank">Java Plug-in</a></li>
                  <li><a href="http://get.adobe.com/flashplayer/" target="_blank">Adobe Flash</a></li>
                  <li><a href="http://get.adobe.com/reader/" target="_blank">PDF Reader</a></li>
                </ul>

                <h4>Hardware Peripherals</h4>
                <ul>
                  <li>Headset, Microphone, Webcam (recommended)</li>
                </ul>
              </div>
            </div>
          </div>
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
  <script src="js/custom.js"></script>
</body>
</html>
