# Console Commands
- mysql -u admin -p (*access mysql from terminal*)
  - show tables;
  - describe [tablename];
- php bin/console
- php bin/console doctrine
- php bin/console make
- composer recipes (*for status on symfony recipes installed*)


- php bin/console cache:clear
- php bin/console about (*php/symfony version, amongst other things*)
- php bin/console debug:router (*lists all the projects routers*)


- php bin/console make:controller
- php bin/console make:entity
- php bin/console make:entity --regenerate (*adds getters and setters to properties missing them*)
- php bin/console doctrine:database:create (*creates the database specified in the doctrine.yaml file or in the .env file; **DATABASE_URL** variable*)
- php bin/console doctrine:schema:update (*updates the database schema based on projects entities*)
- **php bin/console make:migration**


- symfony server:start 
- symfony server:ca:install (*installs an SSL certificate and enables https*)
- symfony local:server:stop
- symfony proxy:start (*127.0.0.1:7080 shows all domains for the project*)
- symfony proxy:domain:attach [domainname] (*set the new domain mame*)