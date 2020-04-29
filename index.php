<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Braxon Part Finder</title>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript">

// Call PHP page to get search results.  ////////////////////////////////
function finderSearch(h, s, p, b, f) {
	var url = "http://adimation.com/braxon/get_info.php?h=" + h + "&s=" + s + "&p=" + p + "&b=" + b + "&f=" + f;
	$("#response_text").load(url, function(responseTxt, statusTxt, xhr) {
		if (statusTxt == "success")
			// Hide spinner.
			$("#spinner").hide();
			// Fade in results.
			$("#response_text").fadeIn( 1000 );
			$("#new_search").fadeIn( 1000 );
	    if (statusTxt == "error")
	    	alert("Error: " + xhr.status + ": " + xhr.statusText);
	});
}

// Call PHP page to get options for next question.  ////////////////////////////////
function finderCheck(h, s, p, b, f) {
	$.ajax({
        type: "GET",
        url: "http://adimation.com/braxon/get_info.php?m=check&bn=" + box_num + "&h=" + h + "&s=" + s + "&p=" + p + "&b=" + b + "&f=" + f,
        dataType: "json",
        success: function(JSONObject) {
        	// Create options for next box.
			var nextBox = "<table border='0' cellspacing='0' cellpadding='0' align='center'>";
        	for (var key in JSONObject) {
        		if (JSONObject.hasOwnProperty(key)) {
					nextBox += "    <tr>";
					nextBox += "        <td class='radio_pad'><input name='radio" + box_num + "' type='radio' value='" + JSONObject[key][0] + "' /></td>";
					nextBox += "        <td class='label_pad'>" + JSONObject[key][1] + "</td>";
					nextBox += "    </tr>";
      			}
      		}
            nextBox += "</table>";

            $("#box_options" + box_num).html(nextBox);
			// Fade in next question.
			fadeIn();
 		},
 		error: function(xhr, status, error){
     		var errorMessage = xhr.status + ': ' + xhr.statusText;
     		alert('*** Error - ' + errorMessage);
        }
    });
}


function fadeIn() {
	// Hide error text in next box in case it was visible
	// (can happen when using the back button).
	$("#error_text" + box_num).hide();
	// Hide spinner.
	$("#spinner").hide();
	// Fade in next box.
	$("#div" + box_num).fadeIn( 1000 );
}

function checkCount() {
	
	// Display searching gif.
	$("#spinner").show();
	
	// Check if all questions have been completed.
	if (box_num == 4) {
		
		// Reset counter for possible new searches.
		box_num = -1;
		// Search the db.
		finderSearch(finder_results[0], finder_results[1], finder_results[2], finder_results[3], finder_results[4]);
	
	// First two boxes have no need for finderCheck.	
	} else if (box_num == -1 || box_num == 0) {
		
		// Increment box_num.
		box_num++;
		// Fade in box.
		fadeIn();
		
	} else {
		
		// Increment box_num.
		box_num++;
		// Check db for next question's possibilities.
		finderCheck(finder_results[0], finder_results[1], finder_results[2], finder_results[3], finder_results[4]);
	
	}
		
}


var box_num = -1;
var finder_results = [];

$(document).ready(function(){
	
    $('.button_next').click(function() {
		
		if (box_num > -1) {
			// Record answer.
			// Convert selected radio button value to an integer.
			var radio_val = parseInt($("input:radio[name=radio"+box_num+"]:checked").val());
			// Check to make sure a selection was made.
			if (isNaN(radio_val)) {
				// Show error message.
				$("#error_text" + box_num).fadeIn(500);
			} else {
				// Put value into array.
				finder_results[box_num] = radio_val;
				// Hide current box.
				$("#div" + box_num).hide(checkCount());
			}
		} else {
			// Clear all forms in case this is a search other than the first.
			$(".finder_form").trigger("reset");
			// Hide welcome screen.
			$("#finder_welcome").hide();
			// Hide results and new search button in case this is a search other than the first.
			$("#response_text").hide();
			$("#new_search").hide();
			$("html, body").animate({ scrollTop: 0 }, "slow");
			box_num++;
			fadeIn();
		}
		
    });
	
    $('.button_back').click(function() {
		if (box_num > 0) {
			// Hide error text in previous box
			// in case it was visible.
			$("#error_text" + (box_num - 1)).hide();
			// Hide current box.
			$("#div" + box_num).hide();
		}
		box_num--;
		fadeIn();
    });
	
});
</script>

