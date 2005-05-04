<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor 1.5 Administrator Documentation</title>
	<base target="body" />

<style>
body {
    font-family: Verdana,Arial,sans-serif;
	font-size: x-small;
	margin: 0px;
	padding: 0px;
	background: #fafafa;
	margin-left: -5px;
}

ul, ol {
	list-style: none;
	padding-left: 0px;
	margin-left: -15px;
}
li {
	margin-left: 19pt;
	padding-top: 2px;
}
a {
	/* white-space: pre; */
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/folder.gif'); */
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/tree.gif'); */
	/* 	background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/leaf.gif'); */
	background-repeat: no-repeat;
	background-position: 0px 1px;
	padding-left: 12px;
	text-decoration: none;
}
a.tree {
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/tree.gif'); */
	background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/folder.gif');
}

a.leaf {
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/leaf.gif'); */
	background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/paper.gif');
}
a:link, a:visited {
	color: #006699;
}
a:hover {
	color: #66AECC;
}
</style>
</head>
<body>

<a href="frame_toc.php" target="_self">Back to Contents</a>
<?php

$_pages['0.0.introduction.php']                  = 'Introduction';
$_pages['1.0.installation.php']                  = 'Installation';
$_pages['1.1.requirements_recommendations.php']  = 'Requirements &amp; Recommendations';
$_pages['1.2.new_installation.php']              = 'New Installation';
$_pages['1.3.upgrading.php']                     = 'Upgrading an Installation';
$_pages['2.0.configuration.php']                 = 'Configuration';
$_pages['2.1.my_account.php']                    = 'My Account';
$_pages['2.2.system_preferences.php']            = 'System Preferences';
$_pages['2.3.languages.php']                     = 'Languages';
$_pages['2.3.1.importing_languages.php']         = 'Importing Languages';
$_pages['2.3.2.managing_existing_languages.php'] = 'Managing Existing Languages';
$_pages['2.3.3.translating_atutor.php']          = 'Translating ATutor';
$_pages['2.4.themes.php']                        = 'Themes';
$_pages['2.4.1.importing_themes.php']            = 'Importing Themes';
$_pages['2.4.2.managing_existing_themes.php']    = 'Managing Existing Themes';
$_pages['2.4.3.creating_themes.php']             = 'Creating Themes';
$_pages['2.5.error_logging.php']                 = 'Error Logging';
$_pages['3.0.users.php']                         = 'Users';
$_pages['3.1.instructor_requests.php']           = 'Instructor Requests';
$_pages['3.2.master_student_list.php']           = 'Master Student List';
$_pages['3.2.1.importing_student_ids.php']       = 'Importing Student IDs';
$_pages['3.3.email_users.php']                   = 'Email Users';
$_pages['3.4.administrators.php']                = 'Administrators';
$_pages['3.4.1.administrator_activity_log.php']  = 'Administrator Activity Log';
$_pages['4.0.courses.php']                       = 'Courses';
$_pages['4.1.backups.php']                       = 'Backups';
$_pages['4.1.1.creating_backups.php']            = 'Creating Backups';
$_pages['4.1.2.restoring_backups.php']           = 'Restoring Backups';
$_pages['4.2.forums.php']                        = 'Forums';
$_pages['4.3.categories.php']                    = 'Categories';
$_pages['5.troubleshooting.php']                 = 'Troubleshooting';

if ($_GET['query']) {
	$_GET['query'] = str_replace(',', ' ', $_GET['query']);
	$_GET['query'] = str_replace('"', '', $_GET['query']);

	if (strlen($_GET['query']) > 3) {
		$_GET['query'] = strtolower($_GET['query']);

		$search_terms = explode(' ', $_GET['query']);

		$results = array();
		foreach ($_pages as $file => $title) {
			$count = 0;
			$contents = strtolower(file_get_contents($file));
			foreach ($search_terms as $term) {
				$term = trim($term);
				if ($term) {
					$count += substr_count($contents, $term);
				}
			}
			if ($count) {
				$results[$file] = $count;
			}
		}

		if ($results) {
			arsort($results);
			echo '<ol>';
			foreach ($results as $file => $count) {
				echo '<li><a href="'.$file.'" class="leaf" target="body">'.$_pages[$file].'</a></li>';
			}
			echo '</ol>';
		} else {
			echo '<p style="padding: 8px;">No results found.</p>';
		}
	} else {
		echo '<p style="padding: 8px;">Search term must be longer than 3 characters.</p>';
	}
}
?>
</body>
</html>