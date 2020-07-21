<?php

style('assembly','explore',);

?>
			
<div id="explore">
	<div class="grid">
		<div class="explore-feed grid-item" news-refresh-masonry>
			<div class="category-wrapper">
				<div class="category">
					Assembléia online
				</div>
			</div>
			<div class="grid-item-content">
				<div class="explore-content">
					<div class="explore-value">
						<div class="explore-subscribe">
							<!-- <a class="button" href="/index.php/apps/assembly/videocall/<?php //echo $group[0]; ?>" target="_blank">Acessar sala</a> -->
							<a class="button" href="https://meet.jit.si/<?php echo $group[0]; ?>" target="_blank">Acessar sala</a>
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
			<div class="grid-item-content">
				<?php
				if(!empty($data)){
					foreach ($data as $row) { ?>
						<div class="explore-content">
							<div class="explore-value">
								<div class="explore-subscribe">
									<a class="button" href="/index.php/apps/forms/<?php echo $row['hash'];?>" target="_blank"><?php echo $row['title'];?></a>
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

			<div class="grid-item-content">
				<?php
				if(!empty($data)){
					foreach ($data as $row) { ?>
						<div class="explore-content">
							<div class="explore-value">
								<div class="explore-subscribe">
									<a class="button" href="/index.php/apps/assembly/report/<?php echo $row['formId'];?>" target="_blank">Resultado <?php echo $row['title'];?></a>
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