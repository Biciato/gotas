/**
 * @file webroot/js/scripts/pages/dashboard_funcionario.js
 * @author Gustavo Souza Gonçalves
 * @date 15/01/2018
 *  
 * Classe javascript para o elemento de buscar
 * 
 */


$(document).ready(function () {

    $(".network_container_outer").on('click', function (data) {
        var id_network = data.currentTarget.attributes['data-value'].value;

        // obtem os dados da rede para exibir no modal 

        $.ajax({
            type: "POST",
            url: "/Redes/getNetworkDetails",
            data: JSON.stringify({
                redes_id: id_network
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            },
            error: function (response) {
                console.log(response);
            }
        }).done(function (result) {

            $(".modal_show_network .modal-title-content").text(result.rede.nome_rede);
            $(".modal_show_network .modal-body-table-content").empty();

            var content = $("<div></div>");

            var title = $("<legend>Brindes oferecidos pela rede:</legend>");

            var orderedList = $("<ul></ul>");

            $.each(result.rede.brindes, function (index, value) {
                var itemList = $("<li>" + value.nome + "</li>");

                orderedList.append(itemList);
            });

            content.append(title);
            content.append(orderedList);

            $(".modal_show_network .network_image_content").attr('src', result.rede.nome_img);

            $(".modal_show_network .modal-body-table-content").append(content);

            $(".modal_show_network .submit_button").on('click', function () { location.href = "/redes/escolher_unidade_rede/2"; });

            $(".modal_show_network").modal();
        });
    })
});
