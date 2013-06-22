<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">
(function($) {

	$(function() {
		$.get("index.php").done(function(data) {
			alert(data);
		});
	});

})(jQuery);
</script>
