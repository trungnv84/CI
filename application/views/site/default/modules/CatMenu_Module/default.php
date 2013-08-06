<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$theme->addCSS('menu.css', 'menu');
echo '<h3 class="mod-title">', $title, '</h3>';
if (isset($menus) && $menus) {
	$level = 0;
	echo '<div class="ver">';
	foreach ($menus as $key => &$menu) {
		if ($menu->level > @$menus[$key - 1]->level)
			echo '<ul class="', ($key ? 'sub-' : ''), 'menu level', ++$level, '">';
		echo '<li><a href="', $menu->alias, '-', Category::$menuPrefix[$menu->section_id], $menu->id, REWRITE_SUFFIX, '"><i></i>', $menu->name, '</a>';
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