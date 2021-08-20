<?php
script('assembly', 'report');
style('assembly', 'result.css#v1.0');
?>

<div id="result">
	<div class="result-feed grid-item" news-refresh-masonry>
		<div class="category-wrapper">
			<div class="category">
				Resultado <?php echo $metadata['title'];?>
				<div class="time-warn">Os resultados são atualizados automaticamente</div>
			</div>

		</div>
		<div class="grid" id="gridVotes">
			<?php
			foreach ($responses as $response) {?>
				<div class="grid-item-content">
					<div class="result-content">
						<h1><?php echo $response['text'];?></h1>
						<div class="result-value">
							<h3><?php echo $response['total'];?></h3>
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
					<div class="result-value" id="total">
						<h3><?php echo $metadata['total'];?></h3>
					</div>
				</div>
			</div>
			<div class="grid-item-content-half">
				<div class="result-content">
					<h1>Total de presentes</h1>
					<div class="result-value" id="aptos">
						<h3><?php echo $metadata['available'];?></h3>
						<p>Total de pessoas que fizeram acesso ao távola após o início da assembleia</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
