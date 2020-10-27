
# Deploying on CleverCloud

Clever cloud provides configuration (like database connexion credentials) through
 **[environment variables](https://en.wikipedia.org/wiki/Environment_variable)**
 as recommended by [the official symfony best practices](http://symfony.com/doc/current/best_practices/configuration.html#moving-sensitive-options-outside-of-symfony-entirely).

## Installation on clever cloud

Before starting, you need to create a **MySQL addon** on CleverCloud.
Then in `Environment variables`, you have to add the corresponding environment variables:

```
DATABASE_USER=lmem
DATABASE_PASSWORD=meml
DATABASE_NAME=kraftbackenddev
DATABASE_HOST=kraft-backend_db
DATABASE_PORT=3306
```

You must also set the Symfony environment to prod by adding the `SYMFONY_ENV` key with the `prod` value.

## Applying Doctrine migrations

This application automatically runs **Doctrine migrations** on **Build**. This is setup [here](../clevercloud/post_build.sh).
