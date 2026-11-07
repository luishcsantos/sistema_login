<?php
// ======================================================
// index.php - Login e cadastro do primeiro administrador
// Se não existe administrador: mostra o cadastro do admin
// Se já existe: mostra a tela de login
// ======================================================
require 'conexao.php';

// Redireciona para o painel quem já está logado
if (isset($_SESSION['usuario'])) {
    header('Location: painel.php');
    exit;
}

// Pergunta ao banco se já existe administrador
$temAdmin = existeAdmin($pdo);

// Guarda a mensagem de erro (começa vazia)
$erro = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($temAdmin) {
        // ----- LOGIN -----
        // Busca o usuário pelo e-mail
        $consulta = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $consulta->execute([$email]);
        $usuario = $consulta->fetch();

        // Confere se o usuário existe e se a senha digitada combina com o hash guardado
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Guarda na sessão os dados de quem entrou (nunca a senha)
            $_SESSION['usuario'] = [
                'id'     => $usuario['id'],
                'nome'   => $usuario['nome'],
                'perfil' => $usuario['perfil']
            ];
            header('Location: painel.php');
            exit;
        }

        // Guarda uma mensagem de erro que não revela se errou o e-mail ou a senha
        $erro = 'E-mail ou senha incorretos.';

    } else {
        // ----- CADASTRO DO PRIMEIRO ADMINISTRADOR -----
        $nome = trim($_POST['nome']);

        // Valida os campos preenchidos
        if ($nome == '' || $email == '' || strlen($senha) < 6) {
            $erro = 'Preencha tudo. A senha precisa ter pelo menos 6 caracteres.';
        } else {
            // Gera o hash da senha (o hash não pode ser desfeito e a senha pura nunca é guardada)
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            // Grava o usuário com o perfil admin
            $consulta = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'admin')");
            $consulta->execute([$nome, $email, $hash]);

            // Guarda a mensagem de sucesso e recarrega a página
            $_SESSION['msg'] = 'Administrador criado! Agora faça o login.';
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mural de Avisos</title>

    <!-- Liga o arquivo de estilos -->
    <link rel="stylesheet" href="estilo.css">

    <!-- Liga o script do botão "Gerar senha" -->
    <script src="senha.js"></script>
</head>
<body class="central">

    <!-- Cartão central da página -->
    <div class="cartao pequeno">

        <?php if ($temAdmin): ?>
            <!-- ----- TELA DE LOGIN ----- -->
            <h1>Entrar</h1>
            <p class="sub">Acesse o Mural de Avisos.</p>

            <!-- Mostra a mensagem de sucesso e a mensagem de erro, se houver -->
            <?php mostrarMensagem(); ?>
            <?php if ($erro != ''): ?>
                <div class="alerta erro"><?= $erro ?></div>
            <?php endif; ?>

            <form method="post">
                <label>E-mail</label>
                <input type="email" name="email" required>

                <label>Senha</label>
                <input type="password" name="senha" required>

                <button type="submit">Entrar</button>
            </form>

        <?php else: ?>
            <!-- ----- TELA DE PRIMEIRO ACESSO ----- -->
            <h1>Primeiro acesso</h1>
            <p class="sub">Ainda não existe administrador. Cadastre o primeiro.</p>

            <!-- Mostra a mensagem de erro, se houver -->
            <?php if ($erro != ''): ?>
                <div class="alerta erro"><?= $erro ?></div>
            <?php endif; ?>

            <form method="post">
                <label>Nome</label>
                <input type="text" name="nome" required>

                <label>E-mail</label>
                <input type="email" name="email" required>

                <label>Senha</label>
                <!-- Campo da senha com o botão que gera uma senha aleatória -->
                <div class="campo-senha">
                    <input type="password" name="senha" id="senha" required>
                    <button type="button" onclick="gerarSenha('senha')">Gerar senha</button>
                </div>

                <button type="submit">Criar administrador</button>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>
