<?php

// генерировать в указанные папки
// править шаблон в config.ini
// править robots.txt
// тайтл и Н1 ключ, еще ключ

// проверить карту сайта!
// добавить в шаблон кол-во просмотров за месяц и неделю
// document.write


// запуск таймера
$mtime = microtime(true);
set_time_limit(100);
ini_set('memory_limit', '3024M');
header("Content-Type: text/html; charset=utf-8"); 

// задаем каталог с данными
$dataDir = 'obyav';
$dataDir = getcwd()."/$dataDir";



// задаем переменные для скриншотов
$width = 400;
$font = 'verdana.ttf';
// стартовый суффикс
$counterPics = mt_rand(5000000,6000000);

// переменные общие: стартовое время, половина - до которой публикуется быстро, дата окончания публикации
$start_time = strtotime("2012-04-01 01:01:01");
$half_time = strtotime("2012-04-14 23:59:59");
$end_time = strtotime("2030-12-31 23:59:59");

// массив с доменами
$arrayDomains = file($dataDir . '/domains.txt');
$arrayDomains = array_unique($arrayDomains);
$arrayDomains = array_diff($arrayDomains, array(''));
$arrayDomains = str_ireplace("\n", "", $arrayDomains);
$arrayDomains = str_ireplace("\r", "", $arrayDomains);
$domainName = trim(current($arrayDomains));

// массив с названием сайта
$sitenames = file($dataDir . '/sitenames.txt');
$sitenames = array_unique($sitenames);
$sitenames = array_diff($sitenames, array(''));
shuffle($sitenames);
$sitenames = str_ireplace("\n", "", $sitenames);
$sitenames = str_ireplace("\r", "", $sitenames);
if ((next($sitenames)) != false)	{$siteName = trim(current($sitenames));}
else {reset($sitenames);	$siteName = trim(current($sitenames));}
$keywordsMain = "@@keywords=$siteName";
$descriptionMain = "@@description=$siteName";

// массив с названиями разделов
$sections = file($dataDir . '/sections.txt');
$sections = array_unique($sections);
$sections = array_diff($sections, array(''));
shuffle($sections);
$sections = str_ireplace("\n", "", $sections);
$sections = str_ireplace("\r", "", $sections);
$numberUV2 = count($sections);

// массив с темами
$arrayThemes = file($dataDir . '/themes.txt');
$arrayThemes = array_unique($arrayThemes);
$arrayThemes = array_diff($arrayThemes, array(''));
$arrayThemes = str_ireplace("\n", "", $arrayThemes);
$arrayThemes = str_ireplace("\r", "", $arrayThemes);

// мах кол-во страниц в файле для первой генерации 
$maxPagesMin = 180;
// $maxPagesMin = 1800; //************************************************************************************************************************
$maxPagesMax = 250;
// $maxPagesMax = 2500; //************************************************************************************************************************
$maxPagesNumber = mt_rand($maxPagesMin,$maxPagesMax);

// мах кол-во страниц УВ3 в разделе для первой генерации 
// $maxUV3Min = 100;//************************************************************************************************************************
$maxUV3Min = 10;
// $maxUV3Max = 200;//************************************************************************************************************************
$maxUV3Max = 20;
$maxUV3 = mt_rand($maxUV3Min,$maxUV3Max);

// счетчик УВ3 в разделе и общий УВ2, общий УВ3
$counterUV3 = 0;
$sumUV2 = 0;
$sumCounterKeys = 0;

// количество минимум и максимум элементов на странце (включая видео, текст, картинки и проч.)
$minElements = 15;
$maxElements = 30;


// счетчик кеев и файлов на выходе
$counterKeys = 1;
// $domainName = 1000;

// Читаем каталоги с файлами
$arrayFilesKeys = glob("$dataDir/keys/*.txt");
$arrayFilesImages = glob("$dataDir/images/*.txt");
$arrayFilesVideo = glob("$dataDir/video/*.txt");

// Создадим массивы текстов, ключевиков, картинок и видео
$arrayKeys = getKeysTextImagesVideo($arrayFilesKeys);
$arrayKeys = array_unique($arrayKeys);
shuffle($arrayKeys);
$arrayKeys = str_ireplace("\n", "", $arrayKeys);
$arrayKeys = str_ireplace("\r", "", $arrayKeys);


// массив с урлами больших картинок
$arrayImages = getKeysTextImagesVideo($arrayFilesImages);

// массив с урлами маленьких картинок
$arrayImagesSmall = str_ireplace(".jpg", "_s.jpg", $arrayImages);

