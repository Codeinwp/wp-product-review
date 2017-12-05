<!DOCTYPE html>
<html>
<head>
	<!-- Disable browser caching of dialog window -->
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="stylesheet" href="<?php echo WPPR_URL . 'includes/admin/layouts/css/tinymce.css?h=' . date( 'dmYHis' ); ?>" type="text/css" media="all" />
</head>
<body>
	<form name="wppr_shortcode_form" id="wppr_shortcode_form">
<?php
if ( $fields ) {
	foreach ( $fields as $field ) {
		$this->add_element( $field );
	}
}
?>
	</form>
</body>
