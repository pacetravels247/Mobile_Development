<script>
/**
 * Play Audio
 */

function post_load_audio()
{
	pause_audio('pre-load');
	pause_audio('landing-load');
	if ($('.r-r-i').length > 0) {
		play_audio('post-load');
	} else {
		play_audio('empty-load');
	}
}

function pre_load_audio()
{
	play_audio('landing-load');
	document.getElementById('landing-load').addEventListener("ended", function() {
		play_audio('pre-load');
	});
}


function play_audio(element_id)
{
	document.getElementById(element_id).play();
}
/**
 * Pause Audio
 */
function pause_audio(element_id)
{
	document.getElementById(element_id).pause();
}

</script>