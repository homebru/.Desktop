<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 8/9/15
 * Time: 11:17 AM
 */

// https://design.ubuntu.com/brand/colour-palette
// https://design.ubuntu.com/web-style-guide

/**
 * WARNING: This code directly uses unfiltered SuperGlobal input
 *			(very unsafe!!). Therefore, think twice before installing
 *			this code on a public facing server!!!
 */

$dir = '/usr/share/applications/';

// Ubuntu official colors
$orange = [
	'#dd4814',  // 100%
	'#e05a2b',  //  90%
	'#e36c43',  //  80%
	'#e77e5a',  //  70%
	'#ea9172',  //  60%
	'#eea389',  //  50%
	'#efac95',  //  45%
	'#f1b5a1',  //  40%
	'#f3beac',  //  35%
	'#f4c8b8',  //  30%
	'#f6d1c4',  //  25%
	'#f8dad0',  //  20%
	'#f9e3db',  //  15%
	'#fbece7',  //  10%
];
$aubergine = [
	'#77216f',  // 100%
	'#84377d',  //  90%
	'#924d8b',  //  80%
	'#9f639a',  //  70%
	'#ad79a8',  //  60%
	'#bb90b7',  //  50%
	'#c19bbe',  //  45%
	'#c8a6c5',  //  40%
	'#cfb1cc',  //  35%
	'#d6bcd3',  //  30%
	'#ddc7db',  //  25%
	'#e3d2e2',  //  20%
	'#eadde9',  //  15%
	'#f1e8f0',  //  10%
];

$newFileLines = [
	"[Desktop Entry]\n",
	"Version=1.0\n",
	"Type=Application\n",
	"Name=Foo Viewer\n",
	"Comment=The best Foo object viewer!\n",
	"TryExec=fooview\n",
	"Exec=fooview %F\n",
	"Icon=fooview\n",
];

if($_POST['data']) {
	$dataArray = [];
	$fname = __DIR__ . '/output/'. $_POST['file'] . ".desktop";
	$handle = fopen($fname, "w+");
	if($handle) {
		foreach ($_POST['data'] as $item) {
			if($item['name'] === 'Enter a name . . .')
				continue;

			switch($item['name'][0])
			{
				case 'b':
					$line = '';
					break;
				case 's':
					$line = $item['value'];
					break;
				case '#':
					$line = str_replace('<br>', "\n", $item['value']);
					break;
				default:
					$line = substr($item['name'], strpos($item['name'], '_')+1) . '=' . $item['value'];
					break;
			}
			$dataArray[substr($item['name'], strpos($item['name'], '_')+1)] = (($item['name'][0] === '#') ? str_replace('<br>', "\n", $item['value']) : $item['value']);
			fwrite($handle, "$line\n");
		}
		fclose($handle);
		chmod($fname, 0644);

		echo $fname;
	}
	return;
}

if($_POST['file']) {
	$section = $comment = '';
	$line = ''; // avoid 'var might not be assigned warning' in the startSection() call
	$blankId = 1;
	$blockArray = [];
	$lines = $_POST['file'] === 'New File' ? $newFileLines : file($dir . $_POST['file'] . '.desktop');
	foreach($lines as $line) {
		$line = trim($line);
		if (!strlen($line)) {
			$line = "blank_{$blankId}=";
			$blankId++;
		}

		if(strlen($comment) && ($line[0] !== '#')) {
			$lineArray['comment'] = substr($comment, 0, strlen($comment)-4);
			$comment = '';
		}

		if($line[0] === '[') {
			startSection($blockArray, $section, $lineArray, substr($line, 1, strlen($line)-2));
			unset($lineArray);
			continue;
		}

		if($line[0] === '#') {
			$comment .= $line . '<br>';
			continue;
		}

		$splode = explode('=', $line);
		$lineArray[$splode[0]] = $splode[1];
	}

	startSection($blockArray, $section, $lineArray, $line);
	$retval = buildHtml($blockArray, $orange);

	echo $retval;
	return;
}

