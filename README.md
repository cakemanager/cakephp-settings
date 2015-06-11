# Settings plugin for CakePHP

This is the Settings-plugin for Cake3.x witch works in combination with the Cakemanager-plugin. The `Configure`-class from CakePHP is able to store keys and values in it's class. This plugin is able to store values in your database. 

- Is easy to use: you can use the `Configure::read()` and `Configure::write()` methods via the [`Setting`-class](#using-the-class).

- Also, you are able to read and write settings by your [console](#using-the-shell).

- Last but not least: If you use the [CakeManager](https://github.com/cakemanager/cakephp-cakemanager) you get an [automatically generated form](#using-the-settings-form) :).

> Note: The Settings-plugin is prefix-minded. An example: `Prefix.Name`.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org). For existing applications you can add the following to your `composer.json` file:

```javascript
"require": {
	"cakemanager/cakephp-settings": "dev-master"
}
```

And run `/composer update`.

## Configuration

You will need to add the following line to your application's bootstrap.php file:

```php
Plugin::load('Settings');

// or run in your shell

$ bin/cake plugin load Settings
```

Next you need to create the table. Use the following command to initialize the settings-table.

```
$ bin/cake migrations migrate -p Settings
```

## Usage

The `Setting`-class works the same like the `Configure`-class from CakePHP itself.

You can include the class with:

```php
use Settings\Core\Setting;
```

### Write

You can write settings with the following:

```php
Setting::write('App.Name', 'Custom Name');
```

The value `Custom Name` is now written to the database with the key `App.Name`. The empty array can contain multiple options

#### Options

- Type - will be documented soon
- Editable - will be documented soon

### Read

Now we gonna read the value from our just created key. Use:

```php
Setting::read('App.Name');
```

This will return our value: `Custom Name`.


### Register

To prevent missing configurations when migrating to another environment the `register` method is introduced.
Use the following to make sure the configuration exists in your application:

```php
Setting::register('App.Name', 'Default Value', []);
```


## Using the setting-forms

If you are using the [CakeManager-Plugin](https://github.com/cakemanager/cakephp-cakemanager), we will create a default form where you can edit your settings (if the field `editable` isset to `1`). The Settings-Plugin will automatically add a menu-item to the admin-area.

If you click the menu-item you will see a list with all editable settings who contains the chosen prefix (or default: `App`).

### Register

To add your prefix to the settings-list use the following:

```php
Configure::write('Settings.Prefixes.Test', 'Test');
```

