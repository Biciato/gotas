/**
 * @author Gustavo Souza Gonçalves
 * @file webroot\js\scripts\pontuacoes\editar_pontuacao.js
 * @date 19/10/2017
 * 
 */


$(document).ready(function () {

    // ----------------------------------------------------------------
    // Funções de Inicialização 

    /**
     * Verifica se o campo possui valor. Se possuir, libera o botão para alterar o usuário
     * Nota: Ele não vai verificar se é o mesmo usuário
     */
    
    
    var quantidade = $("#quantidade_multiplicador").val();
    
    if (quantidade.indexOf(".") == -1 && quantidade.length < 3)
    {
        if (quantidade < 100) {
            quantidade = 0 + $("#quantidade_multiplicador").val();
        }
    
        if (quantidade % 1 == 0) {
            quantidade = quantidade + ".000";
        }    
    }    
    
    $("#quantidade_multiplicador").val(quantidade);
    $("#quantidade_multiplicador").mask("###.999");
});
