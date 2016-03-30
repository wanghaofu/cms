<?php
chdir('../');
require_once "common.php";
require_once INCLUDE_PATH."data.class.php";
require_once INCLUDE_PATH."data.remote.class.php";
//require_once INCLUDE_PATH."functions.php";


require_once KTPL_DIR . 'kTemplate.class.php';
require_once INCLUDE_PATH.'image.class.php';
require_once INCLUDE_PATH."file.class.php";
if (!extension_loaded('ftp')) {
	require_once INCLUDE_PATH."ftp.class.php";
}
require_once INCLUDE_PATH."Error.php";
require_once INCLUDE_PATH."exception.class.php";
require_once INCLUDE_PATH."admin/psn_admin.class.php";
include_once SETTING_DIR."cms.ini.php";
//$db->setDebug(1);
include_once(CACHE_DIR.'Cache_SYS_ENV.php');
include_once(CACHE_DIR.'Cache_PSN.php');
include_once(CACHE_DIR.'Cache_CateList.php');

require_once INCLUDE_PATH."admin/publishAdmin.class.php";
require_once INCLUDE_PATH."admin/content_table_admin.class.php";
require_once INCLUDE_PATH."admin/tplAdmin.class.php";
require_once INCLUDE_PATH."admin/psn_admin.class.php";
require_once INCLUDE_PATH."admin/site_admin.class.php";
require_once INCLUDE_PATH."cms.class.php";
require_once INCLUDE_PATH."cms.func.php";

