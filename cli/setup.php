<?php

include(dirname(__DIR__) . '/includes/application.php');

Application::run('config', 'database');

Application::db()->getInstance()->setup();
