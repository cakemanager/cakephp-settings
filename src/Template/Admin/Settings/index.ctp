<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <?= $this->Menu->menu('main') ?>
    </ul>
</div>
<div class="roles index large-10 medium-9 columns">

    <h3>Settings</h3>

    <?= $this->Html->link('New setting', ['action' => 'add']) ?>



</div>
