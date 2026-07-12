FROM nginx
# копируем конфиг для конкретного домена
COPY ./mysite.local.conf /etc/nginx/conf.d/mysite.local.conf

COPY ./index.html /data/mysite.local/index.html

WORKDIR /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

# docker build -t sf/nginx .
# docker run -d -p 80:80 sf/nginx

# /etc/hosts
# C:\Windows\System32\drivers\etc\hosts

# 127.0.0.1  mysite.local