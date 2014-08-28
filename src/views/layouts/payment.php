<?php
if (empty($content)) {
	$content = '';
}
?>
<!DOCTYPE html>
<html>
<head>
	<?= View::make('ff-qiwi-gate::layouts.inc.head') ?>
	<script type="application/javascript">
		<?php require(__DIR__ . '/../layouts/inc/js/ActionPayment.js') ?>
	</script>
</head>
<body>
<div><?= $content ?></div>
</body>
</html>
