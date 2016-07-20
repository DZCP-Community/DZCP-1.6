<?php
/**
 * DZCP - deV!L`z ClanPortal 1.6 Final /// Translation Version 0.4ru
 * http://www.dzcp.de
 * Russian Translation by www.russian-speznas.com /// xero.ru
 * Russian Translation by www.russian-instinct.ru /// xero.ru
 */

$charset = 'utf-8';
header("Content-type: text/html; charset=".$charset);


## ADDED / REDEFINED FOR 1.6 Final
define('_txt_navi_main', 'Главная Навигация');
define('_txt_navi_clan', 'Kлана Навигация');
define('_txt_navi_server', 'Навигация сервером');
define('_txt_navi_misc', 'Остальные');
define('_txt_userarea', 'Пользовательский раздел');
define('_txt_vote', 'Опросы');
define('_txt_partners', 'Партнеры');
define('_txt_sponsors', 'Спонсоры');
define('_txt_counter', 'Cтатистика');
define('_txt_l_news', 'Hовости');
define('_txt_ftopics', 'Сообщения форума');
define('_txt_l_wars', 'Последние битвы');
define('_txt_n_wars', 'Cледующие битвы');
define('_txt_teams', 'Команды');
define('_txt_gallerie', 'Наши галереи');
define('_txt_top_match', 'Топ матч');
define('_txt_shout', 'Мини-чат');
define('_txt_template_switch', 'Изменить тему');
define('_txt_events', 'События');
define('_txt_kalender', 'Календарь');
define('_txt_l_artikel', 'товар');
define('_txt_l_reg', 'Новый пользователь');
define('_txt_motm', 'Случайный участник клана');
define('_txt_random_gallery', 'Случайное изображение');
define('_txt_server', 'Cервер');
define('_txt_teamspeak', 'Teamspeak');
define('_txt_top_dl', 'Топ закачек');
define('_txt_uotm', 'Случайный пользователь');






































































define('_gal_pics', 'Изображения в галерее');
define('_config_slideshow', 'Слайд-шоу');
define('_perm_slideshow', 'Слайд-шоу-Управление картинками');
define('_slider', 'Слайд-шоу');
define('_slider_admin_add', 'Добавить слайд-шоу изображение');
define('_slider_admin_add_done', 'Слайд-шоу изображение успешно вставлено');
define('_slider_admin_del', 'Действительно удалить слайд-шоу изображениe');
define('_slider_admin_del_done', 'Успешно удален Слайд-Шоу');
define('_slider_admin_edit', 'редактировать слайд-шоу изображение');
define('_slider_admin_edit_done', 'изменение были выполнены успешно!');
define('_slider_admin_error_empty_bezeichnung', 'Вы должны ввести имя');
define('_slider_admin_error_empty_url', 'Вы должны ввести ссылку');
define('_slider_admin_error_nopic', 'Вы должны загрузить картинку');
define('_slider_bezeichnung', 'описание');
define('_slider_new_window', 'В новом окне?');
define('_slider_pic', 'образ');
define('_slider_desc', 'описание');
define('_slider_position', 'позиция');
define('_slider_position_first', 'первым');
define('_slider_position_lazy', '<option value="lazy">- не изменять -</option>');
define('_slider_url', 'Ссылка');
define('_slider_show_title', 'Показать Название');
define('_forum_kat', 'категория');

define('_artikel_userimage', 'Собственные Изображение');
define('_artikelpic_del', 'Удалить изображение?');
define('_artikelpic_deleted', 'Изображение продукта успешно удаленo');

define('_news_userimage', 'Собственные Изображение');
define('_newspic_del', 'Удалить образ новостей?');
define('_newspic_deleted', 'образ новостей успешно удаленo');
define('_max', 'Макс.');

define('_cw_screenshot_deleted', 'Скриншот успешно удален');

define('_perm_galleryintern','Показать внутренняя галерея');
define('_perm_dlintern','Показать  Загрузки');

define('_config_url_linked_head', 'URLs связать');
define('_config_c_m_membermap', 'Все пользователи на карте');
define('_ts_settings_customicon', 'Скачать свои собственные иконки');
define('_ts_settings_showchannels', 'Показать только каналы с пользователями');
define('_ts_settings_showchannels_desc', 'Показать только каналы с пользователями');

define('_upload_error', 'Не удалось загрузить файл!');
define('_login_banned', 'Ваш аккаунт заблокирован администратором!');
define('_lobby_no_mymessages', '<a href="../user/?action=msg">У вас нет новых сообщений!</a>');

define('_perm_smileys', 'Редактировать смайлики');
define('_perm_protocol', 'См протокол администратора');
define('_perm_support', 'Смотрите страницу поддержки');
define('_perm_backup', 'Управление SQL-Backups');
define('_perm_clear', 'Очистить базу данных');
define('_perm_forumkats', 'Управление категории Форумыa');
define('_perm_impressum', 'Управление контактами');
define('_perm_config', 'изменений конфигурации Страницы');
define('_perm_positions', 'Управление рангами пользователей');
define('_perm_partners', 'Управление партнеров');
define('_perm_profile', 'Управление поля в профилe');

define('_dzcp_vcheck', 'DZCP Versions Checker сообщает вам, если новая версия вышла и ваша текущая версия актуальнa.<br><br><span class=fontBold>описание:</span><br><font color=#17D427>зеленый:</font>Up to Date!<br><font color=#FFFF00>желтый:</font> Не удается подключиться к серверу<br><font color=#FF0000>Rot:</font>Существует новое обновление!');
define('_cw_dont_exist', 'Clanwar-ID не существует!');


























































//Steam
define('_steam', 'Steam');
define('_steam_online', 'Онлайн');
define('_steam_offline', 'Последнее соединение: [time]');
define('_steam_offline_simple', 'Офлайн.');
define('_steam_in_game', 'В игре');
define('_config_steam_apikey', 'Steam API-Key');
define('_steam_apikey_info', 'Оформление Steam API-Keys: <a href="http://steamcommunity.com/dev/apikey/" target="_blank">steamcommunity.com</a>');

define('_years', 'Лет');
define('_year', 'год');
define('_months', 'месяцев');
define('_month', 'месяц');
define('_weeks', 'Недели');
define('_week', 'неделя');
define('_days', 'дней');
define('_day', 'день');
define('_hours', 'часов');
define('_hour', 'час');
define('_minutes', 'минут');
define('_minute', 'минута');
define('_seconds', 'секунды');
define('_second', 'секундa');

## ADDED / REDEFINED FOR 1.5 Final
define('_id_dont_exist', 'Который вы указали ID не существует!');
define('_perm_editts', 'Управление TeamSpeak сервер');


