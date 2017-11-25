<?php
Router::get("Main", "Main");

Router::get("Authentication", "Authentication");
Router::post("Authentication", "Authentication");
Router::delete("Authentication", "Authentication");

Router::get("Version", "Version");
Router::post("Version", "Version");
Router::delete("Version", "Version");

Router::get("Felevek", "Felevek");
Router::post("Felevek", "Felevek");
Router::delete("Felevek", "Felevek");
Router::patch("Felevek", "Felevek");


Router::post("Users", "Users");
Router::delete("Users", "Users");