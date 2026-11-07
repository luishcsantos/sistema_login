<?php
// ======================================================
// conexao.php - Conexão com o banco de dados e funções básicas
// Todas as páginas começam com: require 'conexao.php';
// ======================================================

// Inicia a sessão (é ela que guarda quem está logado)
session_start();

// Define os dados de acesso ao banco de dados
$servidor     = 'localhost';
$nomeBanco    = 'sistema_login';
$usuarioBanco = 'root';
$senhaBanco   = '123456';

// Tenta conectar ao banco; se der erro, mostra a mensagem e para
try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$nomeBanco;charset=utf8mb4", $usuarioBanco, $senhaBanco);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $excecao) {
    die('Erro ao conectar no banco: ' . $excecao->getMessage());
}

// Atenção: esta versão didática NÃO usa a função e() (htmlspecialchars)
// Sem ela, os textos digitados pelos usuários aparecem na tela exatamente como vieram
// Isso REMOVE a proteção contra HTML e JavaScript malicioso (ataque XSS)
// Em um sistema real, todo texto de usuário deve passar por htmlspecialchars() antes de aparecer na tela

// Verifica se existe pelo menos um administrador cadastrado
function existeAdmin($pdo) {
    $consulta = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'admin'");
    $total = $consulta->fetchColumn();
    return $total > 0;
}

// Verifica se o usuário logado é administrador
function eAdmin() {
    // Se não há ninguém logado, não é administrador
    if (!isset($_SESSION['usuario'])) {
        return false;
    }

    // Se o perfil é admin, é administrador
    if ($_SESSION['usuario']['perfil'] === 'admin') {
        return true;
    }

    // Qualquer outro perfil não é administrador
    return false;
}

// Redireciona para o login quem não está logado
function exigirLogin() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit;
    }
}

// Redireciona para o painel quem não é administrador
function exigirAdmin() {
    exigirLogin();
    if (!eAdmin()) {
        header('Location: painel.php');
        exit;
    }
}

// Mostra a mensagem de sucesso guardada na sessão e depois a apaga
function mostrarMensagem() {
    if (isset($_SESSION['msg'])) {
        echo '<div class="alerta sucesso">' . $_SESSION['msg'] . '</div>';
        unset($_SESSION['msg']);
    }
}
