<?php
script('assembly', 'script');
style('assembly', 'style');
?>

<div id="app" style="width: 100%;">

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('content/index')); ?>
		</div>
	</div>
</div>

