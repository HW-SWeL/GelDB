# GelDB

## Dependencies

- [Fuseki](https://jena.apache.org/): Triplestore for storing the data
- [Elda](https://github.com/epimorphics/elda): a Java implementation of the [Linked Data API](https://github.com/UKGovLD/linked-data-api/blob/wiki/Specification.md) specification; needs to be deployed in a Web Serlet Engine such as [Tomcat](http://tomcat.apache.org/)
  - elda-common.war
  - elda-assets.war
- Web server running PHP

## Getting Started

- Download and unpack Fuseki
- Start Fuseki in standalone server mode 
  ```fuseki-server --loc=dir /gdb```
  where ```--loc=dir``` is the directory for the database and ```gdb``` is the name of the database, e.g. the database directory could be ```/etc/fuseki/databases/gdb```
- Deploy the elda configuration file (```config.ttl```) to ```/etc/elda/conf.d/elda-common```
  - elda-common is the name of the LDA app being deployed
  - currently GelDB does not rename this in any way
- Drop the two elda war files (without version numbers) into the tomcat webapps directory and start tomcat
- Deploy application files to webserver ensuring to change the base href in index.html to the appropriate deployment location.

