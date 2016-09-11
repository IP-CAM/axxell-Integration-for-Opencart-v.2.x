## Axxell Integration Opencart (extension)

This [Opencart](https://www.opencart.com) extension shows a list of products that may be interested by shoppers. It uses the [Axxell API](https://axxell.cinaq.com) to provide intelligent recommendations.

Note: this is a minimal module that uses the Axxell API. We hope the Opencart Community extends this in the future to make it more user friendly and add more features.

### Features

- Configure Axxell API information
- Configure number items to show (ideal count is 5)
- Configure the type of recommendations to show: personalized or similar items
- Backfill with random products when needed
- Widget can be placed wherever you want

### Installation

- Obtain API credentials by registering an account at [Axxell](https://axxell.cinaq.com).
- Build `axxell-opencart.ocmod.zip` with: `git clone https://github.com/xiwenc/axxell-integration-opencart && cd axxell-integration-opencart && make`
- Install `axxell-opencart.ocmod.zip`, configure and enable the module via Opencart admin.
- Use the [Axxell Dashboard](https://axxell.cinaq.com/dashboard) to monitor the effectiveness of the system.

### Development setup

```
docker-compose up -d
docker-compose exec opencart /scripts/install.sh
docker-compose restart
```

Continue installation at http://localhost:8001

- Database hostname: mysql
- Database username: root
- Database password: root
- Database name: opencart

- Admin username: admin
- Admin password: admin
- Admin email: admin@localhost.local

Finalize setup:
```
docker-compose exec opencart /scripts/post-install.sh
```
Login as admin at http://localhost:8001/admin
Setup FTP login (Settings > Store > Edit > Ftp):
- username: www-data
- password: www-data
- host: locahost
- path: /var/www/html

Now you can install extension


### Disclainer

- Use at your own risk.
- It is provided as-is.
- Non-confidential data is shared with Axxell