// массив с хтмл-кодом видео
$arrayVideo = getKeysTextImagesVideo($arrayFilesVideo);
$arrayVideo = str_ireplace("%picture1%", "Фото " . createKey($arrayKeys), $arrayVideo);
$arrayVideo = str_ireplace("%picture2%", "Рисунок " . createKey($arrayKeys), $arrayVideo);
$arrayVideo = str_ireplace("%picture3%", "Изображение " . createKey($arrayKeys), $arrayVideo);

// создадим массив больших текстов и рандомно заменим кеи
$arrayText0 = file("text-big.txt");
foreach ($arrayText0 as $key => $line) {
	$line = str_ireplace("%key1%", createKey($arrayKeys), $line);
	$line = str_ireplace("%key2%", createKey($arrayKeys), $line);
	$line = str_ireplace("%key3%", createKey($arrayKeys), $line);
	$line = str_ireplace("%key4%", createKey($arrayKeys), $line);
	$arrayText[] = $line;
	}
$arrayText = array_unique($arrayText);

// этот массив будет использоваться для создания анонса и заключительного абзаца статьи
$arrayTextNow = file("text-big.txt");
$arrayTextNow = array_unique($arrayTextNow);

// этот массив для формирования подписей к маленьким картинкам
$arrayTextThumb = file("text-small.txt");
$arrayTextThumb = array_unique($arrayTextThumb);
// print_r($arrayTextThumb);

// перекодировка транслит
$translit = array("\xd1\x91"=>"e","\xd0\xb9"=>"y","\xd1\x86"=>"ts","\xd1\x83"=>"u","\xd0\xba"=>"k","\xd0\xb5"=>"e","\xd0\xbd"=>"n","\xd0\xb3"=>"g","\xd1\x88"=>"sh","\xd1\x89"=>"shch","\xd0\xb7"=>"z","\xd1\x85"=>"kh","\xd1\x8a"=>"","\xd1\x84"=>"f","\xd1\x8b"=>"y","\xd0\xb2"=>"v","\xd0\xb0"=>"a","\xd0\xbf"=>"p","\xd1\x80"=>"r","\xd0\xbe"=>"o","\xd0\xbb"=>"l","\xd0\xb4"=>"d","\xd0\xb6"=>"zh","\xd1\x8d"=>"e","\xd1\x8f"=>"ya","\xd1\x87"=>"ch","\xd1\x81"=>"s","\xd0\xbc"=>"m","\xd0\xb8"=>"i","\xd1\x82"=>"t","\xd1\x8c"=>"","\xd0\xb1"=>"b","\xd1\x8e"=>"yu","\xd0\x81"=>"E","\xd0\x99"=>"Y","\xd0\xa6"=>"TS","\xd0\xa3"=>"U","\xd0\x9a"=>"K","\xd0\x95"=>"E","\xd0\x9d"=>"N","\xd0\x93"=>"G","\xd0\xa8"=>"SH","\xd0\xa9"=>"SHCH","\xd0\x97"=>"Z","\xd0\xa5"=>"KH","\xd0\xaa"=>"","\xd0\xa4"=>"F","\xd0\xab"=>"Y","\xd0\x92"=>"V","\xd0\x90"=>"A","\xd0\x9f"=>"P","\xd0\xa0"=>"R","\xd0\x9e"=>"O","\xd0\x9b"=>"L","\xd0\x94"=>"D","\xd0\x96"=>"ZH","\xd0\xad"=>"E","\xd0\xaf"=>"YA","\xd0\xa7"=>"CH","\xd0\xa1"=>"S","\xd0\x9c"=>"M","\xd0\x98"=>"I","\xd0\xa2"=>"T","\xd0\xac"=>"","\xd0\x91"=>"B","\xd0\xae"=>"YU",);

// создадим первый каталог для файлов, картинок, конфигурационного файла
$folderOutput = getcwd()."/_output";
if (!is_dir($folderOutput)) {mkdir($folderOutput);}
$folderOutput = getcwd()."/_output/$domainName";
if (!is_dir($folderOutput)) {mkdir($folderOutput);}
$folderOutputImages = getcwd()."/_output/$domainName/images";
if (!is_dir($folderOutputImages)) {mkdir($folderOutputImages);}
$folderOutputTools = getcwd()."/_output/$domainName/tools";
if (!is_dir($folderOutputTools)) {mkdir($folderOutputTools);}
$folderOutputZcontent = getcwd()."/_output/$domainName/zcontent";
if (!is_dir($folderOutputZcontent)) {mkdir($folderOutputZcontent);}
$folderOutputConfig = getcwd()."/_output/$domainName/zcontent/config";
if (!is_dir($folderOutputConfig)) {mkdir($folderOutputConfig);}


