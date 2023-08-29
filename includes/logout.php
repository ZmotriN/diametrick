<?php
unset($_SESSION['user_id']);
header('location: '.$this->root.'connexion');
die();