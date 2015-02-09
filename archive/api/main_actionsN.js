 /* JQuery actions */
/* Plug-in to detect scroll bars */

// USE: $('#my_div1').hasScrollBar(); 
// returns true if there's a `vertical` scrollbar, false otherwise..
/*(function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    }
})(jQuery); */
 
$(document).ready(function(){
    
    /* hide JavaScript required message */
    $('.js-required').css({'visibility':'hidden'});
    
    /* set the footer at the bottom of the page by
       re-sizing  stage */
	   
	   
   /* $winHeight = $(window).height();
    if ($.browser.webkit){
        $('.stage').css('height', $winHeight -289);
    }else{
        $('.stage').css('height', $winHeight -286);
    }*/
    
    /* Upon loading a page set the colour of the menu item pertaining to that
       page to white and add the li's background */
    
    $currentPage = document.title;  // detect document title
    
    $menu = {'home':'#menu-home','courses':'#menu-courses','demo':'#menu-demo',
    'student':'#menu-student','faq':'#menu-faq','proctor':'#menu-proctor',
    'services':'#menu-services','contact':'#menu-contact'};  // set menus
    
    setCurrentPage = function(menuTitle){  //  define function to set the new values
      $menTitle = menuTitle;
      $($menTitle).css('color','#FFF')  // set the colour of the anchor
      .closest('li')  // get the closest <li> and set the background
      .css({'background-image':'url("images/slice.png")', 'background-repeat':'repeat-x'});
    };
    
    if ($currentPage == "Home"){
        setCurrentPage($($menu.home));
    }else if ($currentPage == "Courses"){
        setCurrentPage($($menu.courses));
    }else if ($currentPage == "Demo"){
        setCurrentPage($($menu.demo));
    }else if ($currentPage == "Student"){
        setCurrentPage($($menu.student));
    }else if ($currentPage == "FAQs"){
        setCurrentPage($($menu.faq));
    }else if ($currentPage == "Proctor"){
        setCurrentPage($($menu.proctor));
    }else if ($currentPage == "Student Services"){
        setCurrentPage($($menu.services));
    }else if ($currentPage == "Contact Us"){
        setCurrentPage($($menu.contact));
    }
    
    /* resetSelections re-sets the colour and background of any selected menu
       to its default values */
    resetSelections = function(){
        $('.top-nav li a').css('color','#73A9C6');
        $('.top-nav li').css('background-image','none');
    };
    
    /* Makes the colour of the menu just clicked white */
    $('.top-nav li a').click(function(){
        $thisItem = $(this);
        resetSelections();
        $thisItem.css({'color':'#FFF'});
        setBackground($thisItem);
    });//
    
    /* set background image of selected menu item */
    
    setBackground = function(selectedElement){
      selectedElement.closest('li')
      .css({'background-image':'url("images/slice.png")', 'background-repeat':'repeat-x'})
    };
    
    /* trigger query to DB upon change in drop down */
    $('#dd-schools').change(function(){
        $short = $(this + 'option:selected').attr('title'); // get school
        $val = $(this + 'option:selected').attr('value'); // get value
        if ($val != 'default'){
            // Load script
            $.getScript('api/axGetCourses.js', function() {  // load ajax script
            return true;
            });  //getScript {ends}
        }     
    }); // dd-schools {ends}
    
	$title = document.title;
	if ($title != "Couses"){
		$('.stage').css('scroll','no')
	}
	
    if($('.stage').hasScrollBar()){
        //alert("Scrolls!");
        $('#contact, #dates, #system').css({'width':'309px'});
        $('.stage').scrollbar();
    }  // stage hasScrollBar {ends}

});// DOM Ready {ends}

$popup =0;  // Give default value to pop-up
/* Outside of DOM READY */
var divpopup = function ($CID){
        // Make system aware that pop-up is ON
        $popup = 1;
        //Start AJAX transaction
        $ax_result = $.post('ajaxcalls/axGetRequirements.php', {'courseid':$CID}, function(data){
        // capture the returned value (data)
        var $result = data;
            if ($result){
                R = $.parseJSON($result);
                 /* define div big-window and attach it to a variable */
                var bigWindow = '<div id="big-window">'; 
                // add the info from the JSON result to bigWindow
                bigWindow += '<br /><br /><p><span class="big-window-heading">Course: </span>'+ R.coursename + '&nbsp;' + R.coursenumber +'</p>';
                bigWindow += "<p> <span class='big-window-heading'>Title</span>: "+ R.Title + "</p>";
                bigWindow += "<p> <span class='big-window-heading'>Prerequisites</span>: "+ R.prereq + "</p>";
                bigWindow += "<p> <span class='big-window-heading'>Availability</span>: "+ R.availability + "</p>";
                bigWindow += "<p> <span class='big-window-heading'>Textbook</span>: "+ R.textbook + "</p>";
                bigWindow += "<p> <span class='big-window-heading'>System Requirements</span>: "+ R.sysreq + "</p>"; 
                bigWindow += "<p> <span class='big-window-heading'>Credentials</span>: "+ R.credentials + "</p>"; 
                bigWindow += "<p> <span class='big-window-heading'>Login</span>: "+ R.login + "</p>"; 
                bigWindow += "<p>&nbsp;</p>";
                bigWindow += '</div>';
    
                /* define close button  and attach it to a varible */
                var closeButton = "<div style='z-index:220; position:absolute; top:10px; left:10px;'><input class='closewin' type='button' value='Close' /></div>";

                /* append the big-Window to the stage section of the page */
                $(bigWindow).appendTo(".stage");

                /* define the big-window css */
                $("#big-window").css({
                "position":"absolute",
                "top":"146px",
                "left":"105px",
                "width":"740px",
                "min-height":"320px",
                "border":"solid 6px #085A82 ",
				"background-image":"url('images/big_window_gradient.png');",
				"background-repeat":"repeat-x;",
                "background-color":"#FFF",
                "zIndex":"200"
                }); // css definition {ends}

                /* append closeButton to bigWindow */
                $(closeButton).appendTo("#big-window");

                /* define action to close the windows */

                $(".closewin").click(function(){
                    $("#big-window").fadeOut("2500", function(){
                        $(this).remove();
                        $popup = 0;
                    });
                }); // closewin.click {ends}
            }  // if ... result {ends}
        });  // AJAX message {ends}
};  // divpopup {ends}