// получим имена файлов один массив
$arrayFilesScreens = glob("$dataDir/screens/*.txt");

// получим анонсы скриншотов
$arrayAnons = file("screens.txt");
$arrayAnons = array_unique($arrayAnons);
$arrayAnons = array_diff($arrayAnons, array(''));
// проход всех файлов с текстами
foreach ($arrayFilesScreens as $screenFile) {
	$arrayScreen = file($screenFile);
	$arrayScreen = array_unique($arrayScreen);
	$arrayScreen = array_diff($arrayScreen, array(''));
	
	// проход каждого массива с текстом
	foreach ($arrayScreen as $key => $textNow) {
		$screenText[] = iconv("windows-1251", "UTF-8//TRANSLIT", basename($screenFile, ".txt")) . "----%----" . $textNow;
		}
	}	
	
// Выведем статистику, чтобы не было скучно смотреть на точки
echo "<pre>Настройки отложенной публикации:<br />starttime $start_time = " . date("Y-m-d H:i:s", $start_time);
echo "<br />half_time $half_time = " . date("Y-m-d H:i:s", $half_time);
echo "<br />end_time $end_time = " . date("Y-m-d H:i:s", $end_time);
echo "<br /><br />Количество ключевиков (страниц) = " . count($arrayKeys);
echo "<br />Количество текстов = " . count($arrayText);
echo "<br />Количество изображений = " . count($arrayImages);
echo "<br />Количество видео = " . count($arrayVideo);
echo "<br />Количество разделов = " . $numberUV2; 
echo "<br />Время подготовительной работы: " . round((microtime(true) - $mtime) * 1, 4) . " с.<br /><br />"; ob_flush(); flush();
// echo "<br /><br />Создаем файлы pages . "; 

// запишем УВ1 и УВ2 - для первого файла
$output[] = addUV1($output, $siteName, $keywordsMain, $descriptionMain, $start_time, $domainName);

// готовим ув2
// если следующий элемент массива не пуст - пишем его$text);  
if ((next($sections)) != false)	{$sectionNow = trim(current($sections));}
// если пуст - сбрасываем указатель и пишем первый элемент
else {reset($sections);	$sectionNow = trim(current($sections));}

$output[] = addUV2($sectionNow, $output, $maxUV3, $counterUV3, $maxUV3Min, $maxUV3Max, $sumUV2, $section, $siteName, $start_time);
$counterUV3 = 0;
$sumUV2++;
$maxUV3 = mt_rand($maxUV3Min,$maxUV3Max);

