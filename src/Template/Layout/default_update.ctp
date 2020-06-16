<!DOCTYPE html>
<html>

<head lang="pt-br">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $this->fetch('title'); ?></title>
</head>


<body id="main_body" class="">
    <div id="preloader" style="position: fixed; padding: 0; margin: 0; top: 0; left: 0; width: 100%; height: 100%; background: url(/img/loading.gif) white; background-repeat: no-repeat; z-index: 99999; background-position: center center;"></div>
    <?= $this->element('header') ?>
    <div id="wrapper">

        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <?php if (!empty($sessao) && !empty($sessao->usuarioLogado)) : ?>

                            <div class="dropdown profile-element" style="display: flex;flex-direction: column;align-items: center;">
                                <img alt="image" src="<?= $sessao->usuarioLogado->foto_perfil_view ?>" style="border-radius: 50%; max-width: 6rem;" />
                                <!-- <img alt="image" class="rounded-circle" src="" /> -->
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#" style="background-color: inherit">
                                    <span class="block m-t-xs font-bold"><?= $sessao->usuarioLogado->nome ?></span>
                                    <span class="text-muted text-xs block"><?= $this->UserUtil->getProfileType($sessao->usuarioLogado->tipo_perfil) ?> <b class="caret"></b></span>
                                </a>
                                <ul class="dropdown-menu animated fadeInRight m-t-xs" style="left: auto">
                                    <!-- <li><a class="dropdown-item" href="profile.html">Profile</a></li> -->
                                    <!-- <li><a class="dropdown-item" href="contacts.html">Contacts</a></li>
                                    <li><a class="dropdown-item" href="mailbox.html">Mailbox</a></li> -->
                                    <li class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#/usuarios/alterar-senha">Altera senha</a></li>
                                    <li class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/usuarios/logout">Logout</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="logo-element">
                            <img alt="Gotas" src="/img/logotipogotas.png" style="width: 15rem" class="main-logo"/>
                            <img alt="Gotas" src="/img/logotipogotas_mini.png" class="small-logo"/>
                        </div>
                    </li>

                    <?php

                    if ($sessao->usuarioLogado->tipo_perfil === PROFILE_TYPE_ADMIN_DEVELOPER) {
                    ?>

                        <li>
                            <a href="index.html"><i class="fas fa-user"></i> <span class="nav-label">Usuários</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="#/usuarios/index">Consultar usuários</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="index.html"><i class="fas fa-building"></i> <span class="nav-label">Redes</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="#/redes/index">Cadastro de Redes</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fas fa-cogs"></i> <span class="nav-label">Administração</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#/admin/import-sefaz-products">
                                        <i class="fas fa-clipboard-list"></i>
                                        Importação de Gotas da SEFAZ
                                    </a>
                                </li>
                                <li>
                                    <a href="#/admin/correction-user-points">
                                        <em class="fas fa-check-circle"></em>
                                        Correção de Pontos
                                    </a>
                                </li>
                                <li>
                                    <a href="#/admin/manage-user">
                                        <i class="fas fa-eye"></i>
                                        Controlar Usuário
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <?php
                    } elseif ($sessao->usuarioLogado->tipo_perfil === PROFILE_TYPE_ADMIN_NETWORK) {
                    ?>
                        <li>
                            <a href="index.html"><em class="fas fa-cogs"></em> <span class="nav-label">Administração</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li class="active">
                                    <a href="#/admin/network-settings"><em class="fas fa-building"></em> Parâmetros da Rede</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="index.html"><i class="fas fa-user"></i> <span class="nav-label">Minha Equipe (Usuários da Rede)</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li class="active"><a href="#/usuarios/index">Cadastro de Usuários</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="index.html"><em class="fas fa-chart-bar"></em> <span class="nav-label">Relatórios</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li class="active"><a href="/">A fazer</a></li>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-warning " href="#"><i class="fa fa-bars"></i> </a>
                        <!-- <form role="search" class="navbar-form-custom" action="search_results.html">
                            <div class="form-group">
                                <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                            </div>
                        </form> -->
                    </div>
                    <ul class="nav navbar-top-links navbar-right" style="margin-right: 0">
                        <li class="dropdown">
                            <!-- <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-envelope"></i> <span class="label label-warning">16</span>
                            </a>
                            <ul class="dropdown-menu dropdown-messages">
                                <li>
                                    <div class="dropdown-messages-box">
                                        <a class="dropdown-item float-left" href="profile.html">
                                            <img alt="image" class="rounded-circle" src="img/a7.jpg">
                                        </a>
                                        <div class="media-body">
                                            <small class="float-right">46h ago</small>
                                            <strong>Mike Loreipsum</strong> started following <strong>Monica Smith</strong>. <br>
                                            <small class="text-muted">3 days ago at 7:58 pm - 10.06.2014</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <div class="dropdown-messages-box">
                                        <a class="dropdown-item float-left" href="profile.html">
                                            <img alt="image" class="rounded-circle" src="img/a4.jpg">
                                        </a>
                                        <div class="media-body ">
                                            <small class="float-right text-navy">5h ago</small>
                                            <strong>Chris Johnatan Overtunk</strong> started following <strong>Monica Smith</strong>. <br>
                                            <small class="text-muted">Yesterday 1:21 pm - 11.06.2014</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <div class="dropdown-messages-box">
                                        <a class="dropdown-item float-left" href="profile.html">
                                            <img alt="image" class="rounded-circle" src="img/profile.jpg">
                                        </a>
                                        <div class="media-body ">
                                            <small class="float-right">23h ago</small>
                                            <strong>Monica Smith</strong> love <strong>Kim Smith</strong>. <br>
                                            <small class="text-muted">2 days ago at 2:30 am - 11.06.2014</small>
                                        </div>
                                    </div>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a href="mailbox.html" class="dropdown-item">
                                            <i class="fa fa-envelope"></i> <strong>Read All Messages</strong>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-bell"></i> <span class="label label-primary">8</span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>
                                    <a href="mailbox.html" class="dropdown-item">
                                        <div>
                                            <i class="fa fa-envelope fa-fw"></i> You have 16 messages
                                            <span class="float-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a href="profile.html" class="dropdown-item">
                                        <div>
                                            <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                            <span class="float-right text-muted small">12 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a href="grid_options.html" class="dropdown-item">
                                        <div>
                                            <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                            <span class="float-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a href="notifications.html" class="dropdown-item">
                                            <strong>See All Alerts</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li> -->


                        <li>
                            <a href="/usuarios/logout">
                                <i class="fas fa-sign-out-alt"></i> Log out
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>
            <!-- <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>This is main title</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">This is</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Breadcrumb</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    <div class="title-action">
                        <a href="" class="btn btn-primary">This is action area</a>
                    </div>
                </div>
            </div> -->

            <div id="content" style="margin-top: 1em">
                <?php
                echo $this->fetch('content');
                ?>
            </div>
            <div id='content-html'>
            </div>
            <div class="footer">
                <div class="float-right">
                </div>
                <div>
                    <strong>Copyright</strong> App web GOTAS &copy; 2017-2020
                </div>
            </div>

        </div>
    </div>

    <?= $this->element("../Layout/librarys"); ?>
    <?php // echo $this->Html->script("https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js");
    ?>

    <?php echo $this->Html->script("layout-update/inspinia"); ?>
    <?php echo $this->Html->script("layout-update/pace/pace.min"); ?>
    <?php echo $this->Html->script("layout-update/metisMenu/jquery.metisMenu.js"); ?>
    <?php echo $this->Html->script("layout-update/slimscroll/jquery.slimscroll.min.js"); ?>
    <script type="text/javascript">
        window.onload = function() {
            $("#preloader").css('display', 'none');
        };
    </script>
    <?php echo $this->fetch('script'); ?>

</body>

</html>
