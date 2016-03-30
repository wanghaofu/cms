<?php


function utf8_highlight( &$content, $highlightstr, $length = 0, $color1 = "<font color=red>", $color2 = "</font>" )
{
	$keywords = utf8_explode( " ", utf8_trim( $highlightstr ) );
	if ( $keywords[0] != "" )
	{
		$start = utf8_strpos( $content, $keywords[0] );
	}
	if ( $length != 0 )
	{
		$content = utf8_substr( $content, $start, $length );
	}
	$content = utf8_str_replace( $highlightstr, $color1.$highlightstr.$color2, $content );
	foreach ( $keywords as $key => $var )
	{
		if ( $var == $highlightstr )
		{
			continue;
		}
		$content = utf8_str_replace( $var, $color1.$var.$color2, $content );
	}
	return $content;
}

function utf8_encodeFN( $file, $safe = TRUE )
{
	if ( $safe && preg_match( "#^[a-zA-Z0-9/_\\-.%]+\$#", $file ) )
	{
		return $file;
	}
	$file = urlencode( $file );
	$file = str_replace( "%2F", "/", $file );
	return $file;
}

function utf8_decodeFN( $file )
{
	$file = urldecode( $file );
	return $file;
}

function utf8_isASCII( $str )
{
	$i = 0;
	for ( ;	$i < strlen( $str );	++$i	)
	{
		if ( 127 < ord( $str[$i] ) )
		{
			return FALSE;
		}
	}
	return TRUE;
}

function utf8_strip( $str )
{
	$ascii = "";
	$i = 0;
	for ( ;	$i < strlen( $str );	++$i	)
	{
		if ( ord( $str[$i] ) < 128 )
		{
			$ascii .= $str[$i];
		}
	}
	return $ascii;
}

function utf8_check( $Str )
{
	$i = 0;
	for ( ;	$i < strlen( $Str );	++$i	)
	{
		if ( ord( $Str[$i] ) < 128 )
		{
			continue;
		}
		else if ( ( ord( $Str[$i] ) & 224 ) == 192 )
		{
			$n = 1;
		}
		else if ( ( ord( $Str[$i] ) & 240 ) == 224 )
		{
			$n = 2;
		}
		else if ( ( ord( $Str[$i] ) & 248 ) == 240 )
		{
			$n = 3;
		}
		else if ( ( ord( $Str[$i] ) & 252 ) == 248 )
		{
			$n = 4;
		}
		else if ( ( ord( $Str[$i] ) & 254 ) == 252 )
		{
			$n = 5;
		}
		else
		{
			return FALSE;
		}
		$j = 0;
		for ( ;	$j < $n;	++$j	)
		{
			if ( ++$i == strlen( $Str ) || ( ord( $Str[$i] ) & 192 ) != 128 )
			{
				return FALSE;
			}
		}
	}
	return TRUE;
}

function utf8_strlen( $string )
{
	return strlen( utf8_decode( $string ) );
}

function utf8_substr( $str, $start, $length = NULL )
{
	preg_match_all( "/./u", $str, $ar );
	if ( $length != NULL )
	{
		return join( "", array_slice( $ar[0], $start, $length ) );
	}
	else
	{
		return join( "", array_slice( $ar[0], $start ) );
	}
}

function utf8_CsubStr( $str, $start, $len, $suffix = "..." )
{
	preg_match_all( "/[\x01-]|[?遌[?-縘|郲?縘[?-縘|[?颹[?-縘[?-縘|餥?縘[?-縘[?-縘|[?鱙[?-縘[?-縘[?-縘/", $str, $info );
	$len *= 2;
	$i = 0;
	$tmpstr = "";
	while ( $i < $len && array_key_exists( $start, $info[0] ) )
	{
		if ( 1 < strlen( $info[0][$start] ) )
		{
			$i += 2;
			if ( $i <= $len )
			{
				$tmpstr .= $info[0][$start];
			}
			else
			{
				break;
			}
		}
		else
		{
			++$i;
			if ( $i <= $len )
			{
				$tmpstr .= $info[0][$start];
			}
			else
			{
				break;
			}
		}
		++$start;
	}
	return array_key_exists( $start, $info[0] ) ? ( $tmpstr .= $suffix ) : $tmpstr;
}

