

// if localstrorage available, make toggles sticky
if(lsTest() === true){

	// editTable
	$(document).ready(function () {
	    $('#toggle_table').click(function () {
	        $("#transactions_table").slideToggle(function(){
	        	localStorage.setItem('toggle_table', $("#transactions_table").is(':visible'));	
	        });                
	    });    
	    if (localStorage.getItem('toggle_table') == 'false') {
	        $('#transactions_table').hide()
	    }
	});

	//statsGraph
	$(document).ready(function () {
	    $('#toggle_graph').click(function () {
	        $("#table_wrapper").slideToggle(function(){
	        	localStorage.setItem('toggle_graph', $("#table_wrapper").is(':visible'));	
	        });                
	    });    
	    if (localStorage.getItem('toggle_graph') == 'false') {
	        $("#table_wrapper").hide()
	    }
	});

}

// else, simple toggles
else {
	// editTable
	$(document).ready(function () {
	    $('#toggle_table').click(function () {
	        $("#transactions_table").slideToggle();
        });
	});

	//statsGraph
	$(document).ready(function () {
	    $('#toggle_graph').click(function () {
	        $("#table_wrapper").slideToggle();
        });
	});
}


// localstorage test
function lsTest(){
    var test = 'test';
    try {
        localStorage.setItem(test, test);
        localStorage.removeItem(test);
        return true;
    } catch(e) {
        return false;
    }
}

// temporarily set user cookie
function userCookie(value) {
	document.cookie="userType="+value+"";
}

// temporarily set user cookie
function userCookie2() {
	var value = $('input[name="radio_button"]:checked').val();
	document.cookie="userType="+value+"";
}