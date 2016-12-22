# ORM File Field

Easily upload files to a directory and save the filename to database attribute.

## Usage

In your ORM Model:

```php
use Shared\Database\ORM\FileField\FileFieldTrait;

public $files = array(
    'image' => array(),
    'poster' => array(
        'path' => 'uploads/:class_slug/:attribute/:unique_id-:file_name',
        'defaultPath' => 'uploads/default.png'
    )
);
```
In your Controller:

```php
$model = new Poster();

if (Input::hasFile('poster')) {
    $model->poster = Input::file('poster');
}

$model->save();
```
Each field can have filesystem path pattern and default path options. If you don't specify any of them, they will be loaded from default config.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
