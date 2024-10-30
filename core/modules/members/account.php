<?php 

namespace Membership\Core\Modules\Members;

if ( ! defined( 'ABSPATH' ) ) exit;

use Membership\Core\Models\Members;
use Membership\Utils\Helper;
use Membership\Utils\Singleton;
use Membership\Core\Models\Plans as PlanModel;
use Membership\Core\Modules\Members\Emails\Send_Email;

class Account{

    use Singleton;

    /**
     * Call hooks
     */
    public function init() {
        add_action( 'init', array($this,'add_new_endpoint') );
        add_filter( 'user_registration_account_menu_items', array($this,'ur_custom_menu_items'), 10, 1 );
        add_filter( 'woocommerce_account_menu_items', array($this,'ur_custom_menu_items'), 10, 1 );
        add_action( 'user_registration_create-members-details_endpoint_content', array($this,'membership_content') );
        add_action( 'woocommerce_account_create-members-details_endpoint', array($this,'membership_content') );
        add_action('wp_login', array($this,'check_membership_limit'),10,2 );
    }

    public function check_membership_limit($user_login, $user) {
        if (empty($user)) {
           return;
        }
        return self::get_plan_details( $user->ID );
    }

    /**
     * Summary of member and subscription details
     * @param mixed $id
     * @return mixed
     */
    public function get_plan_details($id = null ) {
        if (empty($id)) {
            $user_id        = get_current_user_id(); 
        }else{
            $user_id        = $id; 
        }
        $plan_details   = get_user_meta($user_id, MEM_PLAN_DETAILS ,true);
        $plan_details   = PlanModel::instance()->plan_expire_status($plan_details);
        if (!empty($plan_details['member_id'])) {
            $member_details = Members::instance()->get_single_data($plan_details['member_id']);
            return array_merge($plan_details,$member_details);
        }else{
            return $plan_details;
        }

    }

    public function ur_custom_menu_items( $items ) {
        if (  empty($this->get_plan_details()) ) {
            return $items;
        }
        $new_items = array();
        foreach( $items as $key => $item ){
            if( 'customer-logout' == $key )
                $new_items['create-members-details'] = esc_html__( 'Membership', 'create-members' );
            $new_items[$key] = $item;
        }

        return $new_items;
    }

    public function add_new_endpoint() {
        add_rewrite_endpoint( 'create-members-details', EP_PAGES );
        $this->cancel_membership();
    }

    /**
     * Cancel Membership
     */
    public function cancel_membership() {
        if (empty($_POST) || empty($_POST['cancel_membership'])) {
            return;
        }

        Helper::instance()->verify_nonce('um-account-nonce', sanitize_key($_POST['um-account-nonce']) );
        $cancel = sanitize_key($_POST['cancel_membership']);
        if ($cancel == 'cancel-membership' && !empty(get_current_user_id()) ) {
            $user_id        = get_current_user_id(); 
            $plan_details   = get_user_meta( $user_id, MEM_PLAN_DETAILS , true );
            if ( !empty($plan_details) ) {
                $member_id = $plan_details['member_id'];
                update_post_meta( $member_id , 'status' , MEMBER_CANCEL_STATUS );
                update_user_meta( $member_id , NEW_MEM_MAIL_SENT , '' );
                $this->send_cancel_email( $user_id , $plan_details['plan_id'] );
                wp_redirect('/my-account');
            }

        }
    }

    public function membership_content() {
        Helper::um_get_template( 'modules_dir','members/views/plan-details.php' );
    }

    /**
     * Send Membership cancellation email
     *
     * @param [type] $user_id
     * @param [type] $plan_id
     */
    private function send_cancel_email($user_id , $plan_id ) {
        extract(Helper::get_settings());

        $recipient = get_userdata($user_id)->user_email;
		$args = array(
			'data'      => array('mail_type'=>'cancel_membership'),
			'recipient' => $recipient,
			'subject'   => $cancel_subject,
			'title'     => $cancel_title,
			'message'   => self::cancel_member_mail_body( $plan_id , $cancel_message ),
		);

		$obj = new Send_Email( $args  );

		return $obj->send();

    }

    /**
     * Send Membership cancellation email body
     * @param mixed $plan_id
     * @param mixed $cancel_message
     * @return string
     */
    public static function cancel_member_mail_body($plan_id , $cancel_message) {
        $plan_name = get_post_meta(  $plan_id , 'plan_name' , true );
        $default_body = '<p class="email-p">
			'.esc_html__('You have cancelled membership from','create-members').' '.$plan_name.' '.
            esc_html__('Plan.','create-members').'
		</p>';

		$mail_body = $cancel_message !== '' ? $cancel_message : $default_body;
        $mail_body = Helper::add_template_tags($plan_name,$mail_body);
		return '<h2 class="email-title">'.esc_html__('Membership Cancellation ','create-members') .'</h2>
        '.$mail_body.'';
	}

}

