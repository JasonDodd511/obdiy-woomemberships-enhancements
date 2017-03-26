<?php

/*
Plugin Name: OBDIY WooMemberships Enhancements
Plugin URI: https://github.com/JasonDodd511/obdiy-woomemberships-enhancements
Description: Plugin to house WooMemberships Enhancements
Version: 1.0
Author: Jason Dodd
Author URI: https://cambent.com
License: GPL2
GitHub Plugin URI: https://github.com/JasonDodd511/obdiy-woomemberships-enhancements
GitHub Branch:     master
GitHub Languages:
*/

/**
 * Hide content from members who own specific memberships
 *
 * Used to hide content from members who have access to a
 * particular membership.
 *
 * use:[wcm_hide plans="membership1-slug,membership2-slug"]content to hide goes here[/wcm_hide]
 * Note: you can add multiple memberships separated by a comma
 */
function wcmHide($atts, $content = null ) {

	extract(shortcode_atts(array(
		'plans' => null,
	), $atts));

	$plans = explode(",",$atts['plans']);
	$has_access = false;

	foreach($plans as $plan){
		if(wc_memberships_is_user_active_member($user_id, $plan)) {$has_access = true;}
	}

	if(!$has_access){
		return do_shortcode($content);
	}
	return '';
}

add_shortcode( 'wcm_hide' , wcmHide );


/**
 * Programmatically add all registered users to Basic membership
 *
 */
function fmpm_wc_memberships_user_membership_at_registration( $user_id ) {

	// bail if Memberships isn't active
	if ( ! function_exists( 'wc_memberships' ) ) {
		return;
	}

	$args = array(
		// Enter the ID (post ID) of the plan to grant at registration
		'plan_id'       => 1344,
		'user_id'       => $user_id,
	);

	// magic!
	wc_memberships_create_user_membership( $args );

	// Get the new membership and add a note so we know how this was registered.
	$user_membership = wc_memberships_get_user_membership( $user_id, $args['plan_id'] );
	$user_membership->add_note( 'Membership access granted automatically from registration.' );

}
add_action( 'user_register', 'fmpm_wc_memberships_user_membership_at_registration', 20 );