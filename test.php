<?php

include('includes/application.php');

Application::run('config', 'database');

Application::db()->getInstance()->setup();