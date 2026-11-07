// ======================================================
// senha.js - Gera uma senha aleatória e complexa
// Usado pelo botão "Gerar senha"
// ======================================================

function gerarSenha(idDoCampo) {
    // Define os grupos de caracteres (sem letras parecidas como l, 1, O e 0)
    var maiusculas = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    var minusculas = 'abcdefghijkmnopqrstuvwxyz';
    var numeros    = '23456789';
    var simbolos   = '!@#$%&*?';
    var todos      = maiusculas + minusculas + numeros + simbolos;

    // Sorteia um caractere de dentro de um texto
    function sortear(texto) {
        var numeroAleatorio = new Uint32Array(1);

        // Gera um número aleatório seguro
        crypto.getRandomValues(numeroAleatorio);

        // Calcula a posição sorteada (resto da divisão pelo tamanho do texto)
        var posicao = numeroAleatorio[0] % texto.length;

        // Escolhe o caractere que está nessa posição
        var caractere = texto.charAt(posicao);

        return caractere;
    }

    // Sorteia um caractere de cada tipo
    var umaMaiuscula = sortear(maiusculas);
    var umaMinuscula = sortear(minusculas);
    var umNumero     = sortear(numeros);
    var umSimbolo    = sortear(simbolos);

    // Junta os quatro caracteres para começar a senha
    var senha = umaMaiuscula + umaMinuscula + umNumero + umSimbolo;

    // Completa a senha até 12 caracteres
    while (senha.length < 12) {
        senha = senha + sortear(todos);
    }

    // Escreve a senha no campo e mostra o texto (para a pessoa poder copiar)
    var campo = document.getElementById(idDoCampo);
    campo.value = senha;
    campo.type = 'text';
}
