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
					<h3>Acesse a sala para participar da assembléia</h3>

					<div class="explore-logo">
						<img ng-src="{{ entry.image }}" ng-if="entry.image">
					</div>
					<div class="explore-subscribe">
						<a class="button" href="https://meet.jit.si/AGO_OCB_20200626" target="_blank">Acessar</a>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="grid">
		<div class="explore-feed grid-item" news-refresh-masonry>
			<div class="category-wrapper">
				<div class="category">
					Sala de votação
				</div>
			</div>
			<div class="grid-item-content">
				<div style="clear:both"></div>
				<div class="explore-content">
					<h3>Acesse a sala para votar</h3>

					<div class="explore-logo">
						<img ng-src="{{ entry.image }}" ng-if="entry.image">
					</div>					
					<div class="explore-subscribe">
						<a class="button" href="https://nc.lt.coop.br/apps/forms/QBRyMCCQ58JJdJZS" target="_blank">Acessar</a>
					</div>
				</div>
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
</div>
