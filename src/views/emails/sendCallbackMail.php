<?php
/**
 * @var string $bill_id
 * @var string $textStatus
 */
?>
<html>
<head>
	<title>Изменения в счёте</title>
</head>
<body>
<p>
	Счёт № <?= HTML::entities($bill_id) ?> <?= HTML::entities($textStatus) ?>
</p>
</body>
</html>