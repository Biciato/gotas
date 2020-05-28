<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->fetch('title'); ?></title>

    <?php echo $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'); ?>
    <?php echo $this->Html->css('font-awesome/css/font-awesome.css'); ?>

    <?php echo $this->Html->css('layout-update/animate.css'); ?>
    <?php echo $this->Html->css('layout-update/style.css'); ?>
    <?php echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css'); ?>

</head>

<body class="gray-bg">

    <?php echo $this->fetch('content'); ?>

    <!-- Mainly scripts -->
    <?php echo $this->Html->script("https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"); ?>
    <?php echo $this->Html->script("layout-update/popper.min.js"); ?>
    <?php echo $this->Html->script("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"); ?>
    <?php echo $this->Html->script("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"); ?>
    <?php echo $this->fetch('script'); ?>
   

</body>

</html>