function utf8_substr_replace( $string, $replacement, $start, $length = 0 )
{
	$ret = "";
	if ( 0 < $start )
	{
		$ret .= utf8_substr( $string, 0, $start );
	}
	$ret .= $replacement;
	$ret .= utf8_substr( $string, $start + $length );
	return $ret;
}

function utf8_explode( $sep, $str )
{
	if ( $sep == "" )
	{
		trigger_error( "Empty delimiter", E_USER_WARNING );
		return FALSE;
	}
	return preg_split( "!".preg_quote( $sep, "!" )."!u", $str );
}

function utf8_str_replace( $s, $r, $str )
{
	if ( !is_array( $s ) )
	{
		$s = "!".preg_quote( $s, "!" )."!u";
	}
	else
	{
		foreach ( $s as $k => $v )
		{
			$s[$k] = "!".preg_quote( $v )."!u";
		}
	}
	return preg_replace( $s, $r, $str );
}

function utf8_ltrim( $str, $charlist = "" )
{
	if ( $charlist == "" )
	{
		return ltrim( $str );
	}
	$charlist = preg_replace( "!([\\\\\\-\\]\\[/])!", "\\\\\${1}", $charlist );
	return preg_replace( "/^[".$charlist."]+/u", "", $str );
}

function utf8_rtrim( $str, $charlist = "" )
{
	if ( $charlist == "" )
	{
		return rtrim( $str );
	}
	$charlist = preg_replace( "!([\\\\\\-\\]\\[/])!", "\\\\\${1}", $charlist );
	return preg_replace( "/[".$charlist."]+\$/u", "", $str );
}

function utf8_trim( $str, $charlist = "" )
{
	if ( $charlist == "" )
	{
		return trim( $str );
	}
	return utf8_ltrim( utf8_rtrim( $str ) );
}

function utf8_strtolower( $string )
{
	if ( !defined( "UTF8_NOMBSTRING" ) && function_exists( "mb_strtolower" ) )
	{
		return mb_strtolower( $string, "utf-8" );
	}
	global $UTF8_UPPER_TO_LOWER;
	$uni = utf8_to_unicode( $string );
	$cnt = count( $uni );
	$i = 0;
	for ( ;	$i < $cnt;	++$i	)
	{
		if ( $UTF8_UPPER_TO_LOWER[$uni[$i]] )
		{
			$uni[$i] = $UTF8_UPPER_TO_LOWER[$uni[$i]];
		}
	}
	return unicode_to_utf8( $uni );
}

function utf8_strtoupper( $string )
{
	if ( !defined( "UTF8_NOMBSTRING" ) && function_exists( "mb_strtolower" ) )
	{
		return mb_strtoupper( $string, "utf-8" );
	}
	global $UTF8_LOWER_TO_UPPER;
	$uni = utf8_to_unicode( $string );
	$cnt = count( $uni );
	$i = 0;
	for ( ;	$i < $cnt;	++$i	)
	{
		if ( $UTF8_LOWER_TO_UPPER[$uni[$i]] )
		{
			$uni[$i] = $UTF8_LOWER_TO_UPPER[$uni[$i]];
		}
	}
	return unicode_to_utf8( $uni );
}

function utf8_deaccent( $string, $case = 0 )
{
	if ( $case <= 0 )
	{
		global $UTF8_LOWER_ACCENTS;
		$string = str_replace( array_keys( $UTF8_LOWER_ACCENTS ), array_values( $UTF8_LOWER_ACCENTS ), $string );
	}
	if ( 0 <= $case )
	{
		global $UTF8_UPPER_ACCENTS;
		$string = str_replace( array_keys( $UTF8_UPPER_ACCENTS ), array_values( $UTF8_UPPER_ACCENTS ), $string );
	}
	return $string;
}

function utf8_stripspecials( $string, $repl = "", $additional = "" )
{
	global $UTF8_SPECIAL_CHARS;
	static $specials = NULL;
	if ( is_null( $specials ) )
	{
		$specials = preg_quote( unicode_to_utf8( $UTF8_SPECIAL_CHARS ), "/" );
	}
	return preg_replace( "/[".$additional."\\x00-\\x19".$specials."]/u", $repl, $string );
}

