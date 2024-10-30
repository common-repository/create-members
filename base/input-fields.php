<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

use Membership\Utils\Helper;

if ( ! function_exists( 'pro_tag_markup' ) ) {
	function pro_tag_markup( $disable, $class = '' ) {
		$pro_only = ! empty( $disable ) ? 'pro-fr' : '';
		$pro      = '';
		if ( $pro_only !== '' ) {
			$pro .= '<span class="' . esc_attr( $pro_only . ' ' . $class ) . '">' . esc_html__( 'Upgrade to Pro', 'create-members' ) . '</span>';

		}

		return $pro;
	}
}
if ( ! function_exists( 'pro_link_markup' ) ) {
	function pro_link_markup( $disable, $class = '' ) {
		$pro_link_start = '';
		$pro_link_end   = '';
		if ( ! empty( $disable ) ) {
			$pro_link_start = '<a class="pro-link" target="_blank" href="' . esc_url( 'https://woooplugin.com/ultimate-membership/' ) . '">';
			$pro_link_end   = '</a>';
		}

		return array(
			'pro_link_start' => $pro_link_start,
			'pro_link_end'   => $pro_link_end,
		);
	}
}
if ( ! function_exists( 'membership_checkbox_field' ) ) {

	function membership_checkbox_field( $args ) {

		$name 	 		 = ! empty( $args['name'] ) ? $args['name'] : $args['id'];
		$label 	 		 = ! empty( $args['label'] ) ? $args['label'] : '';
		$input_type 	 = ! empty( $args['input_type'] ) ? $args['input_type'] : 'checkbox';
		$input_class 	 = ! empty( $args['input_class'] ) ? $args['input_class'] : '';
		$condition_class = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$disable         = ( ! empty( $args['disable'] ) && $args['disable'] == true ) ? 'disable' : '';
		$data_label      = ! empty( $args['data_label'] ) ? $args['data_label'] : '';
		$checkbox_label  = ! empty( $args['checkbox_label'] ) ? $args['checkbox_label'] : '';
		$checked         = ( ! empty( $args['checked'] ) && $args['checked'] == 'yes' ) ? 'checked' : '';
		extract( pro_link_markup( $disable ) );

		$html = '
			<div class="single-block ' . $condition_class . '">
				<div class="form-label">' . esc_attr($label) . '</div>
				' . $pro_link_start . '
				<div class="check-wrap">
					<label class="input-section custom-switcher ' . $disable . '">
					<input type="'.esc_attr($input_type).'" class="switcher-ui-toggle '. esc_attr($input_class ) .'" 
					id="' . esc_attr($args['id']) . '"
						name="' . esc_attr($name) . '" value="'.esc_attr($args['id']).'"  ' . esc_attr( $checked ) . '
						data-label="' . esc_attr( $data_label ) . '"
						/>
						<span class="slider round"></span>
						<span class="ml-1" for="'.esc_attr($args['id']).'">' . $checkbox_label . '</span>
					</label>
				</div>
				' . pro_tag_markup( $disable ) . '
				' . $pro_link_end . '
			</div>
		';

		echo Helper::kses( $html );
	}
}

/**
 * Wp editor
 */
