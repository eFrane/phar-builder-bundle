# Debugging Features

## Container Outputs

It may prove helpful to investigate the compiled container. While looking through
the generated PHP is a way to do that, it is not a very concise way. To simplify
matters, the phar container builder can optionally add the Graphviz (aka linked
dependency graph) and Yaml (aka list of all container/service parameters) dumps
to the output.

To do so, you simply need to add 

```yaml
# in config/phar_builder.yaml
phar_builder:
  build:
    dump_container_debug_info: true
```

to your config.

## The `_phar_debug:*` Commands

_missing documentation._

## Box Debugging Features

_missing documentation._
