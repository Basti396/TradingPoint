<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
include_once 'conn.php';

if(isset($_GET['currentDay'])){
	$currentDay = $_GET['currentDay'];
	if($currentDay > 7){
		$currentDay = 1;
	}
	$nextDay = $currentDay+1;
	$prevDay = $currentDay-1;
}

if(isset($_GET['date'])){
    $date = $_GET['date'];
	$currentDate = date('Y-m-d');
	$prev_date = "<a href='book.php?date=".date('Y-m-d', strtotime($date .' -1 day'))."&currentDay=".$prevDay."' class='btn btn-primary'> < vorheriger Tag </a>";
	$next_date = "<a href='book.php?date=".date('Y-m-d', strtotime($date .' +1 day'))."&currentDay=".$nextDay."' class='btn btn-primary'> nächster Tag > </a>";
    $stmt = $mysqli->prepare("select * from bookings_rv where date = ?");
    $stmt->bind_param('s', $date);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['timeslot'];
            }
            $stmt->close();
        }
    }
}

if(isset($_POST['submit'])){

    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $timeslots[] = mysqli_real_escape_string($mysqli, $_POST['timeslot']);
	$timeslots[] = mysqli_real_escape_string($mysqli, $_POST['timeslot_2']);
	$timeslots[] = mysqli_real_escape_string($mysqli, $_POST['timeslot_3']);

	$telefon = mysqli_real_escape_string($mysqli, $_POST['telefon']);
	$street = mysqli_real_escape_string($mysqli, $_POST['street']);
	$location = mysqli_real_escape_string($mysqli, trim($_POST['location']));
	$selectOption = mysqli_real_escape_string($mysqli,$_POST['taskOption']);
	
	$order = mysqli_real_escape_string($mysqli,$_POST['orders']);
	$nummernschild = mysqli_real_escape_string($mysqli,$_POST['nummernschild']);

	foreach ( $timeslots as $number => $timeslot) {
		if($timeslot !== ""){
			if ($number > 0 && $selectOption != "s") {
				break;
			}
			$stmt = $mysqli->prepare("select * from bookings_rv where date = ? AND timeslot=?");
			$stmt->bind_param('ss', $date, $timeslot);
			if($stmt->execute()){
				$result = $stmt->get_result();
				if($result->num_rows>1){
					$msg = "<div class='alert alert-danger'>Bereits gebucht!</div>";
				}else{
					$stmt = $mysqli->prepare("INSERT INTO bookings_rv (name, timeslot, email, date, telefon, street, location, taskOption, orders, nummernschild) VALUES (?,?,?,?,?,?,?,?,?,?)");
					$stmt->bind_param('ssssssssss', $name, $timeslot, $email, $date, $telefon, $street, $location, $selectOption, $order, $nummernschild);
					$stmt->execute();
					$msg = "<div class='alert alert-success'>Buchung am ".date('d.m.Y', strtotime($date))." um ".$timeslot." erfolgreich!</div>";
					$bookings[] = $timeslot;
					$stmt->close();
					$mailSend = true;
					//$mysqli->close();
				}
			}
		}
	}

	$mysqli->close();
	
	
	if(mailSend){
		//Load Composer's autoloader
		//require 'vendor/autoload.php';

		//Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer();
		
		//Server settings
		/*$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = 'user@example.com';                     //SMTP username
		$mail->Password   = 'secret';                               //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
		$mail->Port       = 587; */                                 //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

		//Recipients
		$mail->setFrom('termine@trendplace.de', 'Trendplace Team');
		$mail->addAddress('b.weisshaar@trendplace.de', 'Bastian Weisshaar');     //Add a recipient
		$mail->addAddress('termine@trendplace.de');               //Name is optional
		//$mail->addReplyTo('termine@trendplace.de', 'Trendplace Team');
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		//Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		//Content
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = 'Ihr Termin bei Trendplace';
		$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		
		$mail->send();
	}
}


if($currentDay == 5){
	$start = "13:00";
	$end = "19:00";
}else if($currentDay == 6){
	$start = "12:00";
	$end = "18:00";
}else{
	$start = "14:00";
	$end = "19:00";
}
$duration = 5;
$cleanup = 0;


