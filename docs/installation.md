You can install Heroic on a fresh or existing CodeIgniter 4 project. Just follow these steps:

### 1. Install CodeIgniter 4
You can start with a fresh CodeIgniter 4 installation or use an existing project.

Fresh install example:

```bash
composer create-project codeigniter4/appstarter myapp
cd myapp
```

### 2. Require Heroic via Composer
Run the following command in your project root to install Heroic:

```bash
composer require yllumi/heroic
php spark heroic:install
```

During installation, Heroic will automatically copy the necessary files to the correct location in your project. It is also copy some pages as sample for you to see how Heroic works.

Now if you open your project in browser, i.e. http://localhost:8080 (by using `php spark serve` command), you'll still see CodeIgniter 4 welcome message page. It is because Heroic still support CI4 route mechanism. To see sample page Heroic provide, you may want to comment or delete line of default route for home in app/Config/Routes.php. 

```php
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Comment this line below
// $routes->get('/', 'Home::index');
```

Reload the page at http://localhost:8080, and that’s it — you’re now ready to build progressive CI4 apps the Heroic way!
