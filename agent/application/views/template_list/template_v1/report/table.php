<html>
	<head></head>
	<body>
		<table border="1px" cellspacing="0" cellpadding="3px">
			<?php if(valid_array($head)): ?>
			<thead>
				<tr>
					<th>#</th>
				<?php foreach($head as $hk=>$hv): ?>
					<th><?php echo $hv; ?></th>
				<?php endforeach; ?>
				</tr>
			</thead>
			<?php endif; ?>
			<tbody>
			<?php if(valid_array($body)): ?>
				<?php foreach($body as $bk=>$bv): ?>
				<tr>
					<td><?php echo $bk + 1 ; ?></td>
					<?php foreach($bv as $v): ?>
					<td><?php echo $v; ?></td>
					<?php endforeach; ?>				
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</body>
</html>