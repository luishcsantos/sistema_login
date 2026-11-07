<?php
// ======================================================
// sair.php - Faz logout e volta para o login
// ======================================================
require 'conexao.php';

// Apaga a sessão (isso desloga a pessoa)
session_destroy();

// Volta para a tela de login
header('Location: index.php');
exit;
