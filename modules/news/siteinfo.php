<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Apr 20, 2010 10:47:41 AM
 */

if (! defined('NV_IS_FILE_SITEINFO')) {
    die('Stop!!!');
}

$lang_siteinfo = nv_get_lang_module($mod);

// Tong so bai viet
$number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status= 1')->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_publtime'], 'value' => number_format($number) );
}

//So bai viet thanh vien gui toi
if (! empty($site_mods[$mod]['admins'])) {
    $admins_module = explode(',', $site_mods[$mod]['admins']);
} else {
    $admins_module = array();
}
$result = $db_slave->query('SELECT admin_id FROM ' . NV_AUTHORS_GLOBALTABLE . ' WHERE lev=1 OR lev=2');
while ($row = $result->fetch()) {
    $admins_module[] = $row['admin_id'];
}
$number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE admin_id NOT IN (' . implode(',', $admins_module) . ')')->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_users_send'], 'value' => number_format($number) );
}

// So bai viet cho dang tu dong
$number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status= 1 AND publtime > ' . NV_CURRENTTIME . ' AND (exptime=0 OR exptime>' . NV_CURRENTTIME . ')')->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_pending'], 'value' => number_format($number) );
}

// So bai viet da het han
$number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE exptime > 0 AND exptime<' . NV_CURRENTTIME)->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_expired'], 'value' => number_format($number) );
}

// So bai viet sap het han
$number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_rows WHERE status = 1 AND exptime>' . NV_CURRENTTIME)->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_exptime'], 'value' => number_format($number) );
}

// Tong so binh luan duoc dang
$number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_comment WHERE module=' . $db_slave->quote($mod) . ' AND status = 1')->fetchColumn();
if ($number > 0) {
    $siteinfo[] = array( 'key' => $lang_siteinfo['siteinfo_comment'], 'value' => number_format($number) );
}

// Nhac nho cac tu khoa chua co mo ta
if (! empty($module_config[$mod]['tags_remind'])) {
    $number = $db_slave->query('SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_tags WHERE description = \'\'')->fetchColumn();

    if ($number > 0) {
        $pendinginfo[] = array(
            'key' => $lang_siteinfo['siteinfo_tags_incomplete'],
            'value' => number_format($number),
            'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $mod . '&amp;' . NV_OP_VARIABLE . '=tags&amp;incomplete=1',
        );
    }
}
