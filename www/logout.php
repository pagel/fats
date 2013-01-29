<?php

require_once 'Helpers/SessionHelper.php';

\FATS\Helpers\SessionHelper::destroySession();

header('Location: /', true);

?>