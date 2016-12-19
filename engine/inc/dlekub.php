<?php
/*
  =====================================================
  Admin Module by CaguCT v 1.009 utf-8
  -----------------------------------------------------
  http://caguct.com/
  -----------------------------------------------------
  Copyright (c) 2015
  =====================================================
  Свободная лицензия GPL вродь как
  =====================================================
  Файл: dlekub.php
  -----------------------------------------------------
  Назначение: постинг с сайта videokub.net
  =====================================================
 */
if( !defined('DATALIFEENGINE') OR ! defined('LOGGED_IN') )
{
    die("Hacking attempt!");
}

if( !$user_group[$member_id['user_group']]['admin_rss'] )
{
    msg("error", $lang['index_denied'], $lang['index_denied']);
}

// asd

//////////////// INSTALL /////////////////////
/*
  file: engine/modules/show.full.php
  find:
  $tpl->set( '{full-story}', $row['full_story'] );

  write before:

  include dirname(__FILE__).'/tabgeo_country_v4/tabgeo_country_v4.php';
  if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
  $ip = $_SERVER['REMOTE_ADDR'];
  }
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  $country_code = tabgeo_country_v4($ip);
  if( $country_code == $row['country'] )
  $row['full_story'] = preg_replace("#<iframe.*?</iframe>#isu", '<img src="link for img" />', $row['full_story']);


 */



////////////// DO /////////////////

define('USERAGENT', 'Opera/9.80 (Windows NT 5.1; U; MRA 5.9 (build 4848); ru) Presto/2.9.168 Version/11.52');
$error = '';
$set = '';
$set_file = dirname(__FILE__) . '/vkubset.php';
if( is_file($set_file) )
    include_once $set_file;
else
{
    // Install SQL
    // ALTER TABLE  `dle_post` ADD  `country` VARCHAR( 255 ) NOT NULL AFTER  `metatitle` ;

    if( @file_put_contents($set_file, '') === false )
        die('ОШИБКА! Не удалось создать файл <b>' . $set_file . '</b>, создайте его вручную, выставите права на чтение и запись <b>666</b>.');
}

// REQUEST
$_REQUEST['items_per_page'] = !empty($_REQUEST['items_per_page']) ? $_REQUEST['items_per_page'] : 10;
$_REQUEST['pages'] = !empty($_REQUEST['pages']) ? $_REQUEST['pages'] : 1;
$_POST['mod'] = !empty($_POST['mod']) ? $_POST['mod'] : 'news';
$_GET['category'] = !empty($_GET['category']) ? $_GET['category'] : NULL;
$_GET['items_per_page'] = !empty($_GET['items_per_page']) ? $_GET['items_per_page'] : NULL;
$_POST['post_status'] = !empty($_POST['post_status']) ? $_POST['post_status'] : 'publish';
$_POST['add_tags'] = !empty($_POST['add_tags']) ? $_POST['add_tags'] : '0';
$_POST['catnews'] = !empty($_POST['catnews']) ? $_POST['catnews'] : 0;
$_POST['catblog'] = !empty($_POST['catblog']) ? $_POST['catblog'] : 0;

// REQUEST END

/*
 * Save function array
 * @param array, int
 */
function arrayToPhp( $array, $name = "set", $inc = 0 )
{
    ob_start();
    if( is_array($array) )
    {
        echo "<?php\n";
        echo '$' . $name . ' = ';
        echo "array(\n";

        foreach($array as $key => $item)
        {
            echo str_repeat("\t", $inc + 1);

            if( !is_array($item) )
            {
                echo '\'' . $key . '\'' . ' => \'' . str_replace('\'', "\'", $item) . '\',' . "\n";
            }
            else
            {
                echo '\'' . $key . '\' => ';
                echo arrayToPhp($item, ($inc + 1));
            }
        }

        echo str_repeat("\t", $inc);
        echo ');' . "\n";
    }
    return ob_get_clean();
}

/*
 * Изменяем кодировку на windows-1251
 * @param String, array
 * return string, array
 */

function winiconv( $str )
{
    if( is_array($str) )
    {
        $str = array_map("winiconv", $str);
    }
    else
        $str = iconv('utf-8', 'windows-1251', $str);

    return $str;
}