function utf8_strpos( $haystack, $needle, $offset = 0 )
{
	if ( !defined( "UTF8_NOMBSTRING" ) && function_exists( "mb_strpos" ) )
	{
		return mb_strpos( $haystack, $needle, $offset, "utf-8" );
	}
	if ( !$offset )
	{
		$ar = utf8_explode( $needle, $str );
		if ( 1 < count( $ar ) )
		{
			return utf8_strlen( $ar[0] );
		}
		return FALSE;
	}
	else
	{
		if ( !is_int( $offset ) )
		{
			trigger_error( "Offset must be an integer", E_USER_WARNING );
			return FALSE;
		}
		$str = utf8_substr( $str, $offset );
		if ( FALSE !== ( $pos = utf8_strpos( $str, $needle ) ) )
		{
			return $pos + $offset;
		}
		return FALSE;
	}
}

function utf8_tohtml( $str )
{
	$ret = "";
	$max = strlen( $str );
	$last = 0;
	$i = 0;
	for ( ;	$i < $max;	++$i	)
	{
		$c = $str[$i];
		$c1 = ord( $c );
		if ( $c1 >> 5 == 6 )
		{
			$ret .= substr( $str, $last, $i - $last );
			$c1 &= 31;
			$c2 = ord( $str[++$i] );
			$c2 &= 63;
			$c2 |= ( $c1 & 3 ) << 6;
			$c1 >>= 2;
			$ret .= "&#".( $c1 * 100 + $c2 ).";";
			$last = $i + 1;
		}
	}
	return $ret.substr( $str, $last, $i );
}

function utf8_to_unicode( $str )
{
	$unicode = array( );
	$values = array( );
	$lookingFor = 1;
	$i = 0;
	for ( ;	$i < strlen( $str );	++$i	)
	{
		$thisValue = ord( $str[$i] );
		if ( $thisValue < 128 )
		{
			$unicode[] = $thisValue;
		}
		else
		{
			if ( count( $values ) == 0 )
			{
				$lookingFor = $thisValue < 224 ? 2 : 3;
			}
			$values[] = $thisValue;
			if ( count( $values ) == $lookingFor )
			{
				$number = $lookingFor == 3 ? $values[0] % 16 * 4096 + $values[1] % 64 * 64 + $values[2] % 64 : $values[0] % 32 * 64 + $values[1] % 64;
				$unicode[] = $number;
				$values = array( );
				$lookingFor = 1;
			}
		}
	}
	return $unicode;
}

function unicode_to_utf8( &$str )
{
	if ( !is_array( $str ) )
	{
		return "";
	}
	$utf8 = "";
	foreach ( $str as $unicode )
	{
		if ( $unicode < 128 )
		{
			$utf8 .= chr( $unicode );
		}
		else if ( $unicode < 2048 )
		{
			$utf8 .= chr( 192 + ( $unicode - $unicode % 64 ) / 64 );
			$utf8 .= chr( 128 + $unicode % 64 );
		}
		else
		{
			$utf8 .= chr( 224 + ( $unicode - $unicode % 4096 ) / 4096 );
			$utf8 .= chr( 128 + ( $unicode % 4096 - $unicode % 64 ) / 64 );
			$utf8 .= chr( 128 + $unicode % 64 );
		}
	}
	return $utf8;
}

function utf8_to_utf16be( &$str, $bom = FALSE )
{
	$out = $bom ? "?" : "";
	if ( !defined( "UTF8_NOMBSTRING" ) && function_exists( "mb_convert_encoding" ) )
	{
		return $out.mb_convert_encoding( $str, "UTF-16BE", "UTF-8" );
	}
	$uni = utf8_to_unicode( $str );
	foreach ( $uni as $cp )
	{
		$out .= pack( "n", $cp );
	}
	return $out;
}

function utf16be_to_utf8( &$str )
{
	$uni = unpack( "n*", $str );
	return unicode_to_utf8( $uni );
}

