<?php $this->layout("layouts/error", ['title' => '404 Not found!']); ?>

<?php $notfound = new \App\Controllers\UserController; ?>
<div class="jumbotron text-center" style="margin-top: 40px;">
  <?php echo $notfound->getDefaultPage(); ?>
</div>