<?php
include_once 'header.php';
?>

<?php
	require_once 'includes/dbh.inc.php';
	require_once 'includes/functions.inc.php';
?>

<div class="container">
	<div class="row justify-content-center">
		<div class="col-6">
			<h1>Alle Gutscheine</h1>
		</div>
	
		<div class="col-6 filter-wrapper">
			<input class="filter-number" onchange="numberFilter(this.value)" placeholder="Filter Gutschein Nr.">
		</div>
	</div>
	
	<div class="row">
		<div class="col-12">
			<?php echo getGutscheine($mysqli);?>
		</div>
	</div>

	
</div>

<script>
	function numberFilter(number){
		let zeile = document.querySelectorAll(".zeile");
		if(number == null || number == ""){
			for(var i=0; i<zeile.length; i++){
				zeile[i].style.display = "table-row";
			}
		}else{
			if(document.querySelector(".nr" + number) == null){
				for(var i=0; i<zeile.length; i++){
					zeile[i].style.display = "none";
				}
			}else{
				let targetNr = document.querySelector(".nr" + number)
				for(var i=0; i<zeile.length; i++){
					zeile[i].style.display = "none";
				}
				targetNr.style.display = "table-row";
			}
		}
	}
</script>

<?php
include_once 'footer.php';
?>