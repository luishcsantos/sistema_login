<?php
// ======================================================
// usuarios.php - Cadastro de usuários
// Só o administrador pode acessar esta página
// ======================================================
require 'conexao.php';

// Redireciona para o painel quem não é administrador
exigirAdmin();

// Guarda a mensagem de erro (começa vazia)
$erro = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ----- CADASTRAR USUÁRIO -----
    if ($_POST['acao'] === 'cadastrar') {
        $nome   = trim($_POST['nome']);
        $email  = trim($_POST['email']);
        $senha  = $_POST['senha'];
        $perfil = $_POST['perfil'];

        // Aceita só os perfis 'admin' e 'comum'
        if ($perfil != 'admin' && $perfil != 'comum') {
            $perfil = 'comum';
        }

        // Verifica se o e-mail já está cadastrado
        $consulta = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $consulta->execute([$email]);
        $total = $consulta->fetchColumn();
        $emailJaExiste = $total > 0;

        // Valida os dados e grava o usuário
        if ($nome == '' || $email == '' || strlen($senha) < 6) {
            $erro = 'Preencha tudo. A senha precisa ter pelo menos 6 caracteres.';
        } elseif ($emailJaExiste) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            // Gera o hash da senha (o hash não pode ser desfeito e a senha pura nunca é guardada)
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $consulta = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
            $consulta->execute([$nome, $email, $hash, $perfil]);

            // Guarda a mensagem de sucesso e recarrega a página
            $_SESSION['msg'] = 'Usuário cadastrado!';
            header('Location: usuarios.php');
            exit;
        }
    }

    // ----- EXCLUIR USUÁRIO -----
    if ($_POST['acao'] === 'excluir') {
        $id = (int)$_POST['id'];

        // Impede que o administrador exclua a si mesmo
        if ($id != $_SESSION['usuario']['id']) {
            $consulta = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $consulta->execute([$id]);
            $_SESSION['msg'] = 'Usuário excluído.';
        }

        // Recarrega a página
        header('Location: usuarios.php');
        exit;
    }
}

// Busca todos os usuários, em ordem alfabética
$consulta = $pdo->query("SELECT * FROM usuarios ORDER BY nome");
$usuarios = $consulta->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários | Mural de Avisos</title>

    <!-- Liga o arquivo de estilos -->
    <link rel="stylesheet" href="estilo.css">

    <!-- Liga o script do botão "Gerar senha" -->
    <script src="senha.js"></script>
</head>
<body>

    <!-- Barra de navegação -->
    <?php include 'menu.php'; ?>

    <main class="conteudo">
        <h1>Usuários</h1>

        <!-- Mostra a mensagem de sucesso, se houver -->
        <?php mostrarMensagem(); ?>

        <!-- Formulário de novo usuário -->
        <div class="cartao">
            <h2>Novo usuário</h2>

            <!-- Mostra a mensagem de erro, se houver -->
            <?php if ($erro != ''): ?>
                <div class="alerta erro"><?= $erro ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="acao" value="cadastrar">

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

                <label>Perfil</label>
                <select name="perfil">
                    <option value="comum">Comum (só lê os avisos)</option>
                    <option value="admin">Administrador (pode tudo)</option>
                </select>

                <button type="submit">Cadastrar</button>
            </form>
        </div>

        <!-- Tabela de usuários cadastrados -->
        <div class="cartao">
            <h2>Cadastrados</h2>
            <div class="rolagem">
                <table>
                    <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th></th></tr>

                    <!-- Uma linha para cada usuário -->
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['nome'] ?></td>
                            <td><?= $usuario['email'] ?></td>
                            <td><span class="selo <?= $usuario['perfil'] ?>"><?= $usuario['perfil'] ?></span></td>
                            <td>
                                <!-- Mostra o botão de excluir, exceto para o próprio administrador logado -->
                                <?php if ($usuario['id'] != $_SESSION['usuario']['id']): ?>
                                    <form method="post" onsubmit="return confirm('Excluir este usuário?');">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?= (int)$usuario['id'] ?>">
                                        <button type="submit" class="perigo">Excluir</button>
                                    </form>
                                <?php else: ?>
                                    <small class="data">(você)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