if ( ! function_exists( 'membership_wp_editor' ) ) {
	function membership_wp_editor( $args ) {
		$condition_class = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$label_class     = ! empty( $args['label_class'] ) ? $args['label_class'] : 'form-label';
		$label           = ! empty( $args['label'] ) ? $args['label'] : '';
		$settings        = ! empty( $args['settings'] ) ? $args['settings'] : array();
		$id              = ! empty( $args['id'] ) ? $args['id'] : '';
		$value           = ! empty( $args['value'] ) ? $args['value'] : '';
		?>
		<div class="single-block <?php echo esc_attr( $condition_class ); ?>">
			<div class="<?php echo esc_attr( $label_class ); ?>"><?php echo esc_html( $label ); ?></div>	
			<?php wp_editor( $value, $id, $settings ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'membership_text_area' ) ) {
	function membership_text_area( $args ) {
		$wrapper_class     = ! empty( $args['wrapper_class'] ) ? $args['wrapper_class'] : 'single-block';
		$condition_class   = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$value             = ! empty( $args['value'] ) ? $args['value'] : '';
		$docs              = ! empty( $args['docs'] ) ? $args['docs'] : '';
		$extra_label_class = ! empty( $args['extra_label_class'] ) ? $args['extra_label_class'] : '';
		$id                = ! empty( $args['id'] ) ? $args['id'] : '';
		$cols              = ! empty( $args['cols'] ) ? $args['cols'] : 29;
		$disable           = ( ! empty( $args['disable'] ) && $args['disable'] !== false ) ? 'disable' : '';

		extract( pro_link_markup( $disable ) );

		$html = '
			<div class="' . esc_attr( $wrapper_class ) . ' ' . esc_attr( $condition_class ) . '">
				<div class="form-label">' . $args['label'] . '</div>
				' . $pro_link_start . '
				<div class="input-section">
					<textarea name=' . $id . ' rows="4" cols="' . $cols . '" id=' . $id . '>' . $value . '</textarea>
					<div class="extra-label ' . $extra_label_class . '">' . $docs . '</div>
				</div>
				' . pro_tag_markup( $disable ) . '
				' . $pro_link_end . '
			</div>
		';

		echo Helper::kses( $html );
	}
}

if ( ! function_exists( 'membership_desc_block' ) ) {
	function membership_desc_block( $args ) {
		$wrapper_class   = ! empty( $args['wrapper_class'] ) ? $args['wrapper_class'] : 'single-block';
		$condition_class = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$description     = ! empty( $args['description'] ) ? $args['description'] : '';

		$html = '
			<div class="' . esc_attr( $wrapper_class ) . ' ' . esc_attr( $condition_class ) . '">
				<div class="form-label">' . $args['label'] . '</div>
				<div class="input-section">' . $description . '</div>
			</div>
		';

		echo Helper::kses( $html );
	}
}

if ( ! function_exists( 'membership_radio_field' ) ) {
	function membership_radio_field( $args ) {

	}

}
if ( ! function_exists( 'membership_select_field' ) ) {
	function membership_select_field( $args ) {
		$count_option     = is_array( $args['options'] ) ? count( $args['options'] ) : 0;
		$options_html     = '';
		$disable          = ( ! empty( $args['disable'] ) && $args['disable'] !== false ) ? 'disable' : '';
		$data_label       = ! empty( $args['data_label'] ) ? $args['data_label'] : '';
		$template_disable = ! empty( $args['template_disable'] ) && is_array( $args['options'] ) ? $args['template_disable'] : $count_option + 1;
		$select_type      = ! empty( $args['select_type'] ) ? $args['select_type'] : '';
		$selected         = ! empty( $args['selected'] ) ? $args['selected'] : '';
		$docs             = ! empty( $args['docs'] ) ? $args['docs'] : '';
		$wrapper_class    = ! empty( $args['wrapper_class'] ) ? $args['wrapper_class'] : 'single-block';
		$docs_class       = ! empty( $args['docs_class'] ) ? $args['docs_class'] : '';
		$input_class      = ! empty( $args['input_class'] ) ? $args['input_class'] : '';

		extract( pro_link_markup( $disable ) );
		if ( ! empty( $args['type'] ) && 'attributes' == $args['type'] ) {
			if ( ! empty( $args['options'] ) ) :
				foreach ( $args['options'] as $item ) :
					$options_html .= '<option value="' . $item->attribute_id . '">' . $item->attribute_label . '</option>';
				endforeach;
			endif;
		} elseif ( ! empty( $args['type'] ) && ( 'template' == $args['type'] ) ) {
			$select_type = '';
			if ( ! empty( $args['options'] ) ) :
				foreach ( $args['options'] as $item ) :
					$disabled      = (int) $item > $template_disable ? 'disabled' : '';
					$pro_text      = ! empty( $disabled ) ? ' (' . esc_html__( 'Pro', 'create-members' ) . ')' : '';
					$options_html .= '<option ' . $disabled . ' value="' . $item . '">' . esc_html__( 'Template', 'create-members' ) . '-' . $item . $pro_text . '</option>';
				endforeach;
			endif;
		} elseif ( ! empty( $args['type'] ) && ( 'random' == $args['type'] ) ) {
			if ( ! empty( $args['options'] ) ) :
				foreach ( $args['options'] as $key => $item ) :
					$disabled = ! class_exists( 'CreateMembersPro' ) && ( ! empty( $args['disable_key'] ) && in_array( $key, $args['disable_key'] ) ) ? 'disabled' : '';
					if ( is_array( $selected ) ) {
						$select_opt = in_array( $key, $selected ) ? 'selected' : '';
					} else {
						$select_opt = $key == $selected ? 'selected' : '';
					}

					$pro_text = $disable !== '' ? Helper::pro_text() : '';

					$options_html .= '<option ' . $disabled . ' ' . $select_opt . ' ' . $disabled . ' value="' . $key . '">' . $item . $pro_text . '</option>';
				endforeach;
			endif;
		} elseif ( class_exists( 'WooCommerce' ) && ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $item ) :
				if ( is_array( $item ) ) {
					$term_id = $item['term_id'];
					$name    = $item['name'];
				} else {
					$term_id = $item->term_id;
					$name    = $item->name;
				}
				$options_html .= '<option value="' . $term_id . '">' . $name . '</option>';
				endforeach;
		}

		$condition_class = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$html            = '
			<div class="' . esc_attr( $wrapper_class ) . ' ' . esc_attr( $condition_class ) . '">
				<div class="form-label">' . $args['label'] . '</div>
				' . $pro_link_start . '
				<div class="input-wrap">
					<select class="' . esc_attr($disable) .' '.esc_attr($input_class). '" name="' . $args['id'] . '" id="' . $args['id'] . '" 
					data-option="' . $data_label . '" ' . $select_type . '>'
					. $options_html .
					'</select>
					<div class="docs ' . $docs_class . '">' . $docs . '</div>
				</div>
				' . pro_tag_markup( $disable ) . '
				' . $pro_link_end . '
			</div>
		';

		echo Helper::kses( $html );
	}
}

