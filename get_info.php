<?php

	$servername = "braxon.db.10929588.188.hostedresource.net";
	$username = "braxon";
	$password = "Latches@61";
	$dbname = "braxon";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);

	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}
	
	// Turn querystring into variables.
	if (is_numeric($_GET['bn']))
		$box_num = $_GET['bn'];
	if (is_numeric($_GET['h']) )
		$config = $_GET['h'];
	if (is_numeric($_GET['s']))
		$sparkproof = $_GET['s'];
	if (is_numeric($_GET['p']))
		$pressure_rating_id = $_GET['p'];
	if (is_numeric($_GET['b']))
		$body_metal_id = $_GET['b'];
	if (is_numeric($_GET['f']))
		$finish_id = $_GET['f'];

	if ($_GET['m'] == "check" || $_GET['m'] == "") {
		$mode = $_GET['m'];
	}
	
	// Is this a check or final query?
	if ($mode == "check") {
		
		switch ($box_num) {
			// Pressure rating.
			case 2:
				$distinct = "DISTINCT l.pressure_rating_id, pressure_rating";
				break;
			// Body metal.
			case 3:
				$distinct = "DISTINCT l.body_metal_id, body_metal";
				break;
			// Finish.
			case 4:
				$distinct = "DISTINCT l.finish_id, finish";
				break;
			default:
				break;
		}
				
		// Create db query from querystring values.
		$query = "SELECT " . $distinct . " FROM latches l";
		
		// Join with appropriate table to
		// retrieve text values of options.
		switch ($box_num) {
			// Pressure rating.
			case 2:
				$query .= " INNER JOIN pressure_rating pr ON pr.pressure_rating_id = l.pressure_rating_id";
				break;
			// Body metal.
			case 3:
				$query .= " INNER JOIN body_metal bm ON bm.body_metal_id = l.body_metal_id";
				break;
			// Finish.
			case 4:
				$query .= " INNER JOIN finish f ON f.finish_id = l.finish_id";
				break;
			default:
				break;
		}
		
		$query .= " WHERE config = $config AND sparkproof = $sparkproof";
		
		// Create db query from querystring values.
		if ($box_num > 2)
			$query .= " AND pressure_rating_id = $pressure_rating_id";
		if ($box_num > 3)
			$query .= " AND body_metal_id = $body_metal_id";
		if (!$results = mysqli_query($conn, $query))
		    echo mysqli_error($conn);

		$num_rows = $results->num_rows;
	
		// Create new array to hold option values.
		$info = array();
		$i = 0;
		
		while ($row = $results->fetch_assoc()) {
			switch ($box_num) {
				// Pressure rating.
				case 2:
					$info[$i][0] = $row['pressure_rating_id'];
					$info[$i][1] = $row['pressure_rating'];
					//echo $row['pressure_rating_id'] . " - " . $row['pressure_rating'] . "<br />";
					break;
				// Body metal.
				case 3:
					$info[$i][0] = $row['body_metal_id'];
					$info[$i][1] = $row['body_metal'];
					break;
				// Finish.
				case 4:
					$info[$i][0] = $row['finish_id'];
					$info[$i][1] = $row['finish'];
					break;
				default:
					break;
			}

			$i++;
		}
		
		echo json_encode($info);
		
	} else {
	
		// Create db query from querystring values.
		$query = "SELECT * FROM latches WHERE config = $config AND sparkproof = $sparkproof AND pressure_rating_id = $pressure_rating_id AND body_metal_id = $body_metal_id AND finish_id = $finish_id";
		$results = mysqli_query($conn, $query);

		$num_rows = $results->num_rows;
		$match_text = "<h2>" . $num_rows ." match";
		
		if ($num_rows != 1) {
			$match_text .= "es were";
		} else {
			$match_text .= " was";
		}
		
		$match_text .= " found.</h2>";
		
		echo $match_text;
		
		echo "<table cellpadding='0' cellspacing='2' border='0'>";
		//foreach ( $results as $result ) {
		while ($row = $results->fetch_assoc()) {
			$thumbnail = $row['thumbnail'];
			$product_url = $row['product_url'];
			if ($thumbnail == "")
				$thumbnail ="braxon_icon.png";
			if ($product_url == "")
				$product_url = "#";
			echo "<tr><td><img src='images/latches/" . $thumbnail . "' /></td><td>" . $row['item_num'] . "</td><td>" . $row['item_name'] . "</td><td><a href='" . $product_url . "'>View Product</a></td></tr>";
		}
		echo "</table>";
		
	}

	mysqli_close($conn); 

?>