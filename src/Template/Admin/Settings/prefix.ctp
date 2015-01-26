<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <?= $this->Menu->menu('main') ?>
    </ul>
</div>
<div class="roles index large-10 medium-9 columns">

    <h3><?= $prefix['alias'] ?></h3>

    <?=
    $this->Menu->menu('navbar', [
        'builder' => 'CakeManager.NavbarMenu'
    ])
    ?>

    <?php
    echo $this->Form->create();

    foreach ($settings as $id => $setting) {

        echo $this->Form->input($id . '.id', [
            'type'  => 'hidden',
            'value' => $setting->id,
        ]);
        echo $this->Form->input($id . '.value', [
            'type'    => (($setting->type) ? $setting->type : 'text'),
            'label'   => $setting->name . ' - ' . $setting->description,
            'options' => (($setting->options) ? $setting->options : ''),
            'value'   => $setting->value,
        ]);
    }

    echo $this->Form->button(__('Submit'));

    echo $this->Form->end();
    ?>


</div>
