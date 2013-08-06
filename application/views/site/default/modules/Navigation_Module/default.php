<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->addCSS('menu.css', 'menu');
if (isset($menus) && $menus) {
	$level = 0;
	echo '<div class="nav">';
	foreach ($menus as $key => &$menu) {
		if ($menu->level > @$menus[$key - 1]->level)
			echo '<ul class="', ($key ? 'sub-' : ''), 'menu level', ++$level, '">';
		echo '<li><a href="', $menu->alias, ($menu->alias&&strpos($menu->alias, '://')===false?REWRITE_SUFFIX:''), '">', $menu->name, '</a>';
		if ($menu->level > @$menus[$key + 1]->level) {
			$level--;
			echo str_repeat('</li></ul>', $level);
			if ($level > 1) echo '</li>';
		} elseif ($menu->level == @$menus[$key + 1]->level) {
			echo '</li>';
		}
	}
	echo '</div>';
}