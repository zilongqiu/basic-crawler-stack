# Docker environment setup

## 1. Requirements

- Install [Docker CE](https://docs.docker.com/install/)
- Install [Docker compose](https://docs.docker.com/compose/install/)

## 2. Run docker

- Go to the "`docker-compose.yml`" folder (project root folder)
- Simply launch the command "`docker-compose up --build`".

To access to the project container: "`docker exec -it phpfpm bash`"

To check `nginx` logs, use `tail -f docker/nginx/log/*.log` (`error.log` / `access.log`).

## Informations

- Project url: "`http://localhost:81`"
- Mysql configs (host: `localhost` / port: `3007` / User: `root` / Pwd: no password)

And that's it!!!

