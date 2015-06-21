<h3><?= $prefix ?></h3>

<?= $this->Menu->menu('navbar', 'CakeManager.NavbarMenu') ?>

<?php
echo $this->Form->create();

foreach ($settings as $id => $setting) {

    echo $this->Form->input($id . '.id', [
        'type' => 'hidden',
        'value' => $setting->id,
    ]);
    
    $name = explode('.', $setting->name);
    
    switch($setting->type){
        case 'checkbox':
            echo $this->Form->input($id . '.value', [
                'type' => (($setting->type) ? $setting->type : 'text'),
                'label' => ucfirst(end($name)) . (($setting->description) ? ' - ' . $setting->description : ''),
                'options' => (($setting->options) ? $setting->options : ''),
                'value' => 1,
                'checked' => $setting->value,
            ]);
            break;
        default:
            echo $this->Form->input($id . '.value', [
                'type' => (($setting->type) ? $setting->type : 'text'),
                'label' => ucfirst(end($name)) . (($setting->description) ? ' - ' . $setting->description : ''),
                'options' => (($setting->options) ? $setting->options : ''),
                'value' => $setting->value,
            ]);
            break;
    }
}

echo $this->Form->button(__('Submit'));

echo $this->Form->end();
