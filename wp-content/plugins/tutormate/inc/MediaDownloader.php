<?php
namespace TUTORMATE;

defined( 'ABSPATH' ) || exit;

/**
 * Class MediaDownloader
 * @since 1.0.4
 */
class MediaDownloader {

	private $posts;
	private $json_file = 'tutor_import.json';

	public function __construct() {
		$this->get_file_posts();

		add_action( 'import_data_parsed', array( $this, 'on_import_data_parsed' ) );

		add_action( 'wp_ajax_tutormate_attachments', array( $this, 'tutormate_attachments' ) );
		add_action( 'wp_ajax_tutormate_download_attachment', array( $this, 'download_attachment' ) );
	}

	/**
	 * Call back for import_data_parsed action
	 *
	 * @param array $parsed_data
	 * @return void
	 */
	public function on_import_data_parsed( $parsed_data ) {
		$fp = fopen( trailingslashit( WP_CONTENT_DIR ) . DIRECTORY_SEPARATOR . $this->json_file, 'wb' );
		fwrite( $fp, json_encode( $parsed_data ) );
		fclose( $fp );
	}

	/**
	 * Get parsed json data from file
	 *
	 * @return void
	 */
	public function get_file_posts() {
		$path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->json_file;

		if ( file_exists( $path ) ) {
			$this->posts = json_decode( file_get_contents( $path ), true );
		} else {
			$this->posts = array();
		}
	}

	/**
	 * Filter data by post type
	 *
	 * @param string $post_type
	 * @return array
	 */
	public function filter_by_post_type( $post_type ) {
		$filtered = array();
		foreach ( $this->posts as $post ) {
			$obj = (object) $post['data'];
			if ( $obj->post_type === $post_type ) {
				$filtered[] = $post;
			}
		}

		return $filtered;
	}

	/**
	 * Get all attachment post type data
	 *
	 * @return void
	 */
	public function tutormate_attachments() {
		Helpers::verify_ajax_call();

		$data = $this->filter_by_post_type( 'attachment' );
		wp_send_json(
			array(
				'success' => true,
				'data'    => $data,
			)
		);
		exit;
	}

	/**
	 * Find post by post ID
	 *
	 * @param int|string $post_id
	 * @return null|array
	 */
	public function find( $post_id ) {
		$filtered = null;
		foreach ( $this->posts as $post ) {
			$obj = (object) $post['data'];
			if ( $obj->post_id === (string) $post_id ) {
				$filtered = $post;
				break;
			}
		}

		return $filtered;
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array  $post       Attachment post details from WXR.
	 * @param array  $meta       Attachment post meta details.
	 * @param string $remote_url URL to fetch attachment from.
	 *
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	protected function process_attachment( $post, $meta, $remote_url ) {
		// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
		// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
		$post['upload_date'] = $post['post_date'];
		foreach ( $meta as $meta_item ) {
			if ( $meta_item['key'] !== '_wp_attached_file' ) {
				continue;
			}

			if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta_item['value'], $matches ) ) {
				$post['upload_date'] = $matches[0];
			}
			break;
		}

		$upload = $this->fetch_remote_file( $remote_url, $post );
		if ( is_wp_error( $upload ) ) {
			return $upload;
		}

		$info = wp_check_filetype( $upload['file'] );
		if ( ! $info ) {
			return new \WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'wordpress-importer' ) );
		}

		$post['post_mime_type'] = $info['type'];

		// insert attachment post
		$id = (int) $post['post_id'];
		if ( is_object( get_post( $id ) ) ) {
			// already post exist update by ID
			$post['ID'] = $id;
		} else {
			// if not in database, add post with import_id as ID
			$post['import_id'] = $id;
		}

		$post_id = wp_insert_attachment( $post, $upload['file'] );
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$attachment_metadata = wp_generate_attachment_metadata( $post_id, $upload['file'] );
		wp_update_attachment_metadata( $post_id, $attachment_metadata );

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment.
	 *
	 * @param string $url  URL of item to fetch.
	 * @param array  $post Attachment details.
	 *
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	protected function fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] ) {
			return new \WP_Error( 'upload_dir_error', $upload['error'] );
		}

		// fetch the remote url and write it to the placeholder file
		$response = wp_remote_get(
			$url,
			array(
				'stream'   => true,
				'filename' => $upload['file'],
			)
		);

		// request failed
		if ( is_wp_error( $response ) ) {
			unlink( $upload['file'] );
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );

		// make sure the fetch was successful
		if ( $code !== 200 ) {
			unlink( $upload['file'] );
			return new \WP_Error(
				'import_file_error',
				sprintf(
					__( 'Remote server returned %1$d %2$s for %3$s', 'wordpress-importer' ),
					$code,
					get_status_header_desc( $code ),
					$url
				)
			);
		}

		$filesize = filesize( $upload['file'] );
		$headers  = wp_remote_retrieve_headers( $response );

		if ( 0 === $filesize ) {
			unlink( $upload['file'] );
			return new \WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'wordpress-importer' ) );
		}

		$max_size = 0; // 0 unlimited
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			unlink( $upload['file'] );
			$message = sprintf( __( 'Remote file is too large, limit is %s', 'wordpress-importer' ), size_format( $max_size ) );
			return new \WP_Error( 'import_file_error', $message );
		}

		return $upload;
	}

	public function update_featured_image_meta() {
		// add featured image for each post
		foreach ( $this->posts as $post ) {
			$data = $post['data'];
			$meta = $post['meta'];
			foreach ( $meta as $meta_item ) {
				if ( $meta_item['key'] === '_thumbnail_id' ) {
					$attachment_id = $meta_item['value'];
					$post_id       = $data['post_id'];
					update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
				}
			}
		}

		wp_send_json(
			array(
				'success' => true,
				'message' => 'featured image _thumbnail_id updated',
			)
		);
		exit;
	}

	/**
	 * Download single attachment by attachment ID
	 *
	 * @return void
	 */
	public function download_attachment() {
		Helpers::verify_ajax_call();

		if ( isset( $_GET['update_meta'] ) ) {
			$this->update_featured_image_meta();
		}

		$media_id = $_GET['media_id'] ?? 0;
		$media_id = sanitize_text_field( $media_id );

		// media will have 'data' and 'meta' key
		$media = $this->find( $media_id );

		if ( null === $media ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => 'media id not found',
				)
			);
			exit;
		}

		// download the media

		$data       = $media['data'];
		$meta       = $media['meta'];
		$remote_url = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];

		$data['import_id']   = $media_id;
		$data['post_author'] = get_current_user_id();
		$attachment_id       = $this->process_attachment( $data, $meta, $remote_url );

		if ( is_wp_error( $attachment_id ) ) {
			tutor_log( 'Error on: ' . $remote_url );
			tutor_log( $attachment_id->get_error_message() );
		}

		wp_send_json(
			array(
				'success' => true,
				'message' => 'downloaded',
			)
		);
		exit;
	}
}
