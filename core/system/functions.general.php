<?php
# Session Time
define('SESSION_TIME','72000'); //2 hours

# Session Cookies
define('SEED_1','lskgh30967043sljdhtgth489hg802380gh93890a8hQWA8HD280HH80HDC2LAGHD-02356-2395239U5023U50');
define('SEED_2','skgh30967043sLjdht3562395239U5o2gt3h42220089hg802adfgdsgh9389offfWAdd8222PHdsH80HDC2LAGHD023562395239U5o23U50');

function userLoginCheck ( &$error = '' )
{	
	// $page = strtolower( basename( $_SERVER['PHP_SELF'] ));
	if ($_SESSION['userAuth']=='' || $_SESSION['userAuthSpecial']=='')
	{
		$error = 'nologin';
		return false;
	}	

	$sesstime = 0;
	$logintime = 0;
	$user_id = 0;	
	
	$objRS = mysql_query( "SELECT * FROM users WHERE sessid='".mysql_escape_string($_SESSION['userAuth'])."' LIMIT 1" );

	if ( !($user = @mysql_fetch_assoc($objRS)) )
	{
		$error = 'nouser';
		return false;
	}

	$time_now = time();
	if($time_now > $user['sesstime'] )
	{
		$error = 's1';
		return false;
	}

	$u_left_value = $_SESSION['uAL'];
	$u_right_value = $_SESSION['uAR'];
	//1 $adminAuthSpecialSession = md5( $cookie_left_value . $user['logintime'] . $cookie_right_value );
	$adminAuthSpecialSession = md5( $u_left_value . $user['logintime'] .$u_right_value );	

	if($_SESSION['userAuthSpecial'] != $adminAuthSpecialSession)
	{
		$error = 's2';
		return false;
	}

	$sessTimeNew = time() + SESSION_TIME;

	@mysql_query( "UPDATE users SET sesstime='$sessTimeNew' WHERE id='{$user['id']}'" );

	return $user;
}

function ajax_is_login( &$error = '' )
{
	if ($_SESSION['userAuth']=="" || $_SESSION['userAuthSpecial']=="")
	{
		return false;
	}

	$objRS = mysql_query( "SELECT * FROM users WHERE sessid='".mysql_escape_string($_SESSION['userAuth'])."' and confirm = '1' LIMIT 1" );

	if ( !($user = @mysql_fetch_assoc($objRS)) )
	{
		return false;
	}

	$time_now = time();
	if($time_now > $user['sesstime'] )
	{
		return false;
	}

	$sesstime = 0;
	$logintime = 0;
	$user_id = 0;
	$u_left_value = $_SESSION['uAL'];
	$u_right_value = $_SESSION['uAR'];

	$userAuthSpecialSession = md5( $u_left_value . $user['logintime'] . $u_right_value );

	if($_SESSION['userAuthSpecial'] != $userAuthSpecialSession)
	{
		return false;
	}

	$sessTimeNew = time() + SESSION_TIME;

	@mysql_query( "UPDATE users SET sesstime='$sessTimeNew' WHERE id='{$user['id']}'" );
	
	return $user;
}

