<?php
script('assembly', [
    'build/app.min'
]);

style('assembly', [
    'app',
    'content',
    'custom',
    'explore',
    'mobile',
    'navigation',
    'settings',
    'shortcuts'
]);

?>
			
<div id="explore">
	<div class="grid">
		<div class="explore-feed grid-item" news-refresh-masonry>
			<div class="category-wrapper">
				<div class="category">
					Sala assembléia online
				</div>
			</div>
			<div class="grid-item-content">
				<div style="clear:both"></div>
				<div class="explore-content">
					<h3>Acesse para participar da assembléia</h3>

					<div class="explore-logo">
						<img ng-src="{{ entry.image }}" ng-if="entry.image">
					</div>
					<div class="explore-subscribe">
						<a class="button" href="https://tavola.lt.coop.br/apps/external/1" target="_blank">Acessar</a>
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
					Resultado da votação
				</div>
			</div>
			<div class="grid-item-content">
				<div style="clear:both"></div>
				<div class="explore-content">
					<h3>Acesse o resultado da votação</h3>

					<div class="explore-logo">
						<img ng-src="{{ entry.image }}" ng-if="entry.image">
					</div>
				</div>
				<div class="explore-subscribe">
					<a class="button" href="/index.php/apps/assembly/report" target="_blank">Acessar</a>
				</div>
			</div>
		</div>
	</div>	
<?php

?>
</div>