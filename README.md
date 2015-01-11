yarophp
=======

## Runtime variables:

* **ObjectCacheDisabled** - if TRUE caching is disabled for Object which helps to run cronjobs with fetching a lot of objects from database without memory overload.
* **DatabaseLogDisabled** - if TRUE database log is disabled, only getLastQuery() works, helps to run cronjobs with a lot of database queries.