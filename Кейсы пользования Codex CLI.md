
Есть еще Codex App – для него на рабочем компе нет лицензии винды)  
Поэтому установить его у меня получилось только на домашнем компьютере – в целом ничем не отличается от Codex CLI разве что хранит историю всех диалогов.  
  
## Инструкция к подключению:
https://codex-only.onrender.com/  
API-ключ я покупала на ggsel у поставщика за 1700 руб. 29.06 - хватает на месяц пользования:  
[https://payment.ggsel.com/order/b550fd01-0737-4ee0-ab8d-186383faf34f](https://payment.ggsel.com/order/b550fd01-0737-4ee0-ab8d-186383faf34f)  

# КЕЙСЫ ВОЗМОЖНОГО ИСПОЛЬЗОВАНИЯ  
## Поиск данных в проекте:  
### Промпт: 
«Найди Updater в котором сохраняются данные rosstat_founder в бд и страницу-первоисточник, не могу понять что значит поле updated - дата получения сведений или дата появления их на сайте.»  
### Результат:  
Нужный апдейтер это Updater/Fira/Fira.php:830. Метод downloadRosstatFoundersData($id) в нём забирает учредителей Росстата и складывает их в $result['rosstat_founders'][] на строках Updater/Fira/Fira.php:930. Вызывается он из общего прохода по компаниям на Updater/Fira/Fira.php:296, а потом весь результат сохраняется через company->save(...) на Updater/Fira/Fira.php:323.

  Источник данных у него pro.fira.ru, две страницы подряд:

  - Updater/Fira/Fira.php:834 получает настройки вкладки и check

  - Updater/Fira/Fira.php:881 получает сам блок “Данные Росстата”, откуда парсятся строки items_table_1_data

  В таблицу rosstat_founder это попадает через Contractor/Company/Company.php:3412, где вызывается rosstatFounder->saveList(...), а сама таблица объявлена в Contractor/Company/RosstatFounder.php:22. Поле updated

  проставляется не из сайта Фиры: в базовом сохранении, если дата не передана, ставится текущая date("Y-m-d") на Contractor/AbstractFnsRecord.php:60 и затем записывается в строку как $row['updated'] = $date на Contractor/

  AbstractFnsRecord.php:121.

  То есть для rosstat_founder поле updated здесь означает дату сохранения/обновления записи в нашей системе. В конкретно этом апдейтере Fira дата явно не передаётся, значит это обычно просто дата запуска импорта, а не дата появления сведений на сайте. Дополнительно это подтверждается тем, что rosstat_founders_update_date берётся как max(updated) из нашей таблицы на Contractor/Company/RosstatFounder.php:215.  
Он дает четкий ответ, похожий на документацию по парсерам сайтов, таким образом сокращая время на самостоятельный поиск кода.  
  
## Разработка фронта:  
### Даем четкие инструкции: 
«Нужно сделать поведение для https://deltabez.ru/proverka-kandidata/?picture: при заходе сразу открывать попап с public/images/proverka-kandidata/photo_2026-07-20_16-12-45.jpg, в стилистике существующих модалок Отправить запрос и Получить Демо-доступ.»  
### Результат: 
![[Pasted image 20260727130039.png]]

Codex сначала находит модальные окна проекта и сравнивает их, понимает стилистику, затем вносит изменения в локальный репозиторий в нужные файлы (proverka-kandidata.php и .tpl). Я провела тестирование и рефакторинг кода, проверяю архитектуру изменений.  
+ Попап переиспользован в lending.tpl, а не написан какой-то новый неизвестный  
+ Крестик  
+ Нужное фото  
- Добавлен лишний заголовок «Фото контрагента»  
- Фото растянуто на весь попап  
 
Остается просто подкрутить CSS стили и убрать заголовок, получаем нужный результат.  
По статистике Codex на самом деле хуже справляется с фронтом, чем Claude, но я не пробовала Claude.  
  
## Разработка бэка:
### Промпт:  
Добавь в парсер Коммерсанта  
1) Решение капчи с очеловеченным вводом – когда некоторые буквы стираются, пишутся заново, между написанием слов рандомное кол-во времени и рандомная скорость ввода  
2) Если сайт блокает частые заходы на страницу – сделать 10 попыток подключения к сайту заново.  
### Итог:
Парсер доработан, работает. Только в промпт необходимо также добавить информацию, чтобы он не исправлял существующий код (он удалил комментарии, восстановила ихперед заливкой)

