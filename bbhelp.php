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

    <div id="bbhelp" class="container">
      <div class="main">
        <!-- OLC Navigation -->
        <?php include 'includes/olc-navigation.php'; ?>
        <!-- End of OLC Navigation-->

        <!-- Blackboard Help Page Content -->
        <div class="row">
          <div class="large-12 columns">
            <h2 class="heading-background">Blackboard Help</h2>
          </div>
        </div>

        <div class="row">
          <div class="large-3 columns">
            <div class="panel radius">
              <div class="row">
                <div class="large-12 small-3 medium-2 columns">
                  <img src="images/blackboard_logo.png" alt="Blackboard Logo" />
                </div>
                <div class="large-12 small-9 medium-2 columns">
                  <h3 class="hide-for-medium-only"><a href="http://humber.ca/bb91help/" target="_blank">Blackboard Help Site</a></h3>
                  <h5 class="hide-for-large-up hide-for-small-only"><a href="http://humber.ca/bb91help/students" target="_blank">Blackboard Help Site</a></h5>
                </div>
                <div class="large-12 small-12 medium-8 columns">
                  <p>Need help with Blackboard? <a href="http://humber.ca/bb91help/" target="_blank">Visit the Blackboard Help website</a>.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="large-9 columns">
            <h3>Student Blackboard 9.1 Workshops</h3>
            <div class="responsive-iframe-container">
              <iframe src="https://www.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showCalendars=0&amp;mode=AGENDA&amp;height=600&amp;wkst=2&amp;bgcolor=%23ffffff&amp;src=humbercollegeolc%40gmail.com&amp;color=%23B1440E&amp;ctz=America%2FToronto" style=" border-width:0 " width="800" height="600" frameborder="0" scrolling="no"></iframe>
            </div>
          </div>
        </div>
      </div><!-- Main Close -->

    </div><!-- Container Close -->

    <!-- HUMBER FOOTER -->
    <?php include_once("/var/www/humber_global/footer.php"); ?>
    <!-- END OF HUMBER FOOTER -->
    
    <!-- Popup Modals -->

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
                  <li><a href="#">Java Plug-in</a></li>
                  <li><a href="#">Adobe Flash</a></li>
                  <li><a href="#">PDF Reader</a></li>
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
                  <li><a href="#">Java Software</a></li>
                  <li><a href="#">Adobe Flash</a></li>
                  <li><a href="#">PDF Reader</a></li>
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
  </body>
</html>
