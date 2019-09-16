<?php $__container->servers(['local' => '127.0.0.1']]); ?>
 
<?php $__container->startTask('foo', ['on' => 'web']); ?>
    ls -la
<?php $__container->endTask(); ?>