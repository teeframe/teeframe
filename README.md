# TeeFrame

##  Install system dependencies (Ubuntu 26.04)

```bash
sudo apt install php php-dev php-pear composer libbrotli-dev

sudo pecl install swoole

echo "extension=swoole.so" | sudo tee /etc/php/8.5/cli/conf.d/30-swoole.ini > /dev/null
```

## Download the repository

```bash
git clone https://github.com/teeframe/teeframe.git
cd teeframe
```

## Install composer dependencies

```bash
composer install
```

## Run the server

```bash
php server.php
```
