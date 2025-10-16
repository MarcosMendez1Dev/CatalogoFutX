<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    return htmlspecialchars($html ?? '');
}

// Comprobar si es admin
function isAdmin() {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== "1") {
        header('Location: /login');
        exit;
    }
}
