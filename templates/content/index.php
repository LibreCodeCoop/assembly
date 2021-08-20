<?php

style('assembly','explore');
script("assembly", "dashboard.css#v1.0");
?>

<div id="explore">
	<div class="grid">
		<div class="explore-feed grid-item" news-refresh-masonry>
			<div class="category-wrapper">
				<div class="category">
					Assembleia online
				</div>
			</div>
			<div class="grid-item-content">
				<div class="explore-content">
					<div class="explore-value">
						<div class="explore-subscribe">
							<a class="button" id="btn-meet-link" href="<?php echo $meetUrl; ?>" target="_blank">Acessar sala</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="explore-feed grid-item" news-refresh-masonry>
			<div class="category-wrapper">
				<div class="category">
					Votações
				</div>
			</div>
			<div class="grid-item-content" id="grid-content-forms">
				<?php
				if(!empty($data)){
					foreach ($data as $row) { ?>
						<div class="explore-content">
							<div class="explore-value">
								<div class="explore-subscribe">
									<?php
									if (!empty($row['submission'])) {
										?>Voto registrado<?php
									} else {
										?><a class="button" href="<?php echo $row['vote_url'];?>" target="_blank"><?php echo $row['title'];?></a><?php
									}
									?>
								</div>
							</div>
						</div>
				<?php }
				}else{ ?>
					<div class="explore-content">
						<h3>Nenhuma votação aberta ainda</h3>
					</div>
				<?php
				} ?>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="explore-feed grid-item" news-refresh-masonry>
			<div class="category-wrapper">
				<div class="category">
					Resultados
				</div>
			</div>

			<div class="grid-item-content" id="grid-content-result">
				<?php
				if(!empty($data)){
					foreach ($data as $row) { ?>
						<div class="explore-content">
							<div class="explore-value">
								<div class="explore-subscribe">

								<?php
									if (!empty($row['submission'])) {
										?><a class="button" href="<?php echo $row['result_url'];?>" target="_blank"><?php echo $row['title'];?></a><?php
									} else {
										?>Vote para visualizar a apuração<?php
									}
									?>
								</div>
							</div>
						</div>
				<?php }
				}else{ ?>
					<div class="explore-content">
						<h3>Nenhuma votação aberta ainda</h3>
					</div>
				<?php
				} ?>
			</div>

		</div>
	</div>
</div>
