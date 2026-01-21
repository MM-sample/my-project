#!/bin/bash

# 全パッケージの更新
dnf update -y

# Nginxのインストール（AL2023では直接インストール可能）
dnf install nginx -y

# 開発ツールのインストール（Redisビルド用）
# AL2023では mysql-devel の代わりに mariadb105-devel 等を使用
dnf install gcc jemalloc-devel openssl-devel tcl tcl-devel mariadb105 -y

# Redis-cli のビルドとインストール
cd /tmp
wget http://download.redis.io/redis-stable.tar.gz
tar xvzf redis-stable.tar.gz
cd redis-stable
make BUILD_TLS=yes
install -m 755 ./src/redis-cli /usr/local/bin/

# Nginxの起動と自動起動設定
systemctl start nginx
systemctl enable nginx

# 完了の合図（デバッグ用ログ）
echo "Userdata execution finished" > /var/log/userdata_finish.log