// запускаем проход кеев
foreach ($arrayKeys as $key) 
{
// увеличиваем счетчик кеев 
$counterKeys++;
$counterUV3++;
$sumCounterKeys++;

// проверим, не пора ли создать УВ2
if ($counterUV3>$maxUV3)
	{
	// если следующий элемент массива не пуст - пишем его
	if ((next($sections)) != false)	{$sectionNow = trim(current($sections));}
	// если пуст - сбрасываем указатель и пишем первый элемент
	else {reset($sections);	$sectionNow = trim(current($sections));}
	$output[] = addUV2($sectionNow, $output, $maxUV3, $counterUV3, $maxUV3Min, $maxUV3Max, $sumUV2, $section, $siteName, $start_time);
	$counterUV3 = 0;
	$sumUV2++;
	$maxUV3 = mt_rand($maxUV3Min,$maxUV3Max);
	// echo ". "; ob_flush(); flush(); 
	}

// создаем произвольное количество элементов
for ($elements = 1; $elements <= mt_rand($minElements,$maxElements); $elements++) {

// ТУТ МЕДЛЕННО
// генерируем элементы
$i = mt_rand(0,6);
    switch ($i) {
	// скриншот
	case 0:
		$minitime = microtime(true);
		$imgFilename = strtolower(strtr($sectionNow, $translit));
		$imgFilename = preg_replace('%&.+?;%', '', $imgFilename);
		$imgFilename = preg_replace('%[^a-z0-9,._-]+%', '-', $imgFilename);
		$imgFilename = trim($imgFilename, '-') . '-' . $counterPics . mt_rand(10,99). ".png";
		
		// echo "<br />$imgFilename."; ob_flush(); flush();
		$counterPics++;
		$im = createScrShot ($dataDir,$width,$font,$arrayAnons,$screenText);
		imagepng($im,getcwd()."/_output/$domainName/images/$imgFilename" );
		// очистим переменную
		imagedestroy($im);
		$tempOutput[] = '<br /><img alt="' . createKey($arrayKeys) . '" align="left" src="/images/' . $imgFilename . '" /><br clear="all">';
		$sumMiniTime0 += round((microtime(true) - $minitime) * 1, 4) . " с.";
		break;
	// текст
	case 1:
		$minitime = microtime(true);
		// вариант 1
		// if ((next($arrayText)) != false)	{$blockText = current($arrayText);}
		// else {reset($arrayText);	$blockText = current($arrayText);}
		
		// вариант 2
		srand(); $blockText = $arrayText[(array_rand($arrayText))];
		
		$tempOutput[] = $blockText . '<br clear="all"/>';
		$sumMiniTime1 += round((microtime(true) - $minitime) * 1, 4) . " с.";
		break;
	// видео
	case 2:
		$minitime = microtime(true);
		srand(); $blockVideo = '<div align="center">' . $arrayVideo[(array_rand($arrayVideo))] . '</div>';
		$tempOutput[] = $blockVideo . '<br clear="all"/>';
		$sumMiniTime2 += round((microtime(true) - $minitime) * 1, 4) . " с.";
		break;
	// большая картинка
	case 3:
		$minitime = microtime(true);
		srand(); $blockImageBig = $arrayImages[(array_rand($arrayImages))];
		$tempOutput[] = '<div align="center">' . '<img width="400" alt="' . createKey($arrayKeys) . '" src="' . $blockImageBig . '" /></a></div><br clear="all"/>';
		
		$sumMiniTime3 += round((microtime(true) - $minitime) * 1, 4) . " с.";		
		break;
	// три маленьких картинки
	case 4:
	for ($small = 1; $small <= mt_rand(1,3); $small++) 
		{
		$minitime = microtime(true);
$imageSmall_1 = createImageSmall($arrayImagesSmall);
$imageSmall_2 = createImageSmall($arrayImagesSmall);
$imageSmall_3 = createImageSmall($arrayImagesSmall);
$imageSmall_4 = createImageSmall($arrayImagesSmall);
$imageBig_1 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_1);
$imageBig_2 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_2);
$imageBig_3 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_3);
$imageBig_4 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_4);

$tempOutput[] = 
'<table width="400" border="0" align="center" cellpadding="3" cellspacing="0"><tr>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_1 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_1 . '" /></a><br /><small>' . createTextThumb($arrayTextThumb, $arrayKeys) . '</small></th>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_2 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_2 . '" /></a><br /><small>' . createTextThumb($arrayTextThumb, $arrayKeys) . '</small></th>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_3 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_3 . '" /></a><br /><small>' . createTextThumb($arrayTextThumb, $arrayKeys) . '</small></th>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_4 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_4 . '" /></a><br /><small>' . createTextThumb($arrayTextThumb, $arrayKeys) . '</small></th>
</tr></table><br clear="all"/>';
		$sumMiniTime4 += round((microtime(true) - $minitime) * 1, 4) . " с.";
		}
		break;

	case 5:
	$minitime = microtime(true);
	srand(); $blockImageBig = $arrayImages[(array_rand($arrayImages))];
	$tempOutput[] = '<?php include("ads1.php"); ?>';
	$sumMiniTime5 += round((microtime(true) - $minitime) * 1, 4) . " с.";		
	break;
	
	case 6:
	$minitime = microtime(true);
	srand(); $blockImageBig = $arrayImages[(array_rand($arrayImages))];
	$tempOutput[] = '<?php include("ads2.php"); ?>';
	$sumMiniTime6 += round((microtime(true) - $minitime) * 1, 4) . " с.";		
	break;
	}
}

// формируем страницу
$output[] = addUV3($tempOutput, $sections, $output, $maxUV3, $counterUV3, $maxUV3Min, $maxUV3Max, $sumUV2, $sections, $siteName, $start_time, $half_time, $end_time, $sumCounterKeys, $arrayText, $arrayTextNow, $arrayImages, $arrayVideo, $arrayImagesSmall, $arrayKeys);
// echo "<br />Сгенерили УВ3: " . round((microtime(true) - $mtime) * 1, 4) . " с."; ob_flush(); flush();
$tempOutput = null;

