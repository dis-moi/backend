
# About Clever Cloud & Environment variables

Clever cloud provides configuration (like database connexion credentials) through
 **[environment variables](https://en.wikipedia.org/wiki/Environment_variable)**
 as recommended by [the official symfony best practices](http://symfony.com/doc/current/best_practices/configuration.html#moving-sensitive-options-outside-of-symfony-entirely).

## Configuration of Symfony

Symfony will automatically extract environment variables that start with `SYMFONY` and will make them available in the project using the '%item%' notation.
So, modify your `parameters.yml` (and the `yml.dist`) with the correct syntax:

```
database_host: '%database.host%'
database_port: '%database.port%'
database_name: '%database.name%'
database_user: '%database.user%'
database_password: '%database.password%'
```

## local installation

 To mimic this behavior on your local environment,
and if you are launching your project using a web server you can follow the official [Symfony documentation](http://symfony.com/doc/current/cookbook/configuration/external_parameters.html#environment-variables).
But if you use the built-in serveur using the `bin/console server:run` to run your development environment you have to inject these variable onto your shell.
To be able to make this injection, two ways :
  * add them before launching the command: `SYMFONY__DATABASE__USER=user php bin/console server:run` but this can become very tedious to store and share the configuration.
  * using a tool to inject them automatically from a configuration file, I personally recommend the [foreman project](https://github.com/ddollar/foreman) to do it. Which is what we details in the next section.

### Injection using Foreman

First of all you need the node foreman tool on your machine: `gem install foreman`.
When it is done, create a `.env` file at the root of your symfony project and put into the required information:

```
SYMFONY__DATABASE__USER=user
SYMFONY__DATABASE__PASSWORD=pass
SYMFONY__DATABASE__NAME=symfony
SYMFONY__DATABASE__HOST=host
SYMFONY__DATABASE__PORT=3306
```

Note: these information are for your **development environment**, production environment variables will be provided directly by clever cloud (more about that later).

### Configuring PHP

In order to be able to access environment variable from the built-in php server and because of [an obscure behavior](http://stackoverflow.com/questions/13784116/setting-environment-variables-with-the-built-in-php-web-server),
you'll need to set the `variables_order` directive to `EGPCS` in your php.ini

### Launching Symfony with Node Foreman

Now you can use any of the symfony using `foreman run` with all the configured variable injected in the project:
`foreman run php bin/console server:run`

## Installation on clever cloud

Before starting, you need to create a `mysql addon on clever cloud`.
Then open the `Environment variables` from your application menu, you have to add the corresponding environment variables:

```
SYMFONY__DATABASE__USER=user
SYMFONY__DATABASE__PASSWORD=pass
SYMFONY__DATABASE__NAME=symfony
SYMFONY__DATABASE__HOST=host
SYMFONY__DATABASE__PORT=3306
```

Set the Symfony environment to prod by adding the `SYMFONY_ENV` key with the `prod` value.

## Using doctrine migration

Because a deployment should be indepotent Clever cloud doesn't provide an access to the command line of your server.
So to be able to update the schema or to seed the database you'll have to use the [Doctrine Migration bundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html).

Don't forget to add the migrate command in you composer.json post-install and post-update hook (don't forget the no-iteractive):

```
"post-install-cmd": [
            ...
            "php bin/console doctrine:migration:migrate"
        ],
        "post-update-cmd": [
            ...
            "php bin/console doctrine:migration:migrate"
        ]
```