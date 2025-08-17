(function($){
  $(document).ready(function() {
			console.log('submitted');

		$('tbody').on( 'click', '.activate-license', function(e) {
			e.preventDefault();

			const $wrap = $(this).closest('.stobokit-wrapper');
			const $tr = $(this).closest('tr');

			const slug = $tr.data('slug');
			
			const data = {
				action: 'sbk_activate_license',
				id: $tr.data('id'),
				slug,
				license: $(`[name="${slug}_license_key"]`).val(),
				nonce: $('#stobokit_license_nonce').val()
			};

			$.ajax({
				type: 'post',
				url: ajaxurl,
				cache: false,
				data,
			}).done((response) => {
				if ( response.success ) {
					$tr.find('.status span').removeClass('inactive').addClass(response.data.status).html( response.data.status );
					$tr.find('.expires').html( response.data.expire_date );
				}
				$wrap.find('.license-notice').addClass('active').html(response.data.message);
					
				setTimeout( () => {
					$wrap.find('.license-notice').removeClass('active')
				}, 3000);
			}).always(function () {
			});
		});
  });
``
	$('tbody').on( 'click', '.deactivate-license', function(e) {
		e.preventDefault();

		const $wrap = $(this).closest('.stobokit-wrapper');
		const $tr = $(this).closest('tr');

		const slug = $tr.data('slug');
		
		const data = {
			action: 'sbk_deactivate_license',
			id: $tr.data('id'),
			slug,
			license: $(`[name="${slug}_license_key"]`).val(),
			nonce: $('#stobokit_license_nonce').val()
		};

		$.ajax({
			type: 'post',
			url: ajaxurl,
			cache: false,
			data,
		}).done((response) => {
			if ( response.success ) {
				$tr.find('.status span').removeClass('active').addClass(response.data.status).html( response.data.status );
				$tr.find('.expires').html('');
			}
			$wrap.find('.license-notice').addClass('active').html(response.data.message);
					
			setTimeout( () => {
				$wrap.find('.license-notice').removeClass('active')
			}, 3000);
		}).always(function () {
		});
	});
})(jQuery);