// создаем новый файл, если counterKeys больше установленного максимума страниц
if ($counterKeys>$maxPagesNumber) {
	// запишем файл
	echo "<br />$domainName: УВ2=$sumUV2, УВ3=$counterKeys<br />"; ob_flush(); flush();
	// записываем в него текущий массив output
	
	// создадим каталог для файла
	$folderOutput = getcwd()."/_output";
	if (!is_dir($folderOutput)) {mkdir($folderOutput);}
	$folderOutput = getcwd()."/_output/$domainName";
	if (!is_dir($folderOutput)) {mkdir($folderOutput);}
	$folderOutputImages = getcwd()."/_output/$domainName/images";
	if (!is_dir($folderOutputImages)) {mkdir($folderOutputImages);}
	$folderOutputTools = getcwd()."/_output/$domainName/tools";
	if (!is_dir($folderOutputTools)) {mkdir($folderOutputTools);}
	$folderOutputZcontent = getcwd()."/_output/$domainName/zcontent";
	if (!is_dir($folderOutputZcontent)) {mkdir($folderOutputZcontent);}
	$folderOutputConfig = getcwd()."/_output/$domainName/zcontent/config";
	if (!is_dir($folderOutputConfig)) {mkdir($folderOutputConfig);}
	
	// запишем pages.txt
	file_put_contents("_output/$domainName/tools/pages.txt",$output);
	// запишем robots.txt
	file_put_contents("$folderOutput/robots.txt","User-Agent: *\r\nDisallow: /cgi-bin/\r\nHost: $domainName\r\nSitemap: http://$domainName/sitemap.xml\r\n\r\nUser-agent: ia_archiver\r\nDisallow: /");
	// запишем config.ini
	srand();
	$themeName = $arrayThemes[(array_rand($arrayThemes))];
	$configIni = file_get_contents($dataDir . '/config.ini') . "\r\n$themeName";
	file_put_contents("$folderOutputConfig/config.ini","$configIni");
	
	// обнулим счетчик кеев, выходной массив и создадим новое кол-во кеев
	$counterKeys = 0;
	$output = null;
	$maxPagesNumber = mt_rand($maxPagesMin,$maxPagesMax);
	
	// новое имя сайта
	if ((next($sitenames)) != false)	{$siteName = trim(current($sitenames));}
	else {reset($sitenames);	$siteName = trim(current($sitenames));}
	
	$output[] = addUV1($output, $siteName, $keywordsMain, $descriptionMain, $start_time, $domainName);
	
	// новый раздел
	if ((next($sections)) != false)	{$sectionNow = trim(current($sections));}
	else {reset($sections);	$sectionNow = trim(current($sections));}
	$output[] = addUV2($sectionNow, $output, $maxUV3, $counterUV3, $maxUV3Min, $maxUV3Max, $sumUV2, $section, $siteName, $start_time);
	
	$counterUV3 = 0;
	$sumUV2 = 0;
	$maxUV3 = mt_rand($maxUV3Min,$maxUV3Max);
	if ((next($arrayDomains)) != false)	{$domainName = trim(current($arrayDomains)); echo "<br />Новый домен: $domainName"; }
	else {exit("Закончились домены!");}
	ob_flush(); flush();
	
	
	// создадим каталоги для последующего наполнения
	$folderOutput = getcwd()."/_output";
	if (!is_dir($folderOutput)) {mkdir($folderOutput);}
	$folderOutput = getcwd()."/_output/$domainName";
	if (!is_dir($folderOutput)) {mkdir($folderOutput);}
	$folderOutputImages = getcwd()."/_output/$domainName/images";
	if (!is_dir($folderOutputImages)) {mkdir($folderOutputImages);}
	$folderOutputTools = getcwd()."/_output/$domainName/tools";
	if (!is_dir($folderOutputTools)) {mkdir($folderOutputTools);}
	$folderOutputZcontent = getcwd()."/_output/$domainName/zcontent";
	if (!is_dir($folderOutputZcontent)) {mkdir($folderOutputZcontent);}
	$folderOutputConfig = getcwd()."/_output/$domainName/zcontent/config";
	if (!is_dir($folderOutputConfig)) {mkdir($folderOutputConfig);}
	

	}
}

// запишем остаток в заключительный файл
echo "<br />ЗАКЛЮЧИТЕЛЬНЫЙ ФАЙЛ: $domainName: УВ2=$sumUV2, УВ3=$counterKeys"; ob_flush(); flush();
$folderOutput = getcwd()."/_output/$domainName";
if (!is_dir($folderOutput)) {mkdir($folderOutput);}
$folderOutputImages = getcwd()."/_output/$domainName/images";
if (!is_dir($folderOutputImages)) {mkdir($folderOutputImages);}

