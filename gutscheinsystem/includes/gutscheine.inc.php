<?php
require_once 'functions.inc.php';
require_once 'dbh.inc.php';


if(isset($_POST['submit'])){
	$number = $_POST['gutscheinnummer'];
	$euro = $_POST['wert'];
	$bonnr = $_POST['bonnr'];
	
	makeGutschein($mysqli, $number, $euro, $bonnr);
}else if(isset($_POST['submit_1'])){
	$number = $_POST['gutscheinnummer'];
	$rechnung = $_POST['rechnung'];
	$bonNrCash = $_POST['bonnr'];
	
	cashGutschein($mysqli, $number, $rechnung, $bonNrCash);
}else{
	header("location: ../index.php?error=gutscheineinc-fehler");
	exit();
}