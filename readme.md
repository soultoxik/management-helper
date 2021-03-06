# local machine
```
cp .env.example .env
cp app/.env.example app/.env

docker-compose up -d
```


## generate keys
https://cloud.google.com/compute/docs/instances/adding-removing-ssh-keys#locatesshkeys

``` 
# ssh-keygen -t rsa -f <private-key> -C <user>
ssh-keygen -t rsa -f ~/.ssh/otus-project-ssh-key -C otus.user

# chmod 400 <private-key>
chmod 400 ~/.ssh/otus-project-ssh-key

sudo apt-get install xclip

# copy to bufer public key
xclip -sel clip < ~/.ssh/otus-project-ssh-key.pub
# =>
# google-cloud->add public key

```

# google-cloud-server
- http://35.228.202.200 - our web-site
```http://35.228.202.200/test.php```

```
1. login through ssh
# ssh -i <private-key> viktar.otus@35.228.202.200
ssh -i ~/.ssh/otus-project-ssh-key viktar.otus@35.228.202.200

2. 
sudo su - # если не сделать эту команду, git pull вызывает пока что трабл
cd /home/viktar_otus/management-helper/
git pull


# postgres (separated virtual machine), password - Otus123
psql -h 35.228.74.42 -p 5432 -U otus -d otusdb
psql -h 35.228.74.42 -p 5432 -U postgres -d otusdb

restore from backup: psql -h 35.228.74.42 -p 5432 -U otus -d otusdb  < DB/backup.sql

*
### postgres (docker-compose service), password - docker
psql -h 192.168.15.5 -p 5432 -U docker -d docker
```

```
swagger:
cp app/public/swagger/index-example.html app/public/swagger/index.html
- replace %REPLACE_HOST%, 
    e.g. 
        url: "http://35.228.202.200/swagger.json"
        or
        url: "http://localhost:2280/swagger.json"

- get doccumentation
http://35.228.202.200/swagger/index.html
http://localhost:2280/swagger/index.html

- generate documentation
http://localhost:2280/api/v1/documentation/generate
* после как сгенерится json => вставить в app/public/swagger.json
```

```
RabbitMQ's consumer:
Open PHP docker by cli: docker-compose exec php bash
Need change directory: cd /var/www/html/console
Run command: php console.php student_find_group & php console.php teacher_find_group & php console.php group_find_teacher & php console.php group_change_teacher & php console.php group_form_group &
```