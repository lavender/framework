## Lavender Framework

> **Note:** This repository contains the core code of Lavender. If you want to create an ecommerce store using Lavender, visit the main [Lavender repository](https://github.com/lavender/lavender).

### License

Lavender is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Framework Features:

##### Entities

Lavender's Entity model makes it easy to create and extend entities without having to deal with managing rewrite conflicts, writing database migrations or manually creating relationships. [Just update the entity config!](https://github.com/lavender/lavender/blob/master/config/entity.php) You can use the Lavender service provider in your package to merge package config.

##### Forms

The Form service allows packages to collaborate on a form (such as checkout, login, or the shopping cart) without complicated class rewrites and layout injection logic.

##### Scope

Lavender's Entity model was designed to support attribute scopes, an example usage of this is Lavender's Store entity.
