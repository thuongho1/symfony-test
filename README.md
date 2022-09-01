# Thuong - Go fin go interview test

## Setup
- Create env: `composer dump-env`
- Run migration:
  - `php bin/console doctrine:migrations:migrate`


## Product & Category CRUD
### Product
  - List: /product
  - Create: /product/new
  - Edit: /product/{id}/edit
  - Delete: /product/{id}

## Console command import product/category from json

- `php bin/console app:import-entity --type=product --path=/path/to/file/products.json`
- `php bin/console app:import-entity --type=category --path=/path/to/file/categories.json`
