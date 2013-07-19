<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/1.0.rc.2/handlebars.js"></script>
<script type="text/javascript">
(function($) {

	$(function() {
		$.getJSON("index.php").done(function(data) {
			var show = function(name, message) {
				var template = Handlebars.compile($("#show-template").html());

				$("body").append(template({
					name: name,
					message: message
				}));
			};

			show('Error type ', data.error.type);
			show('Error message ', data.error.message);
			show('Error file ', data.error.file);
			show('Error line ', data.error.line);

			show('all json', JSON.stringify(data));
		});
	});

})(jQuery);
</script>
<script id="show-template" type="text/x-handlebars-template">
	<p>
		<strong>{{ name }}</strong> =
		{{ message }}
	</p>
</script>