/**
 * Button Field
 */
if ( ! function_exists( 'membership_anchor_field' ) ) {
	function membership_anchor_field( $args ) {
		$wrapper_class = ! empty( $args['wrapper_class'] ) ? $args['wrapper_class'] : 'single-block';
		$txt           = ! empty( $args['txt'] ) ? $args['txt'] : '';
		$id            = ! empty( $args['id'] ) ? $args['id'] : '';
		$class         = ! empty( $args['class'] ) ? $args['class'] : '';
		$url           = ! empty( $args['url'] ) ? ' ' . $args['url'] : '';
		$label         = ! empty( $args['label'] ) ? '<div class="form-label">' . $args['label'] . '</div>' : '';

		$html = '
			<div class="' . esc_attr( $wrapper_class ) . '">
				' . $label . '
				<a href="' . esc_url( $url ) . '" target="_blank"
				class="' . esc_attr( $class ) . '" id="' . $id . '" 
				>' . $txt . '</a>
			</div>
		';

		echo Helper::kses( $html );
	}
}

/**
 * Button Field
 */
if ( ! function_exists( 'membership_btn_field' ) ) {
	function membership_btn_field( $args ) {
		$wrapper_class = ! empty( $args['wrapper_class'] ) ? $args['wrapper_class'] : 'single-block';
		$type          = ! empty( $args['type'] ) ? $args['type'] : 'button';
		$btn_txt       = ! empty( $args['btn_txt'] ) ? $args['btn_txt'] : '';
		$id            = ! empty( $args['id'] ) ? $args['id'] : '';
		$btn_class     = ! empty( $args['btn_class'] ) ? $args['btn_class'] : '';
		$url           = ! empty( $args['url'] ) ? ' ' . $args['url'] : '';
		$data          = ! empty( $args['data'] ) ? ' ' . $args['data'] : '';
		$label         = ! empty( $args['label'] ) ? '<div class="form-label">' . $args['label'] . '</div>' : '';

		$html = '
			<div class="' . esc_attr( $wrapper_class ) . '">
				' . $label . '
				<a href="' . esc_url( $url ) . '" target="_blank">
					<button name="' . $id . '"  type="' . $type . '" 
						class="' . esc_attr( $btn_class ) . '" id="' . $id . '" 
						data-name="' . $data . '">' . $btn_txt .
					'</button>
				</a>
			</div>
		';

		echo Helper::kses( $html );
	}
}