function timeslots($duration, $cleanup, $start, $end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();

    for($intStart = $start; $intStart<$end; $intStart->add($interval)->add($cleanupInterval)){
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if($endPeriod>$end){
            break;
        }
        $slots[] = $intStart->format("H:i")." Uhr - ". $endPeriod->format("H:i")." Uhr ";
    }

    return $slots;
}


?>
<!doctype html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title></title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	

  </head>

  <body>
    <div class="container">
        <h1 class="text-center">Buchen Sie für den: <?php echo date('d.m.Y', strtotime($date)); ?></h1>
		<div style="text-align:center";>
			<?php echo $prev_date; ?>
			<a href="calendar.php" class="btn btn-primary text-center">Zur Übersicht</a>
			<?php echo $next_date; ?>
		</div>
		<hr>

		<div class="row">
			<div class="col-md-12">
				<?php echo(isset($msg))?$msg:""; ?>
			</div>

			<?php
			if($currentDay == 7 || $currentDate > $date){
			?>
			<div class="col-md-12">
				<h2>Für diesen Tag stehen keine Termine zur Verfügung!</h2>
			</div>
			<?php
			}else{
			
			$Durat2=15;
			$timeslots = timeslots($duration, $cleanup, $start, $end);
				foreach($timeslots as $ts){
			?>
					<div class="col-md-2">
						<div class="form-group">
							<?php 
							if(in_array($ts, $bookings)){ 
								$tmp = array_count_values($bookings);
								$cnt = $tmp[$ts];
								if($cnt == 2){
							?>
							<button class="btn btn-danger"><?php echo $ts; ?></button>
								
							<?php 
								}else if($cnt == 1){ 
							?>
							<button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
								
							<?php 
							}}  else { 
							?>
							<button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
							<?php 
							}
							?>
						</div>
					</div>
			<?php }} ?>
		</div>


    </div>

	<div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Termin für: <span id="slot"></span></h4>
					<p>Bitte Notieren Sie sich den Termin!</p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
								<div class="form-group">
									<label for="taskOption">Besuchsgrund </label>
                                    <select class="taskOption" name="taskOption" required>
									  <option value="t">Tabak und Zubehör</option>
									  <option value="s">Shishakauf</option>
									</select>
                                </div>
								<div class="form-group notification" style="color:red;">

							    </div>
                               <div class="form-group">
                                    <label for="">Zeitfenster</label>
                                    <input readonly type="text" class="form-control" id="timeslot" name="timeslot">
                                </div>
                                <div class="form-group">
                                    <label for="">Voller Name</label>
                                    <input required type="text" class="form-control" name="name">
                                </div>
                                <div class="form-group">
                                    <label for="">E-Mail</label>
                                    <input required type="email" class="form-control" name="email">
                                </div>
								<div class="form-group">
                                    <label for="">Telefon</label>
                                    <input required type="telefon" class="form-control" name="telefon">
                                </div>
								<div class="form-group">
                                    <label for="">Straße</label>
                                    <input required type="street" class="form-control" name="street">
                                </div>
								<div class="form-group">
                                    <label for="">Ort</label>
                                    <input required type="location" class="form-control" name="location">
                                </div>
								<div class="form-group">
                                    <label for="">Nummernschild</label>
                                    <input type="nummernschild" class="form-control" name="nummernschild">
                                </div>
								<div class="form-group">
									<label for="">Bestellung</label>
									<textarea required class="form-control" placeholder="Hier bitte Bestellung eintragen..." name="orders" cols="35" rows="4"></textarea>
								</div>
								<p>Mit <strong>KLICK</strong> auf "Senden", bestätige ich, dass ich die <a href="https://trendplace.de/Datenschutz_Corona">Datenschutzerklärung</a> durchgelesen habe und dieser zustimme.</p>
                                <div class="form-group pull-right">
                                    <button name="submit" type="submit" class="btn btn-primary postIt">Senden</button>
                                </div>
								<input type="hidden" name="timeslot_2" id="timeslot_2"></input>
								<input type="hidden" name="timeslot_3" id="timeslot_3"></input>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

	<script src="timeslots.js"></script>


	

  </body>

</html>
