<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include 'conn.php';

function build_calendar($month, $year) {
include 'conn.php';
    $stmt = $mysqli->prepare("select * from bookings_rv where MONTH(date) = ? AND YEAR(date)=?");
    $stmt->bind_param('ss', $month, $year);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }
    // Create array containing abbreviations of days of week.
    $daysOfWeek = array('Sonntag', 'Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');

    // What is the first day of the month in question?
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    // How many days does this month contain?
    $numberDays = date('t',$firstDayOfMonth);

    // Retrieve some information about the first day of the
    // month in question.
    $dateComponents = getdate($firstDayOfMonth);

    // What is the name of the month in question?
    $monthName = $dateComponents['month'];

    // What is the index value (0-6) of the first day of the
    // month in question.
    $dayOfWeek = $dateComponents['wday'];

    // Create the table tag opener and day headers
    $datetoday = date('Y-m-d');
    $calendar = "<h1><center>Trendplace Ravensburg</center></h1><table class='table table-bordered'>";

	switch ($monthName) {
		case "January":
			$calendar .= "<center><h2>Januar $year</h2>";
		break;
		case "February":
			$calendar .= "<center><h2>Februar $year</h2>";
		break;
		case "March":
			$calendar .= "<center><h2>März $year</h2>";
		break;
		case "May":
			$calendar .= "<center><h2>Mai $year</h2>";
		break;
		case "June":
			$calendar .= "<center><h2>Juni $year</h2>";
		break;
		case "October":
			$calendar .= "<center><h2>Oktober $year</h2>";
		break;
		case "December":
			$calendar .= "<center><h2>Dezember $year</h2>";
		break;
		default:
			$calendar .= "<center><h2>$monthName $year</h2>";
	}

    $calendar.= "<a class='btn btn-primary' href='?month=".date('m', mktime(0, 0, 0, $month-1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month-1, 1, $year))."'> < Vorheriger Monat</a> ";

    $calendar.= "<a href='?month=".date('m', mktime(0, 0, 0, $month+1, 1, $year))."&year=".date('Y', mktime(0, 0, 0, $month+1, 1, $year))."' class='btn btn-primary'>Nächster Monat > </a></center><br>";

    $calendar .= "<tr>";

    // Create the calendar headers
    foreach($daysOfWeek as $day) {
        $calendar .= "<th  class='header'>$day</th>";
    }

    // Create the rest of the calendar
    // Initiate the day counter, starting with the 1st.
    $currentDay = 1;
    $calendar .= "</tr><tr>";

     // The variable $dayOfWeek is used to
     // ensure that the calendar
     // display consists of exactly 7 columns.

    if($dayOfWeek > 0) {
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar .= "<td  class='empty'></td>";
        }
    }


    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    while ($currentDay <= $numberDays) {
         //Seventh column (Saturday) reached. Start a new row.
         if ($dayOfWeek == 7) {
             $dayOfWeek = 0;
             $calendar .= "</tr><tr>";
         }

         $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
         $date = "$year-$month-$currentDayRel";
         $dayname = strtolower(date('l', strtotime($date)));
         $eventNum = 0;
         $today = $date==date('Y-m-d')? "today" : "";
         if($date<date('Y-m-d') || $dayOfWeek == 0){
             $calendar.="<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>N/A</button>";
         }/*else if($dayOfWeek == 5){
             $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book-friday.php?date=".$date."&currentDay=".$dayOfWeek."' class='btn btn-success btn-xs'>BUCHEN</a>";
         }else if($dayOfWeek == 6){
             $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book-saturday.php?date=".$date."&currentDay=".$dayOfWeek."' class='btn btn-success btn-xs'>BUCHEN</a>";
         }*/else{
             $calendar.="<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."&currentDay=".$dayOfWeek."' class='btn btn-success btn-xs'>BUCHEN</a>";
         }


         $calendar .="</td>";
         //Increment counters
         $currentDay++;
         $dayOfWeek++;
    }

     //Complete the row of the last week in month, if necessary
     if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for($l=0;$l<$remainingDays;$l++){
            $calendar .= "<td class='empty'></td>";
        }
     }

    $calendar .= "</tr>";
    $calendar .= "</table>";



	return $calendar;

}

if(isset($_POST['submit'])){
	$tomorrow = date("Y-m-d", strtotime('+1 day'));
	$ubermorgen = date("Y-m-d", strtotime('+2 days'));
	$inDreiTagen = date("Y-m-d", strtotime('+3 days'));
	$email = mysqli_real_escape_string($mysqli, $_POST['email']);

	$stmt = $mysqli->prepare("select * from bookings_rv where email = ? AND date= ? or date= ? or date= ?");
	$stmt->bind_param('ssss', $email, $tomorrow, $ubermorgen, $inDreiTagen);

	if($stmt->execute()){
		$result = $stmt->get_result();
		if($result->num_rows>0){
			$stmt = $mysqli->prepare("DELETE FROM bookings_rv WHERE email = ? AND date= ? or date= ? or date= ?");
			$stmt->bind_param('ssss', $email, $inDreiTagen, $tomorrow, $ubermorgen);
			$stmt->execute();
			if($result->num_rows==1){
				$msg = "<div class='alert alert-success'>1 Termin gefunden. Stornierung erfolgreich!</div>";
			}else{
				$msg = "<div class='alert alert-success'>$result->num_rows Termine gefunden. Stornierung erfolgreich!</div>";
			}
			$stmt->close();

		}else{
			$msg = "<div class='alert alert-danger'>Kein Termin mit E-Mail: $email gefunden!</div>";
			$stmt->close();
			}
		$mysqli->close();
	}

}
?>

<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="main.css?v=1" media="screen" />

	<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>

<body>
 <div class="container">
  <div class="row">
   <div class="col-md-12">
    <div id="calendar">
     <?php
      $dateComponents = getdate();
      if(isset($_GET['month']) && isset($_GET['year'])){
		  $month = $_GET['month'];
		  $year = $_GET['year'];
	  }else{
		  $month = $dateComponents['mon'];
		  $year = $dateComponents['year'];
	  }
      echo build_calendar($month,$year);
     ?>
	<div class="row">
		<div class="col-md-12">
			<?php echo(isset($msg))?$msg:""; ?>
		</div>
		<div class="col-md-12" style="text-align: center;">
			<button class="btn btn-primary strono">Meinen Termin stornieren</button>
		</div>
	</div>
	 <center><h4>Für Fragen zum Termin bitte im Ladengeschäft anrufen unter: 0751 28502100</h4></center>
    </div>
   </div>


  </div>
 </div>

 <div id="myModal_01" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Termin stornieren<span id="slot"></span></h4>
				<h5>Manuelles stornieren ist bis sp&auml;testens am Tag vor dem Termin möglich</h5>
				<h5>Hierbei werden alle zukünftigen Termine storniert!</h5>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<form action="" method="post">
							<div class="form-group">
								<label for="">E-Mail</label>
								<input required type="email" class="form-control" name="email">
							</div>
							<div class="form-group pull-right">
								<button name="submit" type="submit" class="btn btn-primary">Mein/e Termin/e stornieren</button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>

	</div>
</div>

<script>
		$(".strono").click(function(){
			$("#myModal_01").modal("show");
		});

    $(document).ready(function() {
		var deleteExtern = getUrlVars()["delete"];
		if(deleteExtern != undefined){
			$("#myModal_01").modal("show");
		}
	});

		// Read a page's GET URL variables and return them as an associative array.
	function getUrlVars()
	{
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}
</script>

</body>