<style>
body {
	background: #000;
	font-family: Verdana, Geneva, sans-serif;
	margin: 0;
}
#content {
	margin: 0;
}
#header {
	margin: 15px auto;
	padding: 0;
	width: 1000px;
}
#main_box {
	background: #999;
	border-radius: 25px;
	border: 2px solid #ffcc00;
	height: 100%;
	margin: 0 auto;
	min-height: 800px;
	padding-top: 60px;
	width: 1000px;
}
#finder_welcome {
	color: #e4e4e4;
	text-align: center;
	margin: 0 auto;
	max-width: 700px;
	padding-top: 60px;
	width: 100%;
}
a {
	color: #ffcc00;
}
.finder_box {
    background-color: rgba(204, 204, 204, 1);
    border: 1px solid #000;
	display: none;
	margin: 0 auto;
	max-height: 90%;
	width: 100%;
	padding: 0;
	max-width: 600px;
}
.finder_header {
	background: #2d2d2d;
	color: #ffcc00;
	font-size: 24px;
	padding: 10px 20px;
}
.finder_main {
	font-size: 20px;
	min-height: 170px;
	padding: 20px;
}
.finder_main form {
	margin: 0 auto;
}
.finder_question {
	margin-bottom: 20px;
}
.finder_main .radio_pad {
	padding: 5px 5px 9px 5px;
}
.finder_main .label_pad {
	padding: 5px;
}
.finder_buttons {
	padding: 20px;
	text-align: center;
}
.finder_footer {
	background: #000;
	padding: 5px;
	text-align: right;
}

#response_text {
	color: #353535;
	display: none;
	font-size: 18px;
	margin: 0 auto;
	padding: 0px 30px;
}
#response_text h2 {
	color: #e4e4e4;
}
#response_text table {
    border-spacing: 2px;
    border-collapse: separate;
}
#response_text td {
	background: #2d2d2d;
	color: #fff;
	padding: 7px 10px;
}
#new_search {
	display: none;
	padding: 30px;
	text-align: center;
}
#spinner {
	display: none;
	padding: 150px 0;
	text-align: center;
	width: 100%;
}

</style>

</head>

