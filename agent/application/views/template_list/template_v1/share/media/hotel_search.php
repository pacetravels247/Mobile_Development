<div class="hide">
	<audio id="landing-load">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('ogg/landing-load.ogg')?>" type="audio/ogg">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('mp3/landing-load.mp3')?>" type="audio/mpeg">
	</audio>
	<audio id="pre-load">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('ogg/hotel-pre-load.ogg')?>" type="audio/ogg">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('mp3/hotel-pre-load.mp3')?>" type="audio/mpeg">
	</audio>
	<audio id="post-load">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('ogg/hotel-post-load.ogg')?>" type="audio/ogg">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('mp3/hotel-post-load.mp3')?>" type="audio/mpeg">
	</audio>
	<audio id="empty-load">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('ogg/all-empty-result.ogg')?>" type="audio/ogg">
		<source src="<?=$GLOBALS['CI']->template->template_audio_dir('mp3/all-empty-result.mp3')?>" type="audio/mpeg">
	</audio>
</div>
<?php
echo $GLOBALS['CI']->template->isolated_view('share/media/audio_script');