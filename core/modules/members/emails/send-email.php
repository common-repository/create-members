<?php

namespace Membership\Core\Modules\Members\Emails;

defined( 'ABSPATH' ) || exit;

use Membership\Base\Email;
use Membership\Utils\Singleton;
use CreateMembers;

class Send_Email extends Email {

	use Singleton;

	public $data;
	private $subject   = '';
	private $recipient = '';
	private $title     = '';
	private $message   = '';

	/**
	 * Send Email to all Members
	 */
	public function __construct( $args ) {
		$this->data      = $args['data'];
		$this->subject   = $args['subject'];
		$this->recipient = $args['recipient'];
		$this->title     = $args['title'];
		$this->message   = $args['message'];

		parent::__construct();
	}

	/**
	 * Get email recipient
	 *
	 * @return string
	 */
	public function get_recipient() {
		return $this->recipient;
	}

	/**
	 * Get new event email
	 *
	 * @return string
	 */
	public function get_subject() {
		return $this->subject;
	}

	/**
	 * Get email title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get template new event email
	 */
	public function get_template() {
		if ( file_exists( CreateMembers::modules_dir() . 'members/views/emails/all-members.php' ) ) {
			$member  = $this->data;
			$message = wpautop( html_entity_decode( $this->message ) );
			include CreateMembers::modules_dir() . 'members/views/emails/all-members.php';
		}
	}
}
