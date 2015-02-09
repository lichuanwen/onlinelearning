/* 
 * Load axGetCourses.php via AJAX
 */

//Start AJAX transaction

$.post('ajaxcalls/axGetCourses.php', {'school':$short}, function(data){
// capture the returned value (data)
var $result = data;
if ($result != "Your query returned no results"){
    $previousTable = $('table.school-list').width; 
    
    if ($previousTable != null){  // find if we have previous data
        $('.school-list').remove();  //  and  remove it from screen
    }
    
    $($result).insertAfter('#insertion-point')  // put data on the screen
    $('.stage').scrollbar();  // make sure scrollbars are loaded if needed
    $('table.school-list').hide()  // keep data hidden, then ...
    $('table.school-list').fadeIn(2500);  // slowly reveal it.
} else {
    alert($result);
}
});  // AJAX message {ends}


