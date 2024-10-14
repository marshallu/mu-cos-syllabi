<?php
/**
 * Plugin Name: MU CoS Syllabi
 * Plugin URI: http://www.marshall.edu/cos/
 * Description: College of Science Syllabi Plugin
 * Author: Nick Slate
 * Version: 1.2
 *
 * @package MU_CoS_Syllabi
 */

/**
 * Enqueue scripts and styles.
 */
function cos_syllabi_styles() {
	wp_register_style( 'syllabi_datatables_style', plugins_url( 'mu-cos-syllabi/css/datatables.min.css' ), array(), false, 'all' ); // phpcs:ignore
	wp_register_style( 'syllabi_style', plugins_url( 'mu-cos-syllabi/css/mu_cos_syllabi.css' ), array(), false, 'all' ); // phpcs:ignore
	wp_enqueue_style( 'syllabi_datatables_style' );
	wp_enqueue_style( 'syllabi_style' );
}

/**
 * Enqueue scripts .
 */
function cos_syllabi_scripts() {
	wp_enqueue_script( 'syllabi_datatables', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array( 'jquery' ), false, true ); // phpcs:ignore
	wp_enqueue_script( 'syllabi_js', plugins_url( 'mu_cos_syllabi/js/mu_cos_syllabi.js' ), array( 'jquery' ), false, true ); // phpcs:ignore
}

add_action( 'wp_enqueue_scripts', 'cos_syllabi_styles' );
add_action( 'wp_enqueue_scripts', 'cos_syllabi_scripts' );


/**
 * Shortcode to display the syllabi table.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function cos_syllabi( $atts ) {
	$atts = shortcode_atts(
		array(
			'dept' => '',
		),
		$atts
	);

	$dept_param = $atts['dept'];
	$dept_param = trim( strtoupper( $dept_param ) );
	if ( strlen( $dept_param ) <= 0 || 'A-Z' === $dept_param ) {
		$dept_param = '';
	}

	$result        = '';
	$course_list   = '';
	$course_script = '';

	$course_list .= '<div class="cos_syllabi"><table id="cos_syllabi" class="cos_syllabi display compact" style="width:100%">';
	$course_list .= '<thead><tr><th>Semester &amp; Year</th><th width="20%">Course &amp; Section</th><th width="40%">Title</th><th>Hours</th><th>Instructor(s)</th><th>Collection</th></tr></thead>';
	$course_list .= '<tbody>';
	$course_list .= '</tbody>';
	$course_list .= '</table></div>';

	$course_script .= '<script type="text/javascript">';
	$course_script .= 'var mu_cos_syllabiDept = ' . ( $dept_param ? '\'' . $dept_param . '\'' : 'null' ) . ';';
	$course_script .= '</script>';
	$course_script .= '<noscript><p>This listing of courses and their syllabi can be very large. JavaScript is used to collect, display, sort, and search the listing. Please enable scripts and refresh the page.</p></noscript>';

	$result = $course_list . $course_script;

	return $result;
}

add_shortcode( 'mu_cos_syllabi', 'cos_syllabi' );
