<article class="entry">
	<header class="entry-header">
		<h1 class="entry-title"><?php echo $Entry->title; ?></h1>
		<p class="entry-subtitle">
			By <span class="author"><?php echo $Entry->author; ?></span>,
			on <span class="date"><?php echo $Html->time( $Entry->creation ); ?></span>
		</p>
	</header>

	<div class="entry-body">
		<?php echo $Entry->body; ?>
	</div>
</article>