## ADDED / REDEFINED FOR 1.5.2
define('_button_title_del_account', 'удалить аккаунт?');
define('_confirm_del_account', 'вы действительно хотите удалить свой аккаунт ?');
define('_profil_del_account', 'удалить аккаунт?');
define('_profil_del_admin', '<b>удаление не возможно!</b>');
define('_info_account_deletet', 'Ваш аккаунт был успешно удален');
define('_news_get_timeshift', 'С задержкой Новости?'); ##xero##
define('_news_timeshift_from', 'объявления от:'); ##xero##
define('_config_gb_activ', 'гостевая книга');
define('_config_gb_activ_info', '<center>Объявление должно быть одобрено администратором.</center>');
define('_placeholder', 'местозаполнитель');
define('_menu_kats_head', 'Меню Категории');
define('_menu_add_kat', 'Добавить новою меню категорию');
define('_confirm_del_menu', 'вы действително хотите удалить эту категорию?');
define('_menu_edit_kat', 'категорию в меню исправить');
define('_menukat_updated', 'категорию в меню было успешно исправлено!');
define('_menukat_inserted', 'категорию в меню было успешно добавлено!');
define('_menukat_deleted', 'категорию в меню было успешно удаленно!');
define('_menu_visible', 'открыты для статуса');
define('_menu_kat_info', 'CSS-классы для ссылки автоматически выводятся из шаблона заполнителей.<br />Например, для заполнителя <i>[nav_main]</i> является класс CSS <i>a.navMain</i>');
define('_admin_sqauds_roster', 'Состав команды');
define('_admin_squads_nav_info', 'прямaя связь в навигации, которая привидет к полный вид команды.');
define('_admin_squads_teams', 'показать cостав команды');
define('_admin_squads_no_navi', 'Не добавлять');
define('_config_cache_info', 'здесь настраеваютса интервалы запрашиваеме Teamspeak и игровым сервером, информация так же считывается с кеша.');
define('_config_direct_refresh', 'Прямая переадресация');
define('_config_direct_refresh_info', 'Когда эта функция включена, после действий (например, записи в форума, новости и т.д.) будут направлены напрямую, вместо того чтобы информиреват админов.');
define('_cw_reset_button', 'Admin: сбросить статус игрока');
define('_cw_players_reset', 'Статус игрока был успешно сбросин!');
define('_eintrag_titel_forum', '<a href="[url]" title="Просмотреть сообщения"><span class="fontBold">#[postid]</span></a> am [datum] um [zeit]  [edit] [delete]');
define('_eintrag_titel', '<span class="fontBold">#[postid]</span> [datum] в [zeit]  [edit] [delete]');
## ADDED / REDEFINED FOR 1.5.1
define('_config_double_post', 'Двойное сообщение в форуме');
define('_config_fotum_vote', 'Голосования форума');
define('_config_fotum_vote_info', '<center>Показывает Голосования форума также под Опросоми на сайте.</center>');
## ADDED / REDEFINED FOR 1.5
define('_side_membermap', 'Карта пользователeй');
define('_installdir', "<tr><td colspan=\"15\" class=\"contentMainFirst\"><br /><center><b>Внимание! для безопасности!!</b><br><br>Пожалуйста, удалите сначала папку <b>'/_installer'</b>вашего веб-пространства. Только после этого администратор меню доступно!</center><br /></td></tr>");
define('_no_ts', 'Teamspeak не зарегистрирован');
define('_search_sites', 'Подстраницы');
define('_search_results', 'Результаты поиска');
define('_config_useradd_head', 'Создать пользователя');
define('_config_adduser', 'Добавить пользователя');
define('_uderadd_info', 'Пользователь был успешно добавлен');
define('_useradd_head', 'Создать нового пользователя');
define('_useradd_about', 'Данные пользователя');
define('_login_lostpwd', 'Забыли пароль');
define('_login_signup', 'Зарегистрировать');
define('_config_links', 'Линк(и)');
define('_no_server_navi', 'зарегистрировано ни одного сервера');
define('_vote_menu_no_vote', 'в данный момент опросов нет');
define('_no_top_match', 'в данный момент опросов нет матча');
define('_team_logo', 'Лого команды');
define('_cw_logo', 'Лого противникa');
define('_cw_screenshot', 'Скриншот');
define('_cw_admin_top_setted', 'ClanWar был успешно зарегистрирован в качестве топ матч!');
define('_cw_admin_top_unsetted', 'ClanWar был выписан из топ матч!');
define('_cw_admin_top_set', 'топ матч внесён');
define('_cw_admin_top_unset', 'с топ матч сняли');
define('_sq_banner', 'Команды баннер');
define('_forum_abo_title', 'Подписаться на эту тему');
define('_forum_vote', 'Опрос');
define('_admin_user_clanhead_info', 'Права здесь могут <u>дополнительно</u> к тем каторые групе дали.');
define('_config_positions_boardrights', 'внутренние Права на форумы');
define('_perm_awards', 'Управление наградами');
define('_perm_clankasse', 'Управление Денги клана');
define('_perm_contact', 'Контактный формуляр получять');
define('_perm_editkalender', 'правление записи календаря');
define('_perm_editserver', 'Управление серверами');
define('_perm_edittactics', 'Управление Тактикой');
define('_perm_forum', 'Администратор форума');
define('_perm_gb', 'Администратор гостевой книги');
define('_perm_links', 'Управление Линков');
define('_perm_newsletter', 'Рассылка новостей');
define('_perm_rankings', 'Управление рейтингов');
define('_perm_serverliste', 'Управление список серверов');
define('_perm_votesadmin', 'Управление Опросов');
define('_perm_artikel', 'Управление товаров');
define('_perm_clanwars', 'Управление Clan Wars');
define('_perm_downloads', 'Управление загрузками');
define('_perm_editor', 'Управление страницами');
define('_perm_editsquads', 'Управление отрядов');
define('_perm_editusers', 'Может редактировать пользователей');
define('_perm_gallery', 'Управление галереи');
define('_perm_glossar', 'Управление Глоссарий');
define('_perm_intnews', 'читать внутренние новости');
define('_perm_joinus', 'Получать заявки в клан (JoinUs)');
define('_perm_receivecws', 'Получать заявки нa ClanWar (FightUs)');
define('_perm_news', 'управление новостей');
define('_perm_shoutbox', 'Мини-чат Админ');
define('_perm_votes', 'См внутренние опросы');
define('_perm_gs_showpw', 'См пароль игрового сервера');
define('_config_positions_rights', 'Звания');
define('_admin_pos', 'Пользовательей Звания');
define('_awaycal', 'Список отсутствующих');
define('_clear_away', 'добавить чтобы oчистить cписок отсутствующих?');
define('_config_sponsors', 'Спонсоры');
define('_sponsors_admin_head', 'Спонсоры');
define('_sponsors_admin_add', 'Добавить спонсора');
define('_sponsor_added', 'Спонсор успешно добавлен!');
define('_sponsor_edited', 'Спонсор отредактирован успешно!');
define('_sponsor_deleted', 'Спонсор успешно удален!');
define('_sponsor_name', 'Спонсор');
define('_sponsors_admin_name', 'название');
define('_sponsors_admin_site', 'страница спонсора');
define('_sponsors_admin_addsite', 'На спонсора страницу');
define('_sponsors_admin_add_site', 'Баннер отображается на странице спонсора');
define('_sponsors_admin_upload', 'Загрузить изображение');
define('_sponsors_admin_url', 'Или: URL изображения');
define('_sponsors_admin_banner', 'Баннер ротации');
define('_sponsors_admin_addbanner', 'В ротации баннеров');
define('_sponsors_admin_add_banner', 'Вставить начало ротации баннеров');
define('_sponsors_admin_box', 'Спонсор (BOX)');
define('_sponsors_admin_addbox', 'В cпонсор (BOX)');
define('_sponsors_admin_add_box', 'Баннер будет отображаться в Спонсор (BOX)');
define('_sponsors_empty_name', 'Пожалуйста, введите название спонсора!');
define('_sponsors_empty_beschreibung', 'Вы должны указать описание!');
define('_sponsors_empty_link', 'Вы должны указать адрес ссылки!');
define('_site_away', 'Календарь отсутствующих');
define('_away_list', 'Список отсутствующих');
define('_config_c_away', 'Список отсутствующих');
define('_away_status_new', '<b><font color=orange>Добавлен</font></b>');
define('_away_status_now', '<b><font color=green>Актуально</font></b>');
define('_away_status_done', '<b><font color=red>Истек</font></b>');
define('_away_new', 'Сообщить');
define('_away_empty_titel', 'Пожалуйста, укажите причину');
define('_away_empty_reason', 'Пожалуйста, введите комментарию');
define('_away_error_1', 'Дата окончания не может быть датой начала!');
define('_away_error_2', 'Дата начала больше даты окончания!');
define('_away_to', 'до');
define('_away_to2', 'до');
define('_away_head', 'Отсутствия');
define('_away_new_head', 'Отсутствия зарегистрировать');
define('_away_reason', 'Причина');
define('_away_successful_added', 'Отсутствия было успешно добавленo!');
define('_away_on', 'в');
define('_away_info_head', 'Отсутствия инфо от');
define('_away_addon', 'Добавлено');
define('_away_formto', 'От - До:');
define('_away_back', 'Вернуться к списку');
define('_away_edit_head', 'Отсутствия редактировать');
define('_away_successful_del', 'запись oтсутствия была успешно удалена!');
define('_away_successful_edit', 'Отсутствие успешно изменили!');
define('_away_no_entry', '<tr><td align="center" class="contentMainFirst" colspan="10"><span class="smallfont">Отсутствия нету!</span></td></tr>');
define('_lobby_away', 'в данный момент отсутствиет');
define('_lobby_away_new', 'Сообщение об отсутствии');
define('_user_away', '<tr><td class="contentMainTop" width="25%" valign="top"><span class="fontBold">[naway]:</span></td><td class="contentMainFirst" width="75%">[away]</td>
</tr>');
define('_user_away_currently', '<tr><td class="contentMainTop" width="25%" valign="top"><span class="fontBold">[ncaway]:</span></td><td class="contentMainFirst" width="75%">[caway]</td></tr>
');
define('_user_away_new', '[user] - <b>Причина:</b> <a href="../away/?action=info&id=[id]">[what]</a><br />&nbsp;&nbsp;Отсутствиет от [ab] до [wieder]<br />');
define('_user_away_now', '[user] - <b>Причина:</b> <a href="../away/?action=info&id=[id]">[what]</a><br />&nbsp;&nbsp;еще [wieder] отсутствиет<br />');
define('_away_today', 'а тагже <b>Сегодня</b>');
define('_public', 'Oпубликовать');
define('_non_public', 'не oпубликовать');
define('_no_public', '<b>Неопубликованный</b>');
define('_no_events', 'нет событий');
define('_config_c_events', 'Меню: События');
define('_news_send', 'Добавить новость');
define('_news_send_source', 'Источник');
define('_news_send_titel', 'Новость предложено от [nick]');
define('_news_send_note', 'Сообщение или Замечание для редакторов');
define('_news_send_done', 'Спасибо! Вашa новость была передана успешно редактору');
define('_news_send_description', 'Уважаемый посетитель,<br /><br />в следующем разделе, можно найдены в Интернете или собственыe новости отправиь нам.<br /><br />они будут рассмотрены нашими администраторами и опубликованы.<br /><br />Огромное спасибо');
define('_contact_text_sendnews', '
[nick] представил нам предложение на Новости!<p>&nbsp;</p><p>&nbsp;</p>
<span class="fontBold">Ник:</span> [nick]<p>&nbsp;</p>
<span class="fontBold">Email:</span> [email]<p>&nbsp;</p>
<span class="fontBold">источник:</span> [hp]<p>&nbsp;</p><p>&nbsp;</p>
<span class="fontBold">Название:</span> [titel]<p>&nbsp;</p><p>&nbsp;</p>
<span class="fontUnder"><span class="fontBold">News:</span></span><p>&nbsp;</p>[text]<p>&nbsp;</p><p>&nbsp;</p>
<span class="fontUnder"><span class="fontBold">Внимание:</span></span><p>&nbsp;</p>[info]');

define('_msg_sendnews_user', '
<tr>
  <td align="center" class="contentMainTop"><span class="fontBold">Чтобы другие редакторы зналичто вы будете опубликовать эту новость,<br /> Пожалуйста, нажмите на кнопку. Спасибо</span></td>
</tr>
<tr>
  <td align="center" class="contentMainTop">
    <form action="" method="get" onsubmit="sendMe()">
      <input type="hidden" name="action" value="msg" />
      <input type="hidden" name="do" value="sendnewsdone" />
      <input type="hidden" name="id" value="[id]" />
      <input type="hidden" name="datum" value="[datum]" />
      <input id="contentSubmit" type="submit" class="submit" value="Подтвердить" />
    </form>
  </td>
</tr>');
define('_msg_sendnews_done', '
<tr>
  <td align="center" class="contentMainTop"><span class="fontRed">Эта новость уже/будет редактировать [user] !!!</span></td>
</tr>');
define('_send_news_done', 'Спасибо за подтверждение и настройке новости!');
define('_msg_all_leader', "Все лидером & Co-лидером");
define('_msg_leader', "Лидеру Отряда");
define('_pos_nletter', 'Включить в рассылку (Newsletter) Лидерa Отряда и Co-лидеров');
define('_clankasse_vwz', 'Назначение');
define('_pwd2', 'Введите пароль еще раз');
define('_wrong_pwd', 'Пароли не соответствуют');
define('_info_reg_valid_pwd', 'Вы успешно зарегистрировали, и можете войти !<br/><br/>Ваш Логин информация была отправлена для безопасности на адрес электронной почты. [email]');
define('_profil_pnmail', 'E-mail о новых сообщениях');
define('_admin_pn_subj', 'Тема: Личная-Email');
define('_admin_pn', 'PN-Email Шаблон');
define('_admin_fabo_npost_subj', 'Инфо: подписка форума - Новое сообщение');
define('_admin_fabo_pedit_subj', 'Инфо: подписка форума - Сообщение отредактировано');
define('_admin_fabo_tedit_subj', 'Инфо: подписка форума - Tread отредактирован');
define('_admin_fabo_npost', 'подписка форума: Новое сообщение - шаблон');
define('_admin_fabo_pedit', 'подписка форума: Сообщение отредактировано - шаблон');
define('_admin_fabo_tedit', 'подписка форума: редактировать шаблон');
define('_foum_fabo_checkbox', 'Подписаться на эту тему и получать по электронной почте новыe сообщение?');
define('_forum_fabo_do', 'E-Mail сообщение - успешно изменили!');
define('_user_link_fabo', '[nick]');
define('_forum_vote_del', 'удалить oпрос');
define('_forum_vote_preview', 'Здесь появляется опрос');
define('_forum_spam_text', '[ltext]<p>&nbsp;</p><p>&nbsp;</p><span class="fontBold"></span>[autor] добавил:<p>&nbsp;</p>[ntext]');
####################################################################################
define('_cw_screens_info', 'Только JPG или GIF файлы!');
define('_config_config', 'Общие настройки');
define('_config_dladmin', 'Загрузки');
define('_config_editor', 'Управление сайтом');
define('_config_konto', 'Копилка Клана');
define('_config_dl', 'Категории загружаемых файлов');
define('_config_nletter', 'Рассылка новостей');
define('_config_protocol', 'Админ протокол');
define('_config_serverlist', 'Список серверов');
define('_partnerbuttons_textlink', 'Текстовая ссылка');
define('_config_forum_subkats_add', '
    <form action="" method="get" onsubmit="DZCP.submitButton()">
      <input type="hidden" name="admin" value="forum" />
      <input type="hidden" name="do" value="newskat" />
      <input type="hidden" name="id" value="[id]" />
      <input id="contentSubmit" type="submit" class="submit" value="Добавить новую подкатегорию" />
    </form>
');
define('_msg_answer', '
    <form action="" method="get" onsubmit="DZCP.submitButton()">
      <input type="hidden" name="action" value="msg" />
      <input type="hidden" name="do" value="answer" />
      <input type="hidden" name="id" value="[id]" />
      <input id="contentSubmit" type="submit" class="submit" value="Oтветить" />
    </form>');
define('_user_new_erase', '<form method="get" action="" onsubmit="DZCP.submitButton()"><input type="hidden" name="action" value="erase" /><input id="contentSubmit" type="submit" name="submit" class="submit" value="удалить временные изменения" /></form>');
define('_klapptext_server_link', '<a href="javascript:DZCP.toggle(\'[id]\')"><img src="../inc/images/[moreicon].gif" alt="" id="img[id]">[link]</a>');
define('_profile_add', '<form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="admin" value="profile" />
      <input type="hidden" name="do" value="add" />
      <input id="contentSubmit" type="submit" class="submit" value="Добавить новое поле профиля" />
    </form>');
define('_clankasse_new', '<form action="" method="get" onsubmit="return(DZCP.submitButton())">
      <input type="hidden" name="action" value="admin" />
      <input type="hidden" name="do" value="new" />
      <input id="contentSubmit" type="submit" class="submit" value="Добавить комментарию" />
    </form>');
define('_admin_reg_info', 'Отрегулируйте, если кто обязан зарегистрироваться для одной из областей (Написать сообщения, скачать Загрузки и.т.д.)');
define('_config_c_floods_what', 'Здесь вы можете задать время в секундах,которые пользователь должен ждать, перед тем как написать нови пост');
define('_confirm_del_shout', 'Вы действительно уверены, удалил запись Мини-чатa?');
## ADDED FOR 1.4.5
define('_admin_smiley_exists', 'Yже cуществует смайлик с таким названием!');
## ADDED FOR 1.4.3
define('_download_last_date', 'Последнее скачивание');
## EDITED FOR 1.4.1
define('_ulist_normal', 'Ранги и уровeнь');
## ADDED FOR 1.4.1
define('_lobby_mymessages', '<a href="../user/?action=msg">У вас <span class="fontWichtig">[cnt]</span>  новых сообщений!</a>');
define('_lobby_mymessage', '<a href="../user/?action=msg">У вас <span class="fontWichtig">[cnt]</span> новое сообщение!</a>');
## EDIT/ADDED FOR 1.4
//Added
define('_protocol_action', 'Действие');
define('_protocol', 'Админ протокол');
define('_button_title_del_protocol', 'Полную yдалить протокол?');
define('_protocol_deleted', 'Полный протокол был успешно удален!');
define('_vote_no_answer', 'Вы должны выбрать ответ!');
define('_linkus_admin_edit', 'LinkUs редактировать');
define('_config_linkus', 'LinkUs');
define('_glossar_specialchar', 'Там не должно быть в имени специальных символов!');
define('_admin_gmaps_who', 'Откуда Игроки клана');
define('_gmaps_who_all', 'Просмотр всех пользователей');
define('_gmaps_who_mem', 'Только Игрокoв клана');
define('_urls_linked_info', 'Текстовые ссылки автоматически преобразуются в активные ссылки');
define('_membermap', 'Membermap');
define('_membermap_user', 'Membermap User');
define('_membermap_pic', 'Аватар пользователя');
define('_membermap_nick', 'Ник');
define('_membermap_rank', 'позиция');
define('_membermap_city', 'Место жительства');
define('_sponsoren', 'Спонсоры');
define('_downloads', 'Загрузки');
define('_cw', 'Клан Вары');
define('_awards', 'Награды');
define('_serverlist', 'Список серверов');
define('_ts', 'Teamspeak');
define('_galerie', 'Галерея');
define('_kontakt', 'Контакт');
define('_nachrichten', 'Сообщение');
define('_edit_profile', 'Изменить профиль');
define('_clankasse', 'Деньги Клана');
define('_taktiken', 'Тактика');
define('_user_new_newsc', '&nbsp;&nbsp;<a href="../news/?action=show&amp;id=[id]#lastcomment"><span class="fontWichtig">[cnt]</span> [eintrag] в <span class="fontWichtig">[news]</span></a><br />');
define('_config_c_teamrow', 'Меню: Команды');
define('_config_c_teamrow_info', '(Пользователи - в строке)');
define('_config_c_lartikel', 'Меню: последний товар');
define('_config_hover', 'Информация наведения мыши');
define('_config_seclogin', 'Проверка безопасности Логинa');
define('_config_hover_standard', 'Показать стандартную информацию');
define('_config_hover_all', 'Показать всю информацию');
define('_config_hover_cw', 'Только Клан Вары информацию Показать');
define('_shout_must_reg', 'Только для зарегистрированных пользователей!');
define('_error_vote_show', 'Это открытый опрос, вы можете заглянуть в ee');
define('_login_pwd_dont_match', 'Имя пользователя или пароль неверны !');
define('_sq_aktiv', 'активный');
define('_sq_inaktiv', 'неактивный');
define('_sq_sstatus', '<center>Определяет, будет ли команда указана для FightUs</center>');
define('_internal', 'Внутренне');
define('_sticky', 'Важно');
define('_lobby_new_cwc_1', 'Новая комментария в Клан Вары ');
define('_lobby_new_cwc_2', 'Новыe комментарии в Клан Вары ');
define('_admin_glossar_added', 'Глоссар был успешно зарегистрирован!');
define('_admin_glossar_edited', 'Глоссар был успешно изменен!');
define('_admin_glossar_deleted', 'Глоссар был успешно удален!');
define('_admin_error_glossar_desc', 'Вы должны указать объяснение!');
define('_admin_error_glossar_word', 'Вы должны указать термин!');
define('_admin_glossar_add', 'Добавить новый термин');
define('_config_glossar', 'Глоссар');
define('_config_gallery', 'галерея');
define('_glossar', 'Глоссар');
define('_admin_glossar', 'Админ: Глоссар');
define('_admin_fightus', 'получения FightUs?');
define('_misc', "разные");
define('_all', "все");
define('_glossar_link', 'Нажмите здесь <span class=fontBold>[word]</span> чтобы узнать больше!');
define('_glossar_head', 'Глоссар');
define('_glossar_bez', 'Oбозначение');
define('_glossar_erkl', 'Oбъяснение');
define('_admin_support_head', 'Информационная поддержка');
define('_admin_support_info', 'Важная информация для поддержки запроса в форуме <a href="http://www.dzcp.de" target="_blank">www.dzcp.de</a> для того, чтобы решить вашу проблему быстрее');
define('_config_support', 'Поддержка Инфо');
define('_search_con_or', 'ИЛИ');
define('_search_con_and', 'И');
define('_search_head', 'Функция поиска');
define('_search_word', 'Поиск ...');
define('_search_forum_all', 'Поиск во всех форумах');
define('_search_forum_hint', '(Нажав \'Ключ Ctrl\' вы можете выбрать несколько отдельных форумов)');
define('_search_for_area', 'Область поиска');
define('_search_type_full', 'полнотекстовый поиск');
define('_search_type_title', 'Только поиск Тем');
define('_search_type', 'Тип Поиска');
define('_search_type_autor', 'Найти писателя');
define('_search_type_text', 'Поиск текста и тем');
define('_search_in', 'Поиск в...');
define('_user_profile_of', 'Профиль пользователя ');
define('_sites_not_available', 'Страница, которую вы запросили, не существует!');
define('_wrote', 'написал');
define('_voted_head', 'Уже приняли участие в опросе');
define('_show_who_voted', 'Показать пользователей, которые уже проголосовали');
define('_no_live_status', 'Не живой запрос');
define('_comment_edited', 'Комментария была успешно изменена!');
define('_comments_edit', 'Редактировать комментарию');
define('_forum_post_where_preview', '<a href="javascript:void(0)">[mainkat]</a> <span class="fontBold">форум:</span> <a href="javascript:void(0)">[wherekat]</a> <span class="fontBold">тема:</span> <a href="javascript:void(0)">[wherepost]</a>');
define('_aktiv_icon', '<img src="../inc/images/active_ru.png" alt="" class="icon" />');
define('_inaktiv_icon', '<img src="../inc/images/inactive_ru.png" alt="" class="icon" />');
define('_pn_write_forum', '<a href="../user/?action=msg&amp;do=pn&amp;id=[id]"><img src="../inc/images/forum_pn_ru.png" alt="" title="Написать [nick] личное сообщение" class="icon" /></a>');
define('_uhr', '&nbsp;часов');
define('_kalender_admin_head', 'Календарь - События');
define('_smileys_specialchar', 'Там не должно быть каких-либо специальных символов или пробелов в BBCode!');
define('_award', 'Hаграда');
define('_preview', 'Предварительный просмотр');
define('_error_edit_post', 'Вы не авторизованы редактировать эту запись!');
define('_nletter_prev_head', 'Рассылка Предпросмотр');
define('_error_downloads_upload', 'Не удалось загрузить (файл слишком большой?)');
define('_news_comments_prev', '<a href="javascript:void(0)">0 Комментарии</a>');
define('_only_for_admins', ' (видят только админы)');
define('_content', 'Cодержание');
define('_rootadmin', 'Root Админ');
define('_gb_edit_head', 'Редактировать запись в гостевой книге');
define('_gb_edited', 'Запись в гостевой книге успешно отредактировал');
define('_nletter', 'Рассылка');
define('_subject', 'тема');
define('_server_admin_qport', 'Optional: Queryport');
define('_admin_server_nostatus', 'Нет запроса в прямом эфире');
define('_nletter_head', 'Начать Рассылкy');
define('_squad', 'команда');
define('_confirm_del_cw', 'Этот Clanwar  на самом деле удалить?');
define('_confirm_del_vote', 'Этот Опрос  на самом деле удалить?');
define('_confirm_del_dl', 'Этот Download на самом деле удалить?');
define('_confirm_del_galpic', 'Эту Картинку  на самом деле удалить?');
define('_confirm_del_gallery', 'Эту Галерию на самом деле удалить?');
define('_confirm_del_entry', 'Это сообщение на самом деле удалить?');
define('_confirm_del_navi', 'Действительно удалить Link?');
define('_confirm_del_profil', 'Действительно удалить поля в профилe? \n Все данные вводимыe будут потеряны!');
define('_confirm_del_smiley', 'Действительно удалить Smiley');
define('_confirm_del_kat', 'Действительно удалить категорию?');
define('_confirm_del_news', 'Действительно удалить Новость?');
define('_confirm_del_site', 'Действительно удалить страницу?');
define('_confirm_del_server', 'Действительно удалить сервер?');
define('_confirm_del_artikel', 'Действительно удалить товар?');
define('_confirm_del_team', 'Действительно удалить команду?');
define('_confirm_del_award', 'Действительно удалить награду?');
define('_confirm_del_ranking', 'Действительно удалить Рейтинг?');
define('_confirm_del_link', 'Действительно удалить ссылку?');
define('_confirm_del_sponsor', 'Действительно удалить спонсора?');
define('_confirm_del_kalender', 'Действительно удалить событие?');
define('_confirm_del_taktik', 'Действительно удалить тактику?');
define('_link_type', 'Тип связи');
define('_sponsor', 'Cпонсор');
//-----------------------------------------------
define('_main_info', 'Здесь вы можете настроить общие вещи, заголовоки страницы, шаблон , язык по умолчанию, и т.д ...');
define('_admin_eml_head', 'Emai шаблоны');
define('_admin_eml_info', 'Здесь вы можете редактировать шаблоны писем. Убедитесь, что вы не удалите местозаполнитель в скобках!');
define('_admin_reg_subj', 'Тема: Регистрация');
define('_admin_pwd_subj', 'Тема: Забыл пароль');
define('_admin_nletter_subj', 'Тема: Рассылка');
define('_admin_reg', 'шаблон при регистрации');
define('_admin_pwd', 'шаблон при Забыл пароль');
define('_admin_nletter', 'шаблон при Рассылки');
define('_result', 'Заключительный счет');
define('_opponent', 'Противник');
define('_played_at', 'Играл');
define('_error_empty_nachricht', 'Вы должны ввести сообщение!');
define('_links_empty_text', 'Вы должны указать адрес баннера!');
define('_login_secure_help', 'Введите двузначный код в поле (антиспам) !');
define('_online_head_guests', 'Гости онлайн');
define('_admin_first', 'первой');
define('_admin_squads_nav', 'навигация');
define('_admin_squad_show_info', '<center>Определяет Обзор Команды по умолчанию свернут или развернут</center>');
//Edited
define('_config_c_gallerypics_what', 'Максимальное количество фотографий в галерее пользователя');
define('_dl_getfile', '[file] Скачать');
define('_partners_link_add', 'Кнопку партнера Добавить');
define('_config_forum_kats_add', 'Добавить новую категорию');
define('_config_c_lnews', 'Меню: Последние новости');
define('_msg_new', 'Отправить новое сообщение');
define('_gallery_show_admin', 'Добавить галереию');
define('_dl_titel', '<span class="fontBold">[name]</span> - [cnt] [file]');
define('_config_artikel', 'Артикль');
define('_config_forum', 'Категории Форума');
define('_config_server', 'сервер');
define('_config_serverliste', 'Список серверов');
define('_config_squads', 'Команды');
define('_config_backup', 'Резервноя копия базы данных');
define('_config_news', 'Новости / статьи категории');
define('_config_positions', 'Имя Рангa');
define('_config_allgemein', 'конфигурация');
define('_config_impressum', 'Выходные данные');
define('_config_clankasse', 'Деньги Клана');
define('_config_downloads', 'Категорий загрузок');
define('_config_newsadmin', 'Новости');
define('_config_filebrowser', 'File Editor');
define('_config_navi', 'Навигация');
define('_config_online', 'Управление страниц');
define('_config_partners', 'Партнер Кнопки');
define('_config_clear', 'Oчистки базы данных');
define('_config_smileys', 'Смайли редактор');
define('_config_profile', 'Профиль поля');
define('_config_votes', 'Опросы');
define('_config_cw', 'Clan Wars');
define('_config_awards', 'Награды');
define('_config_rankings', 'Рейтинг');
define('_config_kalender', 'Календарь');
define('_config_einst', 'Настройки');
define('_profil_sig', 'Подпись в форуме');
define('_akt_version', 'DZCP версия');
define('_forum_searchlink', '- <a href="../search/">поиск в форумах</a> -');
define('_msg_deleted', 'Сообщение удалено успешно!');
define('_info_reg_valid', 'Вы успешно зарегистрировались!<br />Ваш пароль был отправлен на адрес электронной почты [email].');
define('_edited_by', '<br /><br /><i>Последний раз редактировалал [autor] в [time]</i>');
define('_linkus_empty_text', 'вы должны указать ссылку на баннер!');
define('_gb_titel', '<span class="fontBold">#[postid]</span> von [nick] пишет:[email] [hp] в [datum] [zeit][uhr] [edit] [delete] [comment] [public]');
define('_gb_titel_noreg', '<span class="fontBold">#[postid]</span> von <span class="fontBold">[nick] пишет:</span> [email] [hp] в [datum] um [zeit][uhr]  [edit] [delete] [comment] [public]');
define('_empty_news_title', 'Вы должны указать URL баннера!');
define('_member_admin_votes', 'видет внутреннее опросы');
define('_member_admin_votesadmin', 'Администраторам: Опросы');
define('_msg_global_all', 'всем юзерам');
define('_smileys_info', 'Вы можете все смайлы через FTP в папку <span class="fontItalic">./inc/images/smileys/</span> Загрузить! имя файла должно быть таким же, как и в BBCodes. например: dzcp.gif = :dzcp:');
define('_pos_empty_kat', 'Вы должны указать обозначение ранга!');
define('_forum_lastpost', '<a href="?action=showthread&amp;id=[tid]&amp;page=[page]#p[id]"><img src="../inc/images/forum_lpost_ru.gif" alt="" title="К последней записи" class="icon" /></a>');
define('_forum_addpost', '<a href="?action=post&amp;do=add&amp;kid=[kid]&amp;id=[id]"><img src="../inc/images/forum_reply_ru.gif" alt="" title="Новая запись" class="icon" /></a>');
define('_pn_write', '<a href="../user/?action=msg&amp;do=pn&amp;id=[id]"><img src="../inc/images/pn_ru.gif" alt="" title="Написать сообщение [nick]" class="icon" /></a>');
define('_forum_new_thread', '<a href="?action=thread&amp;do=add&amp;kid=[id]"><img src="../inc/images/forum_new_ru.gif" alt="" title="Создать новую тему" class="icon" /></a>');
//--------------------------------------------\\
define('_error_invalid_regcode', 'Написанные Вами цифры не соответствуют защитным цифрам на картинке!');
define('_welcome_guest', ' <img src="../inc/images/flaggen/nocountry.gif" alt="" class="icon" /> <a class="welcome" href="../user/?action=register">гость</a>');
define('_online_head', 'Юзер онлайн');
define('_online_whereami', 'Сектор');
define('_back', '<a href="javascript: history.go(-1)" class="files">Назад</a>');
define('_contact_text_fightus', '
Кто-то заполнил контакт на Fightus!<br />
Каждый авторизованный администратор получил это сообщение!<br /><br />
<span class="fontBold">Коллектив:</span> [squad]<br /><br />
<span class="fontUnder"><span class="fontBold">Контактное лицо:</span></span><br />
<span class="fontBold">Ник:</span> [nick]<br />
<span class="fontBold">eMail:</span> [email]<br />
<span class="fontBold">ICQ-Nr.:</span> [icq]<br /><br />
<span class="fontBold"><span class="fontUnder">Данные Клана:</span></span><br />
<span class="fontBold">Название Клана:</span> [clan]<br />
<span class="fontBold">Страница:</span> [hp]<br />
<span class="fontBold">Игра:</span> [game]<br />
<span class="fontBold">XнаX:</span> [us] vs. [to]<br />
<span class="fontBold">Наша Карта:</span> [map]<br />
<span class="fontBold">Число:</span> [date]<br /><span class="fontUnder">
<span class="fontBold">Комментар:</span></span><br />[text]');
## EDITED/ADDED FOR v 1.3.3
define('_cw_info', 'Администратор (FightUs) также принимает запросы !');
define('_level_info', 'внимание: с уровня "Админ" может изменить только корень Админ  (тот, кто установил клан портал)!<br />он получает <span class="fontUnder">неограниченный</span> доступ ко всем районам!');
## EDITED FOR v 1.3.1
define('_related_links','Связанные ссылки:');
define('_cw_admin_lineup_info','Разделяйте имена c запятой!');
define('_profil_email2', 'E-mail #2');
define('_profil_email3', 'E-mail #3');
## Allgemein ##
define('_button_title_del', 'Удалить');
define('_button_title_edit', 'Изменить');
define('_button_title_zitat', 'Цитировать это сообщение');
define('_button_title_comment', 'Сообщение комментиревать');
define('_button_title_menu', 'Ввести в меню');
define('_button_value_add', 'Ввести');
define('_button_value_addto', 'Добавить');
define('_button_value_edit', 'Изменить');
define('_button_value_search', 'Поиск');
define('_button_value_search1', 'Найти');
define('_button_value_vote', 'Проголосовать');
define('_button_value_show', 'Показать');
define('_button_value_send', 'Отправить');
define('_button_value_reg', 'Регистрировать');
define('_button_value_msg', 'Отправить сообщение');
define('_button_value_nletter', 'Отослать Рассылкy ');
define('_button_value_config', 'Сохранить конфигурацию');
define('_button_value_clear', 'Oчистить базу данных');
define('_button_value_save', 'Сохранить');
define('_button_value_upload', 'Загрузить');
define('_editor_from', 'От');
define('intern', '<span class="fontWichtig">Внутренние</span>');
define('_comments_head', 'Комментарии');
define('_click_close', 'Закрыть');
## приветствие ##
define('_welcome_18', 'Добрый вечер,');
define('_welcome_13', 'Добрый день,');
define('_welcome_11', 'Добро пожаловать,');
define('_welcome_5', 'Доброе утро,');
define('_welcome_0', 'Спокойной ночи,');
## месяцы ##
define('_jan', 'Январь');
define('_feb', 'февраль');
define('_mar', 'Март');
define('_apr', 'Апрель');
define('_mai', 'Май');
define('_jun', 'Июнь');
define('_jul', 'Июль');
define('_aug', 'Август');
define('_sep', 'Сентябрь');
define('_okt', 'Октябрь');
define('_nov', 'Ноябрь');
define('_dez', 'Декабрь');
## Список стран ##
define('_country_list', '
<option value="eg"> Египет</option>
<option value="et"> Эфиопия</option>
<option value="al"> Албания</option>
<option value="dz"> Алжир</option>
<option value="ao"> Ангола</option>
<option value="ar"> Аргентина</option>
<option value="am"> Армения</option>
<option value="aw"> Аруба</option>
<option value="au"> Австралия</option>
<option value="az"> Азербайджан</option>
<option value="bs"> Багамские острова</option>
<option value="bh"> Бахрейн</option>
<option value="bd"> Бангладеш</option>
<option value="bb"> Барбадос</option>
<option value="be"> Бельгия</option>
<option value="bz"> Белиз</option>
<option value="bj"> Бенин</option>
<option value="bm"> Бермудские острова</option>
<option value="bt"> Бутан</option>
<option value="bo"> Боливия</option>
<option value="ba"> Босния и Герцеговина</option>
<option value="bw"> Ботсвана</option>
<option value="br"> Бразилия</option>
<option value="bn"> Бруней-Даруссалам</option>
<option value="bg"> Болгария</option>
<option value="bf"> Буркина-Фасо</option>
<option value="bi"> Бурунди</option>
<option value="cv"> Кабо-Верде</option>
<option value="ky"> Каймановы острова</option>
<option value="cl"> Чили</option>
<option value="cn"> Китай</option>
<option value="ck"> острова Кука</option>
<option value="cr"> Коста-Рика</option>
<option value="ci"> Кот-дИвуар</option>
<option value="dk"> Дания</option>
<option value="de"> Германия</option>
<option value="ec"> Эквадор</option>
<option value="er"> Эритрея</option>
<option value="ee"> Эстония</option>
<option value="fo"> Фарерские острова</option>
<option value="fj"> Фиджи</option>
<option value="fi"> Финляндия</option>
<option value="fr"> Франция</option>
<option value="pf"> Французская Полинезия</option>
<option value="ga"> Габон</option>
<option value="ge"> Грузия</option>
<option value="gi"> Гибралтар</option>
<option value="gr"> Греция</option>
<option value="uk"> Великобритания</option>
<option value="gl"> Гренландия</option>
<option value="gp"> Гваделупа</option>
<option value="gu"> Гуам</option>
<option value="gt"> Гватемала</option>
<option value="gy"> Гайана</option>
<option value="ht"> Гаити</option>
<option value="hk"> Гонконг</option>
<option value="is"> Исландия</option>
<option value="in"> Индия</option>
<option value="id"> Индонезия</option>
<option value="ir"> Иран</option>
<option value="iq"> Ирак</option>
<option value="ie"> Ирландия</option>
<option value="il"> Израиль</option>
<option value="it"> Италия</option>
<option value="jm"> Ямайка</option>
<option value="jp"> Япония</option>
<option value="jo"> Иордания</option>
<option value="yu"> Югославия</option>
<option value="kh"> Камбоджа</option>
<option value="cm"> Камерун</option>
<option value="ca"> Канада</option>
<option value="qa"> Катар</option>
<option value="kz"> Казахстан</option>
<option value="ke"> Кения</option>
<option value="ki"> Кирибати</option>
<option value="co"> Колумбия</option>
<option value="cg"> Конго</option>
<option value="hr"> Хорватия</option>
<option value="cu"> Куба</option>
<option value="kg"> Киргизия</option>
<option value="lv"> Латвия</option>
<option value="lb"> Ливан</option>
<option value="ly"> Ливия</option>
<option value="li"> Лихтенштейн</option>
<option value="lt"> Литва</option>
<option value="lu"> Люксембург</option>
<option value="mo"> Макао</option>
<option value="mk"> Македония</option>
<option value="mg"> Мадагаскар</option>
<option value="my"> Малайзия</option>
<option value="ma"> Марокко</option>
<option value="mx"> Мексика</option>
<option value="md"> Молдова</option>
<option value="mc"> Монако</option>
<option value="mn"> Монголия</option>
<option value="ms"> Монтсеррат</option>
<option value="mz"> Мозамбик</option>
<option value="na"> Намибия</option>
<option value="nr"> Науру</option>
<option value="np"> Непал</option>
<option value="nc"> Новая Каледония</option>
<option value="nz"> Новая Зеландия</option>
<option value="nl"> Нидерланды</option>
<option value="an"> Нидерландские Антильские острова</option>
<option value="kp"> Северная Корея</option>
<option value="nf"> Остров Норфолк</option>
<option value="mp"> Северные Марианские острова</option>
<option value="no"> Норвегия</option>
<option value="om"> Оман</option>
<option value="at"> Австрия</option>
<option value="tp"> Восточный Тимор</option>
<option value="pk"> Пакистан</option>
<option value="pa"> Панама</option>
<option value="py"> Парагвай</option>
<option value="pe"> Перу</option>
<option value="ph"> Филиппины</option>
<option value="pl"> Польша</option>
<option value="pt"> Португалия</option>
<option value="pr"> Пуэрто-Рико</option>
<option value="ro"> Румыния</option>
<option value="ru"> Россия</option>
<option value="lc"> Сент-Люсия</option>
<option value="pm"> Сен-Пьер и Микелон</option>
<option value="ws"> Самоа</option>
<option value="sa"> Саудовская Аравия</option>
<option value="sx"> Шотландия</option>
<option value="sl"> Сьерра-Леоне</option>
<option value="sg"> Сингапур</option>
<option value="sk"> Словакия</option>
<option value="si"> Словения</option>
<option value="sb"> Соломоновы Острова</option>
<option value="so"> Сомали</option>
<option value="za"> Южная Африка</option>
<option value="kr"> Южная Корея</option>
<option value="es"> Испания</option>
<option value="lk"> Шри Ланка</option>
<option value="sd"> Судан</option>
<option value="sr"> Суринам</option>
<option value="se"> Швеция</option>
<option value="ch"> Швейцария</option>
<option value="sy"> Сирия</option>
<option value="tw"> Тайвань</option>
<option value="tz"> Танзания</option>
<option value="th"> Таиланд</option>
<option value="tg"> Того</option>
<option value="to"> Тонга</option>
<option value="tt"> Тринидад и Тобаго</option>
<option value="cz"> Чехии</option>
<option value="tn"> Тунис</option>
<option value="tr"> Турция</option>
<option value="tc"> Острова Теркс и Кайкос</option>
<option value="tv"> Тувалу</option>
<option value="ug"> Уганда</option>
<option value="ua"> Украина</option>
<option value="hu"> Венгрия</option>
<option value="uy"> Уругвай</option>
<option value="us"> США</option>
<option value="ve"> Венесуэла</option>
<option value="va"> Ватикан</option>
<option value="ae"> Объединенные Арабские Эмираты</option>
<option value="vn"> Вьетнам</option>
<option value="vg"> Виргинские острова, Британские</option>
<option value="vi"> Виргинские острова, У. С.</option>
<option value="by"> Беларусь</option>
<option value="ye"> Йемен</option>
<option value="zm"> Замбия</option>
<option value="cf"> Центральный Африкан. республика</option>
<option value="cy"> Кипр</option>');
## Глобальные Звания ##
define('_status_banned', 'заблокированный');
define('_status_unregged', 'незарегистрированный');
define('_status_user', 'пользователь');
define('_status_trial', 'рекрут');
define('_status_member', 'юзер');
define('_status_admin', 'администратор');
## Пользователи ##
define('_acc_banned', 'заблокирован');
define('_ulist_acc_banned', 'таблица блокированных');
## Логин ##
define('_login_login', 'Логин!');
## Навигация: Календарь ##
define('_kal_birthday', 'День рождения от ');
define('_kal_cw', 'Clanwar против ');
define('_kal_event', 'Событие: ');
## Linkus ##
//-> Генеральная
define('_linkus_head', 'LinkUs');
//-> Админ
define('_linkus_admin_head', 'Управление LinkUs');
define('_linkus_link', 'ссылка на страницу');
define('_linkus_bsp_target', 'http://www.domain.tld');
define('_linkus_bsp_bannerurl', 'http://www.domain.tld/banner.jpg');
define('_linkus_bsp_desc', 'Описание');
define('_linkus_beschreibung', 'название');
define('_linkus_text', 'Ссылка на Баннер');
define('_linkus_empty_beschreibung', 'Вы должны указать название тега!');
define('_linkus_empty_link', 'Вы должны указать ссылку URL!');
define('_linkus_added', 'LinkUs успешно добавлен!');
define('_linkus_edited', 'LinkUs был успешно изменен!');
define('_linkus_deleted', 'LinkUs был успешно удален!');
define('_linkus', 'LinkUs');
## новости ##
define('_news_kommentar', 'Комментар');
define('_news_kommentare', 'Комментарии');
define('_news_viewed', '[<span class="fontItalic">[viewed] Хиты</span>]');
define('_news_archiv', '<a href="?action=archiv">Архив</a>');
define('_news_comment', '<a href="?action=show&amp;id=[id]">[comments] Комментария</a>');
define('_news_comments', '<a href="?action=show&amp;id=[id]">[comments] Комментарии</a>');
define('_news_comments_write_head', 'Добавить комментарию');
define('_news_archiv_sort', 'Сортировать по');
define('_news_archiv_head', 'Архив новостей');
define('_news_kat_choose', 'Выберите категорию');
## статья ##
define('_artikel_comments_write_head', 'Написать новыю комментарию');
## Форум ##
define('_forum_head', 'Форум');
define('_forum_topic', 'Topic');
define('_forum_subtopic', 'Подзаголовок');
define('_forum_lpost', 'Последнее сообщение');
define('_forum_threads', 'Темы');
define('_forum_thread', 'Тема');
define('_forum_posts', 'Ответы');
define('_forum_cnt_threads', '<span class="fontBold">Количество потоков:</span> [threads]');
define('_forum_cnt_posts', '<span class="fontBold">Количество сообщений:</span> [posts]');
define('_forum_admin_head', 'Admin');
define('_forum_admin_addsticky', 'Марк. <span class="fontWichtig">важно</span> ?');
define('_forum_katname_intern', '<span class="fontWichtig">Внутренние:</span> [katname]');
define('_forum_sticky', '<span class="fontWichtig">Важно:</span>');
define('_forum_subkat_where', '<a href="../forum/">[mainkat]</a> <span class="fontBold">форум:</span> <a href="?action=show&amp;id=[id]">[where]</a>');
define('_forum_head_skat_search', 'Искать в этой категории');
define('_forum_head_threads', 'Threads');
define('_forum_replys', 'Ответить');
define('_forum_thread_lpost', '[nick]<br /> [date] пишет');
define('_forum_new_thread_head', 'Создать новую тему');
define('_empty_topic', 'Вы должны указать тему!');
define('_forum_newthread_successful', 'поток успешно записон в форум!');
define('_forum_new_post_head', 'Добавить сообщение в форум');
define('_forum_newpost_successful', 'Сообщение было успешно записоно в форум!');
define('_posted_by', '<span class="fontBold">&raquo;</span> ');
define('_forum_post_where', '<a href="../forum/">[mainkat]</a> <span class="fontBold">Форум:</span> <a href="?action=show&amp;id=[kid]">[wherekat]</a> <span class="fontBold">Thread:</span> <a href="?action=showthread&amp;id=[tid]">[wherepost]</a>');
define('_forum_lpostlink', 'Последнее сообщение');
define('_forum_user_posts', '<span class="fontBold">Posts:</span> [posts]');
define('_sig', '<br /><br /><hr />');
define('_error_forum_closed', 'Эта тема закрыта!');
define('_forum_search_head', 'поиск в форумах');
define('_forum_edit_post_head', 'Редактировать сообщение');
define('_forum_edit_thread_head', 'Редактировать нить');
define('_forum_editthread_successful', 'Тема успешно отредактирована!');
define('_forum_editpost_successful', 'Запись успешно отредактирована!');
define('_forum_delpost_successful', 'Запись успешно удалена!');
define('_forum_admin_open', 'Тема открыта');
define('_forum_admin_delete', 'Удалить тему?');
define('_forum_admin_close', 'Тема закрыта');
define('_forum_admin_moveto', 'Переместить тему в:');
define('_forum_admin_thread_deleted', 'Тема была успешно удалена!');
define('_forum_admin_do_move', 'Тема была успешно обработана<br />и перемещена в <span class="fontWichtig">[kat]</span> категорию!');
define('_forum_admin_modded', 'Тема была успешно завершена!');
define('_forum_search_what', 'поиск');
define('_forum_search_kat', 'в категории');
define('_forum_search_suchwort', 'Поиск по ключевой фразе');
define('_forum_search_inhalt', 'содержание');
define('_forum_search_kat_all', 'всех категорий');
define('_forum_search_results', 'Результаты поиска');
define('_forum_online_head', 'На форуме сейчас:');
define('_forum_nobody_is_online', 'в данное время - ни одного пользователья в форуме!');
define('_forum_nobody_is_online2', 'в данное время - ни одного пользователья в форуме кроме тебя!');
## Гостевая книга ##
define('_gb_delete_successful', 'Запись была успешно удалена!');
define('_gb_head', 'Гостевая книга');
define('_gb_add_head', 'Ввести новую запись в гостевой книге');
define('_gb_eintragen', '<a href="#eintragen">Ввести</a>');
define('_gb_entry_successful', 'Ваша Запись была направлена ​​на активизацию - Администратору!');
define('_gb_addcomment_head', 'Комментарии');
define('_gb_addcomment_headgb', 'Запись в Гостевой');
define('_gb_comment_added', 'Комментария была успешно добавлена!');
## календарь ##
//-> Генеральная
define('_kalender_head', 'Календарь');
define('_kalender_month_select', '<option value="[i]" [sel]>[month]</option>');
define('_kalender_year_select', '<option value="[i]" [sel]>[year]</option>');
define('_montag', 'Понедельник');
define('_dienstag', 'Вторник');
define('_mittwoch', 'Среда');
define('_donnerstag', 'Четверг');
define('_freitag', 'Пятница');
define('_samstag', 'Суббота');
define('_sonntag', 'Воскресенье');
//-> события
define('_kalender_events_head', 'События в [datum]');
define('_kalender_uhrzeit', 'Время');
//-> Админ
define('_kalender_admin_head_add', 'Добавить событие');
define('_kalender_admin_head_edit', 'Редактировать событие');
define('_kalender_event', 'событие');
define('_kalender_error_no_time', 'Укажите дату и время!');
define('_kalender_error_no_title', 'Вы должны указать название!');
define('_kalender_error_no_event', 'Вы должны описать событие!');
define('_kalender_successful_added', 'Вы успешно зарегистрировали событие!');
define('_kalender_successful_edited', 'Вы успешно изменили событие!');
define('_kalender_deleted', 'событие успешно удалено!');
## Опросы ##
define('_error_vote_closed', 'Этот опрос закрыт!');
define('_votes_admin_closed', 'Закрыть опрос');
define('_votes_head', 'Опросы');
define('_votes_stimmen', 'Голоса');
define('_votes_intern', '<span class="fontWichtig">Внутренние:</span> ');
define('_votes_results_head', 'Результаты опроса');
define('_votes_results_head_vote', 'Ответы');
define('_vote_successful', 'Вы успешно приняли участие в опросе!');
define('_votes_admin_head', 'Добавить опрос');
define('_votes_admin_question', 'вопрос');
define('_votes_admin_answer', 'вариант ответа');
define('_empty_votes_question', 'Вы должны определить вопрос!');
define('_empty_votes_answer', 'не меньше 2 ответа!');
define('_votes_admin_intern', 'Внутренний опрос');
define('_vote_admin_successful', 'Опрос был успешно зарегистрирован!');
define('_vote_admin_delete_successful', 'Опрос был успешно удален!');
define('_vote_admin_successful_menu', 'Опрос теперь доступен в меню!');
define('_vote_admin_menu_isintern', 'Внутренний опрос меню установить невозможно!');
define('_vote_legendemenu', 'Опрос в меню <br /> (нажмите на иконку - включить/выключить)');
define('_votes_admin_edit_head', 'Редактировать опрос');
define('_vote_admin_successful_edited', 'Опрос был успешно изменен!');
define('_vote_admin_successful_menu1', 'Опрос был успешно удален из меню!');
define('_error_voted_again', 'Вы уже принимали участие в этом опросе!');
## Ссылки / Спонсоры ##
define('_links_head', 'Ссылки');
define('_links_admin_head', 'Добавить новую ссылку');
define('_links_admin_head_edit', 'Редактировать ссылку');
define('_links_link', 'адрес связи');
define('_links_beschreibung', 'Описание ссылки');
define('_links_art', 'Тип связи');
define('_links_admin_textlink', 'Текстовая ссылка');
define('_links_admin_bannerlink', 'Ссылка на Баннер');
define('_links_text', 'Адрес на Баннер');
define('_links_empty_beschreibung', 'Вы должны указать описание на ссылку!');
define('_links_empty_link', 'Вы должны указать адрес ссылки!');
define('_link_added', 'Ссылка была успешно добавлена!');
define('_link_edited', 'Ссылка была успешно изменена!');
define('_link_deleted', 'Ссылка успешно удалена!');
define('_sponsor_head', 'Спонсоры');
## Загрузки ##
define('_downloads_head', 'Загрузки');
define('_downloads_download', 'скачать');
define('_downloads_admin_head', 'Добавить загрузки');
define('_downloads_nofile', '<option value="lazy">- нет файла -</option>');
define('_downloads_admin_head_edit', 'Редактировать загрузки');
define('_downloads_lokal', 'Локальный файл');
define('_downloads_exist', 'файл');
define('_downloads_name', 'Имя загрузки');
define('_downloads_url', 'файл');
define('_downloads_kat', 'категория');
define('_downloads_empty_download', 'Вы должны указать названия загрузки!');
define('_downloads_empty_url', 'Вы должны указать файл!');
define('_downloads_empty_beschreibung', 'Вы должны указать описание!');
define('_downloads_added', 'Загрузка успешно добавлено!');
define('_downloads_edited', 'Загрузка успешно изменена!');
define('_downloads_deleted', 'Загрузка успешно удалена!');
define('_dl_info', 'Информация Загрузки');
define('_dl_file', 'файл');
define('_dl_besch', 'описание');
define('_dl_info2', 'информация о файле');
define('_dl_size', 'Размер Файла');
define('_dl_speed', 'скорость');
define('_dl_traffic', 'Используемый трафик');
define('_dl_loaded', 'колличество скачиваний');
define('_dl_date', 'дата загрузки');
define('_dl_wait', 'Загрузить этот файл: ');
## Команды ##
define('_member_squad_head', 'Команды');
define('_member_squad_no_entrys', '<tr><td align="center"><span class="fontBold">Нет зарегистрированных пользователей</span></td></tr>');
define('_member_squad_weare', 'Нас в общем <span class="fontBold">[cm] </span> и разделены на  <span class="fontBold">[cs] Команд(ы)</span>');
## Clanwars ##
define('_cw_comments_head', 'Clanwar Комментарии');
define('_cw_comments_add', 'Добавить комментарию');

define('_cw_head_details', 'Clanwar детали');
define('_cw_head_results', 'Результаты');
define('_cw_head_lineup', 'Состав команды на игру');
define('_cw_head_glineup', 'Противника состав команды на игру');
define('_cw_head_admin', 'Администратор(ы)');
define('_cw_head_squad', 'Команда');
define('_cw_bericht', 'Рапорт');
define('_cw_maps', 'Maps');
define('_cw_serverpwd', '
<tr>
  <td class="contentMainTop"><span class="fontBold">Пароль сервера:</span></td>
  <td class="contentMainFirst" colspan="2" align="center">[cw_serverpwd]</td>
</tr>');
define('_cw_players_head', 'Статус Игрока');
define('_cw_status_set', 'Ваш статус успешно изменен!');
define('_cw_players_play', 'Можете/хотите ли вы играть?');
define('_cw_player_dont_want', '<span class="fontRed">Не хочу играть</span>');
define('_cw_player_want', '<span class="fontGreen">Хочу играть</span>');
define('_cw_player_dont_know', 'Пока не знаю');
define('_cw_admin_head', 'Добавить clanwar');
define('_cw_admin_head_edit', 'Clanwar Изменить');
define('_cw_admin_info', 'Пока не введен резултат, Вар отображается как "Next War"!');
define('_cw_admin_gegnerstuff', 'Информация о противнике');
define('_cw_admin_clantag', 'Клан тег');
define('_cw_admin_warstuff', 'Clan War Информация');
define('_cw_admin_maps', 'Карты');
define('_cw_admin_serverip', 'ServerIP');
define('_cw_admin_empty_gegner', 'Вы должны указать имя противника!');
define('_cw_admin_empty_clantag', 'Вы должны указать клан тег противника!');
define('_cw_admin_deleted', 'Clanwar был успешно удален!');
define('_cw_admin_added', 'Clanwar был успешно добавлен!');
define('_cw_admin_edited', 'Clanwar был успешно изменен!');
define('_cw_admin_head_squads', 'Информация о команде');
define('_cw_admin_head_country', 'Страна');
define('_cw_head_statstik', 'Cтатистика');
define('_cw_gespunkte', 'Общий счет');
define('_cw_stats_ges_wars', '<span class="fontText">Наш клан играл в общем <span class="fontBold">[ge_wars]</span> Clanwar(ы).</span>');
define('_cw_stats_ges_wars_sq', '<span class="fontText">Эта команда играла в общем <span class="fontBold">[ge_wars]</span> Clanwar(ы).</span>');
define('_cw_stats_ges_points', 'Общий щёт: <span class="CwWon">[ges_won]</span> : <span class="CwLost">[ges_lost]</span>');
define('_cw_stats_spiele_squads', 'Мы играем с <span class="fontBold">[anz_squads]</span> с командами и распределены на <span class="fontBold">[anz_games]</span> Игры.');
define('_cw_stats_won_head', 'Выиграли');
define('_cw_stats_lost_head', 'Проиграли');
define('_cw_stats_draw_head', 'Ничья');
define('_cw_head_clanwars', 'Clanwars');
define('_cw_head_game', 'Игра');
define('_cw_head_datum', 'Дата');
define('_cw_head_gegner', 'Противник');
define('_cw_head_liga', 'Лига');
define('_cw_head_gametype', 'Тип игры');
define('_cw_head_xonx', 'XнаX');
define('_cw_head_result', 'Очей');
define('_cw_head_details_show', 'Детали');
define('_cw_head_page', 'Страница: ');
define('_cw_head_legende', 'Легенда');
define('_cw_nothing', '<option value="lazy" class="" class="dropdownKat">--- без изменений ---</option>');
define('_cw_screens', 'Скриншоты');
define('_cw_new', 'Новый');
define('_clanwars_no_show', 'Clanwar в данный момент нет!');
define('_cw_show_all', '
<tr>
  <td class="contentMainFirst" colspan="8" align="center"><a href="../clanwars/?action=showall&amp;id=[id]">Все Clanwar(ы) этой команды покозать</a></td>
</tr>');
## Награды ##
define('_awards_head', 'Награды');
define('_awards_head_squad', 'команда');
define('_awards_head_date', 'дата');
define('_awards_head_place', 'место');
define('_awards_head_prize', 'награда');
define('_awards_head_event', 'Название события');
define('_awards_head_link', 'Ссылка события');
define('_awards_no_show', 'К сожалению, пока нет наград!');
define('_list_all_link', '<tr><td colspan ="7" class="contentMainTop" align="center"><a href="../awards/?action=showall&amp;id=[id]">Показать все награды этой команды</td></tr>');
define('_head_stats', 'статистика');
define('_awards_stats', '<center>У нас есть в общем <span class="fontBold">[anz] Наград</span>!</center>');
define('_awards_stats_1', '<span class="fontBold">[anz]x</span> 1. Место');
define('_awards_stats_2', '<span class="fontBold">[anz]x</span> 2. Место');
define('_awards_stats_3', '<span class="fontBold">[anz]x</span> 3. Место');
define('_awards_empty_url', 'Вы должны указать ссылку на события!');
define('_awards_empty_event', 'Вы должны указать имя события!');
define('_awards_admin_head_add', 'Добавить новую награду');
define('_awards_admin_added', 'Награда была успешно добавлена!');
define('_awards_admin_head_edit', 'Обработка Награды');
define('_awards_admin_edited', 'Награда была успешно изменена!');
define('_awards_admin_deleted', 'Награда была успешно удалена!');
define('_awards_head_legende', 'легенда');
## Рейтинг ##
define('_error_empty_league', 'Вы должны указать Лигу!');
define('_error_empty_url', 'Вы должны указать командый линк!');
define('_error_empty_rank', 'Вы должны указать место!');
## сервер ##
define('_banned_reason', 'Причина');
define('_banned_head', 'Список банов');
define('_banned_gesamt', '<span class="fontText">В целом </span> <span class="fontBold">[ges] User</span> <span class="fontText">в списке банов</span>');
define('_banned_edit_head', 'Редактировать список банов');
define('_error_banned_edited', 'забанетый пользователь был успешно изменен!');
define('_server_head', 'сервер');
define('_server_name', 'Имя Сервера');
define('_server_pwd', '<span class="fontBold">пароль:</span> [pwd]');
define('_server_ip', 'IP');
define('_server_players', 'Игроки');
define('_server_aktmap', 'тек. карта');
define('_server_frags', 'Фрагов');
define('_server_time', 'Время в игре');
define('_server_noplayers', '
<tr>
  <td class="contentMainFirst" align="center" colspan="5"><span class="fontBold">Нет игроков на сервере</span></td>
</tr>');
define('_server_no_connection', '
<tr>
  <td class="contentMainFirst" align="center" colspan="2">Не удается подключиться к серверу</td>
</tr>');
define('_server_splayerstats', 'Статистика игроков');
define('_generated_time', 'Страница сгенерирована за [time] секунды');
define('_slist_head', 'Список серверов');
define('_slist_serverip', 'Сервер IP');
define('_slist_slots', 'Слоты');
define('_slist_add', 'Добавить сервер');
define('_slist_serverport', 'Порт сервера');
define('_slist_addip', 'Нажмите на сервере IP чтобы появилось в HLSW');
define('_slist_added_msg', 'Новая запись в списке серверов!');
define('_slist_title', 'Список серверов');
define('_server_password', 'Пароль сервера');
define('_error_server_saved', 'Ваш сервер был успешно зарегистрирован!<br /> Админ рассмотрит вашу запись.');
define('_error_empty_slots', 'Вы должны указать ваше количество слотов!');
define('_error_empty_ip', 'Вы должны указать ваш IP сервера!');
define('_error_empty_port', 'Вы должны указать ваш порт сервера!');
define('_gallery_head', 'галереи');
define('_subgallery_head', 'галерея');
define('_gallery_images', 'Фотографии');
define('_gal_back', 'назад');
define('_gallery_admin_head', 'Добавить галерею');
define('_gallery_gallery', 'Название галереe');
define('_gallery_count', 'Количество изображений');
define('_gallery_count_new', 'Количество новых изображений');
define('_gallery_added', 'Галерея успешно создана!');
define('_error_gallery', 'Вы должны указать имя галерее!');
define('_gallery_image', 'изображение');
define('_gallery_deleted', 'Галерея удалена успешно!');
define('_gallery_edited', 'Галерея изменена успешно!');
define('_gallery_admin_edit', 'Редактировать галерее');
define('_gallery_pic_deleted', 'Изображение удалено успешно!');
define('_gallery_new', 'Изображения добавили в галерею успешно!');
define('_button_value_newgal', 'Добавить ещё картинки');
define('_contact_pflichtfeld', '<span class="fontWichtig">*</span> = Необходимо');
define('_contact_nachricht', 'Сообщение');
define('_contact_sended', 'Ваше сообщение было успешно направлена администратару!');
define('_contact_title', 'Контакты');
define('_contact_text', '
Кто-то заполнил контактный формуляр!<br /><br />
<span class="fontBold">Ник:</span> [nick]<br />
<span class="fontBold">Email:</span> [email]<br />
<span class="fontBold">ICQ-Nr.:</span> [icq]<br />
<span class="fontBold">Skype:</span> [skype]<br />
<span class="fontBold">Steam:</span> [steam]<br /><br />
<span class="fontUnder"><span class="fontBold">Сообщение:</span></span><br />[text]');
define('_contact_joinus', 'Хочу в ваш Клан-описание');
define('_contact_joinus_why', 'Кратко опишите, почему вы хотите в наш Клан.');
define('_contact_title_joinus', 'Хочу в ваш Клан-Контактный формуляр');
define('_contact_text_joinus', '
Кто-то заполнил контактный формуляр!<br /><br />
<span class="fontBold">Ник:</span> [nick]<br />
<span class="fontBold">Возраст:</span> [age]<br />
<span class="fontBold">Email:</span> [email]<br />
<span class="fontBold">ICQ-Nr.:</span> [icq]<br />
<span class="fontBold">Skype:</span> [skype]<br />
<span class="fontBold">Steam:</span> [steam]<br /><br />
<span class="fontBold">Kоманда:</span> [squad]<br /><br />
<span class="fontUnder"><span class="fontBold">Сообщение:</span></span><br />[text]');
define('_contact_joinus_no_squad_aviable', 'Нет доступных Команд');
define('_contact_joinus_sended', 'Хочу в ваш Клан запрос был успешно отправлен админу!');
define('_contact_fightus', 'Комментарий');
define('_contact_title_fightus', 'FightUs-Контактный формуляр');
define('_contact_fightus_sended', 'Ваш запрос на Clanwar был успешно направлены администратору!');
define('_contact_fightus_partner', 'Контакт');
define('_contact_fightus_clandata', 'Данные Клана');
define('_contact_fightus_clanname', 'Hазвания Клана');
define('_fightus_maps', 'Ваша карта');
define('_empty_fightus_map', 'Вам необходимо ввести карту, которую вы хотите играть!');
define('_empty_fightus_game', 'Вы должны ввести игру, которую вы хотите играть!');
## статистика ##
define('_site_stats', 'Статистика страницы');
define('_stats', 'Статистика');
define('_stats_nkats', 'Категории');
define('_stats_news', 'Внесеные Новости');
define('_stats_comments', 'Написанные комментарии');
define('_stats_cpern', 'ø комментарии в новостях');
define('_stats_npert', 'ø Новостей за день');
define('_stats_gb_all', 'Общее количество записей');
define('_stats_gb_poster', 'Записи гостей / рег. пользователь');
define('_stats_gb_first', 'Первое сообщение');
define('_stats_gb_last', 'Последнее сообщение');
define('_from', 'от');
define('_stats_forum_ppert', 'ø сообщений в потоке');
define('_stats_forum_pperd', 'ø сообщений в день');
define('_stats_forum_top', 'Лучшие авторы');
define('_stats_users_regged', 'рег. пользователь');
define('_stats_users_regged_member', '- В том числе Member');
define('_stats_users_logins', 'в Общем логинов');
define('_stats_users_msg', 'отправленных сообщений');
define('_stats_users_buddys', 'Друзья');
define('_stats_users_votes', 'Юзер праголосовали');
define('_stats_users_aktmsg', '- из которых находящихся в Ловви/Сообщения');
define('_stats_cw_played', 'играли Clanwars');
define('_stats_cw_won', '&nbsp;&nbsp;- из которых выиграло');
define('_stats_cw_draw', '&nbsp;&nbsp;- из которых вничью');
define('_stats_cw_lost', '&nbsp;&nbsp;- из которых проиграно');
define('_stats_cw_points', 'Итого очков');
define('_stats_place', '&nbsp;&nbsp;- из которых место');
define('_stats_place_misc', '&nbsp;&nbsp;- из которых другие места');
define('_stats_awards', 'награды');
define('_stats_mysql', 'MySQL-база данных');
define('_stats_mysql_size', 'Размер базы данных');
define('_stats_mysql_entrys', 'Таблиц');
define('_stats_mysql_rows', 'Общее количество элементов');
define('_site_stats_files', 'Файлов');
define('_stats_hosted', 'резидентные файлы');
define('_stats_dl_size', 'Общий размер');
define('_stats_dl_traffic', 'Общий размер Траффика');
define('_stats_dl_hits', 'Общий размер загрузок');
## пользователь ##
define('_profil_head', '<span class="fontBold">Профиль пользователя [nick]</span> [[profilhits] рассматривался]');
define('_user_noposi', '<option value="lazy" class="dropdownKat">нет поста</option>');
define('_login_head', 'Логин');
define('_new_pwd', 'новый пароль');
define('_register_head', 'Регистрация');
define('_register_confirm', 'код безопасности');
define('_register_confirm_add', 'Введите код');
define('_lostpwd_head', 'Выслать пароль');
define('_profil_edit_head', 'Профиль [nick] Изменить');
define('_profil_clan', 'Клан');
define('_profil_pic', 'Фото');
define('_profil_contact', 'Контакты');
define('_profil_hardware', 'Оборудование');
define('_profil_about', 'О себе');
define('_profil_real', 'Имя');
define('_profil_city', 'Место жительства');
define('_profil_bday', 'День рождения');
define('_profil_age', 'Возраст');
define('_profil_hobbys', 'Хобби');
define('_profil_motto', 'Девиз');
define('_profil_hp', 'Главная страница');
define('_profil_sex', 'Пол');
define('_profil_board', 'Материнская плата');
define('_profil_cpu', 'Процессор');
define('_profil_ram', 'Оперативная');
define('_profil_graka', 'Видеокарта');
define('_profil_monitor', 'Монитор');
define('_profil_maus', 'Мышка');
define('_profil_mauspad', 'коврик для мыши');
define('_profil_hdd', 'Жесткий диск');
define('_profil_headset', 'Наушники');
define('_profil_os', 'Операционная система');
define('_profil_inet', 'Связь');
define('_profil_job', 'Работа');
define('_profil_position', 'Позиция');
define('_profil_exclans', 'Экс-кланы');
define('_profil_status', 'Статус');
define('_aktiv', '<span class=fontGreen>активный</span>');
define('_inaktiv', '<span class=fontRed>неактивный</span>');
define('_male', 'Мужской');
define('_female', 'Женский');
define('_profil_ppic', 'Профильоя Фотография');
define('_profil_gamestuff', 'Играые вещи');
define('_profil_userstats', 'Статистика пользователя');
define('_profil_navi_profil', '<a href="?action=user&amp;id=[id]">Профиль</a>');
define('_profil_navi_gb', '<a href="?action=user&amp;id=[id]&amp;show=gb">Гостевая книга</a>');
define('_profil_navi_gallery', '<a href="?action=user&amp;id=[id]&amp;show=gallery">Галерея</a>');
define('_profil_profilhits', 'Хитов на этот профил');
define('_profil_forenposts', 'Сообщения на форуме');
define('_profil_votes', 'Сколько раз проголосовал');
define('_profil_msgs', 'Отправленные сообщения');
define('_profil_logins', 'Сколько логинов');
define('_profil_registered', 'Зарегистриревался');
define('_profil_last_visit', 'Последнее посещение');
define('_profil_pagehits', 'Хитов на сайт');
define('_pedit_visibility', 'Видимость / права доступа');
define('_pedit_visibility_gb', 'Записи в гостевой книге');
define('_pedit_visibility_gallery', 'галерея');
define('_pedit_perm_public', '<option value="0" selected="selected">Public</option><option value="1">User only</option><option value="2">Member only</option>');
define('_pedit_perm_user', '<option value="0">Public</option><option value="1" selected="selected">User only</option><option value="2">Member only</option>');
define('_pedit_perm_member', '<option value="0">Public</option><option value="1">User only</option><option value="2" selected="selected">Member only</option>');
define('_pedit_perm_allow', '<option value="1" selected="selected">Позволить</option><option value="0">Блокировaть</option>');
define('_pedit_perm_deny', '<option value="1">Позволить</option><option value="0" selected="selected">Блокировaть</option>');
define('_gallery_no_perm', '<div align="center"><br/>Вы не имеете доступа увидеть эту галерею</div>');
define('_profil_cws', 'принимал участие в CW');
define('_profil_edit_pic', '<a href="../upload/?action=userpic">Загрузить</a>');
define('_profil_delete_pic', '<a href="../upload/?action=userpic&amp;do=deletepic">Удалить</a>');
define('_profil_edit_ava', '<a href="../upload/?action=avatar">Загрузить</a>');
define('_profil_delete_ava', '<a href="../upload/?action=avatar&amp;do=delete">Удалить</a>');
define('_pedit_aktiv', '<option value="1" selected="selected">активный</option><option value="0">inaktiv</option>');
define('_pedit_inaktiv', '<option value="1">aktiv</option><option value="0" selected="selected">неактивный</option>');
define('_pedit_male', '<option value="0">Не указано</option><option value="1" selected="selected">Мужской</option><option value="2">Женский</option>');
define('_pedit_female', '<option value="0">Не указано</option><option value="1">Мужской</option><option value="2" selected="selected">Женский</option>');
define('_pedit_sex_ka', '<option value="0">Не указано</option><option value="1">Мужской</option><option value="2">Женский</option>');
define('_info_edit_profile_done', 'Вы успешно изменен профиль!');
define('_delete_pic_successful', 'Ваша картинка была успешно удалена!');
define('_no_pic_available', 'Не удалось найти вашу картину!');
define('_profil_edit_profil_link', '<a href="?action=editprofile">Изменить профиль</a>');
define('_profil_edit_gallery_link', '<a href="?action=editprofile&amp;show=gallery">Изменить соб. галерею</a>');
define('_profil_avatar', 'Аватар');
define('_lostpwd_failed', 'Имя Логина и адрес электронной почты не совпадают!');
define('_lostpwd_valid', 'Вы получите новый пароль он отправлен на ваш адрес электронной почты!');
define('_error_user_already_in', 'Вы уже прошли идентификацию!');
define('_user_is_banned', 'Ваш аккаунт был заблокирован администратором сайта!<br />Выясните, у администратора точные факты.');
define('_msghead', 'Центр сообщений от [nick]');
define('_posteingang', 'Входящие');
define('_postausgang', 'Исходящие');
define('_msg_title', 'Сообщение');
define('_msg_absender', 'Адресант');
define('_msg_empfaenger', 'Приемник');
define('_msg_answer_msg', 'Ответ от [nick]');
define('_msg_sended_msg', 'Сообщение каму [nick]');
define('_msg_answer_done', 'Сообщение было успешно отправлено!');
define('_msg_titel', 'Написать новое сообщение');
define('_msg_titel_answer', 'Ответить');
define('_to', 'Каму');
define('_or', 'или');
define('_msg_to_just_1', 'Вам можно указать только одного адресата!');
define('_msg_not_to_me', 'Вы не можете посылать сообщения себе!');
define('_legende_readed', 'Сообщение было прочитано');
define('_legende_msg', 'Новое сообщение');
define('_msg_from_nick', 'Cообщение от [nick]');
define('_msg_global_reg', 'всем регист. пользователям');
define('_msg_global_squad', 'отдельные команды:');
define('_msg_bot', '<span class="fontBold">Бот сообщений</span>');
define('_msg_global_who', 'приемник');
define('_msg_reg_answer_done', 'Сообщение было отправлено всем зарегистрированным!');
define('_msg_member_answer_done', 'Сообщение было отправлено всем пользователям!');
define('_msg_squad_answer_done', 'Сообщение было отправлено выбранной команды!');
define('_buddyhead', 'Друзей редактировать');
define('_addbuddys', 'Друзей добавить');
define('_buddynick', 'Друзья');
define('_add_buddy_successful', 'Пользователь теперь в ваших друзях!');
define('_buddys_legende_addedtoo', 'Пользователь тоже взял вас в список своих друзей');
define('_buddys_legende_dontaddedtoo', 'Пользователь взял вас в список своих друзей');
define('_buddys_delete_successful', 'Пользователь был успешно удален, из ваших друзей!');
define('_buddy_added_msg', 'Пользователь <span class="fontBold">[user]</span> только что пригласил вас в список своих друзей!');
define('_buddy_title', 'Друзья');
define('_buddy_del_msg', 'Пользователь <span class="fontBold">[user]</span> только что удалили, из своих друзей!');
define('_ulist_lastreg', 'Новые пользователи');
define('_ulist_online', 'Статус');
define('_ulist_age', 'Возраст');
define('_ulist_sex', 'пол');
define('_ulist_country', 'национальность');
define('_ulist_sort', 'Сортировать по:');
define('_usergb_eintragen', '<a href="?action=usergb&amp;id=[id]">занести</a>');
define('_usergb_entry_successful', 'запись в гостевой книге был успешным!');
define('_gallery_pic', 'картинка');
define('_gallery_beschr', 'описание');
define('_gallery_edit_new', '<a href="../upload/?action=usergallery">Добавить фото</a>');
define('_info_edit_gallery_done', 'Вы успешно удалили запись!');
define('_admin_user_edithead', 'Админ: Ред. пользователя');
define('_admin_user_clanhead', 'Авторизации');
define('_admin_user_squadhead', 'Командa');
define('_admin_user_personalhead', 'личнoе');
define('_admin_user_level', 'Level');
define('_admin_user_clankasse', 'Админ: Денги клана');
define('_admin_user_serverliste', 'Админ: список серверов');
define('_admin_user_editserver', 'Админ: сервера');
define('_admin_user_edittactics', 'Админ: Тактики');
define('_admin_user_edituser', 'Редактировать пользователя');
define('_admin_user_editsquads', 'Админ: Teams');
define('_admin_user_editkalender', 'Админ: календаря');
define('_member_admin_newsletter', 'Админ: инфо рассылка');
define('_member_admin_downloads', 'Админ: Загрузки');
define('_member_admin_links', 'Админ: Ссылки');
define('_member_admin_gb', 'Админ: Гостевой книги');
define('_member_admin_forum', 'Админ: Форума');
define('_member_admin_intforum', 'Внутренний форум видет');
define('_member_admin_news', 'Админ: News');
define('_member_admin_clanwars', 'Админ: Клан Вары');
define('_error_edit_myself', 'Вы не можете редактировать самого себя!');
define('_error_edit_admin', 'Вам нельзя редактировать администраторов!');
define('_admin_level_banned', 'Аккаунт заблокиревать');
define('_admin_user_identitat', 'Личность');
define('_admin_user_get_identitat', '<a href="?action=admin&amp;do=identy&amp;id=[id]">Принять Личность пользователя</a>');
define('_identy_admin', 'Вы не можете брать личность администратора!');
define('_admin_squad_del', '<option value="delsq">- Удалить пользователя из команды -</option>');
define('_admin_squad_nosquad', '<option class="dropdownKat" value="lazy">- Пользователь не в команде -</option>');
define('_admin_user_edited', 'Пользователь был успешно изменен!');
define('_userlobby', 'Пользователья лобби');
define('_lobby_new', 'Изменения с момента последнево посещения страницы');
define('_lobby_new_erased', 'Временные изменения были успешно удалены!');
define('_last_forum', 'Последние 10 Темы форума');
define('_lobby_forum', 'Форум записи');
define('_new_post_1', 'Новое сообщение');
define('_new_post_2', 'Новые сообщения');
define('_new_thread', 'в теме ');
define('_no_new_thread', 'Новая тема:');
define('_lobby_gb', 'Новые записи в Гостевой книге');
define('_new_gb', '<br /><span class="fontBoldUnder">Гостевая книга:</span><br />');
define('_new_eintrag_1', 'новоя запис');
define('_new_eintrag_2', 'Новые записи');
define('_lobby_user', 'Зарегистрированный пользователь');
define('_new_users_1', 'новый зарегистрированный пользовател');
define('_new_users_2', 'новые зарегистрированные пользователи');
define('_lobby_membergb', 'Мойя Гостевая книга профилья');
define('_lobby_news', 'Cообщения');
define('_lobby_new_news', 'Новые новости');
define('_lobby_newsc', 'Комментарии новостях');
define('_lobby_new_newsc_1', 'Новоя Комментария в новостях');
define('_lobby_new_newsc_2', 'Новые Комментарии в новостях');
define('_new_msg_1', 'новое сообщение');
define('_new_msg_2', 'новые сообщения');
define('_lobby_votes', 'Опросы');
define('_new_vote_1', 'новое Голосование');
define('_new_vote_2', 'новые Голосование');
define('_lobby_cw', 'Клан Вары');
define('_user_new_cw', '<tr><td style="width:22px;text-align:center"><img src="../inc/images/gameicons/[icon]" class="icon" alt="" /></td><td style="vertical-align:middle"><a href="../clanwars/?action=details&amp;id=[id]">Кланвар в <span class="fontWichtig">[datum]</span> против <span class="fontWichtig">[gegner]</span></a></td></tr>');
define('_user_delete_verify', '
<tr>
  <td class="contentHead"><span class="fontBold">Удалить пользователя</span></td>
</tr>
<tr>
  <td class="contentMainFirst" align="center">
    Дествително, пользователя [user] удалить?<br />
    <span class="fontUnder">все</span> мероприятия этого пользователя будут удалены на этой странице!<br /><br />
    <a href="?action=admin&amp;do=delete&verify=yes&amp;id=[id]">Да, удалить!</a>
  </td>
</tr>');
define('_hlswid', 'Имя Xfire');
define('_hlswstatus', 'XFire');
define('_user_deleted', 'Пользователь был успешно удален!');
define('_admin_user_shoutbox', 'Админ: Мини-чатa');
define('_admin_user_awards', 'Админ: Награды');
define('_userlobby_kal_today', 'Следующее событие <a href="../kalender/?action=show&time=[time]"><span class="fontWichtig">сегодня - [event]</span></a>');
define('_userlobby_kal_not_today', 'Следующее событие <a href="../kalender/?action=show&time=[time]"><span class="fontUnder">[date] - [event]</span></a>');
define('_profil_country', 'Страна');
define('_lobby_awards', 'Награды');
define('_new_awards_1', 'Новая награда');
define('_new_awards_2', 'Новые награды');
define('_lobby_rankings', 'Рейтинг');
define('_new_rankings_1', 'Новое изменения');
define('_new_rankings_2', 'Новые изменения');
define('_profil_favos', 'Фавориты');
define('_profil_drink', 'Напиток');
define('_profil_essen', 'Пища');
define('_profil_film', 'Фильм');
define('_profil_musik', 'Музыка');
define('_profil_song', 'Песня');
define('_profil_buch', 'Книжка');
define('_profil_autor', 'Писател');
define('_profil_person', 'Личность');
define('_profil_sport', 'Спорт');
define('_profil_sportler', 'Спортсмен');
define('_profil_auto', 'Автомобиль');
define('_profil_favospiel', 'Игра');
define('_profil_game', 'Игра');
define('_profil_favoclan', 'Клан');
define('_profil_spieler', 'Игрок');
define('_profil_map', 'Карта');
define('_profil_waffe', 'Оружие');
define('_profil_rasse', 'Раса');
define('_profil_sonst', 'Разное');
define('_profil_url1', 'Страница #1');
define('_profil_url2', 'Страница #2');
define('_profil_url3', 'Страница #3');
define('_profil_ich', 'Описания о себе');
define('_lobby_gallery', 'Галереи');
define('_new_gal_1', 'Новая галерея');
define('_new_gal_2', 'Новые галереи');
## загрузки ##
define('_upload_wrong_size', 'Выбранный файл больше допустимого!');
define('_upload_no_data', 'Вы должны указать файл!');
define('_info_upload_success', 'Файл успешно загружен!');
define('_upload_info', 'Инфо');
define('_upload_file', 'файл');
define('_upload_beschreibung', 'Описания');
define('_upload_button', 'Загрузить');
define('_upload_over_limit', 'Вы не можете загружать фотографии! Удалите старые фотографии для загрузки новых! *количество превысили');
define('_upload_file_exists', 'Указанный файл уже существует! Переименуй файл или выбери другой!');
define('_upload_head', 'Загрузить изображения');
define('_upload_userpic_info', 'Только JPG, GIF или PNG файлы с максимальным размером [userpicsize]KB!<br />Рекомендуемый размер 170px * 210px ');
define('_upload_head_usergallery', 'Изменить вашу галереею');
define('_edit_gallery_done', 'Изменить Галереею успешно завершена!');
define('_upload_usergallery_info', 'Только JPG, GIF или PNG файлы с максимальным размером [userpicsize]KB!');
define('_upload_icons_head', 'GameIcons');
define('_upload_taktiken_head', 'Тактики screens');
define('_upload_ava_head', 'Аватар пользователя');
define('_upload_userava_info', 'Только JPG, GIF или PNG файлы с максимальным размером [userpicsize]KB!<br />Рекомендуемый размер 100px * 100px ');
define('_upload_newskats_head', 'изображения рубрике');
## неназначенные ##
define('_forum_no_last_post', 'последнее сообщение, к сожалению, нельзя показать!');
define('_config_maxwidth', 'Изображения авто. уменьшить');
define('_config_maxwidth_info', 'Здесь вы можете настроить, от которого размера будет изображения уменьшается!');
define('_forum_top_posts', 'Топ 5 сообщений');
define('_error_no_teamspeak', 'Сервер TeamSpeak недоступен!');
define('_user_cant_delete_admin', 'Вы не можете удалять юзеров или администраторов!');
define('_no_entrys_yet', '
<tr>
  <td class="contentMainFirst" colspan="[colspan]" align="center">До сих пор не записи не существует!</td>
</tr>');
define('_nav_no_nextwars', 'пока нет КВ!');
define('_nav_no_lastwars', 'Не до сих пор нет КВ!');
define('_nav_no_ftopics', 'По-прежнему отсутствует запись!');
define('_gallery_folder_exists', 'Указанная папка уже существует!');
define('_server_isnt_live', 'сервер не является в статусe Live!');
define('_target', 'Новое окно');
define('_rankings_edit_head', 'Изменить порядок');
define('_fopen', 'Ваш хост этой странице, не позволяют искомую функцию fopen()');
define('_and', 'и');
define('_lobby_artikelc', 'Комментарии товара');
define('_lobby_new_art_1', 'новый Продукт');
define('_lobby_new_art_2', 'новые продукты');
define('_user_new_art', '&nbsp;&nbsp;<a href="../artikel/"><span class="fontWichtig">[cnt]</span> [eintrag]</span><br />');
define('_lobby_new_artc_1', 'новая комментария продукта');
define('_lobby_new_artc_2', 'новые комментарии продукта');
define('_page', '<span class="fontBold">[num]</span>  ');
define('_profil_nletter', 'Получить рассылку новостей?');
define('_forum_admin_addglobal', '<span class="fontWichtig">Общая </span> запись? (Во всех форумах и подфорумах)');
define('_forum_admin_global', '<span class="fontWichtig">Общая</span> запись?');
define('_forum_global', '<span class="fontWichtig">Общий:</span>');
define('_admin_config_badword', 'Список недопустимых слов(фильтр)');
define('_admin_config_badword_info', 'Здесь вы можете указать слова, при вводе которых с **** предоставляться будут. Слова должны быть разделены запятой!');
define('_iplog_info', '<span class="fontBold">Внимание:</span> В целях безопасности ваш IP был сохранен!');
define('_logged', 'IP был сохранен');
define('_info_ip', 'IP адрес');
define('_info_browser', 'браузер');
define('_info_res', 'разрешение');
define('_unknown_browser', 'неизвестный браузер');
define('_unknown_system', 'система система');
define('_info_sys', 'система');
define('_nav_montag', 'ПО.');
define('_nav_dienstag', 'ВТ.');
define('_nav_mittwoch', 'СР.');
define('_nav_donnerstag', 'ЧЕ.');
define('_nav_freitag', 'ПЯ.');
define('_nav_samstag', 'СУ.');
define('_nav_sonntag', 'ВО.');
define('_age', 'Возраст');
define('_error_empty_age', 'Вы должны ввести свой ​​ввозраст!');
define('_member_admin_intforums', 'внутренние разрешения на Форум');
define('_access', 'авторизация');
define('_error_no_access', 'DУ вас нет необходимых прав войти сюда !');
define('_artikel_show_link', '<a href="../artikel/?action=show&amp;id=[id]">[titel]</a>');
define('_ulist_bday', 'День рождения');
define('_ulist_last_login', 'Последнее Логин');
## тактика ##
define('_taktik_head', 'Bнутренне: Тактики');
define('_taktik_standard_t', '<a href="?action=standard&amp;what=t&amp;id=[id]">Стандартная</a>');
define('_taktik_standard_ct', '<a href="?action=standard&amp;what=ct&amp;id=[id]">Стандартная</a>');
define('_taktik_spar_t', '<a href="?action=spar&amp;what=t&amp;id=[id]">Эконом</a>');
define('_taktik_spar_ct', '<a href="?action=spar&amp;what=ct&amp;id=[id]">Эконом</a>');
define('_taktik_upload', 'изображение тактики загрузить');
define('_taktik_t', 'Terrorists');
define('_taktik_ct', 'Anti-Terrorists');
define('_taktik_posted', 'опубликовал <span class="fontBold">[autor]</span> - [datum]');
define('_taktik_headline', '<span class="fontBold">карта:</span> [map] - <span class="fontBold">тактика:</span> [what]');
define('_taktik_tstandard_t', 'Team 2 -> Стандартная');
define('_taktik_tstandard_ct', 'Team 1 -> Стандартная');
define('_taktik_tspar_t', 'Team 2 -> Эконом');
define('_taktik_tspar_ct', 'Team 1 -> Эконом');
define('_error_taktik_empty_map', 'Вы должны указать карту!');
define('_taktik_new', 'Добавить новую тактику');
define('_taktik_added', 'Тактика была успешно добавлена!');
define('_taktik_deleted', 'Тактика успешно удалена!');
define('_taktik_edit_head', 'Изменить тактику');
define('_taktik_new_head', 'Новая тактика');
define('_error_taktik_edited', 'Тактику успешно изменили!');
## Выходные данные ##
define('_impressum_head', 'Выходные данные');
define('_impressum_autor', 'Автор страницы');
define('_impressum_domain', 'Домен:');
define('_impressum_disclaimer' , 'Disclaimer');
define('_impressum_txt' , '<blockquote>
<h2><span class="fontBold">1. Content</span></h2>
<br />
The author reserves the right not to be responsible for the topicality, correctness,
completeness or quality of the information provided. Liability claims regarding
damage caused by the use of any information provided, including any kind
of information which is incomplete or incorrect,will therefore be rejected.
<br />All offers are not-binding and without obligation. Parts of the pages or the complete
publication including all offers and information might be extended, changed
or partly or completely deleted by the author without separate announcement.
<br /><br />
<h2><span class="fontBold">2. Referrals and links</span></h2>
<br />
The author is not responsible for any contents linked or referred to from his pages - unless he has full knowledge of illegal contents and would be able to prevent the visitors of his site fromviewing those pages.
If any damage occurs by the use of information presented there, only the author of the respective pages might be liable, not the one who has linked to these pages. Furthermore the author is not liable for any postings or messages published by users of discussion boards, guestbooks or mailinglists provided on his page.
<br /><br />
<h2><span class="fontBold">3. Copyright</span></h2>
<br />
The author intended not to use any copyrighted material for the publication or, if not possible, to indicate the copyright of the respective object.
<br />
The copyright for any material created by the author is reserved. Any duplication or use of objects such as images, diagrams, sounds or texts in other
electronic or printed publications is not permitted without the author\'s agreement.
<br /><br />
<h2><span class="fontBold">4. Privacy policy<</span></h2>
<br />
If the opportunity for the input of personal or business data (email addresses, name, addresses) is given, the input of these data takes place voluntarily. The use and payment of all offered services are permitted - if and so far technically possible and reasonable - without specification of any personal data or under specification of anonymized data or an alias.
The use of published postal addresses, telephone or fax numbers and email addresses for marketing purposes is prohibited, offenders sending unwanted spam messages will be punished.
<br /><br />
<h2><span class="fontBold">5. Legal validity of this disclaimer</span></h2>
<br />
This disclaimer is to be regarded as part of the internet publication which you were referred from. If sections or individual terms of this statement are not legal or correct, the content or validity of the other parts remain uninfluenced by this fact.
</blockquote>');
## Админ ##
define('_config_head', 'Админ ареал');
define('_config_empty_katname', 'Вы должны указать название категории!');
define('_config_katname', 'название категории');
define('_config_set', 'Настройки были успешно применены!');
define('_config_forum_status', 'Статус');
define('_config_forum_head', 'Kатегории форума');
define('_config_forum_mainkat', 'Главная категория');
define('_config_forum_subkathead', 'Подкатегории от <span class="fontUnder">[kat]</span>');
define('_config_forum_subkat', 'подкатегория');
define('_config_forum_subkats', '<span class="fontBold">[topic]</span><br /><span class="fontItalic">[subtopic]</span>');
define('_config_forum_kat_head', 'Добавить новую категорию');
define('_config_forum_public', 'публично');
define('_config_forum_intern', 'внутренне');
define('_config_forum_kat_added', 'Категория успешно добавлена!');
define('_config_forum_kat_deleted', 'Категория успешно удалена!');
define('_config_forum_kat_head_edit', 'Редактировать Категорию');
define('_config_forum_kat_edited', 'Категория изменена!');
define('_config_forum_add_skat', 'Добавить новую подкатегорию');
define('_config_forum_skatname', 'имя подкатегории');
define('_config_forum_empty_skat', 'Вы должны указать имя подкатегории!');
define('_config_forum_skat_added', 'Суб-категория успешно добавлена!');
define('_config_forum_stopic', 'подзаголовок');
define('_config_forum_skat_edited', 'Суб-категория успешно изменена!');
define('_config_forum_edit_skat', 'Изменить Суб-категорию');
define('_config_forum_skat_deleted', 'Суб-категория успешно удалена!');
define('_config_newskats_kat', 'Категория');
define('_config_newskats_head', 'Категории новостей/статей');
define('_config_newskats_katbild', 'Изаброжэние Категории');
define('_config_newskats_add', '<a href="?admin=news&amp;do=add">Добавить новую категорию</a>');
define('_config_newskat_deleted', 'Die Kategorie wurde erfolgreich gel&ouml;scht!');
define('_config_newskats_add_head', 'Neue Kategorie hinzuf&uuml;gen');
define('_config_newskats_added', 'Die Kategorie wurde erfolgreich hinzugef&uuml;gt!');
define('_config_newskats_edit_head', 'Kategorie editieren');
define('_config_newskats_edited', 'Die Kategorie wurde erfolgreich editiert!');
define('_config_impressum_head', 'Выходные данные');
define('_config_impressum_domains', 'Domains');
define('_config_impressum_autor', 'Autor der Seite');
define('_config_konto_head', 'Kontodaten');
define('_config_clankasse_head', 'Ein-/Auszahlungsbezeichnungen');
define('_backup_head', 'Datenbankbackup');
define('_backup_info_head', 'Anmerkung');
define('_backup_info', 'Der Backupvorgang kann je nach Gr&ouml;&szlig;e der Datenbank mehrere Minuten in Anspruch nehmen.');
define('_backup_link', 'neues Backup anlegen!');
define('_backup_successful', 'Das Datenbankbackup wurde erfolgreich angelegt!');
define('_backup_last_head', 'Letztes Backup');
define('_backup_last_not_exist', 'Du hast bisher noch kein MySQL-Datenbankbackup angelegt!');
define('_news_admin_head', 'Newsbereich');
define('_admin_news_add', '<a href="?admin=newsadmin&amp;do=add">News hinzuf&uuml;gen</a>');
define('_admin_news_head', 'News hinzuf&uuml;gen');
define('_news_admin_kat', 'Категория');
define('_news_admin_klapptitel', 'Klapptexttitel');
define('_news_admin_more', 'More');
define('_empty_news', 'Du musst eine News eintragen!');
define('_news_sended', 'Die News wurde erfolgreich eingetragen!');
define('_admin_news_edit_head', 'News editieren');
define('_news_edited', 'Die News wurde erfolgreich editiert!');
define('_news_deleted', 'Die News wurde erfolgreich gel&ouml;scht!');
define('_member_admin_header', 'Teambereich');
define('_member_admin_squad', 'Team');
define('_member_admin_game', 'Game');
define('_member_admin_icon', 'Icon');
define('_member_admin_add', '<a href="?admin=squads&amp;do=add">Team hinzuf&uuml;gen</a>');
define('_admin_squad_deleted', 'Das Team wurde erfolgreich gel&ouml;scht!');
define('_member_admin_add_header', 'Team hinzuf&uuml;gen');
define('_admin_squad_no_squad', 'Du musst einen Teamnamen angeben!');
define('_admin_squad_no_game', 'Du musst ein Game angeben, welches dieses Team spielt!');
define('_admin_squad_add_successful', 'Das Team wurde erfolgreich hinzugef&uuml;gt!');
define('_admin_squad_edit_successful', 'Das Team wurde erfolgreich editiert!');
define('_member_admin_edit_header', 'Team editieren');
define('_error_server_edit', 'Der Server wurde erfolgreich editiert!');
define('_error_empty_clanname', 'Du musst euren Clannamen angeben!');
define('_error_server_accept', 'Die ausgew&auml;hlten Server wurden erfolgreich freigeschaltet!');
define('_error_server_dont_accept', 'Die ausgew&auml;hlten Server wurden erfolgreich aus der Liste genommen!');
define('_slist_head_admin', 'Serverliste');
define('_slist_server_deleted', 'Der Server wurde erfolgreich gel&ouml;scht!');
define('_server_admin_head', 'Server');
define('_server_add_new', '<a href="?admin=server&amp;do=new">Neuen Server hinzuf&uuml;gen</a>');
define('_admin_server_edit', 'Server editieren');
define('_empty_ip', 'Du musst eine IP angeben!');
define('_server_admin_edited', 'Der Server wurde erfolgreich editiert!');
define('_server_admin_deleted', 'Der Server wurde erfolgreich gel&ouml;scht!');
define('_admin_server_new', 'Neuen Server hinzuf&uuml;gen');
define('_server_admin_added', 'Der Server wurde erfolgreich hinzugef&uuml;gt!');
define('_empty_game', 'Du musst ein Icon ausw&auml;hlen!');
define('_empty_servername', 'Du musst einen Servernamen angeben!');
define('_config_server_mapname', 'Mapname');
define('_config_server_maps_head', 'Servermaps');
define('_config_server_map_deleted', 'Der Mapscreen wurde erfolgreich gel&ouml;scht!');
define('_admin_dlkat', 'Downloadkategorien');
define('_admin_download_kat', 'Bezeichnung');
define('_dl_add_new', '<a href="?admin=dl&amp;do=new">Neue Kategorie hinzuf&uuml;gen</a>');
define('_dl_new_head', 'Neue Downloadkategorie hinzuf&uuml;gen');
define('_dl_dlkat', 'Kategorie');
define('_dl_empty_kat', 'Du musst eine Kategoriebezeichnung angeben!');
define('_dl_admin_added', 'Die Downloadkategorie wurde erfolgreich hinzugef&uuml;gt!');
define('_dl_admin_deleted', 'Die Downloadkategorie wurde erfolgreich gel&ouml;scht!');
define('_dl_edit_head', 'Downloadkategorie editieren');
define('_dl_admin_edited', 'Die Downloadkategorie wurde erfolgreich editiert!');
define('_config_global_head', 'Konfiguration');
define('_config_c_limits', 'Seitenaufteilungen (LIMITS)');
define('_config_c_limits_what', 'Hier kannst du die Eintr&auml;ge einstellen, die pro Bereich maximal angezeigt werden');
define('_config_c_usergb', 'User-G&auml;stebuch');
define('_config_c_clankasse', 'Clankasse');
define('_config_c_gb', 'G&auml;stebuch');
define('_config_c_archivnews', 'News-Archiv');
define('_config_c_news', 'News');
define('_config_c_banned', 'Bannliste');
define('_config_c_adminnews', 'News-Admin');
define('_config_c_clanwars', 'Clanwars');
define('_config_c_shout', 'Shoutbox');
define('_config_c_userlist', 'Userliste');
define('_config_c_comments', 'Newskommentare');
define('_config_c_fthreads', 'Forumthreads');
define('_config_c_fposts', 'Forumposts');
define('_config_c_floods', 'Anti-Flooding');
define('_config_c_forum', 'Forum');
define('_config_c_length', 'L&auml;ngenangaben');
define('_config_c_length_what', 'Hier kannst du die L&auml;nge in Anzahl der Zeichen angeben, bei der nach &Uuml;berschreitung die Ausgabe gek&uuml;rzt wird.');
define('_config_c_newsadmin', 'Newsadmin: Titel');
define('_config_c_shouttext', 'Shoutbox: Text');
define('_config_c_newsarchiv', 'Newsarchiv: Titel');
define('_config_c_forumtopic', 'Forum: Topic');
define('_config_c_forumsubtopic', 'Forum: Subtopic');
define('_config_c_topdl', 'Men&uuml;: Top Downloads');
define('_config_c_ftopics', 'Men&uuml;: Last Forumtopics');
define('_config_c_lcws', 'Clanwars: Gegnername');
define('_config_c_lwars', 'Men&uuml;: Last Wars');
define('_config_c_nwars', 'Men&uuml;: Next Wars');
define('_config_c_main', 'Allgemeine Einstellungen');
define('_config_c_clanname', 'Clanname');
define('_config_c_pagetitel', 'Seitentitel');
define('_config_c_language', 'Default-Sprache');
define('_config_c_upicsize', 'Global: Uploadgr&ouml;sse Bilder');
define('_config_c_gallerypics', 'User: Usergalerie');
define('_config_c_upicsize_what', 'erlaubte Gr&ouml;&szlig;e der Bilder in KB (Newsbilder, Userprofilbilder usw.)');
define('_config_c_regcode', 'Reg: Sicherheitscode');
define('_config_c_regcode_what', 'Fragt bei der Registrierung einen Sicherheitscode ab');
define('_pos_add_new', '<a href="?admin=positions&amp;do=new">Neuen Rang hinzuf&uuml;gen</a>');
define('_pos_new_head', 'Neuen Rang hinzuf&uuml;gen');
define('_pos_edit_head', 'Rang editieren');
define('_pos_admin_edited', 'Der Rang wurde erfolgreich editiert!');
define('_pos_admin_deleted', 'Der Rang wurde erfolgreich gel&ouml;scht!');
define('_pos_admin_added', 'Der Rang wurde erfolgreich hinzugef&uuml;gt!');
define('_admin_clankasse_add', '<a href="?admin=konto&amp;do=new">Neue Bezeichnung hinzuf&uuml;gen</a>');
define('_clankasse_new_head', 'Neue Ein-/Auszahlungsbezeichnung hinzuf&uuml;gen');
define('_clankasse_edit_head', 'Ein-/Auszahlungsbezeichnung editieren');
define('_clankasse_empty_kat', 'Du musst eine Ein-/Auszahlungsbezeichnung angeben!');
define('_clankasse_kat_added', 'Die Ein-/Auszahlungsbezeichnung wurde erfolgreich hinzugef&uuml;gt!');
define('_clankasse_kat_edited', 'Die Ein-/Auszahlungsbezeichnung wurde erfolgreich editiert!');
define('_clankasse_kat_deleted', 'Die Ein-/Auszahlungsbezeichnung wurde erfolgreich gel&ouml;scht!');
define('_config_c_gallery', 'Galerie');
define('_config_info_gallery', 'Anzahl der Bilder die maximal in einer Reihe gezeigt werden');
define('_config_server_ts_updated', 'Die TeamspeakIP wurde erfogreich gesetzt!');
define('_ts_sport', 'Server Queryport');
define('_ts_width', 'Breite der Anzeige');
define('_config_c_awards', 'Awards');
define('_counter_start', 'Counter');
define('_counter_start_info', 'Hier kannst du eine Zahl eintragen, die zum Counter dazuaddiert wird.');
define('_admin_nc', 'Newskommentare');
define('_admin_reg_head', 'Registrierungspflicht');
define('_config_shoutarchiv', 'Shoutbox: Archiv');
define('_config_zeichen', 'Shoutbox: Zeichen');
define('_config_zeichen_info', 'Hier kannst du einstellen, nach wieviel Zeichen das Eingabefeld der Shoutbox gesperrt wird.');
define('_wartungsmodus_info', 'wenn eingeschaltet kann keiner, ausser der Admin die Seite betreten.');
define('_navi_kat', 'Oбласть');
define('_navi_name', 'Нозвания связи');
define('_navi_url', 'Переадресовка');
define('_navi_shown', 'Видимо');
define('_navi_type', 'Тир');
define('_navi_wichtig', 'Выделить');
define('_navi_space', '<b>Пространство</b>');
define('_navi_head', 'Администрация Навигацийи');
define('_navi_add', '<a href="?admin=navi&amp;do=add">Создать новую ссылку</a>');
define('_navi_add_head', 'Добавить новую ссылку');
define('_navi_edit_head', 'Редактировать Ссылку');
define('_navi_url_to', 'Выложить на');
define('_posi', 'Позиция');
define('_nach', 'на');
define('_navi_no_name', 'Вы должны указать имя ссылки!');
define('_navi_no_url', 'Вы должны укозать адрес переадресации!');
define('_navi_no_pos', 'Вы должны указать местоположение для связи!');
define('_navi_added', 'Связь успешно создана!');
define('_navi_deleted', 'Связь успешно удалена!');
define('_navi_edited', 'Связь успешно изменена!');
define('_editor_head', 'Станицу создвть/редоктиревать');
define('_editor_name', 'Обозначение страницы');
define('_editor_add', '<a href="?admin=editor&amp;do=add">Создать новую страницу</a>');
define('_editor_add_head', 'Добавить новую страницу');
define('_inhalt', 'Содержание');
define('_allow', 'Разрешыть');
define('_deny', 'Запрет');
define('_editor_allow_html', 'Разрешить HTML?');
define('_empty_editor_inhalt', 'Hеобходимо ввести текст!');
define('_site_added', 'Страницу успешно зарегистрировали!');
define('_editor_linkname', 'Hазвание линька');
define('_editor_deleted', 'Страницa успешно удалена!');
define('_editor_edit_head', 'Редактировать страницу');
define('_site_edited', 'Страницу успешно изменили!');
define('_navi_standard', 'Стандарт успешно восстановлен!');
define('_standard_sicher', 'Действительно восстановить стандартные настройки?<br />Все ранее созданные ссылки и новые страницы будут удалены!');
define('_partners_head', 'Баттон Партнеров');
define('_partners_button', 'Баттон');
define('_partners_add_head', 'Баттон Партнеров добавить');
define('_partners_edit_head', 'Баттон Партнеров редактировать');
define('_partners_select_icons', '<option value="[icon]" [sel]>[icon]</option>');
define('_partners_added', 'Баттон Партнеров добавлен!');
define('_partners_edited', 'Баттон Партнеров изменен!');
define('_partners_deleted', 'Баттон Партнеров удален!');
define('_clear_head', 'Очистить базу данных');
define('_clear_news', 'Сообщения Новостей в числеть?');
define('_clear_forum', 'Сообщения Форума в числеть?');
define('_clear_forum_info', 'Сообщения Форума - Удаление записей, которые старше... <span class="fontWichtig">важно</span> выделены не будут удалены!');
define('_clear_misc', 'Разное в числеть (рекомендуется)?');
define('_clear_days', 'Удаление записей, которые старше...');
define('_clear_what', 'Дни');
define('_clear_deleted', 'База данных очистили!');
define('_clear_error_days', 'Вы должны указать дату, с которой всё удалить');
define('_admin_status', 'Живой статус');
define('_error_unregistered', 'вам необходимо зарегистрироваться, чтобы использовать эту функцию!');
define('_seiten', 'Cтраница:');
define('_admin_user_gallery', 'Админ: галереи');
define('_user_admin_joinus', 'получять зоявки в клан (JoinUS)?');
define('_user_admin_contact', 'Получат контактный формуляр?');
define('_user_admin_formulare', 'Бланки');
define('_smileys_error_file', 'Вы должны указать смайлик!');
define('_smileys_error_bbcode', 'Вы должны ввести BBCode!');
define('_smileys_error_type', 'Tолько GIF файлы допускается!');
define('_smileys_added', 'Смайлик успешно добавлен!');
define('_smileys_edited', 'Смайлик успешно изменен!');
define('_smileys_deleted', 'Смайлик успешно удален!');
define('_smileys_normals', 'Стандартные смайлики (не могут быть удалены!)');
define('_smileys_customs', 'Новые смайлики');
define('_smileys_head', 'Pедактор смайликoв');
define('_smileys_smiley', 'Cмайлик');
define('_smileys_bbcode', 'BBCode');
define('_smileys_head_add', 'Добавить смайлик');
define('_smileys_head_edit', 'Редактировать Смайли');
define('_head_waehrung', 'валюта');
define('_dl_version', 'загружаемая версия');
define('_admin_artikel_add', '<a href="?admin=artikel&amp;do=add">Добавить Статью</a>');
define('_artikel_add', 'Добавить Статью');
define('_artikel_added', 'Статья успешно добавленa');
define('_artikel_edit', 'Редактировать Статью');
define('_artikel_edited', 'Статью успешно измененили!');
define('_artikel_deleted', 'Статью успешно удаленa!');
define('_empty_artikel_title', 'Вы должны указать название!');
define('_empty_artikel', 'Вы должны указать продукт!');
define('_admin_artikel', 'Админ: Статей');
define('_c_l_shoutnick', 'Меню: Мини-чат: Ник');
define('_config_c_martikel', 'Продукты');
define('_config_c_madminartikel', 'Админ продуктов');
define('_reg_artikel', 'Комментарии продуктов');
define('_cw_comments', 'Комментарии КВ');
define('_on', 'включен');
define('_off', 'выключен');
define('_pers_info_info', 'Отображает окно сообщения в заголовке с личной информацией, например IP, браузер, разрешение и т.д.');
define('_pers_info', 'Факты');
define('_config_lreg', 'Меню: Последний рег. пользователь');
define('_config_mailfrom', 'Отправитель имайлa');
define('_config_mailfrom_info', 'Этот адрес электронной почты отображается при отправке Рассылки,Регист. и т.д кaк отправитель этого письма!');
define('_profile_del_confirm', 'Внимание! Все пользовательские входы того поляу будут у всех удалены. Вы действительно хотите удалить?');
define('_profile_about', 'О себе');
define('_profile_clan', 'Kлан');
define('_profile_contact', 'Kонтакт');
define('_profile_favos', 'Фавориты');
define('_profile_hardware', 'Оборудование');
define('_profile_name', 'Название поля');
define('_profile_type', 'Тип поля');
define('_profile_kat', 'Категория');
define('_profile_head', 'Управление полями профиля');
define('_profile_edit_head', 'Изменить Профильное поле');
define('_profile_shown', 'Показать');
define('_profile_type_1', 'Текстовое поле');
define('_profile_type_2', 'URL');
define('_profile_type_3', 'Адрес электронной почты');
define('_profile_shown_dropdown','
<option value="1">Показать</option>
<option value="2">Скрыть</option>');
define('_profile_kat_dropdown', '
<option value="1">О себе</option>
<option value="2">Клан</option>
<option value="3">Контакт</option>
<option value="4">Фавориты</option>
<option value="5">Аппаратные средства</option>');
define('_profile_type_dropdown', '
<option value="1">Текстовое поле</option>
<option value="2">URL</option>
<option value="3">Адрес электронной почты</option>');
define('_profile_add_head', 'Profilfeld hinzuf&uuml;gen');
define('_profile_added', 'Профилное поля успешно добавили!');
define('_profil_no_name', 'Вы должны указать имя поля!');
define('_profil_deleted', 'Профилное поля успешно удалоно!');
define('_profile_edited', 'Профилное поля успешно изменили!');
## Капилка Клана ##
define('_clankasse_saved', 'Запись успешно добавлена в Капилку Клана!');
define('_clankasse_deleted', 'Запись была успешно удалена из Капилки Клана!');
define('_error_clankasse_empty_datum', 'Вы должны указать дату!');
define('_clankasse_edited', 'Сумма была успешно изменена!');
define('_error_clankasse_empty_transaktion', 'Вы должны ввести описание сделки!');
define('_error_clankasse_empty_betrag', 'Вы должны указать количество!');
define('_clankasse_ctransaktion', 'что');
define('_clankasse_cbetrag', 'сумма');
define('_clankasse_server_head', 'Клан счета');
define('_clankasse_nr', 'Номер счета');
define('_clankasse_blz', 'Код банка - БИК');
define('_clankasse_inhaber', 'Владелец');
define('_clankasse_bank', 'Банк');
define('_clankasse_head', 'Капилка Клана');
define('_clankasse_cakt', 'Банковский баланс');
define('_clankasse_admin_minus', 'Минус');

















define('_clankasse_plus', '<span class="fontGreen">[betrag] [w]</span>');
define('_clankasse_minus', '<span class="fontRed">- [betrag] [w]</span>');
define('_clankasse_summe_plus', '<span class="fontGreen">[summe] [w]</span>');
define('_clankasse_summe_minus', '<span class="fontRed">[summe] [w]</span>');
define('_clankasse_trans', '[transaktion] От / кому [member]');
define('_clankasse_head_edit', 'Платеж изменить');
define('_clankasse_head_new', 'Добавить платеж');
define('_clankasse_sonstiges', 'другойе');
define('_clankasse_for', 'От / кому');
define('_clankasse_einzahlung', '-> Оплата');
define('_clankasse_auszahlung', '-> Выплата');
define('_clankasse_didpayed', 'Статус оплаты');
define('_clankasse_status_status', 'Статус');
define('_clankasse_status_bis', 'до');
define('_clankasse_status_payed', '<span class="fontGreen">заплатил</span> до <span>[payed]</span>');
define('_clankasse_status_today', '<span class="fontGreen">заплатил</span> до <span class="fontBold">сегодня</span>');
define('_clankasse_status_notpayed', '<span class="fontRed">Запоздавший</span> уже с <span class="fontBold">[payed]</span>');
define('_clankasse_status_noentry', 'Никакой ввод');
define('_clankasse_edit_paycheck', 'Редактировать Статус оплаты');
define('_clankasse_payed_till', 'Оплаченно');
define('_info_clankass_status_edited', 'Статус платежа был успешно установлен!');
## Shoutbox ##
define('_shoutbox_head', 'Чат');
define('_error_empty_shout', 'Вы должны ввести текст в чате!');
define('_error_shout_saved', 'Ваш отзыв в чате!');
define('_shoutbox_archiv', 'Архив');
define('_shout_archiv_head', 'чат Архив');
define('_noch', 'еще');
define('_zeichen', 'Знаков');
## Misc ##
define('_error_have_to_be_logged', 'Вы должны быть зарегистрированы, чтобы эту функцию можо было провестьи!');
define('_error_invalid_email', 'Вы ввели неправильный адрес электронной почты!');
define('_error_invalid_url', 'Указанный сайт не доступен!');
define('_error_nick_exists', 'Ник, к сожалению, уже зарегистрирован!');
define('_error_user_exists', 'Логин, к сожалению, уже зарегистрирован!');
define('_error_passwords_dont_match', 'Введенные пароли не совпадают!');
define('_error_email_exists', 'Адрес электронной почты, который вы ввели уже используется кем-то!');
define('_info_edit_profile_done_pwd', 'Вы успешно изменен профиль!');
define('_error_select_buddy', 'Вы не указали пользователя!');
define('_error_buddy_self', 'Вы не можете себя в качестве Бадди поставить!');
define('_error_buddy_already_in', 'Пользователь уже в Баддиспискe!');
define('_error_msg_self', 'Вы не можете писать самаму себе сообщение!');
define('_error_back', 'Назад');
define('_user_dont_exist', 'Этот пользователь не существует!');
define('_error_fwd', 'Далее');
define('_error_wrong_permissions', 'У вас нет необходимых прав для выполнения этого действия!');
define('_error_flood_post', 'Вы можете только каждые [sek] секунд, написать новую запись!');
define('_empty_titel', 'Вы должны указать название!');
define('_empty_eintrag', 'Вы должны написать сообщение');
define('_empty_nick', 'Вы должны ввести свой ник!');
define('_empty_email', 'Необходимо ввести адрес электронной почты!');
define('_empty_user', 'Вы должны ввести имя Логина или е-майл!');
define('_empty_to', 'Вы должны указать адресатa!');
define('_empty_url', 'Вы должны указать URL!');
define('_empty_datum', 'Вы должны указать дату!');
define('_index_headtitle', '[clanname]');
define('_site_sponsor', 'Спонсоры');
define('_site_user', 'Пользовательи');
define('_site_online', 'Посетители онлайн');
define('_site_member', 'Юзер(ы)');
define('_site_serverlist', 'Список серверов');
define('_site_rankings', 'Рейтинг');
define('_site_server', 'Игровой сервер');
define('_site_forum', 'Форум');
define('_site_backup', 'Резервноя копия базы данных');
define('_site_links', 'Cсылки');
define('_site_dl', 'Загрузки');
define('_site_news', 'Новости');
define('_site_messerjocke', 'Messerjocke');
define('_site_banned', 'Список банов');
define('_site_gb', 'Гостевая книга');
define('_site_clankasse', 'Копилка клана');
define('_site_clanwars', 'Клан Вар');
define('_site_upload', 'загрузить');
define('_site_taktiken', 'Тактики');
define('_site_ulist', 'Список пользователей');
define('_site_msg', 'Сообщения');
define('_site_reg', 'Регистрация');
define('_site_shoutbox', 'Чат');
define('_site_user_login', 'Логин');
define('_site_user_lostpwd', 'Забыл Пароль');
define('_site_user_logout', 'Выйти');
define('_site_artikel', 'Артикль');
define('_site_user_lobby', 'Пользователя лобби');
define('_site_user_profil', 'Профиль пользователя');
define('_site_user_editprofil', 'Изменить профиль');
define('_site_user_buddys', 'Бадди');
define('_site_impressum', 'Импрессум');
define('_site_votes', 'Опросы');
define('_site_gallery', 'Галерея');
define('_site_config', '- Админка -');
define('_login', 'Войти');
define('_register', 'зарегистрировать');
define('_userlist', 'Список пользователей');
define('_rankings', 'Рейтинг');
define('_gallery', 'Галерея');
define('_news', 'Новости');
define('_newsarchiv', 'Архив новостей');
define('_serverliste', 'Список серверов');
define('_banned', 'Список банов');
define('_links', 'Линки');
define('_impressum', 'Импрессум');
define('_contact', 'Контакт');
define('_clanwars', 'Клан Вар');
define('_artikel', 'Артикль');
define('_dl', 'Загрузки');
define('_votes', 'Опросы');
define('_forum', 'Форум');
define('_gb', 'Гостевая книга');
define('_squads', 'Команды');
define('_squads_joinus', 'Team-JoinUs');
define('_squads_fightus', 'Team-FightUs');
define('_server', 'Сервер');
define('_editprofil', 'Изменить профиль');
define('_logout', 'Выход');
define('_msg', 'Cообщения');
define('_lobby', 'Лобби');
define('_buddys', 'Бадди');
define('_admin_config', 'Админ');
define('_head_online', 'Онлайн');
define('_head_visits', 'Посетители');
define('_head_max', 'Макс.');
define('_cnt_user', 'Пользователи');
define('_cnt_guests', 'Гости');
define('_cnt_today', 'Сегодня');
define('_cnt_yesterday', 'Вчера');
define('_cnt_online', 'Онлайн');
define('_cnt_all', 'Общий');
define('_cnt_pperday', 'ø в день');
define('_cnt_perday', 'в день');
define('_show', 'Показать');
define('_dont_show', 'Не показывать');
define('_status', 'Статус');
define('_position', 'Позиция');
define('_kind', 'Тип');
define('_cnt', '#');
define('_membergb', 'Профиль Гостевой');
define('_pwd', 'Пароль');
define('_loginname', 'Логин-Имя');
define('_email', 'E-Mail');
define('_hp', 'Страница');
define('_icq', 'ICQ-Nr.');
define('_member', 'Юзер');
define('_user', 'Пользователь');
define('_gast', 'Незарегистрированный');
define('_nothing', '<option value="lazy" class="dropdownKat">- без изменений -</option>');
define('_pn', 'Сообщение');
define('_nick', 'Ник');
define('_info', 'Информация');
define('_error', 'Ошибка');
define('_datum', 'Число');
define('_legende', 'Легенда');
define('_steamid', 'Steam Community-ID');
define('_xboxid', 'Xbox Live');
define('_xboxstatus', 'Xbox Live');
define('_xboxuserpic', 'Xbox Live Avatar:');
define('_psnid', 'Playstation Network');
define('_psnstatus', 'Playstation Network');
define('_skypeid', 'Skype ID (ник)');
define('_skypestatus', 'Skype');
define('_originid', 'Origin');
define('_originstatus', 'Origin');
define('_battlenetid', 'Battlenet');
define('_battlenetstatus', 'Battlenet');
define('_link', 'Линк');
define('_linkname', 'Адрес линка');
define('_url', 'Ссылка');
define('_admin', 'Админка');
define('_hits', 'Хитов');
define('_map', 'Карта');
define('_game', 'Игра');
define('_autor', 'Писатель');
define('_yes', 'Да');
define('_no', 'Нет');
define('_maybe', 'Возможно');
define('_beschreibung', 'Описание');
define('_admin_user_get_identy', 'Вы успешно взcли личность пользователя [nick] !');
define('_comment_added', 'Kомментарию успешно добавили!');
define('_comment_deleted', 'Kомментариуспешно успешно удаленa!');
define('_stichwort', 'Ключевое слово');
define('_eintragen_titel', 'Занести');
define('_titel', 'Название');
define('_bbcode', 'BBCode');
define('_answer', 'Ответ');
define('_eintrag', 'Запись');
define('_weiter', 'Далее');
define('_site_teamspeak', 'Teamspeak');
define('_teamspeak', 'Teamspeak');
define('_site_contact', 'Контакт');
define('_site_joinus', 'JoinUs вступить в клан');
define('_site_fightus', 'FightUs - Заявки на ВАР');
define('_joinus', 'Вступить в клан');
define('_fightus', 'Заявки на ВАР');
define('_site_msg_new', 'Вы получили новые сообщения!<br />
                         Нажмите <a href="../user/?action=msg">здесь</a> чтобы попасть в меню новых сообщений!');
define('_site_kalender', 'Календарь');
define('_login_permanent', ' Авто Логин');
define('_msg_del', 'Удалить отмеченные');
define('_wartungsmodus', 'Сайт пока закрыт на техническое обслуживание!<br />
Пожалуйста, попробуйте еще раз через несколько минут!');
define('_wartungsmodus_head', 'Режим обслуживания');
define('_kalender', 'Календарь');
define('_ts_head', 'Teamspeak');
define('_ts_name', 'Hазвание сервера');
define('_ts_os', 'Oперационная система');
define('_ts_uptime', 'Аптайм');
define('_ts_channels', 'Kаналы');
define('_ts_user', 'Пользователи');
define('_ts_users_head', 'информация о пользователе');
define('_ts_player', 'Пользователь');
define('_ts_channel', 'Kанал');
define('_ts_logintime', 'на сервере');
define('_ts_idletime', 'АФК, уже как');
define('_ts_channel_head', 'Информация о канале');
define('_taktik_choose', ' - Выберите - ');
define('_config_tmpdir', 'Стандартный дизайн');
define('_rankings_head', 'Рейтинг');
define('_rankings_league', 'Лига');
define('_rankings_place', 'Mесто старое/новое');
define('_rankings_admin_place', 'Место');
define('_rankings_squad', 'Kоманда');
define('_rankings_teamlink', 'Ссылка нa Командy');
define('_ranking_added', 'Рейтинг успешно добавлен!');
define('_ranking_edited', 'Рейтинг успешно изменили!');
define('_ranking_deleted', 'Рейтинг успешно удалили!');
define('_ranking_empty_league', 'Вы должны указать лигу!');
define('_ranking_empty_url', 'Вы должны указать ссылку на лигу!');
define('_ranking_empty_rank', 'Вы должны указать ранг!');
define('_rankings_add_head', 'Добавить рейтинг');
define('_navi_info', 'Все "_" Название (как _admin_) являются шаблономи, для соответствующего перевода требуется!');
define('_member_admin_intnews', 'Bидит внутренние новости?');
define('_news_admin_intern', 'Внутренние новости?');
define('_news_sticky', '<span class="fontWichtig">Прикрепить:</span>');
define('_news_get_sticky', 'к Новостям прикрепить?');
define('_news_sticky_till', 'до:');
define('_cw_xonx', 'XнаX');
define('_forum_lp_head', 'Последнее сообщение Форума');
define('_forum_previews', 'Предварительный просмотр');
define('_site_awards', 'Награды');
define('_error_unregistered_nc', '
<tr>
  <td class="contentMainFirst" align="center" colspan="2">
    <span class="fontBold">Вы должны быть зарегистрированы чтобы оставить свое мнение!</span>
  </td>
</tr>');
define('_server_legendemenu', 'Сервер зарегистрировать в меню? (Нажмите, на иконку чтобы изменить статус)<br />(Несколько записей возможно!)');
define('_config_c_servernavi', 'Меню: Состояние сервера');
define('_upload_partners_head', 'Партнер Кнопки');
define('_upload_partners_info', 'Только jpg, gif и png Файлы. Рекомендуемый размер: 88px * 31px');
define('_select_field_ranking_add', '<option value="[value]" [sel]>[what]</option>');
define('_user_list_ck', 'Указать в меню: Капилку клана?');
define('_fightus_squad', 'Желаемую команду');