file_put_contents("_output/$domainName/pages.txt",$output);
// $domainName++;


// выведем статистику
echo "<br /><br /><br />Обработано кеев: " . $sumCounterKeys;
echo "<br />Заключительное имя файла: " . $domainName;
echo "<br />Время работы модуля 0: " . $sumMiniTime0 . " с.";
echo "<br />Время работы модуля 1: " . $sumMiniTime1 . " с.";
echo "<br />Время работы модуля 2: " . $sumMiniTime2 . " с.";
echo "<br />Время работы модуля 3: " . $sumMiniTime3 . " с.";
echo "<br />Время работы модуля 4: " . $sumMiniTime4 . " с.";
echo "<br />Время работы модуля 5: " . $sumMiniTime5 . " с.";
echo "<br />Время работы модуля 6: " . $sumMiniTime6 . " с.";
echo "<br /><br />Время работы скрипта: " . round((microtime(true) - $mtime) * 1, 4) . " с.";
echo "</pre>";


function rndPublic ($start_time, $half_time, $end_time) {
	$half = mt_rand (1,100);
	
	// ближайшее время
	if ($half <= 50) {
		$rndTime = mt_rand ($start_time,$half_time);
		}
	// отдаленное время
	else {
		$rndTime = mt_rand ($half_time,$end_time);
		}
	// так будет вывод time
	// return $rndTime;
	// а так будет вывод publish
	// echo "<br />" . date("Y-m-d H:i:s", $rndTime);
	return date("Y-m-d H:i:s", $rndTime);
	}		
	
// функция создаем элемент - ключевик
function createKey($arrayKeys) {
	srand();
	$blockKey = $arrayKeys[(array_rand($arrayKeys))];
	return $blockKey;
	}
	
// функция создаем элемент - текст для маленьких картинок
function createTextThumb($arrayTextThumb, $arrayKeys) {
	srand();
	$textThumb = $arrayTextThumb[(array_rand($arrayTextThumb))];
	// echo "<br />" . $textThumb;
	$textThumb = str_ireplace("%key%", createKey($arrayKeys), $textThumb);
	$textThumb = str_ireplace("%randNum%", mt_rand(1000,9999), $textThumb);
	return $textThumb;
	}	
	
// функция создаем элемент - картинки маленькие
function createImageSmall($arrayImagesSmall) {
	srand();
	$blockImageSmall = $arrayImagesSmall[(array_rand($arrayImagesSmall))];
	return $blockImageSmall;
	}

// функция, которая из массива файлов возвращает массив их содержимого
function getKeysTextImagesVideo($arrayFilesKeys) {
	foreach ($arrayFilesKeys as $txtFile) {
		$arrayTxtFile .= "\n" . iconv("windows-1251", "UTF-8//TRANSLIT", file_get_contents($txtFile));
		}
	$arrayKeys = explode("\n", $arrayTxtFile);
	$arrayKeys = array_unique($arrayKeys);
	$arrayKeys = array_diff($arrayKeys, array(''));
	return ($arrayKeys);
	}

function addUV1($output, $siteName, $keywordsMain, $descriptionMain, $start_time, $domainName) {
	$output = "##$siteName\n@@file=index\n$keywordsMain\n$descriptionMain\n@@nomenuitem=1\n@@donotlist=1\n@@module=zmodule_allpages\n@@menuorder=time desc\n@@params.perpage=10\n@@filter=zfilter_php\n@@publish=" . date("Y-m-d H:i:s", $start_time) . "\n@@last-modified=" . date("Y-m-d H:i:s", $start_time) . 
	"\n\n##Карта сайта\n@@file=sitemap\n@@nomenuitem=1\n@@donotlist=1\n@@module=zmodule_sitemap\n@@publish=" . date("Y-m-d H:i:s", $start_time) . "\n@@last-modified=" . date("Y-m-d H:i:s", $start_time)
	;
	
	echo "<br /><br />Новый сайт: $siteName"; ob_flush(); flush();
	// $folderOutput = getcwd()."/_output/$domainName";
	// if (!is_dir($folderOutput)) {mkdir($folderOutput);}
	// $folderOutputImages = getcwd()."/_output/$domainName/images";
	// if (!is_dir($folderOutputImages)) {mkdir($folderOutputImages);}
	return $output;
	}
	
