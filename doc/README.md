# Connections

Connect the temperature sensors and the relay as show in the [image](schematic.png)

# Update

    sudo apt-get update
    sudo apt-get -y upgrade
    sudo reboot

# Install DHT22 software

    sudo apt-get -y install build-essential python-dev
    git clone https://github.com/adafruit/Adafruit_Python_DHT.git
    cd Adafruit_Python_DHT
    sudo python setup.py install
    cd ..
    rm -rf Adafruit_Python_DHT
    sudo reboot

# Install mysql

    sudo apt-get -y install mysql-server mysql-client python-mysqldb
    mysql -u root -p

Inside the mysql shell, copy and paste the code form the file [create_tables.sql](create_tables.sql).

# Install php and apache

    sudo apt-get -y install apache2 libapache2-mod-auth-mysql php5-cli php5-mysql php5 libapache2-mod-php5
    sudo a2enmod auth_mysql
    sudo service apache2 restart