function buildHtml($blockArray, $orange) {
	$html = '';
	$id = $section = 0;
	foreach($blockArray as $key => $subArray) {
		// Deal with header comments if there are any...
		if($key === 'comment') {
			$html .= "<blockquote class=\"blockquote\"><p><span class=\"trash fa fa-trash hand\" aria-label=\"Delete Item\" title=\"Delete Item\"></span><br>{$subArray}</p><input type=\"hidden\" name=\"#{$id}_#comment\" value=\"".htmlentities($subArray)."\"></blockquote>";
			continue;
		}
		elseif(substr($key, 0, 5) === 'blank') {
			$html .= "<input type=\"hidden\" name=\"b{$id}_blank\" value=\"\">";
			continue;
		}

		$id++;
		$theOrange = $section > 13 ? $orange[13] : $orange[$section];
		$html .= "<div class=\"panel panel-primary\" style=\"border-color:{$theOrange};\">
					  <div class=\"panel-heading\" style=\"background-color:{$theOrange};border-color:{$theOrange};\">
						<div class=\"col-md-10\">
							<h4 class=\"hand\" title=\"Section Name\">{$key}</h4><input type=\"hidden\" name=\"s{$section}_[{$key}]\" value=\"[{$key}]\">
						</div>
						<div class=\"col-md-2 text-right\">
							<button type=\"button\" class=\"btn btn-addnew\" aria-label=\"Add New Item\" title=\"Add New Item\"><span class=\"fa fa-plus\"></span></button>
							<button type=\"button\" class=\"btn btn-comment\" aria-label=\"Add New Comment\" title=\"Add New Comment\"><span class=\"fa fa-comment-o\"></span></button>
							<button type=\"button\" class=\"btn btn-delete\" aria-label=\"Delete Section\" title=\"Delete Section\"><span class=\"fa fa-trash\"></span></button>
						</div>
					  </div>
					  <div class=\"panel-body\">";
		$section++;

		foreach($subArray as $name => $val) {
			$id++;
			if($name[0] === 'b') {
				$html .= "<input type=\"hidden\" name=\"b{$id}_blank\" value=\"\">";
				continue;
			}

			if($name[0] === 'c') {
				$html .= "<blockquote class=\"blockquote-reverse\"><p>{$val}<span class=\"trash fa fa-trash hand\" aria-label=\"Delete Item\" title=\"Delete Item\"></span></p><input type=\"hidden\" name=\"#{$id}_#comment\" value=\"{$val}\"></blockquote>";
				continue;
			}

			$html .= "
				<div class=\"input-group\">
				  <span class=\"input-group-addon right-text hand\" title=\"Item Name\" id=\"basic-addon{$id}\" style=\"background-color:{$theOrange};\">{$name}</span>
				  <input type=\"text\" class=\"form-control\" placeholder=\"{$name} . . .\" aria-describedby=\"basic-addon{$id}\" name=\"v{$id}_{$name}\" value=\"{$val}\">
				  <span class=\"input-group-addon trash fa fa-trash hand\" aria-label=\"Delete Item\" title=\"Delete Item\"></span>
				</div>";
		}

		$html .= "
			</div>
		  </div>";
	}

	return $html;
}

function startSection(&$blockArray, &$section, &$lineArray, $line) {
	if(strlen($section))
		$blockArray[$section] = $lineArray;
	elseif($lineArray) {
		foreach($lineArray as $key => $val)
			$blockArray[$key] = $val;
	}

	$section = $line;
}

function endswith($string, $test) {
	$strlen = strlen($string);
	$testlen = strlen($test);
	if ($testlen > $strlen) return false;
	return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

// Load our list of files in alpha order
$files = scandir($dir);
natcasesort($files);
$files[0] = 'New File.desktop';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Desktop File Utility</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet">
	<link href="http://assets.ubuntu.com/sites/guidelines/css/latest/ubuntu-styles.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
	<link href="css/sweetalert2.css" rel="stylesheet" type="text/css">
	<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<form class="container">
		<div class="col-md-12" id="header">
			<div class="col-md-1" id="title"><a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; ?>" class="no-href">.Desktop<br><img src="images/hero-dots.png" width="84px" /></a></div>
			<div class="col-md-10" id="browser">
				<label class="col-md-3 text-right lowered" for="file-list">.desktop File: </label>
				<select id="file-list">
					<option value=''></option>
					<?php foreach($files as $file) {
							if(endswith($file, '.desktop')) {
								$file = substr($file, 0, strpos($file, '.desk'));
								echo "<option value=\"{$file}\">{$file}</option>";
							}
						}
					?>
				</select>
				<button class="btn btn-save pushed-right hide" type="button" value="New" id="new-section" name="new-section" title="Add New Section"><span class="fa fa-clone"></span></button>
				<button class="btn btn-save pushed-right" type="button" value="Save" id="save" name="save" title="Save Changes"><span class="fa fa-floppy-o"></span> Save</button>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-md-12" id="guts"></div>
	</form>
	<a href="javascript:void(null)" id="btn-top-scroller" class="bluezed-scroll-top"><span class="glyphicon glyphicon-menu-up bluezed-scroll-top-circle"></span></a>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script src="js/sweetalert2.min.js"></script>
<script>
	// main.js uses this array of Ubuntu Orange colors...
	var orange = <?php echo json_encode($orange); ?>;
	var dir = <?php echo json_encode($dir); ?>;
</script>
<script src="js/main.js"></script>
</body>
</html>
