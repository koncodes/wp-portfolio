# WordPress Docker Environmentß

# Set working directory
```shell
WORKDIR /var/www/html/wp-content/themes/understrap
```

# Install Node.js dependencies
```shell
RUN npm install -g gulp-cli
RUN npm install
```

# Set up a command to watch for SASS changes
```shell
CMD ["npm", "run", "watch"]
```

## Launch environment

Execute the docker-compose.yml file AFTER completing the setup: 
```shell
docker compose up -d
```

### WordPress
<http://localhost>

### phpMyAdmin
<http://localhost:8081>

### MailHog
<http://localhost:8025>

## WP CLI
You can run a single command:
```shell
docker exec -it wp-portfolio-wpcli-1 wp user list
```
or login via the terminal:
```shell
docker exec -it wp-portfolio-wpcli-1 bash
```
or open the WPCLI terminal in Docker Desktop

## Setup
1. Create a new branch to keep this "starter" clean.
2. Review and update the docker-compose file. Make sure to change the database volume for each different site.
3. Create environment file and update variables.
    ```shell
   cp .env.example .env
    ```
4. Execute the docker-compose.yml the .env file:
   ```shell
   docker compose up -d
   ```

## Install and activate plugins
```shell
docker exec -it wp-portfolio-wpcli-1 bash -c " 
wp plugin delete hello akismet ; 
wp plugin install health-check query-monitor --activate ;  
wp plugin activate portfolio-projects ;
wp plugin activate mailhog ;
wp theme activate understrap ;
wp theme delete zakra twentytwenty twentytwentyone twentytwentytwo twentytwentythree twentytwentyfour twentytwentyfive;"
```
5. Setup WordPress @ <http://localhost/>

## MailHog
1. Explore MailHog:
   * Activate MailHog plugin.
   * Create a new user to catch emails.
   * View emails @ <http://localhost:8025/>
