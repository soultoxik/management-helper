http://35.228.202.200/swagger/index.html - Swagger

http://35.228.202.200:15672/ - RabbitMQ Management

http://35.228.202.200:5601/ - Kibana
http://35.228.202.200:9200/ - Elasticsearch

# deploy
```
# 1. env
cp .env.example .env
cp app/.env.example app/.env

# 2. swagger
cp app/public/swagger/index-example.html app/public/swagger/index.html
* replace %REPLACE_HOST% (e.g. "%REPLACE_HOST%/swagger.json" => "http://35.228.202.200/swagger.json")

# 3.
docker-compose up -d


# update api-documentation (example)
http://localhost:2280/api/v1/documentation/generate
=> insert new generated json into app/public/swagger.json
```

# prod (google-cloud)
```
http://35.228.202.200/swagger/index.html - api-documentation
http://35.228.202.200:15672 - rabbitmq

# login
# local terminal: ssh -i <private-key-path> viktar.otus@35.228.202.200
# example: ssh -i ~/.ssh/otus-project-ssh-key viktar.otus@35.228.202.200
su viktar_otus
cd /home/viktar_otus/management-helper/

git pull

#----------------------------------------------------------
# generate keys
# https://cloud.google.com/compute/docs/instances/adding-removing-ssh-keys#locatesshkeys

# ssh-keygen -t rsa -f <private-key> -C <user>
ssh-keygen -t rsa -f ~/.ssh/otus-project-ssh-key -C otus.user

# chmod 400 <private-key>
chmod 400 ~/.ssh/otus-project-ssh-key

sudo apt-get install xclip

# copy to bufer public key
xclip -sel clip < ~/.ssh/otus-project-ssh-key.pub # => add public key into google-cloud
```

# postgres
```
# separated virtual machine, password - Otus123
psql -h 35.228.74.42 -p 5432 -U otus -d otusdb
# or
psql -h 35.228.74.42 -p 5432 -U postgres -d otusdb

*
# docker-compose service, password - docker
psql -h 192.168.15.5 -p 5432 -U docker -d docker

# restore database from backup
psql -h 35.228.74.42 -p 5432 -U postgres -d otusdb  < DB/backup.sql
psql -h 192.168.15.5 -p 5432 -U docker -d docker  < DB/backup.sql
```

# RabbitMQ
```
RabbitMQ's consumer:
Open PHP docker by cli: docker-compose exec php bash
Need change directory: cd /var/www/html/console
Run command: php console.php student_find_group & php console.php teacher_find_group & \
 php console.php group_find_teacher & php console.php group_change_teacher & php console.php group_form_group
```
