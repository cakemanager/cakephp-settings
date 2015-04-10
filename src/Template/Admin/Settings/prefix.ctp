<h3><?= $prefix['alias'] ?></h3>

<?= $this->Menu->menu('navbar', 'CakeManager.NavbarMenu') ?>

<?php
echo $this->Form->create();

foreach ($settings as $id => $setting) {

    echo $this->Form->input($id . '.id', [
        'type' => 'hidden',
        'value' => $setting->id,
    ]);
    
    $name = explode('.', $setting->name);
    
    echo $this->Form->input($id . '.value', [
        'type' => (($setting->type) ? $setting->type : 'text'),
        'label' => ucfirst(end($name)) . (($setting->description) ? ' - ' . $setting->description : ''),
        'options' => (($setting->options) ? $setting->options : ''),
        'value' => $setting->value,
    ]);
}

echo $this->Form->button(__('Submit'));

echo $this->Form->end();

//    \Cake\Core\Configure::write('debug', false);
debug(\Settings\Core\Setting::read('App.debug', 'bool'));
debug(\Cake\Core\Configure::read());
?>


<!--{1:"On",0:"Off"}-->