<?php

namespace Membership\Base;

use Membership\Utils\Helper;
use CreateMembers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Email
 */
abstract class Email {
	/**
	 * Constructor for core email class
	 *
	 * @return  void
	 */
	public function __construct() {
		add_action( 'membership-email-header', array( $this, 'add_email_header' ) );
		add_action( 'membership-email-footer', array( $this, 'add_email_footer' ) );
		add_action( 'membership-email-body', array( $this, 'add_email_body' ) );
	}

	/**
	 * Get email subject
	 *
	 * @return  string
	 */
	abstract public function get_subject();

	/**
	 * Get email html template
	 *
	 * @return string
	 */
	abstract public function get_template();

	/**
	 * Get email recipient
	 *
	 * @return string
	 */
	abstract public function get_recipient();

	/**
	 * Get email header
	 */
	public function get_headers() {
		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . ' <' . get_option( 'admin_email' ) . '>' );

		return $headers;
	}

	/**
	 * Add email header
	 *
	 * @return void
	 */
	public function add_email_header() {
		if ( file_exists( CreateMembers::template_dir() . '/emails/header.php' ) ) {
			include CreateMembers::template_dir() . '/emails/header.php';
		}
	}

	/**
	 * Add email body
	 */
	public function add_email_body() {
		$template = $this->get_template();

		if ( ! empty( $template ) && file_exists( $template ) ) {
			ob_start();
			include $template;
			$body = ob_get_clean();
			echo Helper::kses( $body );
		}
	}
	/**
	 * Add email footer
	 *
	 * @return  void
	 */
	public function add_email_footer() {
		if ( file_exists( CreateMembers::template_dir() . '/emails/footer.php' ) ) {
			include CreateMembers::template_dir() . '/emails/footer.php';
		}
	}

	/**
	 * Get email content
	 *
	 * @return string
	 */
	public function get_template_content() {
		ob_start();
		$this->add_email_body();
		$template = ob_get_clean();

		return $template;
	}

	/**
	 * Send email using email template
	 *
	 * @return  bool
	 */
	public function send() {
		$headers = $this->get_headers();
		$subject = $this->get_subject();
		$to      = $this->get_recipient();
		$message = $this->get_template_content();

		return wp_mail( $to, $subject, $message, $headers );
	}
}
