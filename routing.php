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

Router::get("Users", "Users");
Router::post("Users", "Users");
Router::patch("Users", "Users");
Router::delete("Users", "Users");
Router::propfind("Users", "Users");

Router::get("FaFajok", "FaFajok");
Router::post("FaFajok", "FaFajok");
Router::delete("FaFajok", "FaFajok");
Router::patch("FaFajok", "FaFajok");

Router::get("FaKategoriak", "FaKategoriak");
Router::post("FaKategoriak", "FaKategoriak");
Router::delete("FaKategoriak", "FaKategoriak");
Router::patch("FaKategoriak", "FaKategoriak");

Router::get("Utcak", "Utcak");
Router::post("Utcak", "Utcak");
Router::delete("Utcak", "Utcak");
Router::patch("Utcak", "Utcak");

Router::get("UsersUtcak", "UsersUtcak");
Router::post("UsersUtcak", "UsersUtcak");
Router::delete("UsersUtcak", "UsersUtcak");

Router::get("FaRogzitesek", "FaRogzitesek");
Router::post("FaRogzitesek", "FaRogzitesek");
Router::delete("FaRogzitesek", "FaRogzitesek");
Router::patch("FaRogzitesek", "FaRogzitesek");