$(document).ready(function() {
    
    // Link Panel on Home Page to BBHelp Page
    $('.home-panel-2').click(function(){
		window.location.assign("bbhelp.php");
	});

	// Courses Page
	function changeModalContent(){
	    $("a.course-link").click(function(){
	      var courseID = $(this).html();
	      var courseTitle = $(this).parent().siblings(".course-title").html();
	      var courseHeading = courseID + ' - ' + courseTitle;

	      var courseName = $(this).html().substring(0,4);
	      var courseNum = $(this).html().substring(5,8);
	      var courseAvailability = 'http://www.humber.ca/continuingeducation/courses/online';

	      if($(this).parent().siblings(".lms").html() == "Blackboard") {
	        $("#inner-blackboard .course-heading").html(courseHeading);
	        $("#inner-blackboard .course-availability").attr('href', courseAvailability);
	        $("#course-info-modal").html($("#inner-blackboard").html());
	      }
	      else if ($(this).parent().siblings(".lms").html() == "Ontario Learn") {
	        $("#inner-ontariolearn .course-heading").html(courseHeading);
	        $("#inner-ontariolearn .course-availability").attr('href', courseAvailability);
	        $("#course-info-modal").html($("#inner-ontariolearn").html());
	      }
	      else if ($(this).parent().siblings(".lms").html() == "eHotel") {
	        $("#inner-ehotel .course-heading").html(courseHeading);
	        $("#inner-ehotel .course-availability").attr('href', courseAvailability);
	        $("#course-info-modal").html($("#inner-ehotel").html());
	      }
	      else if ($(this).parent().siblings(".lms").html() == "Education System") {
	        $("#inner-edusys .course-heading").html(courseHeading);
	        $("#inner-edusys .course-availability").attr('href', courseAvailability);
	        $("#course-info-modal").html($("#inner-edusys").html());
	      }
	    });
	}
	changeModalContent();

	$(".dataTables_wrapper select, .dataTables_wrapper input").change(function(){
		changeModalContent();
	});
	$(".paginate_enabled_previous, .paginate_enabled_next").click(function(){
		changeModalContent();
	});

	
	/*function preloadModal(){
		$("a.course-link").trigger("click", function(){
			$(this).preventDefault();
			$('#course-info-modal').foundation('reveal', 'close');
		});
	}*/
	//preloadModal();

	$(".courseSort").wrap('<div class="responsive-table"></div>');
	
	// FAQ Page
    $('dl.accordion dd:even').addClass('even');
    $('dl.accordion dd:odd').addClass('odd');

	$(".accordion").on("click", "dd", function (event) {
		$("dd.active").slideToggle(400);
		$(this).find(".content").slideToggle(400);
	});

	$(".btn-expand").click(function(){
		var $this = $(".btn-expand");
		$this.toggleClass("expand-me");
		$this.append('<span class="icon-plus-squared-alt"></span>Expand All');

		if($this.hasClass("expand-me")){
			$this.html('<span class="icon-minus-squared-alt"></span>Collapse All');
			$("dd").find(".content").slideDown(400);
		}
		else {
			$this.html('<span class="icon-plus-squared-alt"></span>Expand All');
			$("dd").find(".content").slideUp(400);
		}
		return false;
	});
});