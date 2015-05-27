<?php

/*

* start a server in my parent dir

	$ cd .../app
	$ php -S 127.0.0.1:8080

* run this file in a browser

	http://127.0.0.1:8080/combo.php

*/

$loader = require './../vendor/autoload.php';

use Acme\Html\Component\Select;

$select = new Select('test-select');
$select
		->addOption('opt-1', 'Option 1')
		->addOption('opt-2', 'Option 2', true)
		->addOption('opt-3', 'Option 3');
?>

<html>
	<body>
		<?php echo $select; ?>
	</body>
</html>