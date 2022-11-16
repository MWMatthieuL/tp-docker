Installation
============

1. Get sources

```bash
git clone git@github.com:MWMatthieuL/tp-docker.git
```  

2. Environment variables

Create a .env.local file used to override the values in the .env file.

3. Specific docker configuration

You need to override the configuration of Docker. To do this, just copy `docker-compose-dev.yml.dist` to 
`docker-compose-dev.yml` and modify services as you need.

---

Note : If you are on arm system, you can copy the content of `docker-compose-dev-m1.yml.dist` instead.

3. Start project

```
make start
```

4. Add host to your system

Add `127.0.0.1    matop.local` in your hosts file
	- `/etc/hosts` on MacOS and Linux
    - `C:\WINDOWS\system32\drivers\etc\hosts` on Windows

9. Go to https://matop.local/
