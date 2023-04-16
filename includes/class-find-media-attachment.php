<?php

class Find_Media_Attachment {

	public function __construct() {

		add_action( 'plugins_loaded',             array( $this, 'load_plugin_textdomain' ) );

		add_filter( 'attachment_fields_to_edit',  array( $this, 'attachment_fields_to_edit' ), 10, 2 );

	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'safe-media-delete' );
	}

	public function get_posts_by_attachment_id( $attachment_id ) {
		$used_as_thumbnail = array();

		if ( wp_attachment_is_image( $attachment_id ) ) {
			$thumbnail_query = new WP_Query( array(
				'meta_key'       => '_thumbnail_id',
				'meta_value'     => $attachment_id,
				'post_type'      => 'any',	
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			) );

			$used_as_thumbnail = $thumbnail_query->posts;
		}

		$attachment_urls = array( wp_get_attachment_url( $attachment_id ) );

		if ( wp_attachment_is_image( $attachment_id ) ) {
			foreach ( get_intermediate_image_sizes() as $size ) {
				$intermediate = image_get_intermediate_size( $attachment_id, $size );
				if ( $intermediate ) {
					$attachment_urls[] = $intermediate['url'];
				}
			}
		}

		$used_in_content = array();

		foreach ( $attachment_urls as $attachment_url ) {
			$content_query = new WP_Query( array(
				's'              => $attachment_url,
				'post_type'      => 'any',	
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			) );

			$used_in_content = array_merge( $used_in_content, $content_query->posts );
		}

		$used_in_content = array_unique( $used_in_content );

		$posts = array(
			'thumbnail' => $used_as_thumbnail,
			'content'   => $used_in_content,
		);

		return $posts;

		
	}



	public function get_posts_using_attachment( $attachment_id, $context ) {

		$post_ids = $this->get_posts_by_attachment_id( $attachment_id );

		$posts = array_merge( $post_ids['thumbnail'], $post_ids['content'] );
		$posts = array_unique( $posts );

		switch ( $context ) {
			case 'column':
				$item_format   = '<strong>%1$s</strong>, %2$s %3$s<br />';
				$output_format = '%s';
				break;
			case 'details':
			default:
				$item_format   = '%1$s %3$s<br />';
				$output_format = '<div style="padding-top: 8px">%s</div>';
				break;
		}

		$output = '';

	

		foreach ( $posts as $post_id ) {
			$post = get_post( $post_id );
			if ( ! $post ) {
				continue;
			}

			$post_title = get_the_title( $post );
			$post_type  = get_post_type_object( $post->post_type );

			if ( $post_type && $post_type->show_ui && current_user_can( 'edit_post', $post_id ) ) {
				$link = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post_id ), $post_title );
			} else {
				$link = $post_title;
			}

			if ( in_array( $post_id, $post_ids['thumbnail'] ) && in_array( $post_id, $post_ids['content'] ) ) {
				
				$usage_context = __( '(as Featured Image and in content)', 'safe-media-delete' );
			} elseif ( in_array( $post_id, $post_ids['thumbnail'] ) ) {

				$usage_context = __( '(as Featured Image)', 'safe-media-delete' );

			} else {

				$usage_context = __( '(in content)', 'safe-media-delete' );

			}

			$output .= sprintf( $item_format, $link, get_the_time( __( 'Y/m/d', 'safe-media-delete' ) ), $usage_context );
		}

		$terms = get_terms( array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
		) );
		

		$term_vals = [];

		foreach( $terms as $term) {

			$term_vals[] = get_term_meta($term->term_id,'term_attached_image_id', true);
			
		}

		$term_vals_filter = array_filter($term_vals);

		if(is_array($term_vals_filter)) {

			$term_used = "";
					
			if(in_array($attachment_id,$term_vals_filter)) {

				$term_name = get_term( $term->term_id  )->name;
				$edit_link = esc_url( get_edit_term_link( $term->term_id ) );
				$term_used = "<a href='".$edit_link."'>".  __( 'Category of ', 'safe-media-delete') .$term_name."</a> ";
			} 

		
		}

		$output_format = [];

		foreach( $terms as $term) {

			$term_vals = get_term_meta($term->term_id,'term_attached_image_id', true);
			
		

			$term_used = "";
				
			if($term_vals == $attachment_id) {


				$term_name = get_term( $term->term_id  )->name;
				$edit_link = esc_url( get_edit_term_link( $term->term_id ) );
				$term_used = "<a href='".$edit_link."'>".$term_name .",</a>";
				$formatted_term = $term_used . ' '.get_the_time( __( 'Y/m/d', 'safe-media-delete' ) ).' '. __( '(as Term) ', 'safe-media-delete').'<br/';
			
				
				$output_format[] = $formatted_term.'<br/>';

				

			}

		}

		for ($x = 0; $x <= count($output_format); $x++) {

			$output .= $output_format[$x];

		}
	


		if ( ! $output ) {
			$output = __( '(Unused)', 'safe-media-delete' );
		}

		return $output;

	
	}

	public function attachment_fields_to_edit( $form_fields, $attachment ) {
		$form_fields['used_in'] = array(
			'label' => __( 'Attached Objects', 'safe-media-delete' ),
			'input' => 'html',
			'html'  => $this->get_posts_using_attachment( $attachment->ID, 'details' ),
		);

		return $form_fields;
	}

	public function manage_media_columns( $columns ) {
		$filtered_columns = array();

		foreach ( $columns as $key => $column ) {
			$filtered_columns[ $key ] = $column;

			if ( 'parent' === $key ) {
				$filtered_columns['used_in'] = __( 'Attached Objects', 'safe-media-delete' );
			}
		}

		return $filtered_columns;
	}

	public function manage_media_custom_column( $column_name, $attachment_id ) {
		switch ( $column_name ) {
			case 'used_in':
				echo $this->get_posts_using_attachment( $attachment_id, 'column' );
				break;
		}
	}

	public function test_true_phpunit() {
		return true;
	}
	

}

?>