// $str1="暗夜传说,边缘OL,冰风传奇,霸王大陆,百年战争,	百变金刚OL,赤壁,春秋外传,超级跑跑,	传奇世界,	苍生OL,宠物小精灵,穿越火线,超级西西三国,春秋Q传,传奇归来,传世群英传,穿越OL,C9,沧海,传奇外传2.0,传奇,彩虹岛(LaTale),创世OL,宠物森林,超级舞者,超级武林大富翁,苍天,传奇3,苍穹,冲锋岛,大话战国,大话水浒,东游记,大唐豪侠外传,17173,大冒险2,大话轩辕,蛋清OL,刀剑英雄2,大话西游3,弹头奇兵,大唐无双,刀剑?英雄,地牢骑士团,第二人生,大话仙剑,大富翁Online,断剑问情,大航海时代,地下城与勇士,大明龙权,2061,恶魔法则,EVE,Online,疯狂派对,疯狂弹头,反恐行动,疯狂飚车,飞天风云,新封印传说,风色幻想OL,FIFA,Online2,风火之旅,反恐精英OL,封神世界,风雨寻秦,富甲西游,17173,凤舞天骄,风云,封神榜2,纷争OL,凤舞天骄,功夫世界,鬼吹灯OL,光之国度,GT,劲舞团2,光线飞车,国威,鬼吹灯外传,功夫OnLine,盖娅战记,光明与黑暗OL,灵游记,古域,QQ,幻想世界,幻想学园,航海世纪国际版,魂Online,红楼Q梦,华夏OnLine,华夏Ⅱ,海盗王,海战集结号,混沌大陆,幻想大陆,海战传奇,黄易群侠传,劲爆篮球,极品飞车OL,机甲世纪II,极道车神,剑侠贰外传,剑侠世界,精武世界,巨星,剑仙,九阴真经,精灵乐章,极速轮滑,17173,金庸2,剑舞江南,极光世界,劲乐团,江湖行,激战海陆空,机战,剑网3,惊天动地,江湖OL,劲舞世界,九洲英雄,剑侠情缘,剑侠情缘二,激斗,机甲世纪,极限飚车,巨人,街头篮球,金庸群侠传Ⅱ,九界,欢乐君主,劲舞团,KO,堂,哼哼哈嘿,开创世纪,昆仑OL,空战世纪,口袋西游-蓝龙,开心OL,抗战Online,抗战英雄传,开心果,龙与地下城,路尼亚战记,龙之谷,乱世英杰,猎国,龙,炼狱online,亮剑,英雄联盟,乱武天下,新龙骑士OL,狼队,龙影,绿色征途,龙魂,龙腾世界,烈焰飞雪,聊斋,LUNA,龙神传说,龙的传人,鹿鼎记,洛奇,猎刃,六道OL,六脉神剑,梦想岛,新密传,名将三国,魔力宝贝,梦三国OL,魔神争霸,冒险岛,梦幻国度,梦幻诛仙,魔法之门,梦幻迪士尼,梦幻骑士,梦幻西游,墨香,名将,魔骑士OL,魔盗OL,梦想时空,梦想世界,魔咒OnLine,梦幻战国,梦幻龙族,魔域,真爱西游,魔力宝贝2,魔界OL,梦幻古龙,梦幻星球,魔兽世界,倾国倾城,MKZ-,军魂,诺亚传说,纳雅外传,NBA,STREET,OL,光之冒险,飘流幻境3.0,蓬莱,泡泡堂,跑跑卡丁车,飘邈之旅,千秋,七剑,七龙珠online,QQ,自由幻想,QQ,飞车,火力风暴,奇迹传说(MUX),17173,启航,秦伤,梦幻情天,群英赋,QQ,飞行岛,奇侠传,QQ,华夏,QQ,仙境,QQ,三国,拳皇世界,QQ,炫舞,QQ,仙侠传,QQ,幻想,QQ,西游,QQ,封神记,秦始皇OL,热血天下,人生OL,合金战纪,RF2.0,星战,热血,RUSH-,冲锋,热舞派对,R2,仙境传说2,洛汗,热血江湖,守护者OL,三国杀OL,仙,SD,敢达OL,山海志,神魔大陆,三国泡泡,石器Q传,盛世OL,神仙OL,水浒Q传,三国志OL,三国群英传2OL,神兽,十二之天2,数码宝贝,闪投部落,三国传奇online,生肖传说,神谕,石器世界,生肖外传,兽血沸腾,生死格斗OL,三国鼎立,三国征战,蜀山新传,盛世缘,丝路传说,水晶物语OL,控魂大师,神墓OL,圣灵传说,圣斗士OL,神州,神将,蜀门,石器时代2,三国群英传OL,三国斩,圣域传说OL,神兵传奇,神泣,时空之泪,神鬼传奇,挑战,天羽传奇,天涯OL,天下贰,滑轮风,天机Online,天龙八部2,天道online,天使之恋,突袭OL,探索Online,天上人间,天堂二,吞食天地,天外飞仙,新天下无双,天空战记,天之翼,天地传说,特种部队,投名状Online,童梦OL,体育帝国,天地online,天元,吞食天地2OL,天子,天之痕OL,武林群侠传,完美世界,武神,王者世界,武林群侠传2,51,新炫舞,王者三国,王者之印,武林外传,完美国际,梦幻问情,舞街区,武者OL,网球宝贝,万王之王3,问天OL,问鼎,问道,武易,武侠世界,武林外史,我的小傻瓜,王者,逍遥传说,寻仙,新蜀山剑侠,星尘传说,侠道金刚OL,天尊online,仙剑OL,侠客行,仙途,侠义道Ⅱ,新三国策IV,新奇迹世界,仙境传说,笑闹天宫,仙侣奇缘二,新侠义道,西游记,新天翼之链,新海盗王,新绝代双骄,暇月战歌,西游天下,降龙之剑,轩辕传奇,星空传奇,侠义道,星空之恋,笑傲江湖OL,侠客列传,仙魔OL,星座OL,远征OL,永恒之塔,英雄美人,勇者传说,倚天Ⅱ自由世界,倚天2外传,英雄年代2,佣兵天下,易三国,勇士OL,翼魂,倚天,伊苏战记,勇气online,异界,炎龙骑士团OL,预言online,英雄Ⅱ,炎黄传说,新倚天剑与屠龙刀,游戏人生,英雄岛,妖怪A梦,英雄之门,英雄连Online,英雄无敌在线,御龙在天,永久基地,纵横OL,新战国英雄,征战,蒸汽幻想,峥嵘天下OL,征途,真三国,征途2,中华英雄,战锤online,战地之王(AVA),战火：红警,最终幻想XI,真女神转生,真?,三国无双OL,真水浒,蒸汽幻想2:战争之王,众神之战,征服,战神传说,诸侯,指环王OL,征途怀旧版,诛仙2,卓越之剑GE,纵横时空,";
// $str2="鏖战,WEB暗战,傲视天地,冰封天下,变型金刚,online,飚马,OnWeb,便利商店,楚汉风云,超级明星,SuperStar,春秋霸业,创世英雄,叱咤三国,17173,创世之光,虫虫总动员,地产风云,大庄家,大汉风云,篮球大联盟,帝国重生,斗法修仙传,大唐英雄,滴血玫瑰,帝国崛起,大兵小将,帝国文明,刀光剑影,大联盟篮球经理,刀剑江湖OW,天外圣传,帝国争霸,大海战之纵横四海,大富豪,弹弹堂,二战风云,疯狂足球,废铜烂铁,封神之兵发岐山,飞天西游,封神天下,封神无敌,封神西游,疯狂坦克,WB,封神行,凡人修真,勾引部落,功夫,OnWeb,红警：左岸,海岛帝国,幻境2,海宝彩虹城,红尘战国,海战英豪,欢乐江湖,幻世觉醒,幻想三国,黑暗契约,黄金国度,海洋时代,华山论剑,海战,Online,幻境,online,海狼,Ikariam(艾卡瑞恩),Iworld,剑侠情缘,web,决战天下,精灵世界,江湖风云,江山多娇,江湖传说,机甲归来,江山,极地争霸,江湖,九洲战记,江湖外传,剑仙风云,金融风暴,Online,决战光明顶,警戒,君临三国,WEB,绝地战争,开心西游,开心炮,狂想之都,K2乐园,WEB,口袋精灵,昆仑,OnWeb,快乐女声,乐土,篮球风暴,龙与乡巴佬,篮球江湖,乱世三国,领土争霸,领主传说,乱世英雄传,龙之刃,龙城领主,乱世Q战,乱舞春秋,龙之霸业,乱武,龙门,乱世霸业,乱世隋唐,领主OL,美眉梦工场,OnWeb,魔兽外传,明珠三国,怪叔叔的阴谋,明朝时代,魔幻三国志,灭神WEB,魔怪世界,名剑,魔法风云,魔法传奇,魔晶幻想,猫狗大战,猫游记,梦幻足球,牧场,OnWeb,魔界,OnWeb,明1644,魔法之城,魔兽风云,墨攻天下,魔力学堂,Miu!圣光,摩登三国2,魔塔世界,梦幻红楼,FLASH,美国1930,Norron,北欧神话,office,三国,飘渺西游,盘龙神墓记,泡面三国,飘渺仙剑,绿豆蛙漂流岛,千军破,倾城,全民三国,WEB枪炮玫瑰,七龙纪,七龙纪Ⅱ,奇侠天下,群英会,热血征途,热血大唐,荣誉,热血水浒,热血三国志,热血2,RPG三国,热血三国,神鬼世界,三国风云,三国天下,OnWeb,射雕传,商业大亨,Online,WEB士兵突击,山寨英雄传,三国兵临城下,三国之群雄崛起,神魔界,商周霸业,水晶战记,食神小当家,神魔令,商战,飞黄腾达,三十六计,时空贰,山海英雄,太阁无双,天空左岸,铁血英雄,天下无双2,天策,精灵战纪,天问,图腾三国,坦克大战,Online,太阁立志,天书奇谈,Travian,部落战争,武林情缘,武侠世界,Flash,问仙,武林传奇2,武林之王,武侠风云,文明WEB,武林帝国,网娃总动员,武林传奇,网页三国,网页兽血,WEB三国志,完美农场,王朝战争,XBA篮球经理,星客志愿,雄心,星空2英雄传说,逍遥三国志,雄霸三国,仙域,轩辕英雄网络,星际传说,星球大战,星石传说,现代战争,星空战史,神魔剑,西游外传,星际家园Ⅱ,星际警戒,轩辕异示录,西游传,修真,逍遥宝贝,西游战记,星际前夕,仙之侠道,星际帝国,宇宙英雄大乱斗,又一城,元游世界,翼三国,英雄之王,遗迹守望者,异幻城棋牌,远洋传说,云之秘境,英雄世界,永恒世界,妖魔道,妖姬无双,羊狼战争,炎龙骑士的远征,游界DOTA,英雄之城,永恒之剑,17173,悠游三国,勇士无敌,原版三国,银河战谷,野人纪,终极学院,指挥官,2146,战将传奇,战国风云,中国故事,中华客栈,OnWeb,争封,终极杀手,征战天下,星战,诸神的黄昏,最三国,战神世界Ⅱ,中华小当家,战斧,Online,真命,足球天下,铸剑,诸神的黄昏,Ⅱ,征途风云";

