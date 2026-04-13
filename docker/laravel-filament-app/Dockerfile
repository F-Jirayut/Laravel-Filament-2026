FROM php:8.4-fpm

# 1. หัวใจสำคัญ: สั่ง upgrade แพ็กเกจเดิมที่มีช่องโหว่ให้เป็นเวอร์ชันล่าสุด
# และรวมคำสั่งติดตั้งเข้าด้วยกันเพื่อลดจำนวน Layer
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    zip unzip git curl libzip-dev libicu-dev libonig-dev \
    libpq-dev supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. ติดตั้ง PHP Extensions
RUN docker-php-ext-install pdo_pgsql mbstring zip exif pcntl gd intl

# 3. ติดตั้ง Redis
RUN pecl install redis && docker-php-ext-enable redis

# 4. ใช้ Composer แบบระบุเวอร์ชัน (ช่วยเรื่อง Security Audit)
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 5. จัดการ User และ Permissions
# สร้าง User www หากยังไม่มี และกำหนด Directory ให้ถูกต้อง
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# สร้างโฟลเดอร์ที่จำเป็นและกำหนดสิทธิ์ก่อนสลับ User
RUN mkdir -p /var/log/supervisor /var/run /var/www/storage /var/www/bootstrap/cache && \
    chown -R www:www /var/log/supervisor /var/run /var/www

RUN mkdir -p /var/www/storage/framework/sessions \
             /var/www/storage/framework/views \
             /var/www/storage/framework/cache \
             /var/www/storage/logs \
             /var/www/bootstrap/cache

USER www

EXPOSE 9000
CMD ["php-fpm"]