<body>
	<div id="content">

		<div id="header"><img src="images/braxon_logo.png" width="300" height="90"></div>
    
        <div id="main_box">
    
            <div id="response_text">
            </div>
            
            <div id="new_search">
                <button class="button_next">Search Again</button>
            </div>
            
            <div id="spinner">
            	<img src="images/loading-png-gif.gif" width="100" height="100" />
            </div>
        
        	<div id="finder_welcome">
            	<h1>Welcome to the Braxon Latch Finder</h1>
                Click the button below to begin your search.<br /><br />
                <button class="button_next">Start Your Search</button>
            </div>
            
            <!-- Handle ----------------------------------->
            <div id="div0" class="finder_box">
            	<div class="finder_header">
                	HANDLE
                </div>
                <div class="finder_main">
                	<div id="error_text0">
                    	Please make a selection before continuing.
                    </div>
                	<div class="finder_question">
                		Do need a handle with your latch?
                    </div>
                	<form id="finder_form-0" class="finder_form" action="" method="post">
                    	<table border="0" cellspacing="0" cellpadding="0" align="center">
                          <tr>
                            <td class="radio_pad"><input name="radio0" type="radio" value="1" /></td>
                            <td class="label_pad">Yes, I need a handle.</td>
                          </tr>
                          <tr>
                            <td class="radio_pad"><input name="radio0" type="radio" value="0" /></td>
                            <td class="label_pad">No, I don't need a handle.</td>
                          </tr>
                        </table>
                    </form>
                </div>
                <div class="finder_buttons">
                	<button class="button_next">Next</button>
                </div>
                <div class="finder_footer">
                	<img width="100" height="30" alt="Braxon Pressure Release Safety Latches – Explosion Proof Latches" src="images/braxon_logo.png">
                </div>
            </div>
            
            <!-- Sparkproof ----------------------------------->
            <div id="div1" class="finder_box">
            	<div class="finder_header">
                	SPARK-PROOF
                </div>
                <div class="finder_main">
                	<div id="error_text1">
                    	Please make a selection before continuing.
                    </div>
                	<div class="finder_question">
                		Does your latch need to be spark-proof?
                    </div>
                	<form id="finder_form-1" class="finder_form" action="" method="post">
                    	<table border="0" cellspacing="0" cellpadding="0" align="center">
                          <tr>
                            <td class="radio_pad"><input name="radio1" type="radio" value="1" /></td>
                            <td class="label_pad">Yes, it needs to be spark-proof.</td>
                          </tr>
                          <tr>
                            <td class="radio_pad"><input name="radio1" type="radio" value="0" /></td>
                            <td class="label_pad">No, it doesn't need to be spark-proof.</td>
                          </tr>
                        </table>
                    </form>
                </div>
                <div class="finder_buttons">
                	<button class="button_back">Back</button>&nbsp;&nbsp;<button class="button_next">Next</button>
                </div>
                <div class="finder_footer">
                	<img width="100" height="30" alt="Braxon Pressure Release Safety Latches – Explosion Proof Latches" src="images/braxon_logo.png">
                </div>
            </div>
            
            <!-- Pressure rating ----------------------------------->
            <div id="div2" class="finder_box">
            	<div class="finder_header">
                	PRESSURE RATING
                </div>
                <div class="finder_main">
                	<div id="error_text2">
                    	Please make a selection before continuing.
                    </div>
                	<div class="finder_question">
                		What pressure rating do you require?
                    </div>
                	<form id="finder_form-2" class="finder_form" action="" method="post">
                    	<div id="box_options2"></div>
                    </form>
                </div>
                <div class="finder_buttons">
                	<button class="button_back">Back</button>&nbsp;&nbsp;<button class="button_next">Next</button>
                </div>
                <div class="finder_footer">
                	<img width="100" height="30" alt="Braxon Pressure Release Safety Latches – Explosion Proof Latches" src="images/braxon_logo.png">
                </div>
            </div>
            
            <!-- Body metal ----------------------------------->
            <div id="div3" class="finder_box">
            	<div class="finder_header">
                	BODY METAL
                </div>
                <div class="finder_main">
                	<div id="error_text3">
                    	Please make a selection before continuing.
                    </div>
                	<div class="finder_question">
                		What body metal would you like?
                    </div>
                	<form id="finder_form-3" class="finder_form" action="" method="post">
                    	<div id="box_options3"></div>
                    </form>
                </div>
                <div class="finder_buttons">
                	<button class="button_back">Back</button>&nbsp;&nbsp;<button class="button_next">Next</button>
                </div>
                <div class="finder_footer">
                	<img width="100" height="30" alt="Braxon Pressure Release Safety Latches – Explosion Proof Latches" src="images/braxon_logo.png">
                </div>
            </div>
            
            <!-- Finish ----------------------------------->
            <div id="div4" class="finder_box">
            	<div class="finder_header">
                	FINISH
                </div>
                <div class="finder_main">
                	<div id="error_text4">
                    	Please make a selection before continuing.
                    </div>
                	<div class="finder_question">
                		Which finish would you like?
                    </div>
                	<form id="finder_form-4" class="finder_form" action="" method="post">
                    	<div id="box_options4"></div>
                    </form>
                </div>
                <div class="finder_buttons">
                	<button class="button_back">Back</button>&nbsp;&nbsp;<button class="button_next">Next</button>
                </div>
                <div class="finder_footer">
                	<img width="100" height="30" alt="Braxon Pressure Release Safety Latches – Explosion Proof Latches" src="images/braxon_logo.png">
                </div>
            </div>
            
        </div>
        
	</div>

</body>
</html>