## SQL-запросы:
### Промпт:
Составь запрос для выгрузки: Нужны список внешних запросов (typeId = число) для клиента volkova2004 по последнему периоду. В таблице должны быть колонки – логин  пользователя, его менеджер из црм, ИНН компании этого логина, список внешних источников и количество отправленных запросов по этим источникам. Мне нужен sql запрос который я отправлю и получу данные по таблицам. Не забывай о внешних ключах и помни что к таблицам можно обращаться как deltaincom_security.clients - как пример. Используй для создания sql запроса данные проектов и файлы database.md и database.json.

### Итог:  
Сначала он дал мне выгрузку по АПИ-периодам – в промпте можно уточнить, что нужны не АПИ-периоды, а обычные из user_periods. Также добавить в промпт то, что запросы должны быть максимально легковесными, чтобы в phpMyAdmin не выходила ошибка 504 – но это быстро решается также подключением к бд и отправкой запросов из консоли без phpMyAdmin
```
SELECT
      u.login AS `Логин пользователя`,
      CONCAT_WS(' ', crmmanager.last_name, crmmanager.name, crmmanager.middle_name) AS `Менеджер CRM`,
      crmcompany.inn AS `ИНН компании`,
      rt.type_id AS `ID внешнего источника`,
      rt.name AS `Внешний источник`,
      COUNT(*) AS `Количество отправленных запросов`
  FROM
      (
          SELECT
              up.period_id,
              up.user_id,
              up.copy_id,
              up.client_id,
              up.begin_date,
              up.end_date
          FROM deltaincom_security.user_periods up
          INNER JOIN deltaincom_clients.users u0
              ON u0.user_id = up.user_id
          WHERE up.deleted = 0
            AND up.api_access = 0
            AND up.copy_id = 4
            AND u0.login = 'volkova2004'
          ORDER BY up.end_date DESC, up.period_id DESC
          LIMIT 1
      ) last_period
  INNER JOIN deltaincom_clients.users u
      ON u.user_id = last_period.user_id
  LEFT JOIN deltaincom_security.clients cl
      ON cl.user_id = last_period.user_id
     AND cl.copy_id = last_period.copy_id
     AND cl.deleted = 0
  LEFT JOIN deltaincom_crm.users crmmanager
      ON crmmanager.user_id = cl.manager_id
  LEFT JOIN deltaincom_crm.companies crmcompany
      ON crmcompany.company_id = cl.company_id
  INNER JOIN deltaincom_security.req_list rl
      ON rl.user_id = last_period.user_id
     AND rl.copy_id = last_period.copy_id
     AND rl.period_id = last_period.period_id
     AND rl.created_time >= last_period.begin_date
     AND rl.created_time < DATE_ADD(last_period.end_date, INTERVAL 1 DAY)
     AND rl.types IS NOT NULL
     AND rl.types <> ''
  INNER JOIN deltaincom_security.req_types rt
      ON FIND_IN_SET(rt.type_id, rl.types) > 0
  GROUP BY
      u.login,
      crmmanager.last_name,
      crmmanager.name,
      crmmanager.middle_name,
      crmcompany.inn,
      rt.type_id,
      rt.name
  ORDER BY
      `Количество отправленных запросов` DESC,
      rt.name;
```
![[Pasted image 20260727122420.png]]

