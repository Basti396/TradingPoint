<?php
include_once 'header.php';

require_once 'includes/functions.inc.php';
require_once 'includes/dbh.inc.php';
?>

<script>
$(document).ready(function(){
	$(".makegutschein").click(function(){
		$("#makeGutschein_Modal").modal("show");
	});
	$(".cashgutschein").click(function(){
		$("#cashGutschein_Modal").modal("show");
	});
	$(".allgutscheine").click(function(){
		window.location.href = 'uebersicht.php';
	});
});
</script>

<div class="container buttons">
	<div class="row">
		<div class="col-4">
			<button class="btn btn-primary makegutschein">Gutschein erstellen</button>
		</div>
		<div class="col-4">
			<button class="btn btn-primary cashgutschein">Gutschein einlösen</button>
		</div>
		<div class="col-4">
			<button class="btn btn-primary allgutscheine">Alle Gutscheine</button>
		</div>
	</div>
</div>

<!-- Modal -->
<div id="makeGutschein_Modal" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Gutschein erstellen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<form method="post" action="includes/gutscheine.inc.php" enctype="multipart/form-data">
			<div class="form-group">
				<label for="gutscheinnummer">Gutschein-Nummer</label>
				<input readonly type="number" name="gutscheinnummer" value="<?php echo getNewGutscheinNr($mysqli) ?>">
			</div>
			<div class="form-group">
				<label for="bonnr">Bon Nummer</label>
				<input type="text" name="bonnr">
			</div>
			<div class="form-group">
				<label for="wert">Wert in Euro</label>
				<input type="number" name="wert" step=".01" min="0">
			</div>
			<div class="form-group">
				<button class="btn btn-secondary" type="submit" name="submit">Gutschein erstellen</button>
			</div>
		</form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="cashGutschein_Modal" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Gutschein einlösen</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<form method="post" action="includes/gutscheine.inc.php" enctype="multipart/form-data">
			<div class="form-group">
				<label for="gutscheinnummer">Gutschein-Nummer</label>
				<input type="number" name="gutscheinnummer">
			</div>
			<div class="form-group">
				<label for="bonnr">Bon Nummer</label>
				<input type="text" name="bonnr">
			</div>
			<div class="form-group">
				<label for="rechnung">Zu bezahlender Betrag</label>
				<input type="number" name="rechnung" step=".01" min="0">
			</div>
			<div class="form-group">
				<button class="btn btn-secondary" type="submit_1" name="submit_1">Gutschein einlösen</button>
			</div>
		</form>
      </div>
    </div>
  </div>
</div>

<div class="container">
	<div class="row justify-content-center">
		<div class="col-8">
		<?php 
			if(isset($_GET["error"])){
				if($_GET["error"] == "stmtfailed"){
					echo "<p class='alert alert-danger'>Irgend etwas ist scheif gelaufen. Bitte wende dich an den Entwickler.</p>";
				}else if($_GET["error"] == "gutscheinnummerblocked"){
					echo "<p class='alert alert-danger'>Diese Gutscheinnummer scheint schon vergeben zu sein. Bitte versuche es mit einer neuen Gutscheinnummer.</p>";
				}else if($_GET["error"] == "gutscheinerstellt"){
					echo "<p class='alert alert-success'>Der Gutschein mit der Nummer <b>".$_GET["number"]."</b> im Wert von <b>".$_GET["wert"]."€</b> wurde erfolgreich erstellt.</p>";
				}else if($_GET["error"] == "gutscheineinc-fehler"){
					echo "<p class='alert alert-success'>Etwas ist schief geloffen. Bitte wenden Sie sich an den Entwickler.</p>";
				}
			}
			
			if(isset($_GET["gutscheinerstellen"])){
				if($_GET["gutscheinerstellen"] == 1){
					echo "<p class='alert alert-primary'>Ursprünglicher Gutscheinwert war <b>".$_GET["wertAlt"]." €</b> - ursprünglicher Rechnungsbetrag war <b>".$_GET["rechnung"]." €</b></p>";
					echo "<p class='alert alert-primary'>Der Gutschein hat noch ein Restwert von <b>".$_GET["saldo"]." €</b></p>";
					echo "<p class='alert alert-primary'>Restlicher zu bezahlender Betrag: <b>0 €</b></p>";
				}else if($_GET["gutscheinerstellen"] == 0){
					echo "<p class='alert alert-primary'>Ursprünglicher Gutscheinwert war <b>".$_GET["wertAlt"]." €</b> - ursprünglicher Rechnungsbetrag war <b>".$_GET["rechnung"]." €</b></p>";
					echo "<p class='alert alert-primary'>Der Gutschein wurde komplett aufgebraucht.</p>";
					echo "<p class='alert alert-primary'>Restlicher zu bezahlender Betrag: <b>".$_GET["saldo"]." €</b></p>";
				}
			}

		?>
		</div>
	</div>
</div>

<?php
include_once 'footer.php';
?>