/**
 * Number/Text/Hidden
 */
if ( ! function_exists( 'membership_number_input_field' ) ) {
	function membership_number_input_field( $args ) {
		$id                 = ! empty( $args['id'] ) ? $args['id'] : '';
		$wrapper_type       = ! empty( $args['wrapper_type'] ) ? $args['wrapper_type'] : '';
		$wrapper_class      = ! empty( $args['wrapper_class'] ) ? $args['wrapper_class'] : 'single-block';
		$label_class        = ! empty( $args['label_class'] ) ? $args['label_class'] : 'form-label';
		$value              = ! empty( $args['value'] ) ? $args['value'] : '';
		$label              = ! empty( $args['label'] ) ? $args['label'] : '';
		$field_type         = ! empty( $args['field_type'] ) ? $args['field_type'] : 'text';
		$condition_class    = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$disable            = ( ! empty( $args['disable'] ) && $args['disable'] == true ) ? 'disable' : '';
		$number_attr        = ! empty( $args['number_attr'] ) ? $args['number_attr'] : '';
		$docs               = ! empty( $args['docs'] ) ? $args['docs'] : '';
		$docs1              = ! empty( $args['docs1'] ) ? $args['docs1'] : '';
		$placeholder        = ! empty( $args['placeholder'] ) ? $args['placeholder'] : '';
		$data_label         = ! empty( $args['data_label'] ) ? $args['data_label'] : '';
		$extra_label_class  = ! empty( $args['extra_label_class'] ) ? $args['extra_label_class'] : '';
		$extra_label_class1 = ! empty( $args['extra_label_class1'] ) ? $args['extra_label_class1'] : '';
		$input_class        = ! empty( $args['input_class'] ) ? $args['input_class'] : '';
		$extra_label_text1  = ! empty( $args['extra_label_text1'] ) ? $args['extra_label_text1'] : '';
		$hidden_class       = $field_type == 'hidden' ? 'd-none' : '';
		extract( pro_link_markup( $disable ) );
		$html = '
		<div class="' . esc_attr( $wrapper_class ) . ' ' . esc_attr( $condition_class ) . ' ' . esc_attr( $hidden_class ) . '">
			<label class="' . $label_class . '" for="' . esc_attr( $id ) . '">' . $label . '</label>
			' . $pro_link_start . '
				<div class="input-wrap ' . esc_attr( $disable ) . '">
					<div>
						<input type="' . esc_attr( $field_type ) . '" name="' . esc_attr( $id ) . '" 
						id="' . esc_attr( $wrapper_type . $id ) . '"
						value="' . $value . '" class="' . esc_attr( $input_class ) . '"  
						data-option="' . esc_attr( $data_label ) . '" 
						placeholder="' . esc_attr( $placeholder ) . '"
						' . ' ' . $number_attr . '
						/>
						<span class="' . $extra_label_class1 . '">' . $extra_label_text1 . '</span>
					</div>
					<div class="docs ' . $extra_label_class . '">' . $docs . '</div>
					<div class="extra-label ' . $extra_label_class . '">' . $docs1 . '</div>
				</div>
			' . pro_tag_markup( $disable ) . '
			' . $pro_link_end . '
		</div>
		';

		echo Helper::kses( $html );
	}
}

