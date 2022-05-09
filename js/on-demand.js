$( document ).ready(function() {
			let table = $('#on-demand-table').DataTable({
			dom: 'rt<"top"f>rt<"bottom right"p><"bottom left"i>',
			responsive: true,
			ordering:false,
			pageLength: 2,
			columnDefs: [
			{ orderable: false, className: 'dt-center', targets: '_all' }
		]
		});


		$('#on-demand-table_filter').prop('hidden',true);
		$('#on-demand-table').show();
		$('#on-demand-table').addClass('display');

		$("input#search").on("keyup", function() {
		$('input[type="search"]').val($(this).val()).trigger('keyup');
		$('#info').html($('#on-demand-table_info').text());
		});

	

		$('#clearall').on('click', function(e){
			$('input#search, input[type="search"]').val("").trigger('keyup');
		});

		if($('#search').val() != ""){
			$('input#search, input[type="search"]').val($('#search').val()).trigger('keyup');
		}
	});