# Admin Brand Manager

This package provides an Admin Brand Manager for managing product or content brands within your application.

## Features

- Create, edit, and delete brand entries
- Assign logos or images to brands
- SEO-friendly brand pages with metadata
- Brand activation/deactivation status
- User permissions and access control

## Usage

1. **Create**: Add a new brand with name, logo, and status.
2. **Read**: View all brands in a paginated list.
3. **Update**: Edit brand information.
4. **Delete**: Remove brands that are no longer relevant.

## Example Endpoints

| Method | Endpoint        | Description        |
|--------|-----------------|--------------------|
| GET    | `/brands`       | List all brands    |
| POST   | `/brands`       | Create a new brand |
| GET    | `/brands/{id}`  | Get brand details  |
| PUT    | `/brands/{id}`  | Update a brand     |
| DELETE | `/brands/{id}`  | Delete a brand     |

## Requirements

- PHP 8.2+
- Laravel Framework

## Update `composer.json`

Add the following to your `composer.json` to use the package from a local path:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-brands.git"
    }
]
```

## Installation

```bash
composer require admin/brands --dev
```

## Usage

1. Publish the configuration and migration files:
    ```bash
    php artisan brand:publish --force

    composer dump-autoload
    
    php artisan migrate
    ```
2. Access the Brand manager from your admin dashboard.

## CRUD Example

```php
// Creating a new Brand
$brand = new Brand();
$brand->name = 'Levi\'s';
$brand->status = true;
$brand->save();
```

## Customization

You can customize views, routes, and permissions by editing the configuration file.

## License

This package is open-sourced software licensed under the Dotsquares.write code in the readme.md file regarding to the admin/brand manager
