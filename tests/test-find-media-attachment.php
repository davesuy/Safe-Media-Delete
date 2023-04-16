<?php

class Test_Find_Media_Attachment extends WP_UnitTestCase {

	
	public function test_construct() {

		$class_find_media_attachment = new Find_Media_Attachment;

		$has_filter = has_filter('attachment_fields_to_edit', [$class_find_media_attachment, 'attachment_fields_to_edit']);
		$has_filter = (10 == $has_filter);

		$this->assertTrue($has_filter);

	}

	public function test_get_posts_by_attachment_id() {

		$attachment_id = 15040;
		$context = 'column';

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
		$this->assertIsArray( $posts );

	}


	public function test_get_posts_using_attachment() {

		
		$class_find_media_attachment = new Find_Media_Attachment;

		$attachment_id = 15040;
		$context = "column";

		
		$post_ids = $class_find_media_attachment->get_posts_by_attachment_id( $attachment_id );

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


		$this->assertIsString($output);

	}

	public function test_manage_media_columns() {

		$has_filter = has_filter('manage_media_columns');
	
		$this->assertTrue($has_filter);

	}


}
