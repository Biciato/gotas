<?php

/**
 * Arquivo que irá guardar as configurações que são diferentes por servidor / branch
 * Este arquivo concentra a configuração de todos
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-02-19
 */

// host
const __HOST__ = "";
// Server
// const __HOST__ = "sistema.gotas.com.br";

// Branch Master -> Servidor RTI
// const __SERVER__ = "https://sistema.gotas.com.br/";
// Branch Devel -> Servidor RTI
// const __SERVER__ = "https://sistema-devel.gotas.com.br/";
// Branch Master-> Servidor local
// const __SERVER__ = "https://sistema.gotas.local/";
// Branch Devel -> Servidor local
// const __SERVER__ = "https://sistema-devel.gotas.local/";
const __SERVER__ = "https://" . __HOST__ . "/";
// const __DATABASE__ = "rti_gotas";
const __DATABASE__ = "";
const __DATABASE_USERNAME__ = '';
const __DATABASE_PASSWORD__ = '';
const __SITEKEY__ = '';
const __SECRETKEY__ = '';
