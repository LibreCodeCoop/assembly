<?php
use OCA\News\Plugin\Client\Plugin;

script('news', [
    'build/app.min'
]);

style('news', [
    'app',
    'content',
    'custom',
    'explore',
    'mobile',
    'navigation',
    'settings',
    'shortcuts'
]);

// load plugin scripts and styles
foreach (Plugin::getStyles() as $appName => $fileName) {
    style($appName, $fileName);
}
foreach (Plugin::getScripts() as $appName => $fileName) {
    script($appName, $fileName);
}
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
					<p>Acesse a sala para participar da assembléia</p>

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
					<p>Acesse a sala apara votar</p>

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
					<p>Acesse o resultado da votação</p>

					<div class="explore-logo">
						<img ng-src="{{ entry.image }}" ng-if="entry.image">
					</div>
				</div>
				<div class="explore-subscribe">
					<a class="button" href="https://nc.lt.coop.br/apps/assembly/img/resultado.png" target="_blank">Acessar</a>
				</div>
			</div>
		</div>
	</div>	
</div>