$UTF8_LOWER_TO_UPPER = array( 97 => 65, 966 => 934, 355 => 354, 229 => 197, 98 => 66, 314 => 313, 225 => 193, 322 => 321, 973 => 910, 257 => 256, 1169 => 1168, 948 => 916, 347 => 346, 100 => 68, 947 => 915, 244 => 212, 1098 => 1066, 1081 => 1049, 275 => 274, 1084 => 1052, 351 => 350, 324 => 323, 238 => 206, 1118 => 1038, 1103 => 1071, 954 => 922, 341 => 340, 105 => 73, 115 => 83, 7711 => 7710, 309 => 308, 1095 => 1063, 960 => 928, 1080 => 1048, 243 => 211, 1088 => 1056, 1108 => 1028, 1077 => 1045, 1097 => 1065, 331 => 330, 1073 => 1041, 1113 => 1033, 7683 => 7682, 246 => 214, 249 => 217, 110 => 78, 1105 => 1025, 964 => 932, 1091 => 1059, 349 => 348, 1107 => 1027, 968 => 936, 345 => 344, 103 => 71, 228 => 196, 940 => 902, 942 => 905, 359 => 358, 958 => 926, 357 => 356, 279 => 278, 265 => 264, 118 => 86, 254 => 222, 343 => 342, 250 => 218, 7777 => 7776, 7811 => 7810, 226 => 194, 281 => 280, 326 => 325, 112 => 80, 337 => 336, 1102 => 1070, 297 => 296, 967 => 935, 318 => 317, 1090 => 1058, 122 => 90, 1096 => 1064, 961 => 929, 7809 => 7808, 365 => 364, 245 => 213, 117 => 85, 375 => 374, 252 => 220, 7767 => 7766, 963 => 931, 1082 => 1050, 109 => 77, 363 => 362, 369 => 368, 1092 => 1060, 236 => 204, 361 => 360, 959 => 927, 107 => 75, 242 => 210, 224 => 192, 1076 => 1044, 969 => 937, 7787 => 7786, 227 => 195, 1101 => 1069, 1078 => 1046, 417 => 416, 269 => 268, 285 => 284, 240 => 208, 316 => 315, 1119 => 1039, 1114 => 1034, 232 => 200, 965 => 933, 102 => 70, 253 => 221, 99 => 67, 539 => 538, 234 => 202, 953 => 921, 378 => 377, 239 => 207, 432 => 431, 101 => 69, 955 => 923, 952 => 920, 956 => 924, 1116 => 1036, 1087 => 1055, 1100 => 1068, 254 => 222, 240 => 208, 7923 => 7922, 104 => 72, 235 => 203, 273 => 272, 1075 => 1043, 303 => 302, 230 => 198, 120 => 88, 353 => 352, 367 => 366, 945 => 913, 1111 => 1031, 371 => 370, 255 => 376, 111 => 79, 1083 => 1051, 949 => 917, 1093 => 1061, 289 => 288, 382 => 381, 380 => 379, 950 => 918, 946 => 914, 941 => 904, 7813 => 7812, 373 => 372, 113 => 81, 1079 => 1047, 7691 => 7690, 328 => 327, 261 => 260, 1112 => 1032, 333 => 332, 237 => 205, 121 => 89, 267 => 266, 974 => 911, 114 => 82, 1072 => 1040, 1109 => 1029, 1106 => 1026, 295 => 294, 311 => 310, 299 => 298, 943 => 906, 1099 => 1067, 108 => 76, 951 => 919, 293 => 292, 537 => 536, 251 => 219, 287 => 286, 1086 => 1054, 7745 => 7744, 957 => 925, 263 => 262, 971 => 939, 1094 => 1062, 254 => 222, 231 => 199, 970 => 938, 1089 => 1057, 1074 => 1042, 271 => 270, 248 => 216, 119 => 87, 283 => 282, 116 => 84, 106 => 74, 1115 => 1035, 1110 => 1030, 259 => 258, 955 => 923, 241 => 209, 1085 => 1053, 972 => 908, 233 => 201, 240 => 208, 1111 => 1031, 291 => 290 );
$UTF8_UPPER_TO_LOWER = @array_flip( $UTF8_LOWER_TO_UPPER );
$UTF8_LOWER_ACCENTS = array( "脿" => "a", "么" => "o", "膹" => "d", "岣" => "f", "毛" => "e", "拧" => "s", "啤" => "o", "脽" => "ss", "膬" => "a", "艡" => "r", "葲" => "t", "艌" => "n", "腻" => "a", "姆" => "k", "楫" => "s", "峄" => "y", "艈" => "n", "暮" => "l", "魔" => "h", "峁" => "p", "贸" => "o", "煤" => "u", "臎" => "e", "茅" => "e", "莽" => "c", "岷" => "w", "膵" => "c", "玫" => "o", "峁" => "s", "酶" => "o", "模" => "g", "脓" => "t", "药" => "s", "腊" => "e", "膲" => "c", "橹" => "s", "卯" => "i", "疟" => "u", "膰" => "c", "臀" => "e", "诺" => "w", "峁" => "t", "奴" => "u", "膷" => "c", "枚" => "oe", "猫" => "e", "欧" => "y", "膮" => "a", "艂" => "l", "懦" => "u", "暖" => "u", "艧" => "s", "臒" => "g", "募" => "l", "茠" => "f", "啪" => "z", "岷" => "w", "岣" => "b", "氓" => "a", "矛" => "i", "茂" => "i", "岣" => "d", "钮" => "t", "艞" => "r", "盲" => "ae", "铆" => "i", "艜" => "r", "锚" => "e", "眉" => "ue", "貌" => "o", "脓" => "e", "帽" => "n", "艅" => "n", "磨" => "h", "臐" => "g", "胆" => "d", "牡" => "j", "每" => "y", "农" => "u", "怒" => "u", "瓢" => "u", "牛" => "t", "媒" => "y", "艖" => "o", "芒" => "a", "木" => "l", "岷" => "w", "偶" => "z", "墨" => "i", "茫" => "a", "摹" => "g", "峁" => "m", "艒" => "o", "末" => "i", "霉" => "u", "寞" => "i", "藕" => "z", "谩" => "a", "没" => "u", "镁" => "th", "冒" => "dh", "忙" => "ae", "碌" => "u" );
$UTF8_UPPER_ACCENTS = array( "脌" => "A", "脭" => "O", "髓" => "D", "岣" => "F", "唇" => "E", "艩" => "S", "茽" => "O", "膫" => "A", "艠" => "R", "葰" => "T", "舶" => "N", "胴" => "A", "亩" => "K", "舣" => "S", "峄" => "Y", "艆" => "N", "墓" => "L", "摩" => "H", "峁" => "P", "脱" => "O", "脷" => "U", "脐" => "E", "脡" => "E", "脟" => "C", "岷?" => "W", "膴" => "C", "脮" => "O", "峁" => "S", "痞" => "O", "蘑" => "G", "纽" => "T", "葮" => "S", "臇" => "E", "膱" => "C", "樯" => "S", "脦" => "I", "虐" => "U", "膯" => "C", "脸" => "E", "糯" => "W", "峁" => "T", "弄" => "U", "膶" => "C", "脰" => "Oe", "脠" => "E", "哦" => "Y", "膭" => "A", "造" => "L", "挪" => "U", "女" => "U", "舰" => "S", "臑" => "G", "幕" => "L", "茟" => "F", "沤" => "Z", "岷" => "W", "岣" => "B", "脜" => "A", "脤" => "I", "脧" => "I", "岣" => "D", "扭" => "T", "艝" => "R", "胫" => "Ae", "脥" => "I", "艛" => "R", "脢" => "E", "脺" => "Ue", "脪" => "O", "脍" => "E", "脩" => "N", "艃" => "N", "膜" => "H", "膑" => "G", "膼" => "D", "拇" => "J", "鸥" => "Y", "浓" => "U", "努" => "U", "漂" => "U", "泞" => "T", "脻" => "Y", "艕" => "O", "吻" => "A", "慕" => "L", "岷" => "W", "呕" => "Z", "莫" => "I", "脙" => "A", "臓" => "G", "峁?" => "M", "艑" => "O", "抹" => "I", "脵" => "U", "漠" => "I", "殴" => "Z", "脕" => "A", "胀" => "U", "脼" => "Th", "脨" => "Dh", "脝" => "Ae" );
$UTF8_SPECIAL_CHARS = array( 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 47, 59, 60, 61, 62, 63, 64, 91, 92, 93, 94, 96, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 215, 247, 711, 728, 729, 730, 731, 732, 733, 768, 769, 771, 777, 803, 900, 901, 903, 946, 966, 977, 978, 981, 982, 1456, 1457, 1458, 1459, 1460, 1461, 1462, 1463, 1464, 1465, 1467, 1468, 1469, 1470, 1471, 1472, 1473, 1474, 1475, 1523, 1524, 1548, 1563, 1567, 1600, 1611, 1612, 1613, 1614, 1615, 1616, 1617, 1618, 1642, 3647, 8204, 8205, 8206, 8207, 8211, 8212, 8213, 8215, 8216, 8217, 8218, 8220, 8221, 8222, 8224, 8225, 8226, 8230, 8240, 8242, 8243, 8249, 8250, 8260, 8359, 8362, 8363, 8364, 8470, 8472, 8482, 8486, 8501, 8592, 8593, 8594, 8595, 8596, 8597, 8629, 8656, 8657, 8658, 8659, 8660, 8704, 8706, 8707, 8709, 8710, 8711, 8712, 8713, 8715, 8719, 8721, 8722, 8725, 8727, 8729, 8730, 8733, 8734, 8736, 8743, 8744, 8745, 8746, 8747, 8756, 8764, 8773, 8776, 8800, 8801, 8804, 8805, 8834, 8835, 8836, 8838, 8839, 8853, 8855, 8869, 8901, 8976, 8992, 8993, 9001, 9002, 9321, 9472, 9474, 9484, 9488, 9492, 9496, 9500, 9508, 9516, 9524, 9532, 9552, 9553, 9554, 9555, 9556, 9557, 9558, 9559, 9560, 9561, 9562, 9563, 9564, 9565, 9566, 9567, 9568, 9569, 9570, 9571, 9572, 9573, 9574, 9575, 9576, 9577, 9578, 9579, 9580, 9600, 9604, 9608, 9612, 9616, 9617, 9618, 9619, 9632, 9650, 9660, 9670, 9674, 9679, 9687, 9733, 9742, 9755, 9758, 9824, 9827, 9829, 9830, 9985, 9986, 9987, 9988, 9990, 9991, 9992, 9993, 9996, 9997, 9998, 9999, 10000, 10001, 10002, 10003, 10004, 10005, 10006, 10007, 10008, 10009, 10010, 10011, 10012, 10013, 10014, 10015, 10016, 10017, 10018, 10019, 10020, 10021, 10022, 10023, 10025, 10026, 10027, 10028, 10029, 10030, 10031, 10032, 10033, 10034, 10035, 10036, 10037, 10038, 10039, 10040, 10041, 10042, 10043, 10044, 10045, 10046, 10047, 10048, 10049, 10050, 10051, 10052, 10053, 10054, 10055, 10056, 10057, 10058, 10059, 10061, 10063, 10064, 10065, 10066, 10070, 10072, 10073, 10074, 10075, 10076, 10077, 10078, 10081, 10082, 10083, 10084, 10085, 10086, 10087, 10111, 10121, 10131, 10132, 10136, 10137, 10138, 10139, 10140, 10141, 10142, 10143, 10144, 10145, 10146, 10147, 10148, 10149, 10150, 10151, 10152, 10153, 10154, 10155, 10156, 10157, 10158, 10159, 10161, 10162, 10163, 10164, 10165, 10166, 10167, 10168, 10169, 10170, 10171, 10172, 10173, 10174, 63193, 63194, 63195, 63703, 63704, 63705, 63706, 63707, 63708, 63709, 63710, 63711, 63712, 63713, 63714, 63715, 63716, 63717, 63718, 63719, 63720, 63721, 63722, 63723, 63724, 63725, 63726, 63727, 63728, 63729, 63730, 63731, 63732, 63733, 63734, 63735, 63736, 63737, 63738, 63739, 63740, 63741, 63742, 65148, 65149 );
?>
