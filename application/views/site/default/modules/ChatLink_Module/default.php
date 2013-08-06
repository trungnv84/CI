<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="chat_link">
	<?php for($i=0; $i<3; $i++):
		$nick = 'yahoo' . $i;
		if(!$params->$nick) continue;
		?>
	<a href="ymsgr:sendIM?<?php echo $params->$nick;?>">
		<img src='http://opi.yahoo.com/online?u=<?php echo $params->$nick;?>&m=g&t=1'/>
	</a>
	<?php endfor;?>
</div>