<?php

style('assembly', 'result');
?>

<div id="result">	
	<div class="result-feed grid-item" news-refresh-masonry>		
		<div class="category-wrapper">
			<div class="category">
				Resultado <?php echo $metadata['title'];?>
			</div>
		</div>
		<div class="grid">
			<?php
			foreach ($responses as $key => $value) {?>
				<div class="grid-item-content">
					<div class="result-content">
						<h1><?php echo $key;?></h1>
						<div class="result-value">
							<h3><?php echo $value;?></h3>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>
		<div class="grid">
			<div class="grid-item-content-half">
				<div class="result-content">
					<h1>Total de votos</h1>
					<div class="result-value">
						<h3><?php echo $metadata['total'];?></h3>
					</div>
				</div>
			</div>			
			<div class="grid-item-content-half">
				<div class="result-content">
					<h1>Aptos a votar</h1>
					<div class="result-value">
						<h3><?php echo $metadata['available'];?></h3>
					</div>
				</div>
			</div>	
		</div>			
	</div>
</div>