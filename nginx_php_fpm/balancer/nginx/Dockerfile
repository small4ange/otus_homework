FROM nginx:alpine

# Устанавливаем envsubst для замены переменных
RUN apk add --no-cache gettext

# Копируем шаблон конфига
COPY mysite.local.conf /etc/nginx/templates/default.conf.template

# Шаблон будет обработан при старте nginx
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]