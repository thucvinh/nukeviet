<?php

/**
 * @Project NUKEVIET 3.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2012 VINADES ., JSC. All rights reserved
 * @Createdate Jun 20, 2010 8:59:32 PM
 */

if( ! defined( 'NV_MAINFILE' ) )
	die( 'Stop!!!' );

define( 'NV_MODULE_SETUP_DEFAULT', 'users,statistics,banners,search,news,contact,about,voting,rss,menu,page' );

function nv_create_table_news( $lang_data, $module_data, $catid )
{
	global $db, $db_config;
	return $db->exec( 'CREATE TABLE ' . $db_config['prefix'] . '_' . $lang_data . '_' . $module_data . '_' . $catid . ' AS SELECT * FROM ' . $db_config['prefix'] . '_' . $lang_data . '_' . $module_data . '_rows where 1=0' );
}

function nv_delete_table_sys( $lang )
{
	global $db_config, $global_config;

	$sql_drop_table = array();
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_modules";
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_blocks_groups";
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_blocks_weight";
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_modfuncs";
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_searchkeys";
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_referer_stats";
	$sql_drop_table[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_modthemes";
	$sql_drop_table[] = "ALTER TABLE " . $db_config['prefix'] . "_cronjobs DROP COLUMN " . $lang . "_cron_name";

	return $sql_drop_table;
}

function nv_create_table_sys( $lang )
{
	global $db_config, $global_config;

	$xml = simplexml_load_file( NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/config.ini' );
	$layoutdefault = ( string )$xml->layoutdefault;

	$sql_create_table = array();
	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_modules (
		 title VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 module_file VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 module_data VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 custom_title VARCHAR2(255 CHAR) DEFAULT '' NOT NULL ENABLE,
		 admin_title VARCHAR2(255 CHAR) DEFAULT '',
		 set_time NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 main_file NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 admin_file NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 theme VARCHAR2(100 CHAR) DEFAULT '',
		 mobile VARCHAR2(100 CHAR) DEFAULT '',
	 	 description VARCHAR2(255 CHAR) DEFAULT '',
		 keywords VARCHAR2(4000 CHAR) DEFAULT '',
		 groups_view VARCHAR2(255 CHAR) DEFAULT '' NOT NULL ENABLE,
		 in_menu NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 weight NUMBER(3,0) DEFAULT 1 NOT NULL ENABLE,
		 submenu NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 act NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 admins VARCHAR2(255 CHAR) DEFAULT '',
		 rss NUMBER(3,0) DEFAULT 1 NOT NULL ENABLE,
		 gid NUMBER(5,0) DEFAULT 0 NOT NULL ENABLE,
		 primary key (title)
	)";

	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_blocks_groups (
		 bid NUMBER(8,0) DEFAULT 0 NOT NULL ENABLE ,
		 theme VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 module VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 file_name VARCHAR2(55 CHAR)DEFAULT '',
		 title VARCHAR2(255 CHAR)DEFAULT '',
		 link VARCHAR2(255 CHAR)DEFAULT '',
		 template VARCHAR2(55 CHAR)DEFAULT '',
		 position VARCHAR2(55 CHAR)DEFAULT '',
		 exp_time NUMBER(11,0) DEFAULT 0,
		 active NUMBER(3,0) DEFAULT 0,
		 groups_view VARCHAR2(255 CHAR) DEFAULT '',
		 all_func NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 weight NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 config VARCHAR2(4000 CHAR),
		 primary key (bid)
	)";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_blocks_groups_theme ON " . $db_config['prefix'] . "_" . $lang . "_blocks_groups(theme) TABLESPACE USERS";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_blocks_groups_module ON " . $db_config['prefix'] . "_" . $lang . "_blocks_groups(module) TABLESPACE USERS";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_blocks_groups_position ON " . $db_config['prefix'] . "_" . $lang . "_blocks_groups(position) TABLESPACE USERS";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_blocks_groups_exp_time ON " . $db_config['prefix'] . "_" . $lang . "_blocks_groups(exp_time) TABLESPACE USERS";

	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_blocks_weight (
		 bid NUMBER(8,0) DEFAULT 0 NOT NULL ENABLE,
		 func_id NUMBER(8,0) DEFAULT 0 NOT NULL ENABLE,
		 weight NUMBER(8,0) DEFAULT 0 NOT NULL ENABLE,
		 CONSTRAINT cnv_" . $lang . "blocks_weight_bid UNIQUE (bid,func_id)
	)";

	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_modfuncs (
		 func_id NUMBER(8,0) DEFAULT 0 NOT NULL ENABLE ,
		 func_name VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 alias VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 func_custom_name VARCHAR2(255 CHAR) DEFAULT '' NOT NULL ENABLE,
		 in_module VARCHAR2(55 CHAR) DEFAULT '' NOT NULL ENABLE,
		 show_func NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 in_submenu NUMBER(3,0) DEFAULT 0 NOT NULL ENABLE,
		 subweight NUMBER(5,0) DEFAULT 1 NOT NULL ENABLE,
		 setting VARCHAR2(255 CHAR) DEFAULT NULL,
		 primary key (func_id),
		 CONSTRAINT cnv_" . $lang . "modfuncs_func_name UNIQUE (func_name,in_module),
		 CONSTRAINT cnv_" . $lang . "modfuncs_alias UNIQUE (alias,in_module)
	)";

	$sql_create_table[] = 'create sequence SNV_' . strtoupper( $lang ) . '_MODFUNCS';
	$sql_create_table[] = 'CREATE OR REPLACE TRIGGER TNV_' . strtoupper( $lang ) . '_MODFUNCS
	 BEFORE INSERT ON ' . $db_config['prefix'] . '_' . $lang . '_modfuncs
	 FOR EACH ROW WHEN (new.func_id is null)
		BEGIN
		 SELECT SNV_' . strtoupper( $lang ) . '_MODFUNCS.nextval INTO :new.func_id FROM DUAL;
		END TNV_' . strtoupper( $lang ) . '_MODFUNCS;';

	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_searchkeys (
		 id VARCHAR2(32 CHAR) DEFAULT '' NOT NULL ENABLE,
		 skey VARCHAR2(255 CHAR) DEFAULT '' NOT NULL ENABLE,
		 total NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 search_engine VARCHAR2(50 CHAR) DEFAULT '' NOT NULL ENABLE
	)";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_searchkey_sid ON " . $db_config['prefix'] . "_" . $lang . "_searchkeys(id) TABLESPACE USERS";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_searchkey_skey ON " . $db_config['prefix'] . "_" . $lang . "_searchkeys(skey) TABLESPACE USERS";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_searchkey_engine ON " . $db_config['prefix'] . "_" . $lang . "_searchkeys(search_engine) TABLESPACE USERS";

	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_referer_stats (
		 host VARCHAR2(255 CHAR) DEFAULT '' NOT NULL ENABLE,
		 total NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month01 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month02 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month03 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month04 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month05 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month06 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month07 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month08 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month09 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month10 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month11 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 month12 NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 last_update NUMBER(11,0) DEFAULT 0 NOT NULL ENABLE,
		 CONSTRAINT cnv_" . $lang . "referer_stats_host UNIQUE (host)
	)";
	$sql_create_table[] = "CREATE INDEX inv_" . $lang . "_referer_stats_total ON " . $db_config['prefix'] . "_" . $lang . "_referer_stats(total) TABLESPACE USERS";

	$sql_create_table[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_modthemes (
		 func_id NUMBER(8,0) DEFAULT 0,
		 layout VARCHAR2(100 CHAR)DEFAULT '',
		 theme VARCHAR2(100 CHAR)DEFAULT '',
		 CONSTRAINT cnv_" . $lang . "modthemes_func_id UNIQUE (func_id,layout,theme)
	 )";

	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('about', 'page', 'about', 'About', '', 1276333182, 1, 1, '', '', '', '', '0', 1, 1, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('news', 'news', 'news', 'News', '',1270400000, 1, 1, '', '', '', '', '0', 1, 2, 1, 1, '', 1, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('users', 'users', 'users', 'Users', '', 1274080277, 1, 1, '', '', '', '', '0', 1, 5, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('contact', 'contact', 'contact', 'Contact', '',1275351337, 1, 1, '', '', '', '', '0', 1, 6, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('statistics', 'statistics', 'statistics', 'Statistics', '', 1276520928, 1, 0, '', '', '', 'online, statistics', '0', 1, 7, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('voting', 'voting', 'voting', 'Voting', '', 1275315261, 1, 1, '', '', '', '', '0', 0, 8, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('banners', 'banners', 'banners', 'Banners', '',1270400000, 1,1, '', '', '', '', '0', 0, 9, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('search', 'search', 'search', 'Search', '', 1273474173, 1, 0, '', '', '', '', '0', 0, 10, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('menu', 'menu', 'menu', 'Menu Site', '', 1295287334, 0, 1, '', '', '', '', '0', 0, 9, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('rss', 'rss', 'rss', 'Rss', '', 1279360267, 1, 1, '', '', '', '', '0', 0, 11, 1, 1, '', 0, 0)";
	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modules (title, module_file, module_data, custom_title, admin_title, set_time, main_file, admin_file, theme, mobile, description, keywords, groups_view, in_menu, weight, submenu, act, admins, rss, gid) VALUES ('page', 'page', 'page', 'page', '', 1279360267, 1, 1, '', '', '', '', '0', 0, 11, 1, 1, '', 0, 0)";

	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'site_name', 'NukeViet CMS 3.x')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'site_logo', 'images/logo.png')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'site_description', 'NukeViet CMS 3.x Developed by VINADES.,JSC')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'site_keywords', '')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'site_theme', '" . $global_config['site_theme'] . "')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'site_home_module', 'users')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'switch_mobi_des', '1')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'upload_logo', 'images/logo.png')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'autologosize1', '50')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'autologosize2', '40')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'autologosize3', '30')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'autologomod', '')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'metaTagsOgp', '1')";
	$sql_create_table[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', 'global', 'disable_site_content', 'For technical reasons Web site temporary not available. we are very sorry for any inconvenience!')";

	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_setup_language (lang, setup) VALUES('" . $lang . "', 1)";

	$sql_create_table[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_modthemes (func_id, layout, theme) VALUES ('0','" . $layoutdefault . "', '" . $global_config['site_theme'] . "')";

	$sql_create_table[] = "ALTER TABLE " . $db_config['prefix'] . "_cronjobs ADD " . $lang . "_cron_name VARCHAR2(255 CHAR) DEFAULT ''";

	return $sql_create_table;
}
?>