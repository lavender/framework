## Lavender Framework

> **Note:** This repository contains the core code of Lavender. If you want to create an ecommerce store using Lavender, visit the main [Lavender repository](https://github.com/lavender/lavender).

### License

Lavender is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Framework Features:

#### Config

Config files from modules can be merged into the global collection, allowing for rewrites. This merge can be defered until a theme is booted, allowing for theme fallbacks on a config level. The config service also provides automatic default value merging.

##### Entities

A common task while developing features for an e-commerce site is extending existing entities (customers, orders, products,
etc) and creating new ones (blog posts, banners, etc) and handling relationships. Lavender's Entity model (see 'entity.php' config files) makes it easy to create and extend entities without having to deal with managing rewrite conflicts, writing database migrations or manually creating relationships.

##### Workflows

Additionally we spend a lot of time customizing user workflows such as the shopping cart, checkout, various forms, etc.
The Workflow service (see 'workflow.php' config file) allows modules to collaborate on a single user workflow without complicated class rewrites and layout injection logic. Workflows are wonderful stateful eventful forms which are unique to Lavender.

##### Scope

Lavender's Entity model was designed to support attribute scopes, an example usage of this is Lavender's Store entity.
