SF Skeleton
===========

Install project 
---------------

- [Install project](docs/install.md)
- Go to https://matop.local/
- Go to http://matop.local:8080 to see database (username: mw, password: mw)
- Go to http://matop.local:1080 to see catched emails

Make Commands : 

| Command                     | Usage                                     |
|-----------------------------|-------------------------------------------|
| make start                  | Start the project                         |
| make stop                   | Stop all containers                       |
| make rm                     | Stop and Delete all containers            |
| make ssh                    | Connect to app container on ssh           |
| make run c='bash command'   | Run any bash command                      |
| make sf c=' '               | Run any bin/console command               |
| make db                     | Init database with data fixtures          |
| make cc                     | Clear cache                               |
| make assets                 | Regenerate assets files (css+js)          |
| make assets-watch           | Regenerate assets files and watch changes |
| make tf                     | Execute all functional tests              |
| make tfdev                  | Execute functional tests with tag @dev    |
| make tu                     | Execute unit tests                        |
| make vendor                 | Install dependencies PHP + Node           |
| make trans                  | Import translations on lexik              |
| make sonar                  | Exec sonar scanner                        |

*Use of branches:*

- master (last version of project)


List of users for testing
-------------------------

| Email               | Password | Roles                                    |
|---------------------|----------|------------------------------------------|
| superadmin@matop.fr | xxx      | ROLE_SUPER_ADMIN, ROLE_ALLOWED_TO_SWITCH |
| admin@matop.fr      | xxx      | ROLE_ADMIN                               |
| user{1..9}@matop.fr | xxx      | ROLE_USER                                |


Functional tests
----------------

If you want to see tests in the browser:

On MacOS:
```bash
open vnc://127.0.0.1
password: secret
```

On Linux:

- USE VNC & add new session:
  - Protocole: VNC
  - Serveur: 127.0.0.1
  - Username: /
  - Password: secret
 - Save and launch the session


How to participate
------------------

- Create new branch with the name of the feature
- Create Pull Request
- Wait for approval


Deploy
-----

Update prod: `make update-prod`