$arr1=explode(',',$str1);
$db->setDebug( 1 );
foreach( $arr1 as $key => $value )
{
	if (!$value) exit();
	$sql1= "select * from tiantangwan_cn_content_70 where game_name like '%{$value}%'; ";
	
	$time=time();
	$sql="insert into tiantangwan_cn_content_70 (Creationdate,game_name,game_class,game_status,shoufei,game_type,huamian,public_date) values({$time},'{$value}','网络游戏','运营中','道具收费','暂无','暂无',$time);";
//	$sql="insert into tiantangwan_cn_publish_70 (contentID,PublishDate,game_name,game_class,game_status) values({$time},'{$value}','网络游戏','运营中');";
	echo $sql1."\n<br/>";
	echo $sql."\n<br/>";

	$res = $db->getRow($sql1);
	if (!$res){
		echo $key."\n<br/>";
		$res=$db->Execute($sql);
		if ( $res ){
		$conId =$db->Insert_ID();
			$sql3="insert into tiantangwan_cn_content_index (ContentId,NodeId,TableId,PublishDate) 
			values({$conId},8,70,$time);";
			echo $sql3;
			$res=$db->Execute($sql3);
			$sql4="update tiantangwan_cn_content_index set ParentIndexID=IndexId";
			$res=$db->Execute($sql4);
		}
		$res?'ok':'error';
	}
	
}


?>