function ajax_login ($email, $password, &$error = '')
{	
	$result['status'] = 0;
	$result['text'] = 'Invalid Account';
	
	$check_user = get_query ('users', "WHERE email='".mysql_real_escape_string($email)."' AND password='".mysql_real_escape_string(md5($password))."'", 1);
	
	if ( $check_user )
	{
		if ( isset ($check_user['confirm']) && $check_user['confirm'] == 0 )
		{
			$result['text'] = 'Your account is still not confirmed.';
			return $result;
		}						
	}
	else{
		return $result;
	}	
	
	$timing_now = time();

	preg_match("/<address>(.*?)<\/address>/", $_SERVER['SERVER_SIGNATURE'], $SERVER_SIGNATURE_array);
	$SERVER_SIGNATURE = str_replace(" ", "", $SERVER_SIGNATURE_array[1]);
	$SERVER_SIGNATURE = $SERVER_SIGNATURE . $timing_now;

	$sessId2_p1 = $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . '-' . $timing_now . '-'.$_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'];

	$SEED_1 = SEED_1;
	$SEED_2 = SEED_2;
	
	$SEED_1_value = '';
	$SEED_2_value = '';

	for($i=1; $i<=10; $i++)
	{
		$random = rand(0, strlen( SEED_1 ));
		$SEED_1_value .= substr( SEED_1, $random, 1);

		$random = rand(0, strlen( SEED_2 ));
		$SEED_2_value .= substr( SEED_2, $random, 1);
	}

	$sessId2_p1 = $SEED_1_value . sha1( $sessId2_p1 );
	$sessId2_p2 = md5( $SERVER_SIGNATURE ) . $SEED_2_value;
	
	$_SESSION['uAL'] = $sessId2_p1;
	$_SESSION['uAR'] = $sessId2_p2;
	
	$sessId2 = $sessId2_p1 . $timing_now . $sessId2_p2;
	$_SESSION['userAuthSpecial'] = md5( $sessId2 );

	$last_id_admin_logger = mysql_insert_id();
	$sessTime = $timing_now + SESSION_TIME;
	$sessId = md5( $last_id_admin_logger . time() . $check_user['username'] . time() . $check_user['password'] );

	$_SESSION['userAuth'] = $sessId;
	
	$user_id = (isset($check_user['id'])) ? sqlEscape($check_user['id']) : 0;
	
	$q = @mysql_query( "UPDATE users SET sessid='$sessId', sesstime='$sessTime', logintime='$timing_now' WHERE id='{$user_id}' LIMIT 1" );

	$result['status'] = 1;
	$result['text'] = 'Good';

	return $result;
}

function user_logout()
{
	if( empty( $_SESSION['userAuth'] ))
	{
		return true;
	}
	$objRS2=mysql_query( "UPDATE users SET sessid='',sesstime='0',logintime='0' WHERE sessid='".sqlEscape($_SESSION['userAuth'])."'" );
	
	unset($_SESSION['uAL']);
	unset($_SESSION['uAR']);
	unset($_SESSION['userAuthSpecial']);
	unset($_SESSION['userAuth']);	
	return true;
}

function custom_query ($query, $limit=0)
{
	$data = array();
	$q = @mysql_query($query);	
	if ($q && mysql_num_rows($q) > 0){
		while ($row = mysql_fetch_assoc ($q)) {
			$data[] = $row;
		}
	}
	return ($limit != 1) ? $data : $data[0];
}

function get_query_pagination ($tablename, $where='', $getParam ='', $current_page, $per_page)
{
	if ($limit != '') {$limitStr = 'limit '.intval($limit);}
	
	$data = array();
	$tablename = sqlEscape ($tablename);
	
	$_q = "select * from {$tablename} {$where}";
	
	$_qQueried = mysql_query ($_q);
	$total = @mysql_num_rows ($_qQueried);
	$_qPaginated = pagesSQL ($_q, $per_page, $current_page);
	$resultPaginated = mysql_query ($_qPaginated);
	$count_total = @mysql_num_rows ($resultPaginated);
		
	if ($count_total > 0) {
		while ($row = mysql_fetch_array ($resultPaginated)) {
			$data[] = $row;
		}
	}
	
	return ($limit != 1) ? array('results'=>$data,'current_total'=>$count_total, 'total'=>$total) : array('results'=>$data[0],'current_total'=>1, 'total'=>1);
}
function get_query ($tablename, $where = '', $limit = '', $show_sql = 0)
{
	$data = array();
	$tablename = sqlEscape ($tablename);
	
	if ($limit > 0){
		$limitStr = 'LIMIT '.sqlEscape($limit);
	}
	
	if ($show_sql)
		echo "SELECT * FROM {$tablename} {$where} {$limitStr}";
	
	$q = mysql_query ("SELECT * FROM {$tablename} {$where} {$limitStr}");
	
	if ($q && mysql_num_rows($q)) {
		while ($row = mysql_fetch_assoc ($q)) {
			$data[] = $row;
		}
	}
	
	if ($limit == 1 && !isset($data[0]) ){
		$data[0] = '';
	}
	
	return ($limit == 1) ? $data[0] : $data;
}
function replaceArabicNum( $text )
{
	$newText = '';
	$len = strlen( $text );
	for( $i=0; $i<$len; $i++)
	{
		if( is_numeric( $text[$i] ) )
		{
			$newText .= '&#x066' . $text[$i] . ';';
		}
		else
		{
			$newText .= $text[$i];
		}
	}
	return $newText;
}
function shorten($text, $length)
{
    if(strlen($text) > $length) {
        $text = substr($text, 0, strpos($text, ' ', $length));
    }

    return $text;
}

