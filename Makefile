rss-up: build
	docker run --rm --link mysql:mysql -p 8080:80 -p 8022:22 -P --name test test &

build: authorized_keys Dockerfile
	docker build -t test .

authorized_keys:
	cp ~/.ssh/authorized_keys authorized_keys

Dockerfile:
	perl build-dockerfile.pl

rss-down:
	docker stop test

mysql-up:
	docker run --rm -p 3306 -P -e MYSQL_DATABASE=docker -e MYSQL_USER=docker -e MYSQL_PASSWORD=docker --name mysql orchardup/mysql &

mysql-down:
	docker stop mysql