function addUV2($sectionNow, $output, $maxUV3, $counterUV3, $maxUV3Min, $maxUV3Max, $sumUV2, $section, $siteName, $start_time) {
	$timeUV2 = rndPublic ($start_time, ($start_time + 85555), ($start_time + 85555));
	$keywordsUV2 = "@@keywords=$sectionNow,$siteName";
	$descriptionUV2 = "@@description=$sectionNow - $siteName";
	$output = "\n\n##$sectionNow - $siteName\n$keywordsUV2\n$descriptionUV2\n@@menu=$sectionNow\n@@publish=" . $timeUV2 . "\n@@last-modified=" . $timeUV2 . "\n@@nosubmenu=true\n@@donotlist=1\n@@params.perpage=999\n@@filter=zfilter_php\n@@module=zmodule_listpages\n@@menuorder=time desc";
	echo "<br />$sectionNow"; ob_flush(); flush();
	return $output;
	}
	
function addUV3($tempOutput, $sections, $output, $maxUV3, $counterUV3, $maxUV3Min, $maxUV3Max, $sumUV2, $sections, $siteName, $start_time, $half_time, $end_time, $sumCounterKeys, $arrayText, $arrayTextNow, $arrayImages, $arrayVideo, $arrayImagesSmall, $arrayKeys) {

	$keyNow = createKey($arrayKeys);
	
	// офигенный аналог ucfirst, которая не работает c UTF-8
	$keyNowBig=preg_replace('/^\s*(\S)/eu',"mb_strtoupper('\\1', 'UTF-8')",$keyNow);
	
	$timeNow = rndPublic ($start_time, $half_time, $end_time);
	// echo '<br />' . $timeNow;
	$keywordsUV3 = '@@keywords=' . $keyNowBig . ',' . preg_replace('/^\s*(\S)/eu',"mb_strtoupper('\\1', 'UTF-8')",createKey($arrayKeys)) . ',' . $siteName;
	$descriptionUV3 = '@@description=' . $keyNowBig . '. ' . preg_replace('/^\s*(\S)/eu',"mb_strtoupper('\\1', 'UTF-8')",createKey($arrayKeys)) . '. ' . current($sections) . ' - ' . $siteName;
	
	// готовим код анонса (включает текст и фото/видео)
	srand();
	$textAnons = $arrayTextNow[(array_rand($arrayTextNow))];
	$textAnons = str_ireplace("%key1%", $keyNow, $textAnons);
	$textAnons = str_ireplace("%key2%", createKey($arrayKeys), $textAnons);
	$textAnons = str_ireplace("%key3%", createKey($arrayKeys), $textAnons);
	$textAnons = str_ireplace("%key4%", createKey($arrayKeys), $textAnons);
	
	// готовим текст заключительного абзаца
	srand();
	$textEnd = $arrayTextNow[(array_rand($arrayTextNow))];
	$textEnd = str_ireplace("%key1%", $keyNow, $textEnd);
	$textEnd = str_ireplace("%key2%", createKey($arrayKeys), $textEnd);
	$textEnd = str_ireplace("%key3%", createKey($arrayKeys), $textEnd);
	$textEnd = str_ireplace("%key4%", createKey($arrayKeys), $textEnd);
	
	// варианты начальной картинки
	
	$i = mt_rand(0,3);
    switch ($i) {
	// картинка маленькая
	case 0:
		$anons = '<img hspace="5" vspace="5" alt="' . $keyNowBig . '" align="right" src="' . createImageSmall($arrayImagesSmall) . '" />' . $textAnons . '<br clear="all">';
		break;
	// картинка большая
	case 1:
	srand(); $blockImageBig = $arrayImages[(array_rand($arrayImages))];
		$anons = '<div align="center"><img width="400" alt="' . $keyNowBig . '" align="center" src="' . $blockImageBig . '" /></div><br clear="all">' . $textAnons;
		break;
	// видео
	case 2:
		srand(); $blockVideo = '<div align="center">' . $arrayVideo[(array_rand($arrayVideo))] . '</div>';
		$anons = $blockVideo . '<br clear="all"/>'  . $textAnons;
		break;
	// три маленьких картинки
	case 3:
	for ($small = 1; $small <= mt_rand(1,3); $small++) 
		{
	$imageSmall_1 = createImageSmall($arrayImagesSmall);
	$imageSmall_2 = createImageSmall($arrayImagesSmall);
	$imageSmall_3 = createImageSmall($arrayImagesSmall);
	$imageSmall_4 = createImageSmall($arrayImagesSmall);
	$imageBig_1 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_1);
	$imageBig_2 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_2);
	$imageBig_3 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_3);
	$imageBig_4 = str_ireplace("_s.jpg", "_z.jpg", $imageSmall_4);
	
	
	