function randStr( $num = 10, $sign = false )
{

    $a = range(0, 9);
    $b = range('a', 'z');
    $c = range('A', 'Z');
    $d = range('!', '@');

    $arr = array_merge($a, $b);
    $arr = array_merge($arr, $a);
    $arr = array_merge($arr, $c);

    if( $sign === true )
        $arr = array_merge($arr, $d);

    $key = '';
    $rand = microtime(true);

    for($i = 0; $i < $num; ++$i)
    {
        shuffle($arr);
        $countArr = count($arr) - 1;
        $key .= $arr[ceil(round(($rand * 1000 - floor($rand * 1000)), 2) * $countArr)];
        $rand = microtime(true);
    }
    return $key;
}

/// ############################################################################ ///
// Set template
$set['vkub_shortstory'] = !empty($set['vkub_shortstory']) ? $set['vkub_shortstory'] : NULL;
if( !empty($_POST['why']) )
    $set['vkub_shortstory'] = !empty($_POST['shortstory']) ? $_POST['shortstory'] : NULL;
$_POST['shortstory'] = !empty($set['vkub_shortstory']) ? stripslashes($set['vkub_shortstory']) : '[image]' . "\n" . '[description]';

$set['vkub_fullstory'] = !empty($set['vkub_fullstory']) ? $set['vkub_fullstory'] : NULL;
if( !empty($_POST['why']) )
    $set['vkub_fullstory'] = !empty($_POST['fullstory']) ? $_POST['fullstory'] : NULL;
$_POST['fullstory'] = !empty($set['vkub_fullstory']) ? stripslashes($set['vkub_fullstory']) : '[description]' . "\n" . '[video=680x450]';


// Set attach
$set['add_attach'] = !empty($set['add_attach']) ? $set['add_attach'] : 0;
if( !empty($_POST['why']) )
    $set['add_attach'] = !empty($_POST['add_attach']) ? $_POST['add_attach'] : 0;

// Set tags
$set['add_tags'] = !empty($set['add_tags']) ? $set['add_tags'] : 0;
if( !empty($_POST['why']) )
    $set['add_tags'] = !empty($_POST['add_tags']) ? $_POST['add_tags'] : 0;

// Set country
$set['add_cous'] = !empty($set['add_cous']) ? $set['add_cous'] : 0;
if( !empty($_POST['why']) )
    $set['add_cous'] = !empty($_POST['add_cous']) ? $_POST['add_cous'] : 0;

// Set see new
$set['see_new'] = !empty($set['see_new']) ? $set['see_new'] : 0;
if( !empty($_POST['why']) )
    $set['see_new'] = !empty($_POST['see_new']) ? $_POST['see_new'] : 0;

// Set rubr
$set['rubr'] = !empty($set['rubr']) ? $set['rubr'] : 0;
if( !empty($_POST['why']) )
    $set['rubr'] = !empty($_POST['cat' . $set['mod']]) ? $_POST['cat' . $set['mod']] : 0;

// Set post_status
$set['post_status'] = !empty($set['post_status']) ? $set['post_status'] : 0;
if( !empty($_POST['why']) )
    $set['post_status'] = !empty($_POST['post_status']) ? $_POST['post_status'] : 'publish';
// Set template end /////////////////////////////////////////////////////////////////////////////

if( !empty($_POST['why']) )
    file_put_contents($set_file, arrayToPhp($set));

$api = 'http://www.videokub.net/api/';
$url = $api . $_REQUEST['items_per_page'] . '/' . $_REQUEST['pages'] . '/';
$json = file_get_contents($url);
$json = preg_replace("#\t#is", ' ', $json);
$array = $config['charset'] == 'windows-1251' ? winiconv(json_decode($json, true)) : json_decode($json, true);

$category_option = '';
$category = array();
foreach($array['category'][0] as $key => $val)
{
    if( !empty($_REQUEST['category']) && $_REQUEST['category'] == $key )
        $category_option .= '<option value="' . $key . '" selected="selected">' . $val[0][key($val[0])] . '</option>';
    else
        $category_option .= '<option value="' . $key . '">' . $val[0][key($val[0])] . '</option>';

    $category[$key] = key($val[0]);
}

$url = $api;

$modpage = $config['http_home_url'] . $config['admin_path'] . '?mod=' . $mod;
if( !empty($_REQUEST['category']) && $_REQUEST['category'] !== 0 )
{
    $url .= $category[$_REQUEST['category']] . '/';
    $modpage .= '&category=' . $_REQUEST['category'];
}
if( !empty($_REQUEST['items_per_page']) )
{
    $url .= $_REQUEST['items_per_page'] . '/';
    $modpage .= '&items_per_page=' . $_REQUEST['items_per_page'];
}

