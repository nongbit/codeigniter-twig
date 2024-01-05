# Codeigniter Twig
Integrate Twig to CodeIgniter 4.

## Setup

Download package using composer.

```shell
composer require nongbit/codeigniter-twig
```

Open `APPPATH/Controllers/BaseController.php`.

```php
use Nongbit\Twig\Traits\Twig;

abstract class BaseController extends Controller
{
    use Twig;

    ...

    public function initController(...)
    {
        ...

        $this->initTwig();
    }
}
```

## Usage

### Template

To display template, we can use `display()` provided by the trait.

```php
$this->display('hello', ['title' => 'Acme'])
```

By default, Twig will look for template files inside `APPPATH/Views`. To add other locations use `addPath()`.

```php
$this->twig->addPath(ROOTPATH . 'templates');
```

The default file extension is `html` but we can change it by creating a configuration file `App\Config\Twig.php`.

```php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Twig extends BaseConfig
{
    public string $fileExtension = 'twig';
}
```

### Global

```php
$this->twig->addGlobals('title', 'Acme');
$this->twig->addGlobals(['title' => 'Acme']);
```

Inside view:

```html
{{ title }}
```

### Filter

```php
$this->twig->addFilters('rot13', 'rot13');
$this->twig->addFilters('rot13', function($string) {
    return str_rot13($string);
});
$this->twig->addFitlers(['rot13']);
```

Inside view :

```html
{{ 'Twig'|rot13 }}
```

### Function

Function behaves similarly to a filter.
