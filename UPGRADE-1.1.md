1.1.0
=====

Use this sql script for your migration:

```sql
ALTER TABLE catch_lookup_element RENAME elementfinder_lookup_element;
ALTER TABLE catch_lookup_meta RENAME elementfinder_lookup_meta;
```

CatchController has been renamed to ConfigController.

```
# admin_routing.yml
  
# vorher
phlexible_element_finder_catch:
    resource: "@PhlexibleElementFinderBundle/Controller/CatchController.php"
    type:     annotation
 
# ändern in
phlexible_element_finder_config:
    resource: "@PhlexibleElementFinderBundle/Controller/ConfigController.php"
    type:     annotation
```
