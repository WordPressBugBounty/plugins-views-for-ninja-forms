<?php

function nf_views_lite_get_ninja_form_fields( $form_id, $json_encode = true ) {
	if ( empty( $form_id ) ) {
		return '{}';
	}

	$form_fields_obj = new stdClass();
	$fields          = Ninja_Forms()->form( $form_id )->get_fields();
	foreach ( $fields as $field ) {
		$values  = array();
		$options = $field->get_setting( 'options' );
		if ( ! empty( $options ) ) {
			foreach ( $options as $option ) {
				$values[ $option['value'] ] = $option['label'];
			}
		}
		if ( $field->get_setting( 'type' ) == 'listcountry' ) {
			$values = array();
			foreach ( Ninja_Forms()->config( 'CountryList' ) as $label => $value ) {
				$values[ $value ] = $label;
			}
		}

		if ( $field->get_setting( 'type' ) === 'checkbox' ) {
			$values          = array();
			$checked_value   = $field->get_setting( 'checked_value' );
			$unchecked_value = $field->get_setting( 'unchecked_value' );
			$values['1']     = $checked_value;
			$values['0']     = $unchecked_value;
		}

		$form_fields_obj->{$field->get_id()} = (object) array(
			'id'        => $field->get_id(),
			'label'     => $field->get_setting( 'label' ),
			'fieldType' => $field->get_setting( 'type' ),
			'type'      => $field->get_setting( 'type' ),
			'values'    => $values,
			'key'       => $field->get_setting( 'key' ),
		);
	}
	if ( $json_encode ) {
		return json_encode( $form_fields_obj );
	} else {
		return $form_fields_obj;
	}

}


/**
 * Get submissions based on specific critera.
 *
 * @since 2.7
 * @param array $args
 * @return array $sub_ids
 */
function nf_views_lite_get_submissions( $args ) {

	$query_args = array(
		'post_type'      => 'nf_sub',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'date_query'     => array(
			'inclusive' => true,
		),
	);
	global $wpdb;
	$join_string     = '';
	$where_string    = '';
	$orderby_string  = '';
		$form_fields = nf_views_lite_get_ninja_form_fields( $args['form_id'], false );

	// Sort
	if ( isset( $args['sort_order'] ) ) {
		$orderby = array();

		foreach ( $args['sort_order'] as $sortfield ) {
			if ( $sortfield['field'] === 'submission_id' || $sortfield['field'] === 'entryId' ) {
				if ( ! isset( $query_args['meta_query']['seq_num_clause'] ) ) {
					$query_args['meta_query']['seq_num_clause'] = array(
						'key'  => '_seq_num',
						'type' => 'numeric',
					);
				}
				$orderby['seq_num_clause'] = $sortfield['value'];
			} elseif ( $sortfield['field'] === 'entryDate' ) {
				$orderby['date'] = $sortfield['value'];
			} elseif ( $form_fields->{$sortfield['field']}->type === 'date' ) {
				$col_name     = 'snfv' . $sortfield['field'] . '.meta_value';
				$join_string .= " LEFT JOIN {$wpdb->postmeta} AS snfv{$sortfield['field']} ON ( {$wpdb->posts}.ID = snfv{$sortfield['field']}.post_id AND snfv{$sortfield['field']}.meta_key='_field_{$sortfield['field']}')";

				$date_col_string = nf_views_cast_to_mysql_date( $args['form_id'], $sortfield['field'], $col_name );
				$orderby_string .= "$date_col_string " . $sortfield['value'];

			} else {
				$query_args['meta_query'][ '_field_' . $sortfield['field'] . '_clause' ] = array(
					'key' => '_field_' . $sortfield['field'],
				);
				$orderby[ '_field_' . $sortfield['field'] . '_clause' ]                  = $sortfield['value'];
			}
		}
		if ( ! empty( $orderby ) ) {
			$query_args['orderby'] = $orderby;
		}
	}
	if ( isset( $args['posts_per_page'] ) && ! empty( $args['posts_per_page'] ) ) {
		$query_args['posts_per_page'] = $args['posts_per_page'];
	}
	if ( isset( $args['offset'] ) && ! empty( $args['offset'] ) ) {
		$query_args['offset'] = $args['offset'];
	}

	if ( isset( $args['form_id'] ) ) {
		$query_args['meta_query'][] = array(
			'key'   => '_form_id',
			'value' => $args['form_id'],
		);
	}

	if ( isset( $args['seq_num'] ) ) {
		$query_args['meta_query'][] = array(
			'key'   => '_seq_num',
			'value' => $args['seq_num'],
		);
	}

	if ( isset( $args['user_id'] ) ) {
		$query_args['author'] = $args['user_id'];
	}

	if ( isset( $args['action'] ) ) {
		$query_args['meta_query'][] = array(
			'key'   => '_action',
			'value' => $args['action'],
		);
	}

	if ( isset( $args['meta'] ) ) {
		foreach ( $args['meta'] as $key => $value ) {
			$query_args['meta_query'][] = array(
				'key'   => $key,
				'value' => $value,
			);
		}
	}

	if ( isset( $args['fields'] ) ) {
		foreach ( $args['fields'] as $field_id => $value ) {
			$query_args['meta_query'][] = array(
				'key'   => '_field_' . $field_id,
				'value' => $value,
			);
		}
	}

	if ( isset( $args['begin_date'] ) and $args['begin_date'] != '' ) {
		// $query_args['date_query']['after'] = nf_get_begin_date( $args['begin_date'] )->format( "Y-m-d G:i:s" );
	}

	if ( isset( $args['end_date'] ) and $args['end_date'] != '' ) {
		// $query_args['date_query']['before'] = nf_get_end_date( $args['end_date'] )->format( "Y-m-d G:i:s" );
	}

	 $query_args = apply_filters( 'nf_views_query_args', $query_args, $args );

	if ( ! empty( $join_string ) ) {
		NF_Views_Query_Modifiers()->set_join_string( $join_string );
		NF_Views_Query_Modifiers()->set_orderby_string( $orderby_string );

		add_filter( 'posts_join', array( 'NF_Views_Query_Modifiers', 'update_join' ) );
		add_filter( 'posts_orderby', array( 'NF_Views_Query_Modifiers', 'update_orderby' ), 1 );
	}
	$subs = new WP_Query( $query_args );
	// echo '<pre>'; print_r( $subs ); die;
	if ( ! empty( $join_string ) ) {
		remove_filter( 'posts_where', array( 'NF_Views_Query_Modifiers', 'update_join' ) );
		remove_filter( 'posts_orderby', array( 'NF_Views_Query_Modifiers', 'update_orderby' ), 1 );
	}

	$submissions = array();
	if ( is_array( $subs->posts ) && ! empty( $subs->posts ) ) {
		$submissions['total_count'] = $subs->found_posts;
		$submissions['subs']        = $subs->posts;
	}

	 wp_reset_postdata();
	return $submissions;
}




