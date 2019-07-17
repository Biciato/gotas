<?php

?>

<legend>Instalar Aplicação</legend>
<div class="container">
    <div class="center-block">
        <p class="text-center">
            <h4 class="text-center">Prezado cliente, seja bem vindo ao <img src="/webroot/img/icons/gotas.jpg" />!</h4>
        </p>
        <p class="text-center">
            Para continuar usufruindo da aplicação, <strong>instale o aplicativo em seu dispositivo</strong> através dos seguintes links:
        </p>
        <div class="text-center">
            <a href="https://play.google.com/store/apps/details?id=br.com.rtibrindes" target="_blank">
                <img src="/webroot/img/icons/google-play.png" class="img-stores" />
            </a>
            <a href="https://apps.apple.com/br/app/gotas-rti/id1459190917" target="_blank">
                <img src="/webroot/img/icons/apple-store.png" class="img-stores" />
            </a>
        </div>
        <p class="text-center">
            Ou se preferir, leia o QR Code a seguir utilizando o próprio Smartphone e instale a aplicação:
        </p>

        <div class="text-center">
            <img src="/webroot/img/icons/qr-code-apps-store.jpg" class="qr-code-img-stores" />
        </div>

        <?php if (empty($usuarioLogado)) : ?>
            <div class="text-right">
                <a href="/usuarios/login">
                    <div class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Logar</div>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- estilo -->

<link rel="stylesheet" href="/webroot/css/styles/pages/instala_mobile.css" />