<?php

function woptifications_push_js() {
	$notification_type_push = $woptifications_options['notification_type']['push'];

	if($notification_type_push == 1) {
?>

	<script type="text/javascript">

	document.addEventListener("DOMContentLoaded", function () {
	  if (Notification.permission !== "granted")
	    Notification.requestPermission();
	});

	</script>

<?php
	}
}
add_action('wp_footer', 'woptifications_push_js');

?>