$url .= $_REQUEST['pages'] . '/';

$json = file_get_contents($url);
$json = preg_replace("#\t#is", ' ', $json);
$array_utf = json_decode($json, true);
$array = $config['charset'] == 'windows-1251' ? winiconv($array_utf) : $array_utf;

// Count Pages
$page_count = ceil($array['videos_count'] / $_REQUEST['items_per_page']);
$_REQUEST['pages'] = $_REQUEST['pages'] > $page_count ? $page_count : $_REQUEST['pages'];

$disabled = '';
$prev_pages = '';
$next_pages = '';

if( $_REQUEST['pages'] == 1 )
    $disabled = 'disabled';
$prev_pages .= '<a class="first-page ' . $disabled . '" title="Перейти на первую страницу" href="' . $modpage . '">«</a>';
$prev = $_REQUEST['pages'] > 1 ? $modpage . '&pages=' . ($_REQUEST['pages'] - 1) : $modpage;
$prev_pages .= '<a class="prev-page ' . $disabled . '" title="Перейти на предыдущую страницу" href="' . $prev . '">‹</a>';
$disabled = '';
if( $_REQUEST['pages'] == $page_count )
    $disabled = 'disabled';
$next = $_REQUEST['pages'] < $page_count ? $modpage . '&pages=' . ($_REQUEST['pages'] + 1) : $modpage . '&pages=' . $page_count;
$next_pages .= '<a class="next-page ' . $disabled . '" title="Перейти на следующую страницу" href="' . $next . '">›</a>';
$next_pages .= '<a class="last-page ' . $disabled . '" title="Перейти на последнюю страницу" href="' . $modpage . '&pages=' . $page_count . '">»</a>';