$anons = 
'<br /><table width="400" border="0" align="center" cellpadding="3" cellspacing="0"><tr>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_1 .'" target="_blank" rel="nofollow"><img alt="' . $keyNowBig . '" src="' . $imageSmall_1 . '" /></a></th>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_2 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_2 . '" /></a></th>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_3 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_3 . '" /></a></th>
<th width="100" align="center" valign="top" scope="col"><a href="' . $imageBig_4 .'" target="_blank" rel="nofollow"><img alt="' . createKey($arrayKeys) . '" src="' . $imageSmall_4 . '" /></a></th>
</tr></table><br clear="all"/><br clear="all">' . $textAnons;
		}

		break;
	}

	$contentUV3 = "\n\n###" . $keyNowBig . "\n@@publish=" . $timeNow . "\n@@last-modified=" . $timeNow .
	"\n$keywordsUV3\n$descriptionUV3\n@@menu=" . $keyNowBig . "\n@@filter=zfilter_php\n@@h1=" . $keyNowBig . "\n" .
	$anons . 
	' <!--more--> <br clear="all" /><br />' . implode ("<br />", $tempOutput) . '<br />' . $textEnd;
	return $contentUV3;
}	
	
	
	
function createScrShot ($dataDir,$width,$font,$arrayAnons,$screenText) {

	
srand();
$anonsAndText1 = explode("----%----",$screenText[(array_rand($screenText))]);
$anonsAndText2 = explode("----%----",$screenText[(array_rand($screenText))]);

// сделаем переносы
$anons1 = str_ireplace("\r\n", "", $arrayAnons[(array_rand($arrayAnons))]) . wordwrap($anonsAndText1[0], 72, "\n");
$text1 = wordwrap($anonsAndText1[1], 90, "\n");
$anons2 = str_ireplace("\r\n", "", $arrayAnons[(array_rand($arrayAnons))])  . wordwrap($anonsAndText2[0], 72, "\n");
$text2 = wordwrap($anonsAndText2[1], 90, "\n");

// вычислим координаты
$boxAnons1 = imagettfbbox(14, 0, $font, $anons1); // координаты анонса1
$boxText1 = imagettfbbox(10, 0, $font, $text1); // координаты текста1
$anonsYcoord1 = abs($boxAnons1[1])+abs($boxAnons1[7]); // Y текста1
$boxYcoord1 = abs($boxText1[1])+abs($boxText1[7]); // Y анонса1
$boxAnons2 = imagettfbbox(14, 0, $font, $anons2); // координаты анонса2
$boxText2 = imagettfbbox(10, 0, $font, $text2); // координаты текста2
$anonsYcoord2 =  abs($boxAnons2[1])+abs($boxAnons2[7]); // Y текста2
$boxYcoord2 =  abs($boxText2[1])+abs($boxText2[7]); // Y анонса2

// создадим изображение
$im = imagecreatetruecolor($width, 56+$anonsYcoord1+$boxYcoord1+$anonsYcoord2+$boxYcoord2);

// создадим цвета, заполним подложку прозрачным цветом
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
imagecolortransparent($im, $white);
imagefilledrectangle($im, 0, 0, $width, 151+$anonsYcoord1+$boxYcoord1+$anonsYcoord2+$boxYcoord2, $white);

// нарисуем анонс и текст
imagettftext($im, 14, 0, 5, 19, $black, $font, $anons1);
imagettftext($im, 10, 0, 5, 25 + $anonsYcoord1, $black, $font, $text1);
imagettftext($im, 14, 0, 5, 45 + $anonsYcoord1+$boxYcoord1, $black, $font, $anons2);
imagettftext($im, 10, 0, 5, 51 + $anonsYcoord1+$boxYcoord1+$anonsYcoord2, $black, $font, $text2);

return $im;
}
	

	// Photo Source URLs
// http://www.flickr.com/services/api/misc.urls.html
// Size Suffixes

// The letter suffixes are as follows:
// s	small square 75x75
// t	thumbnail, 100 on longest side
// m	small, 240 on longest side
// -	medium, 500 on longest side
// z	medium 640, 640 on longest side
// b	large, 1024 on longest side*
// o	original image, either a jpg, gif or png, depending on source format
?>