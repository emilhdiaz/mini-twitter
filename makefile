


install:
    cd /usr/local/bin/composer
    curl -s https://getcomposer.org/installer | php

    cd /var/www
    wget https://github.com/zendframework/ZendSkeletonApplication/archive/master.zip
    mv ZendSkeletonApplication-master/ src