/* * ***************************************************** */
/* * ********** *//////// Add on site /////////*************/
/* * ***************************************************** */
if( !empty($_POST['why']) && $_POST['why'] == 'addpost' && isset($_POST['post']) )
{
    if( !empty($_POST['url']) && preg_match('#^http://www\\.videokub.*$#isu', $_POST['url']) )
    {
        $addjson = file_get_contents($_POST['url']);
        $addarray_utf = json_decode($addjson, true);
        $addarray = $config['charset'] == 'windows-1251' ? winiconv($addarray_utf) : $addarray_utf;
        $videos = array();
        foreach($addarray['videos'] as $val)
            $videos[$val['id']] = $val;
        $description_utf = array();
        foreach($addarray_utf['videos'] as $val)
            $description_utf[$val['id']] = $val['description'];


        $post_update = 0;
        $post_insert = 0;
        $i = 0;
        $len = count($_POST['post']);
        $_POST['post'] = array_reverse($_POST['post'], true);
        foreach($_POST['post'] as $val)
        {
            if( !empty($videos[$val]['id']) )
            {

                if( $config['charset'] == 'windows-1251' )
                    $description = iconv('utf-8', 'windows-1251', urldecode($description_utf[$val]));
                else
                    $description = urldecode($description_utf[$val]);

                //print_r($videos[$val]);
                $shortstory = nl2br($_POST['shortstory']);
                // Replace [title]
                $shortstory = preg_replace("#\\[title\\]#is", $db->safesql($videos[$val]['title']), $shortstory);
                // Replace [description]
                $shortstory = strtr($shortstory, array('[description]' => $description));
                // Replace [image]
                $shortstory = preg_replace("#\\[image\\]#is", $videos[$val]['preview'], $shortstory);
                // Replace [video]
                $shortstory = preg_replace("#\\[video=([0-9]+)x([0-9]+)\\]#is", '<iframe width="$1" height="$2" src="http://www.videokub.net/embed/' . $videos[$val]['id'] . '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>', $shortstory);

                //print_r($videos[$val]);
                $fullstory = nl2br($_POST['fullstory']);
                // Replace [title]
                $fullstory = preg_replace("#\\[title\\]#is", $db->safesql($videos[$val]['title']), $fullstory);
                // Replace [description]
                $fullstory = strtr($fullstory, array('[description]' => $description));
                // Replace [image]
                $fullstory = preg_replace("#\\[image\\]#is", $videos[$val]['preview'], $fullstory);
                // Replace [video]
                $fullstory = preg_replace("#\\[video=([0-9]+)x([0-9]+)\\]#is", '<iframe width="$1" height="$2" src="http://www.videokub.net/embed/' . $videos[$val]['id'] . '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>', $fullstory);

                $tags = isset($videos[$val]['tags'][0]) ? $videos[$val]['tags'][0] : NULL;
                $country = '';

                $category_arr = isset($videos[$val]['category'][0]) ? $videos[$val]['category'][0] : NULL;

                if( $set['add_tags'] == 0 )
                    $tags = array();
                #if( $set['add_cous'] == 1 ) $country = 'UA';
                $res = array_search($val, $_POST['country']);
                if( $res !== false && $res !== NULL )
                    $country = 'UA';

                $autor = $member_id['name'];
                $date = date("Y-m-d H:i:s");
                $title = $db->safesql($videos[$val]['title']);
                $alt_name = $db->safesql($videos[$val]['alt_title']);
                $descr = substr(strip_tags($description), 0, 100);
                if( $config['charset'] != 'windows-1251' )
                    $descr = mb_substr(strip_tags($description, 0, 100, "utf-8"));

                $allow_comm = 1;
                $allow_main = 1;
                $approve = 1;
                $allow_br = 1;
                $tags = implode(', ', $tags);
                $category = array();

                // Add category
                foreach($category_arr as $value)
                {
                    $sql = $db->super_query("SELECT id FROM " . PREFIX . "_category WHERE name='" . $value . "'");
                    if( empty($sql['id']) )
                    {
                        // add cat
                        $value = $db->safesql($value);
                        $sql = $db->query("INSERT INTO " . PREFIX . "_category (`name`, `alt_name`) VALUES ('" . $value . "', '" . trim(totranslit(stripslashes($value))) . "')");
                        //$sql['id'] = $db->insert_id(); сука не везде пашет
                        $sql = $db->super_query("SELECT id FROM " . PREFIX . "_category WHERE name='" . $value . "'");

                        @unlink(ENGINE_DIR . '/cache/system/category.php');
                        clear_cache();
                    }
                    $category[] = $sql['id'];
                }
                $category = implode(', ', $category);

                // Черновик
                if( $set['post_status'] == 'draft' )
                    $approve = 0;

                if( !empty($_POST['add_attach']) )
                {
                    // Main image
                    $filename = randStr(5) . '_' . pathinfo($videos[$val]['preview'], PATHINFO_BASENAME);
                    $datedir = date("Y-m");
                    if( !is_file(ROOT_DIR . "/uploads/posts/" . $datedir . "/" . $filename) )
                    {
                        $result = file_get_contents($videos[$val]['preview']);
                        if( $http_response_header[0] !== 'HTTP/1.1 404 Not Found' )
                        {

                            if( !is_dir(ROOT_DIR . "/uploads/posts/" . $datedir . "/") )
                            {

                                @mkdir(ROOT_DIR . "/uploads/posts/" . $datedir . "/", 0777);
                                @chmod(ROOT_DIR . "/uploads/posts/" . $datedir . "/", 0777);
                            }
                            if( file_put_contents(ROOT_DIR . "/uploads/posts/" . $datedir . "/" . $filename, $result) )
                            {
                                $shortstory = strtr($shortstory, array($videos[$val]['preview'] => $config['http_home_url'] . 'uploads/posts/' . $datedir . "/" . $filename));
                                $fullstory = strtr($fullstory, array($videos[$val]['preview'] => $config['http_home_url'] . 'uploads/posts/' . $datedir . "/" . $filename));
                            }
                        }
                    }
                    else
                    {
                        $shortstory = strtr($shortstory, array($videos[$val]['preview'] => $config['http_home_url'] . 'uploads/files/' . date("Y-m") . "/" . $filename));
                        $fullstory = strtr($fullstory, array($videos[$val]['preview'] => $config['http_home_url'] . 'uploads/files/' . date("Y-m") . "/" . $filename));
                    }
                }

                // Test on base
                $sql = $db->super_query("SELECT id FROM " . PREFIX . "_post WHERE title='" . $title . "' AND alt_name='" . $alt_name . "'");

                if( empty($sql['id']) )
                {

                    // add on site
                    $sql = $db->query("INSERT INTO " . PREFIX . "_post
									(autor,
									 date,
									 short_story,
									 full_story,
									 title,
									 descr,
									 category,
									 alt_name,
									 allow_comm,
									 allow_main,
									 approve,
									 allow_br,
									 tags,
									 country
									 )
									VALUES
									('" . $autor . "',
									 '" . $date . "',
									 '" . $db->safesql($shortstory) . "',
									 '" . $db->safesql($fullstory) . "',
									 '" . $title . "',
									 '" . $db->safesql($descr) . "',
									 '" . $category . "',
									 '" . $alt_name . "',
									 '1',
									 '1',
									 " . (int) $approve . ",
									 '1',
									 '" . $db->safesql($tags) . "',
									 '" . $country . "')");

                    $row = $db->insert_id();
                    $db->query("INSERT INTO " . PREFIX . "_post_extras (news_id, user_id) VALUES('{$row}', '{$member_id['user_id']}')");

                    // Tags
                    if( $tags != "" )
                    {

                        $dletags = array();

                        $tags = explode(",", $tags);

                        foreach($tags as $value)
                        {

                            $dletags[] = "('" . $row . "', '" . trim($value) . "')";
                        }

                        $dletags = implode(", ", $dletags);
                        $db->query("INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $dletags);
                    }

                    ++$post_insert;
                }
                else
                {
                    // Update
                    $sql = $db->query("UPDATE " . PREFIX . "_post
									SET `autor`='" . $autor . "',
									 `date`='" . $date . "',
									 `title`='" . $title . "',
									 `short_story`='" . $db->safesql($shortstory) . "',
									 `full_story`='" . $db->safesql($fullstory) . "',
									 `descr`='" . $db->safesql($descr) . "',
									 `category`='" . $category . "',
									 `alt_name`='" . $alt_name . "',
									 `allow_comm`=1,
									 `allow_main`=1,
									 `approve`=" . (int) $approve . ",
									 `allow_br`=1,
									 `tags`='" . $db->safesql($tags) . "',
									 `country`='" . $country . "'
									WHERE id='" . $sql['id'] . "'");

                    ++$post_update;
                }
            }
        }

        $error = '<b>Добавлено</b> ' . $post_insert . ' видео, <b>обновлено</b> ' . $post_update . ' видео';
    }
}



/* * ***************************************************** */
/* * ********** *//////// View on site /////////************/
/* * ***************************************************** */

$query = '';
ob_start();
foreach($array['videos'] as $key => $val)
{
    if( isset($val['category']) )
        $val['category'] = implode(', ', $val['category'][0]);
    else
        $val['category'] = '';
    if( isset($val['tags']) )
        $val['tags'] = implode(', ', $val['tags'][0]);
    else
        $val['tags'] = '';

    if( $config['charset'] == 'windows-1251' )
        $description = substr(iconv('utf-8', 'windows-1251', urldecode($array_utf['videos'][$key]['description'])), 0, 100);
    else
        $description = mb_substr(urldecode($array['videos'][$key]['description']), 0, 100, "utf-8");

    if( !empty($query) )
        $query .= ' OR ';
    $query .= "title='" . $db->safesql($val['title']) . "' AND alt_name='" . $db->safesql($val['alt_title']) . "'";

    // Test on base
    $sql = NULL;
    $val['new'] = '+';
    $sql = $db->super_query("SELECT id FROM " . PREFIX . "_post WHERE title='" . $db->safesql($val['title']) . "' AND alt_name='" . $db->safesql($val['alt_title']) . "'");
    if( !empty($sql['id']) )
        $val['new'] = NULL;

    if( $set['see_new'] == 1 and $val['new'] == NULL )
    {

    }
    else
    {
        echo '<tr class="type-post">
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="cb-select-1316">Выбрать ' . $val['title'] . '</label>
				<input id="cb-select-1316" type="checkbox" name="post[]" value="' . $val['id'] . '">
			</th>
			<th scope="row" class="check-column-2">
				<input type="checkbox" name="country[]" value="' . $val['id'] . '">
			</th>
			<th>
		  <span>' . $val['new'] . '</span>
	  </th>
			<td class="images" width="50px"><img style="width:50px" src="' . $val['image']['150x113'][0][1] . '"></td>
			<td class="post-title">
				<strong><a class="row-title" href="' . $val['url'] . '" target="_blank">' . $val['title'] . '</a></strong>
			</td>
			<td class="categories column-categories">' . $val['category'] . '</td>
			<td class="tag">' . $val['tags'] . '</td>
			<td class="description">' . $description . '</td>
			<td class="date column-date" width="140px">' . $val['date'] . '</td>
		</tr>';
    }
}
$newslist = ob_get_clean();


// Auto
$_GET['auto'] = !empty($_GET['auto']) ? $_GET['auto'] : NULL;
$_GET['time'] = !empty($_GET['time']) ? $_GET['time'] : 0;
if( !empty($_GET['auto']) )
{
    $_POST['url'] = $api . $_GET['auto'] . '/';
    $ids = json_decode(file_get_contents($_POST['url']), true);
    foreach($ids['videos'] as $key => $val)
    {
        $_POST['post'][] = $val['id'];
    }
    if( is_array($_POST['post']) )
    {
        $_POST['why'] = 'addpost';

        if( $_GET['time'] > 0 )
            header('Refresh: ' . (60 * $_GET['time']) . '; url=/admin.php?mod=dlekub&auto=' . $_GET['auto'] . '&time=' . $_GET['time']);
    }
}

// Coounts
$count_new = 0;
if( !empty($query) )
{
    $sql = $db->super_query("SELECT COUNT(id) FROM " . PREFIX . "_post WHERE " . $query);
    $count_new = $_REQUEST['items_per_page'] - $sql['COUNT(id)'];
}
$sql = $db->super_query("SELECT title FROM " . PREFIX . "_post ORDER BY id DESC LIMIT 0,1");
$last_news = $sql['title'];


////////////// CONTENT ////////////////////
echoheader("Постинг с videokub.online", 'Выбор постинга по категориям, количеству, и страницам с сайта videokub.online');
?>
<style>
    .vkub a {
        color:#06F;
        text-decoration:none;
    }
    .vkub a:hover {
        color:#09F;
    }
    .vkub p {
        margin:10px 0;
    }
    .vkub .install-help {
        color:#666;
        font-size:18px;
        margin:0 20px;
    }
    a[href="#items_per_page"], a[href="#category"] {
        border-bottom:#09F 1px dashed;
    }
    .clr { clear:both; }
    .vkub h2 {
        margin:20px;
    }
    .vkub table {
        background:#fff;
        box-shadow:#999 0 0 2px;
    }
    .vkub table thead th {
        padding:10px;
        border-bottom:#ccc 1px solid;
    }
    .vkub table tfoot th {
        padding:10px;
        border-top:#ccc 1px solid;
    }
    .vkub table tbody {
        font-size:14px;
    }
    .vkub table tbody th {
        padding:10px;
    }
    .vkub table tbody td {
        padding:5px 10px;
    }
    .vkub .screen-reader-text {
        display:none;
    }
    .vkub .alignleft {
        margin:0 20px 0 0;
        float:left;
    }
    .vkub .content {
        padding:20px;
    }
    .vkub .tablenav {
        margin:10px 0 20px 0;
    }
    .vkub .tablenav-pages {
        float:right;
    }
    .vkub .tablenav .tablenav-pages a {
        font-size: 16px;
        font-weight: 400;
        margin:0 0 0 2px;
        padding: 3px 10px 6px;
        background: #ddd;
    }
    .vkub .tablenav .tablenav-pages a.disabled {
        color:#999;
    }
    .vkub input[type="text"], select, input[type="button"], input[type="submit"] { padding:3px; }

    .vkub .template {
        margin:0 0 15px 0;
    }
    .vkub .template label { margin:0 15px 0 0; }
    .vkub .template label input { margin:0 5px 0 0; position:relative; top:2px; }
    .vkub textarea {
        margin:0 0 10px 0;
        padding:10px;
    }
</style>

<div style="padding-top:5px;padding-bottom:2px;">
    <table width="100%">
        <tr>
            <td width="4"></td>
            <td></td>
            <td width="6"></td>
        </tr>
        <tr>
            <td></td>
            <td style="padding:5px;" bgcolor="#FFFFFF">
                <table width="100%">
                    <tr>
                        <td></td>
                    </tr>
                </table>
                <div class="unterline"></div>
                <div class="vkub">
                    <p class="install-help">Добавлять видеозаписи можно как последние, так и по <a href="#category" onclick="return false;">категориям</a>. Так-же можно указать <a href="#items_per_page" onclick="return false;">количество</a> и выбрать <a href="#" onClick="jQuery('.template').toggle('slow'); return false;">шаблон добавления</a>.</p>
                    <script>
                        $('a[href="#category"]').hover(function () {
                            $('#cat').css('background', '#F3D9D9');
                        }, function () {
                            $('#cat').css('background', '#fff');
                        });
                        $('a[href="#items_per_page"]').hover(function () {
                            $('#items_per_page').css('background', '#F3D9D9');
                        }, function () {
                            $('#items_per_page').css('background', '#fff');
                        });
                    </script>
                    <div class="content">
                        <?php if( !empty($error) ) : ?><div id="message" class="error"><p><?php echo $error; ?></p></div><?php endif; ?>
                        <?php if( isset($_GET['auto'], $_GET['time']) ) : ?><div id="message" class="error"><p><a href="/admin.php?mod=<?php echo $mod; ?>">Остановить автопостинг</a></p></div><?php endif; ?>
                        <form method="post" action="<?php echo $modpage; ?>">
                            <input type="hidden" name="why" value="enter">
                            <input type="hidden" name="url" value="<?php echo $url; ?>">

                            <div class="template" style="display:none;">
                                <p>В шаблоне используются теги: <b>[video=680x450]</b>, <b>[description]</b>, <b>[title]</b>, <b>[image]</b></p>
                                <p>Краткая новость</p>
                                <textarea name="shortstory" style="width:100%; height:200px;"><?php echo $_POST['shortstory']; ?></textarea><br />
                                <p>Полная новость</p>
                                <textarea name="fullstory" style="width:100%; height:200px;"><?php echo $_POST['fullstory']; ?></textarea>
                                <label class="selectit"><input value="1" type="checkbox" <?php if( $set['add_attach'] == 1 ) echo 'checked="checked"'; ?> name="add_attach" id="add_attach"> Грузить картинку на сайт</label>
                                <label class="selectit"><input value="1" type="checkbox" <?php if( $set['add_tags'] == 1 ) echo 'checked="checked"'; ?> name="add_tags" id="add_tags"> Добавлять теги c videokub.online?</label>
                                <label class="selectit"><input value="1" type="checkbox" <?php if( $set['add_cous'] == 1 ) echo 'checked="checked"'; ?> name="add_cous" id="add_cous"> Скрыть видео для Украины?</label>
                                <label class="selectit"><input value="1" type="checkbox" <?php if( $set['see_new'] == 1 ) echo 'checked="checked"'; ?> name="see_new" id="see_new"> Показывать только новые</label>
                            </div>

                            <div class="tablenav top">
                                <div class="alignleft actions bulkactions">
                                    <label for="bulk-action-selector-top" class="screen-reader-text">Выберите массовое действие</label>
                                    <select name="post_status" id="bulk-action-selector-top">
                                        <option value="publish" selected="selected">Добавить на сайт</option>
                                        <option value="draft">Добавить в черновики</option>
                                    </select>
                                    <input type="button" name="send" onClick="with(this) {
                                                form.why.value = 'addpost';
                                                form.submit()
                                            }" id="doaction" class="button action btn btn-green" value="Применить">
                                </div>
                                <div class="alignleft actions">
                                    <label for="filter-by-date" class="screen-reader-text">Элементов на странице</label>
                                    <select name="items_per_page" id="items_per_page">
                                        <option <?php if( $_REQUEST['items_per_page'] == 10 ) echo 'selected="selected"'; ?> value="10">10</option>
                                        <option <?php if( $_REQUEST['items_per_page'] == 20 ) echo 'selected="selected"'; ?> value="20">20</option>
                                        <option <?php if( $_REQUEST['items_per_page'] == 50 ) echo 'selected="selected"'; ?> value="50">50</option>
                                        <option <?php if( $_REQUEST['items_per_page'] == 100 ) echo 'selected="selected"'; ?> value="100">100</option>
                                    </select>
                                    <select name="category" id="cat" class="postform">
                                        <option value="0">Все категории</options>
                                            <?php echo $category_option; ?>
                                    </select>
                                    <input type="submit" name="filter_action" id="post-query-submit" class="button btn btn-gray" value="Фильтр">
                                </div>
                                <div class="tablenav-pages">
                                    <span class="displaying-num"><?php echo $array['videos_count']; ?> элементов</span>
                                    <span class="pagination-links">
                                        <?php echo $prev_pages; ?>
                                        <span class="paging-input">
                                            <label for="current-page-selector" class="screen-reader-text">Выберите страницу</label>
                                            <input class="current-page" id="current-page-selector" title="Текущая страница" type="text" name="pages" value="<?php echo $_REQUEST['pages']; ?>" size="2"> из <span class="total-pages"><?php echo $page_count; ?></span>
                                        </span>
                                        <?php echo $next_pages; ?>
                                    </span>
                                </div>
                                <br class="clr">
                            </div>

                            <p>Последняя добавленая новость на сайте: <b><?php echo $last_news; ?></b><br />
                                Новых новостей: <b><?php echo $count_new; ?></b></p>

                            <table cellpadding="0" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>
                                            <label class="screen-reader-text" for="cb-select-all-1">Выделить все</label><input id="cb-select-all-1" type="checkbox">
                                        </th>
                                        <th>
                                            <span></span>
                                        </th>
                                        <th>
                                            <span></span>
                                        </th>
                                        <th width="50px">
                                            <span></span>
                                        </th>
                                        <th scope="col" id="title" class="manage-column column-title sortable desc">
                                            <span>Заголовок</span>
                                        </th>
                                        <th>Рубрики</th>
                                        <th>Теги</th>
                                        <th>Описание</th>
                                        <th><span>Дата</span></th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th>
                                            <label class="screen-reader-text" for="cb-select-all-1">Выделить все</label><input id="cb-select-all-2" type="checkbox">
                                        </th>
                                        <th>
                                            <span></span>
                                        </th>
                                        <th>
                                            <span></span>
                                        </th>
                                        <th>
                                            <span></span>
                                        </th>
                                        <th>
                                            <span>Заголовок</span>
                                        </th>
                                        <th>Рубрики</th>
                                        <th>Теги</th>
                                        <th>Описание</th>
                                        <th><span>Дата</span></th>
                                    </tr>
                                </tfoot>

                                <tbody id="the-list">
                                    <?php echo $newslist; ?>
                                </tbody>
                            </table>
                        </form>
                        <div class="tablenav top">
                            <div class="alignleft actions bulkactions">
                                <form action="/admin.php" method="get">
                                    <input type="hidden" name="mod" value="<?php echo $mod; ?>" />
                                    <select name="auto" id="auto">
                                        <option <?php if( $_GET['auto'] == 10 ) echo 'selected="selected"'; ?> value="10">10 видео</option>
                                        <option <?php if( $_GET['auto'] == 20 ) echo 'selected="selected"'; ?> value="20">20 видео</option>
                                        <option <?php if( $_GET['auto'] == 50 ) echo 'selected="selected"'; ?> value="50">50 видео</option>
                                        <option <?php if( $_GET['auto'] == 100 ) echo 'selected="selected"'; ?> value="100">100 видео</option>
                                    </select>
                                    <select name="time" id="time">
                                        <option <?php if( $_GET['time'] == 1 ) echo 'selected="selected"'; ?> value="1">в 1 минуту</option>
                                        <option <?php if( $_GET['time'] == 5 ) echo 'selected="selected"'; ?> value="5">в 5 минут</option>
                                        <option <?php if( $_GET['time'] == 10 ) echo 'selected="selected"'; ?> value="10">в 10 минут</option>
                                        <option <?php if( $_GET['time'] == 60 ) echo 'selected="selected"'; ?> value="60">в 60 минут</option>
                                    </select>
                                    <input type="submit" name="send" id="doaction" class="button action btn btn-green" value="Автопостинг">
                                </form>
                            </div>
                            <div class="tablenav-pages">
                                <span class="displaying-num"><?php echo $array['videos_count']; ?> элементов</span>
                                <span class="pagination-links">
                                    <?php echo $prev_pages; ?>
                                    <span class="paging-input">
                                        <?php echo $_REQUEST['pages']; ?>
                                    </span>
                                    <?php echo $next_pages; ?>
                                </span>
                            </div>
                            <br class="clear">
                        </div>
                    </div>
                </div>
                <script>
                    $('#cb-select-all-1, #cb-select-all-2').click(function () {
                        allcb = $('input[name="post[]"]');

                        if( this.checked == true )
                            $('#cb-select-all-1, #cb-select-all-2').attr('checked', true);
                        else
                            $('#cb-select-all-1, #cb-select-all-2').attr('checked', false);

                        for(var i = 0; i < allcb.length; i++) {
                            allcb[i].checked = this.checked;
                        }
                    });
                </script>

                <?php
                echofooter();
