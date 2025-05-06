<?php
require_once 'funciones.php';

session_destroy();
redirect('login.php');
