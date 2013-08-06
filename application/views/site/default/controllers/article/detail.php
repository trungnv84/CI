<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->loadModule(array('name' => 'Breadcrumb_Module', 'title' => null, 'params' => null));
//$CI =& get_instance();
?>
<div id="article-detail">
	<h2 class="article-title"><?php echo $article->title?></h2>
	<p class="article-content">
		<?php echo $article->content?>
		<div style="clear: both"></div>
	</p>
</div>