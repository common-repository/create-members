(function ($) {
	'use strict';
	$( document ).ready(
		function () {
			/**
			 * Settings Tab
			 */
			const $settings_tab_li = $( '.settings_tab_pan li' );
			const active_tab       =
			window.location?.hash.slice( 1 ) == ''
			? 'settings'
			: window.location?.hash.slice( 1 );

			$settings_tab_li.removeClass( 'active' );
			$( '.tab-content div' ).removeClass( 'active' );
			$( `li[data-item="${active_tab}"]` ).addClass( 'active' );
			$( `#${active_tab}` ).addClass( 'active' );

			$settings_tab_li.on(
				'click',
				function () {
					const $this = $( this );
					let current = `#${$this.data( 'item' )}`;
					$settings_tab_li.removeClass();
					$( '.tab-content > div' ).hide();
					$( this ).addClass( 'active' );
					const index = $settings_tab_li.index( this );
					hide_submit_btn( current );
					$( '.tab-content > div:eq(' + index + ')' ).show();
					window.history.replaceState( null, null, current );
				}
			);

			hide_submit_btn( `#${active_tab}` );
			function hide_submit_btn(index) {
				let $admin_button = $( '.admin-button' );
				if ( index == '#short-codes' ) {
					$admin_button.hide();
				} else {
					$admin_button.show();
				}
			}

			/**
			 * Block show / hide
			 */
			const input_arr = ['#hide_price','#access_type','#plan_cost','#non_member_offer','#discount_in',
			'#recurring_subscription'
			];
			input_arr.forEach(
				(item) => {
                $( item ).on(
						'change',
						function () {
							let $this 	= $( this );
							const id 	= $this.attr( 'id' );
							const input = $( `.${id}` );
							toggle_show_hide( $this,input );
						}
				);
				}
			);

			function toggle_show_hide($this,input) {
				let input_type = $this.prop( 'type' );
				switch (input_type) {
					case 'select-one':
						if (input.hasClass( 'd-none' )) {
							input.css( 'display','grid' );
							input.removeClass( 'd-none' );
						} else {
							input.addClass( "d-none" );
							input.removeAttr( "style" )
						}
						if ($this.prop( 'name' ) == 'discount_in') {
							let val      = $this.val();
							let product  = $( '.product' );
							let category = $( '.category' );
							if (val == 'product') {
								category.fadeOut();
								product.fadeIn();
								if (product.hasClass( 'd-none' )) {
									product.removeClass( 'd-none' );
								}
							} else {
								product.fadeOut();
								category.removeAttr( 'style' ).removeClass( 'd-none' );
							}
						}

						break;
					case 'checkbox':
						if ($this.is( ':checked' ) == 'yes' || $this.is( ':checked' ) == true) {
							input.css( 'display','flex' );
							if ($this.attr( 'id' ) == 'recurring_subscription') {
								$( '.recurring_section' ).removeClass( 'd-none' ).fadeOut();
							}
							if (input.hasClass( 'd-none' )) {
								input.removeClass( 'd-none' );
							}
						} else {
							input.fadeOut();
							if ($this.attr( 'id' ) == 'recurring_subscription') {
								$( '.recurring_section' ).removeClass( 'd-none' ).fadeIn();
							}
						}
						break;
					default:
						break;
				}

			}

			/**
			 * Discount type on change
			 */
			$( '#discount_type' ).on(
				'change',
				function () {
					const $this = $( this );
					if ($this.val() == 'percent_product') {
						$( '.discount_number_label' ).html( '' ).html( '%' );
					} else if ($this.val() == 'fixed_product') {
						$( '.discount_number_label' )
						.html( '' )
						.html( membership_admin.currency );
					}
				}
			);

			/**
			 * Select 2
			 */
			const ids = ['#hide_price_product','#restrict_products','#restrict_prices','#plan_product',
			'#restrict_contents','#message_plan','#member_plan_id','#member_user','#redirect_content',
			'#filter_by_products','#filter_by_category', '#short_code_module', '#wp_hide_pages',
			'#post_categories','#packages','#single_post'
			];
			$.each(
				ids,
				function (index, value) {
					let  single_selects = [ '#member_user' , '#member_plan_id',
					'#redirect_content', '#plan_product' ];
					let multiple        = ( $.inArray( value, single_selects ) !== -1  ) ? false : true;
					$( value ).select2(
						{
							placeholder: 'Select',
							allowClear: false,
							width: '100%',
							multiple: multiple,
						}
					);
				}
			);
			let packages     = $( '#packages' );
			let packaged_ids = packages.data( 'packages' );
			let package_ids  = packaged_ids;

			if ( ! $.isNumeric( package_ids ) && packaged_ids ?.indexOf( ',' ) > -1) {
				package_ids = packaged_ids.split( ',' )
			}

			packages.val( package_ids ).trigger( 'change' );

			$( document ).on(
				'select2:open',
				() => {
                document.querySelector( '.select2-search__field' ).focus();
				}
			);

			/**
			 * Save settings / Add plans
			 */
			const save_settings = $( '#membership-settings' );
			save_settings.submit(
				function (e) {
					e.preventDefault();
					const form       = $( this );
					const form_data  = form.serializeArray();
					let message_plan = $( '#message_plan' );

					let obj              = {};
					let submit_button    = $( '.admin-button' );
					let settings_message = $( '.settings_message' );
					form_data.map(
						function (x, item) {
							obj[x.name] = x.value;
						}
					);

					if ( message_plan.length > 0 ) {
						obj.message_plan = message_plan.val();
					}
					if ( $( '.membership-settings-form' ).length > 0 ) {

						obj.is_enable_membership = $( '#is_enable_membership' ).prop( 'checked' ) ? 'yes' : '';
					}

					obj        = membership_plan_data( obj );
					const data = {
						action: 'save_membership_settings',
						params: obj,
						ult_mem_nonce: membership_admin.ult_mem_nonce,
					};

					$.ajax(
						{
							url: membership_admin.ajax_url,
							method: 'POST',
							data,
							dataType: 'json',
							beforeSend() {
								submit_button.addClass( 'loading' );
							},
							success( response ) {
								if ( $( '.membership-plan' ).length > 0 || $( '.membership-plan' ).length > 0 ) {
									window.location.href = membership_admin.subscription_page;
								} else if ( $( '.add-member' ).length > 0 ) {
									window.location.href = membership_admin.member_page;
								} else {
									message_plan.val( '' ).trigger( 'change' );
									$( '#members_email_subject' ).val( '' );
									$( '#members_email_title' ).val( '' );
									$( '#Message' ).val( '' );
									settings_message.removeClass( 'd-none' ).
									html( '' ).html( response ?.data ?.message ).fadeOut( 'slow' );
									submit_button.removeClass( 'loading' );
								}
							},
						}
					);
				}
			);

			function membership_plan_data(obj) {
				if ( $( '.new-plan' ).length > 0 ) {
					let $single_post           = $( '#single_post' ).val();
					let $wp_hide_pages         = $( '#wp_hide_pages' ).val();
					let $post_categories       = $( '#post_categories' ).val();
					let $filter_by_products    = $( '#filter_by_products' ).val();
					let $filter_by_category    = $( '#filter_by_category' ).val();
					let $rest_prod             = $( '#restrict_products' ).val();
					let $rest_prices           = $( '#restrict_prices' ).val();
					let $rest_cont             = $( '#restrict_contents' ).val();
					obj.status                 = $( '#status' ).is( ':checked' ) == false ? 'no' : 'yes';
					obj.recurring_subscription = $( '#recurring_subscription' ).is( ':checked' ) == false ? 'no' : 'yes';
					obj.free_shipping          = $( '#free_shipping' ).is( ':checked' ) == false ? 'no' : 'yes';
					obj.restrict_contents      = $rest_cont.length == 0 ? '' : $rest_cont;
					obj.restrict_products      = $rest_prod.length == 0 ? '' : $rest_prod;
					obj.restrict_prices        = $rest_prices.length == 0 ? '' : $rest_prices;
					obj.filter_by_products     = $filter_by_products.length == 0 ? '' : $filter_by_products;
					obj.filter_by_category     = $filter_by_category.length == 0 ? '' : $filter_by_category;
					obj.wp_hide_pages          = $wp_hide_pages.length == 0 ? '' : $wp_hide_pages;
					obj.post_categories        = $post_categories.length == 0 ? '' : $post_categories;
					obj.single_post            = $single_post.length == 0 ? '' : $single_post;
				}
				return obj;
			}

			/**
			 * Open modal
			 */
			// const $modal_content = '#membership-modal';
			// $('.add-new-member').on('click', function (e) {
			// e.preventDefault();
			// $($modal_content).show();
			// });

			// closing
			// const $modal_close = $('.modal-close');
			// $modal_close.on('click', function () {
			// $($modal_content).fadeOut(500);
			// });

			// $(document).click(function (e) {
			// if ($(e.target).is($modal_content)) {
			// $($modal_content).fadeOut(500);
			// }
			// });

			let expire_date = $( "#expire_date" );
			// reset expire date
			$( ".reset_expire" ).on(
				'click',
				function () {
					expire_date.val( '' );
				}
			);

			/**
			 * Call flatpickr
			 */
			expire_date.flatpickr(
				{
					minDate: "today",
				}
			);

			/**
			 * Update Plan
			 */
			const $update_member = $( '.update-member' );
			$update_member.click(
				function () {
					const $this       = $( this );
					const ID          = $this.data( 'id' );
					const show_values = {
						ID: ID,
						member_user: $this.data( 'member_user' ),
						member_plan_id: $this.data( 'member_plan_id' ),
						status: $this.data( 'status' )
					};

					$.each(
						show_values,
						function (key, valueObj) {
							const input = $( '.membership-plan' ).find( '#' + key );
							if ( input.prop( 'type' ) == 'select-one' || input.prop( 'type' ) == 'select-multiple' ) {
								let data = typeof valueObj === 'string' ? valueObj.split( ',' ) : [valueObj];
								input.val( data ).trigger( 'change' );
							} else if ( input.prop( 'type' ) == 'checkbox' ) {
								if ( valueObj == 'yes' ) {
									input.prop( 'checked', true );
								} else {
									input.prop( 'checked', false );
								}
							} else {
								input.val( valueObj );
							}
						}
					);

					$( $modal_content ).show();
				}
			);

			/**
			 * Resend New member email
			 */
			$( '.new-mem-resend' ).on(
				'click',
				function () {
					let $this = $( this );

					if ( $this.data( 'id' ) !== 'new-mem-resend' ) {
						return;
					}
					const data = {
						action: 'resend_email',
						re_send_email: $this.data( 'id' ),
						user_id: $this.data( 'user_id' ),
						plan_id: $this.data( 'plan_id' ),
						ult_mem_nonce: membership_admin.ult_mem_nonce,
					};

					$.ajax(
						{
							url: membership_admin.ajax_url,
							method: 'POST',
							data,
							dataType: 'json',
							beforeSend() {
								$this.addClass( 'loading' );
							},
							success( response ) {
								$this.html( '' ).html( response ?.data ?.txt );
								$this.removeClass( 'loading' );
							},
						}
					);
				}
			)
		}
	);

	/**
	 * Set Plan id
	 */
	$( '.member_plan_id' ).on(
		'change',
		function (e) {
			e.preventDefault();
			let $this = $( this );
			$( '.plan_id' ).val( $this.val() );
		}
	);

	/**
	 * Change Email notification section
	 */
	$( '#notification_type' ).on(
		'change',
		function (e) {
			e.preventDefault();
			let $this         = $( this );
			let $mail_section = `.${$this.val()}`;

			$( $mail_section ).removeClass( 'd-none' ).show();
			if ( $this.val() == 'all_members') {
				$( '.new_member' ).hide();
				$( '.cancel_member' ).hide();
			} else if ( $this.val() == 'new_member') {
				$( '.all_members' ).hide();
				$( '.cancel_member' ).hide();
			} else if ( $this.val() == 'cancel_member') {
				$( '.all_members' ).hide();
				$( '.new_member' ).hide();
			}

		}
	);

	let level_type 	= $( '#level_type' );
	let plan_btn 	= $( '.plan-btn' );
	let woo_subs 	= $( `.woo_subscription` );
	let woo_modules = 'woo_modules';
	let wp_modules  = 'wp_modules';
	if ( level_type.val() == woo_modules && membership_admin.plan_cost == 'subscription' ) {
		woo_subs.removeClass( 'd-none' ).fadeIn();
	}

	level_type.on(
		'change',
		function (e) {
			e.preventDefault();
			let $this  = $( this );
			let $class = $( `.${$( this ).val()}` );
			$class.fadeIn();
			$class.removeClass( 'd-none' );
			if ( $this.val() == 'wp_modules' ) {
				woo_subs.removeClass( 'd-none' ).fadeIn();
				$( `.${woo_modules}` ).fadeOut();
				if ( ! membership_admin.is_pro_active) {
					plan_btn.attr( 'disabled',true );
				}
			} else if ( $this.val() == 'woo_modules' && membership_admin.plan_cost == 'subscription' ) {
				woo_subs.removeClass( 'd-none' ).fadeIn();
			} else if ( $this.val() == 'woo_modules' ) {
				woo_subs.addClass( 'd-none' ).fadeOut();
				$( `.${wp_modules}` ).fadeOut();
				plan_btn.removeAttr( 'disabled',false );
			}
		}
	);
	let checked_value = ['#woo_modules','#wp_modules'];
	checked_value.forEach(
		(item) => {
			$( item ).on(
			'change',
			function () {
				if ($( item ).is( ':checked' ) == 'yes' || $( item ).is( ':checked' ) == true) {
					$( item ).val( 'yes' );
				} else {
					$( item ).val( 'no' );
				}
			}
			);
		}
	);

	/**
	 * ShortCode generator
	 */
	generateShortCode();
	function generateShortCode() {
		$( '.generate-block' ).each(
			function (index, value) {
				const _this = $( this );
				_this.on(
					'click',
					function (e) {
						e.preventDefault();
						if (_this.hasClass( 'disable' )) {
							return; }

						const results        = _this.siblings().find( '#full_input' );
						const parent_block   = _this.parents( '.shortcode-block' );
						const shortcode_name = parent_block.data( 'name' );
						const input_value    = findInputValue(
							parent_block.find( '.shortcode_value' )
						);
						const shortcode      = `[${shortcode_name} ${input_value}]`;
						results.val( '' ).val( shortcode );
						copyTextData( results );
					}
				);
			}
		);
	}

	// find input value
	function findInputValue(_this) {
		let result       = '';
		const checkbox   = _this.find( 'input:checkbox' );
		const input_text = _this.find( 'input:text' );
		const select_box = _this.find( 'select' );

		// select box
		if (select_box.length > 0) {
			select_box.each(
				function () {
					const $this   = $( this );
					const is_true = shortcode_input_disable( $this );
					if (is_true) {
						return;
					}
					// select option
					if ($.isArray( $this.val() )) {
						result += ` ${$this.data( 'option' )} = "${$this.val().toString()}"`;
					} else {
						result += ` ${$this.data( 'option' )} = "${$this.val()}"`;
					}
				}
			);
		}

		// check box
		if (checkbox.length > 0) {
			checkbox.each(
				function () {
					const $this   = $( this );
					const is_true = shortcode_input_disable( $this );
					if (is_true) {
						return;
					}
					const value = $this.is( ':checked' ) ? 'yes' : 'no';

					result += ` ${$this.data( 'label' )} = "${value}"`;
				}
			);
		}

		// input text
		result = shortcode_input_value( input_text, result );

		return result;
	}

	function shortcode_input_value(input_data, result) {
		if (input_data.length > 0) {
			input_data.each(
				function () {
					const $this   = $( this );
					const is_true = shortcode_input_disable( $this );
					if (is_true) {
						return;
					}
					// input value
					if ($.isArray( $this.val() )) {
						result += ` ${$this.data( 'option' )} = "${$this.val().toString()}"`;
					} else {
						result += ` ${$this.data( 'option' )} = "${$this.val()}"`;
					}
				}
			);
		}

		return result;
	}

	function shortcode_input_disable($this) {
		const disable     = $this.parent().hasClass( 'disable' );
		const disable_pro = $this.parent().hasClass( 'pro-disable' );
		const d_none      = $this.parents( '.d-none' );
		if (disable_pro || disable || d_none.length > 0) {
			return true;
		}
		return false;
	}

	// copy text
	function copyTextData(fieldId) {
		if (fieldId.length > 0) {
			fieldId.select();
			document.execCommand( 'copy' );
			alert( 'Copied On clipboard' );
		}
	}

	/**
	 * Admin report filter
	 */
	$( '.filter_modules_type' ).on(
		'change',
		function (e) {
			e.preventDefault();
			$( '.modules_type' ).val( $( this ).val() );

		}
	);
})( jQuery );