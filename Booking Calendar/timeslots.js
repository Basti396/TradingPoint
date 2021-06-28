$(".book").click(function(){
	var element_0 = $(this).parent("div").parent("div");
	var timeslot = $(this).attr('data-timeslot');
	$("#slot").html(timeslot);
	$("#timeslot").val(timeslot);
	$("#myModal").modal("show");
	$(".taskOption").val("-1");
	$(".notification").empty();
	$(".postIt").show();
	
	$( ".taskOption" ).change(function() {
  		if ($(".taskOption").val() == "s"){
			var timeslot_2 = element_0.next();
			var timeslot_3 = timeslot_2.next();
			timeslot_2_data = timeslot_2.children("div").children("button").attr('data-timeslot');
			timeslot_3_data = timeslot_3.children("div").children("button").attr('data-timeslot');
			if (timeslot_2.children("div").children("button").hasClass("btn-danger") || timeslot_3.children("div").children("button").hasClass("btn-danger")){
				// so gehts nicht !
				$(".notification").html("Zu diesem Zeitraum ist leider kein Shishakauf möglich. Bitte Wählen sie einen Zeitraum aus, bei welchem 2 darauffolgende Termine ebenfalls frei sind ODER wählen sie einen anderen Buchungsgrund!");
				$(".postIt").hide();
			}else{
				$(".notification").empty();
				$(".postIt").show();
			}
			console.log(timeslot_2_data, timeslot_3_data);
			$("#timeslot_2").val(timeslot_2_data);
			$("#timeslot_3").val(timeslot_3_data);
		}else{
			$(".notification").empty();
			$(".postIt").show();
		}
	});
});