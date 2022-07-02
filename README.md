install docker centos

##Set up the repository
```sudo yum install -y yum-utils```

````
sudo yum-config-manager \
--add-repo \
https://download.docker.com/linux/centos/docker-ce.repo
````

##Install Docker Engine
```sudo yum install docker-ce docker-ce-cli containerd.io docker-compose-plugin```

##Start Docker
```sudo systemctl start docker```

##Install curl
```sudo yum install curl```

##Install docker-compose
```sudo curl -L "https://github.com/docker/compose/releases/download/1.29.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose```
 - ###Set permission
```sudo chmod +x /usr/local/bin/docker-compose```