function codeRand( $len = 0 )
{
	if( $len < 1 ) $len = 5;
	$a = 'abcdefghijklmnopqrstuvwxyz1234567890';
	$c = $a[rand(0, 25)];
	for($i=0; $i< $len-1; $i++) { $c .= $a[rand(0, 35)]; }
	return $c;
}
function display_meta ($title, $desc, $url, $img)
{?>
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?php echo cleanMeta($title);?>" />
  <meta property="og:description" content="<?php echo cleanMeta($desc);?>" />
  <meta property="og:url" content="<?php echo cleanMeta($url);?>" />
  <meta property="og:image" content="<?php echo cleanMeta(str_replace(' ', '%20',$img));?>" />
  <meta name='twitter:card' content='summary'>
  <meta name="twitter:site" content="@acme_lb">
  <meta name='twitter:title' content='<?php echo cleanMeta($title);?>'>
  <meta name='twitter:description' content='<?php echo cleanMeta($desc);?>'>
  <meta name='twitter:image:src' content='<?php echo cleanMeta(str_replace(' ', '%20',$img));?>'>
  <meta name="twitter:domain" content="<?php echo SITE_BASE;?>">  
	<?php
}
function cleanMeta ($s)
{
	return cleanHTML(str_replace(array('\n','\r'),'',$s));
}
function cleanHTML($input)
{
	$input = htmlspecialchars($input, ENT_QUOTES);
	return $input;
}

