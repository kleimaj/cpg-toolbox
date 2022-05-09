$( document ).ready(function() {
	let table = $('#upcoming-table').DataTable({
		dom: 'rt<"top"f>rt<"bottom right"p><"bottom left"i>',
		responsive: true,
		ordering:false,
		pageLength: 5,
		columnDefs: [
			{ orderable: false, className: 'dt-center', targets: '_all' }
		]
	});


	$('#upcoming-table_filter').prop('hidden',true);
	$('#upcoming-table').show();
	$('#upcoming-table').addClass('display');

	$("input#search").on("keyup", function() {
	   $('input[type="search"]').val($(this).val()).trigger('keyup');
	   $('#info').html($('#upcoming-table_info').text());
	});

   

	$('#clearall').on('click', function(e){
		$('input#search, input[type="search"]').val("").trigger('keyup');
	});
	
	
});

function sendemail(id=null){
	if(id ==null) return;

	$('#shader, #PopUp').addClass('active');

		$.post({
			url:"emailcalendar.php",
			data:{id: id}
			})
		.done(function(response, status, xhr){

			$('#btn' + id).removeClass('added');
			$('#btn' + id).removeClass('failed');

			if(response == 'Pass'){
				console.log(response);
				setTimeout(function(){
					$('#PopUp').removeClass('active');
					$('#CompletePopUp, .completeStat').addClass('active')
					setTimeout(function(){
						$('#shader, #CompletePopUp, .completeStat').removeClass('active');
					}, 3500);
				}, 500);
				$('#btn' + id).addClass('added');
			}else{
				
				console.log(response)
				setTimeout(function(){
					$('#PopUp').removeClass('active');
					$('#CompletePopUp, .errorStat').addClass('active')
					setTimeout(function(){
						$('#shader, #CompletePopUp, .errorStat').removeClass('active');
					}, 3500);
				}, 500);
				$('#btn' + id).addClass('failed');
			}

			
			$('#btnRe' + id).addClass('active');
			
		})
		.fail(function(response, status, xhr){
			console.log('It failed...')
		});
}


function openwebcast(id=null){
	if(id ==null) return;

	window.location.href = window.location.origin + "/cpg-toolbox-gk/on-demand.php?id="+ id ;
}
