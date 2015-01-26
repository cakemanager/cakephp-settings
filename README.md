# cakephp-settings

This is a pre-alpha version of the Settings-plugin for Cake3.x in combination with the Cakemanager-plugin

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
Plugin::load('Migrations');
```

Next you need to create the table. Use the following command to initialize the settings-table.

```
$ bin/cake migrations migrate -p Settings
```

## Using the shell

Via the shell you are able to read and write settings.

### Write

This is an example of writing a key to the database:

```
$ bin/cake setting write App.Name "My Name With Spaces"
```

This will write our value to the database.

#### Options

- Type - will be documented soon
- Editable - will be documented soon

### Read

This is an example of reading a key:

```
$ bin/cake setting read App.Name
```

This will return:
Key:            App.Name
Value:          Custom Name

#### Options

By using the `-i / --info` options you get more information about the requested key.

Example:

```
$ bin/cake setting read -i App.Name
```

## Using the class (app-based)

The `Setting`-class works the same like the `Configure`-class from CakePHP itself.

You can include the class like:

```php
use Settings\Core\Setting;
```

### Write

You can write settings with the following:

```php
Setting('App.Name', 'Custom Name', []);
```

The value `Custom Name` is now written to the database with the key `App.Name`.

### Read

## Using the setting-forms

