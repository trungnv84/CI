<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->loadModule(array('name' => 'Breadcrumb_Module', 'title' => null, 'params' => null));?>
<div class="articles">
<?php foreach($articles as $article) { ?>
	<div class="article">
		<?php if($article->images):?>
		<div class="article-image">
			<img src="thumb_124_122/<?php echo $article->images?>"/>
		</div>
		<?php endif;?>
		<h2 class="article-title">
			<a href="<?php echo $article->alias, '-a', $article->id, REWRITE_SUFFIX?>">
				<?php echo $article->title?>
			</a>
		</h2>
		<p class="article-intro">
			<?php echo $article->intro?>
		</p>
	</div>
<?php }?>
</div>
<div class="pagination">
	<?php if(isset($pagination))echo $pagination?>
</div>