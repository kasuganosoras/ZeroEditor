<?php
declare(ticks = 1);

function signal_handler($signal) {
	global $text;
	system("clear");
	system('stty echo');
	$savename = "";
	while($savename == "") {
		echo "请输入想要保存的文件名，不输入则不保存\nInput> ";
		$name = trim(fgets(STDIN));
		if(!empty($name)) {
			$savepath =  __DIR__ . "/" . $name;
			if(file_exists($savepath)) {
				echo "文件 {$name} 已存在，是否覆盖？(y/n)\nInput> ";
				while(empty($yesorno)) {
					$check = trim(fgets(STDIN));
					if(strtolower($check) == "y") {
						$savename = $name;
						$yesorno = "y";
					}
					$yesorno = "n";
				}
			} else {
				$savename = $name;
			}
		} else {
			exit;
		}
	}
	echo "已保存到：" . $savepath;
	// 这行注释掉就可以保存文件
	// file_put_contents($savepath, $text);
	exit;
}

pcntl_signal(SIGINT, "signal_handler");

function replaceOut($str) {
    $numNewLines = substr_count($str, "\n");
    echo chr(27) . "[0G"; // Set cursor to first column
    echo $str;
    echo chr(27) . "[" . $numNewLines ."A"; // Set cursor up x lines
}

$linewidth = trim(str_replace(";", "", shell_exec('stty -a |grep columns | awk "{print \$7}"')));
define("SCREEN_WIDTH", $linewidth);
system('stty cbreak');
system('stty -echo');
$stdin = fopen('php://stdin', 'r');
$text = "";
$titlebar = "ZeroEditor 编辑器 | F1 撤销编辑 | F2 恢复编辑 | F3 查找内容 | F4 替换内容 |";
system("clear");
echo $titlebar . "\n" . str_repeat("=", SCREEN_WIDTH) . "\n1 | ";
$controling = false;
$extratext = "";
$nowline = 1;
while (1) {
	$c = fgetc($stdin);
	$a = ord($c);
	if($a == 20 || $a == 22) {
		exit;
	}
	if($a == 8) {
		$controling = false;
		$text = mb_substr($text, 0, mb_strlen($text) -1);
	} elseif($a == 27) {
		$controling = true;
	} elseif($controling) {
		if($a == 91) {
			$controling = true;
		}
		switch($a) {
			case 65:
				echo chr(27) . "[1A";
				$controling = false;
				break;
		}
	} else {
		$controling = false;
		$text .= $c;
	}
	system("clear");
	echo $titlebar . "\n" . str_repeat("=", SCREEN_WIDTH) . "\n";
	$exp = explode("\n", $text);
	$sp = count($exp);
	//foreach($exp as $line => $value) {
	for($i = 0;$i < count($exp);$i++) {
		$line = $i + 1;
		$pd = str_repeat(" ", strlen("{$sp}") - strlen("{$line}") + 1) . "| ";
		echo $line . $pd . $exp[$i] . $extratext;
		$nowline = $line;
		if($line !== count($exp)) {
			echo "\n";
		}
	}
}
