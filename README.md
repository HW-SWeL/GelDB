# GelDB

## Dependencies

- [Fuseki](https://jena.apache.org/)

## Getting Started

- Download and unpack Fuseki
- Start Fuseki in standalone server mode 
  ```fuseki-server --loc=dir /gdb```
  where ```--loc=dir``` is the directory for the database and ```gdb``` is the name of the database, e.g. the database directory could be ```/etc/fuseki/databases/gdb```
- Deploy files to webserver ensuring to change the base href in index.html to the appropriate deployment location.

