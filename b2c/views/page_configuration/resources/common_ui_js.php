<!--<script defer type="text/javascript" src="?php echo JQUERY_UI_LIBRARY_DIR; ?>jquery-ui.min.js"></script>
-->
<!-- Libraries Needed For Social Network Login Only -->
<?php
$GLOBALS['CI']->load->library('social_network/google');
echo $GLOBALS['CI']->google->load_library()?>
<!-- DATE AND TIME PICKER  -->
<!--<script defer src="?php echo DATEPICKER_LIBRARY_DIR;?>jquery.datetimepicker.js" async defer ></script>
<link defer rel="stylesheet" type="text/css" href="?php echo DATEPICKER_LIBRARY_DIR;?>jquery.datetimepicker.css" />
--><!-- DATE AND TIME PICKER TO WINDOW START -->