function nf_views_get_mysql_date_string( $date_format ) {
	$date_string = '';
	switch ( $date_format ) {
		case 'MM/DD/YYYY':
		case 'm/d/Y':
			$date_string = '%m/%d/%Y';
			break;
		case 'MM-DD-YYYY':
		case 'm-d-Y':
			$date_string = '%m-%d-%Y';
			break;
		case 'MM.DD.YYYY':
		case 'm.d.Y':
			$date_string = '%m.%d.%Y';
			break;
		case 'DD/MM/YYYY':
		case 'd/m/Y':
			$date_string = '%d/%m/%Y';
			break;
		case 'DD-MM-YYYY':
		case 'd-m-Y':
			$date_string = '%d-%m-%Y';
			break;
		case 'DD.MM.YYYY':
		case 'd.m.Y':
			$date_string = '%d.%m.%Y';
			break;
		case 'YYYY-MM-DD':
		case 'Y-m-d':
			$date_string = '%Y-%m-%d';
			break;
		case 'YYYY/MM/DD':
		case 'Y/m/d':
			$date_string = '%Y/%m/%d';
			break;
		case 'YYYY.MM.DD':
		case 'Y.m.d':
			$date_string = '%Y.%m.%d';
			break;
		case 'F j, Y':
			$date_string = '%M %d, %Y';
			break;
		case 'dddd, MMMM D YYYY':
		case 'l, F j, Y':
			$date_string = '%W, %M %d %Y';
			break;
	}
	return $date_string;
}

function nf_views_cast_to_mysql_date( $form_id, $field_id, $col_name ) {
	$nf_field          = Ninja_Forms()->form( $form_id )->get_field( $field_id );
	$field_date_format = $nf_field->get_setting( 'date_format' );

	if ( $field_date_format === 'default' ) {
		$field_date_format = get_option( 'date_format' );
	}

	$date_col_string  = "STR_TO_DATE($col_name, ";
	$date_col_string .= "'" . nf_views_get_mysql_date_string( $field_date_format ) . "'";
	$date_col_string .= ')';
	return $date_col_string;
}