if ( ! function_exists( 'membership_select_field' ) ) {
	function membership_select_field( $args ) {
		$count_option     = is_array( $args['options'] ) ? count( $args['options'] ) : 0;
		$options_html     = '';
		$disable          = ! empty( $args['disable'] ) ? 'disable' : '';
		$data_label       = ! empty( $args['data_label'] ) ? $args['data_label'] : '';
		$template_disable = ! empty( $args['template_disable'] ) && is_array( $args['options'] ) ? $args['template_disable'] : $count_option + 1;
		$select_type      = ! empty( $args['select_type'] ) ? $args['select_type'] : '';
		$selected         = ! empty( $args['selected'] ) ? $args['selected'] : '';
		$desc             = ! empty( $args['desc'] ) ? $args['desc'] : '';

		extract( pro_link_markup( $disable ) );
		if ( ! empty( $args['type'] ) && 'attributes' == $args['type'] ) {
			if ( ! empty( $args['options'] ) ) :
				foreach ( $args['options'] as $item ) :
					$options_html .= '<option value="' . $item->attribute_id . '">' . $item->attribute_label . '</option>';
				endforeach;
			endif;
		} elseif ( ! empty( $args['type'] ) && ( 'template' == $args['type'] ) ) {
			$select_type = '';
			if ( ! empty( $args['options'] ) ) :
				foreach ( $args['options'] as $item ) :
					$disabled      = (int) $item > $template_disable ? 'disabled' : '';
					$pro_text      = ! empty( $disabled ) ? ' (' . esc_html__( 'Pro', 'create-members' ) . ')' : '';
					$options_html .= '<option ' . $disabled . ' value="' . $item . '">' . esc_html__( 'Template', 'create-members' ) . '-' . $item . $pro_text . '</option>';
				endforeach;
			endif;
		} elseif ( ! empty( $args['type'] ) && ( 'random' == $args['type'] ) ) {
			if ( ! empty( $args['options'] ) ) :
				foreach ( $args['options'] as $key => $item ) :
					$disabled = ! empty( $disable ) ? 'disabled' : '';
					if ( is_array( $selected ) ) {
						$select_opt = in_array( $key, $selected ) ? 'selected' : '';
					} else {
						$select_opt = $key == $selected ? 'selected' : '';
					}

					$pro_text      = ! empty( $disabled ) ? ' (' . esc_html__( 'Pro', 'create-members' ) . ')' : '';
					$options_html .= '<option ' . $disabled . ' ' . $select_opt . ' ' . $disabled . ' value="' . $key . '">' . $item . $pro_text . '</option>';
				endforeach;
			endif;
		} elseif ( class_exists( 'WooCommerce' ) && ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $item ) :
				if ( is_array( $item ) ) {
					$term_id = $item['term_id'];
					$name    = $item['name'];
				} else {
					$term_id = $item->term_id;
					$name    = $item->name;
				}
				$options_html .= '<option value="' . $term_id . '">' . $name . '</option>';
				endforeach;
		}
		$condition_class = ! empty( $args['condition_class'] ) ? $args['condition_class'] : '';
		$docs            = '';
		if ( ! empty( $args['docs'] ) ) {
			$docs = doc_html( $args['docs'] );
		}

		$html = '
			<div class="single-block ' . $condition_class . '">
				<div class="form-label">' . $args['label'] . '</div>
				<div class="input-section">
					<select class="' . $disable . '" name="' . $args['id'] . '" id="' . $args['id'] . '" data-option="' . $data_label . '" ' . $select_type . '>' . $options_html .
					'</select>
					' . $docs . '
				</div>
			</div>
		';

		echo Helper::kses( $html );
	}
}

if ( ! function_exists( 'doc_html' ) ) {
	function doc_html( $text ) {
		$html = '
			<div class="docs">
				' . $text . '
			</div>
		';

		return $html;
	}
}