function today() {
	return mktime(gmdate("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
}

function go404 () {
	header ("Location:".SITE_BASE."404");
	exit;
}
function is_mobile(){
	// returns true if one of the specified mobile browsers is detected
	$regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
	$regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
	$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";	
	$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
	$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
	$regex_match.=")/i";		
	return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}

function summarizeHtml($text='',$words='20',$link='...'){
	preg_match('/^([^.!?\s]*[\.!?\s]+){0,'.$words.'}/', strip_tags($text), $abstract);
	return $abstract[0].$link;
}
function is_email($e){
	return preg_match('/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i',$e);
}
function sqlEscape($s) {
	if(get_magic_quotes_gpc()) {
		$s = stripslashes($s);
	}
	return mysql_real_escape_string ( strip_tags($s) );
}
function cleanIt ($s){
	return sqlEscape ( strip_tags($s) );
}
function cleanTitleURL( $s )
{
	$s = strtolower($s);	
	$s = preg_replace("/[^a-z0-9-_\s]/i", "", $s );
	$s = preg_replace("/\s+/", "-", trim( $s ) );
	
	return $s;
}

function pagesSQL ($sqlFinal, $listingPerPage, $p)
{
	if (!is_numeric($p) || $p==0) 
		$p=1; 
	else 
		$p=intval(abs($p));
	$listingStart = ($p-1)*($listingPerPage);
	
	$sqlFinal = $sqlFinal." limit $listingStart,$listingPerPage";
	return $sqlFinal;
}

function paginationBar ($total, $listingPerPage,$p, $paginationLink, $getParam='')
{
	if (!is_numeric($p) || $p<1) { $p=1; }	

	$pagesTotal = ceil ($total / $listingPerPage);
	
	if ($pagesTotal > 1)
	{
		echo '<ul class="pagination">';
		$j = ($p-1 != 1) ? $p-1 : '';
		if ($p > 1) echo "<li><a href='$paginationLink/{$j}{$getParam}'>Back</a></li>";
		
		for ($i=1 ; $i <= $pagesTotal; $i++)
		{
			$j = ($i == 1) ? '' : $i;
				
			if ($i != intval($p))
				echo "<li><a href='$paginationLink/{$j}{$getParam}'>{$i}</a></li>";
			else
				echo "<li class='active'><a href='$paginationLink/{$j}{$getParam}'>$i</a></li>";
		}
		$next = $p+1;
		if ($p < $pagesTotal) echo "<li><a href='$paginationLink/{$next}$getParam'>Next</a></li>";
			
		echo '</ul>';
	}
}

function getFeaturedImage ($id)
{
	$id = sqlEscape ($id);
	$s = get_query ('media', "WHERE id='{$id}'", 1);	

	return ( isset($s['media_link']) ) ? $s['media_link'] : '';	
}
function getImageThumb ($full_link)
{
	$thumb_location = substr ($full_link, strpos($full_link, '/uploads/') + 9, strlen($full_link));
	$thumbYear = substr($thumb_location, 0, 8);
	$thumbName = substr($thumb_location, 8);
	$thumb = $thumbYear.'thumbs/'.$thumbName;
	$thumb_link = dirname(dirname(dirname(__FILE__))).'/uploads/'.$thumb;
	$final_thumb = ( file_exists ($thumb_link) ) ? SITE_BASE.'uploads/'.$thumb : $full_link;
	
	return $final_thumb;
}

function get_category_posts_by_slug ($post_type, $post_name)
{
  // Get all posts with category
  // Post type
  // Post slug
  
  $post_type = sqlEscape ($post_type);
  $post_name = sqlEscape ($post_name);
  
  $q = custom_query("SELECT * FROM posts WHERE id IN (SELECT post_taxonomies.post_id FROM taxonomies LEFT JOIN post_taxonomies ON taxonomies.id=post_taxonomies.category WHERE taxonomies.post_name='$post_name' AND taxonomies.post_type='$post_type')");
  
  return $q;
}

function getCategories ($id, $format='')
{
  $id = sqlEscape ($id);
  $s = get_query ('post_taxonomies', "WHERE post_id = '{$id}'");
  $string = '';	

  if ( $format == ',' )
  {
    foreach ($s as $taxo)
    {
      $category_id = ( isset($taxo['category']) ) ? sqlEscape ($taxo['category']) : '';
      $get_category = get_query ('taxonomies', "WHERE id='{$category_id}'", 1);
      $string .= $get_category['post_title'].', ';
    }
    $string = rtrim($string, ', ');
    
    return $string;
  }

  return $s;
}
function get_meta ($id, $key){
	$id = sqlEscape ($id);
	$key = sqlEscape ($key);
	$s = get_query ('post_meta', "WHERE post_id = '{$id}' AND meta_key = '{$key}'", 1);	
	return ( isset($s['meta_value']) ) ? $s['meta_value'] : '';
}

function make_slug ($s){
	$s = preg_replace('/\W+/','-',$s);
	$s = preg_replace('/[^A-Za-z0-9-]+/', '-', $s);
   return strtolower($s);
} 

function mailer( $to, $subject, $msg )
{
	$headers = '';
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "Return-Path: \"".SITE_FROM_EMAIL_NAME."\" <". SITE_FROM_EMAIL_ADDRESS .">\r\n";
	$headers .= "Reply-To: \"".SITE_FROM_EMAIL_NAME."\" <". SITE_FROM_EMAIL_ADDRESS .">\r\n";
	$headers .= "From: \"".SITE_FROM_EMAIL_NAME."\" <". SITE_FROM_EMAIL_ADDRESS .">\r\n";
	$headers .= "X-Priority: 1\r\n";
	$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
	$headers .= "Content-Transfer-Encoding: 8bit\r\n";
	$headers .= "Priority: Urgent\r\n";
	$headers .= "Importance: high";

	return @mail($to, $subject, $msg, $headers);
}

function getDefaultPage ($args_array)
{
  $args = array ('default_page_key'=> ( isset($args_array['default_page_key'])) ? $args_array['default_page_key'] : '',
                 'template_key'=> ( isset($args_array['template_key'])) ? $args_array['template_key'] : '',
                 'template_slug'=> ( isset($args_array['template_slug'])) ? $args_array['template_slug'] : '',
                 );

  if ( file_exists (TEMPLATES_DIR_NAME.$args['template_key'].'-'.$args['default_page_key'].'.php') ){
    header('Location:'.SITE_BASE.$args['template_slug'].'/'.$args['default_page_key'], true, 301);
  }
  else{
    header('Location:'.SITE_BASE);
  }
  exit;
}