# Schema generator

Some entities were bootstrapped using the api-platform schema-generator:
https://api-platform.com/docs/schema-generator/

The generator uses the vocabulary formalized on [schema.org](https://schema.org).

## Configuration

[config/schema_generator.yaml](config/schema_generator.yaml)

## Generation

```shell
docker-compose exec php vendor/bin/schema generate src/ config/schema_generator.yaml
```