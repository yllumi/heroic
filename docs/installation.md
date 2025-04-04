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
```

> During installation, Heroic will automatically copy the necessary frontend assets (including heroic.min.js) to the correct location in your public/vendor directory. No additional manual step is required.

### 3. Include the Heroic JavaScript

To enable Heroic’s frontend features like SPA-like transitions and AlpineJS integration, add the following script before the closing </body> tag in your main layout file (e.g., app/Views/layouts/default.php):

```html
<script src="/vendor/heroic/heroic.min.js"></script>
```

> Make sure your public directory is accessible from the web root, and the vendor/heroic assets are correctly located.

And that’s it — you’re now ready to build progressive CI4 apps the Heroic way!

