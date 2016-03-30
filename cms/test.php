<?php

//初始化
$ch = curl_init();
//设置选项，包括URL
curl_setopt($ch, CURLOPT_URL, "http://www.iteye.com/index/human_test");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch,CURLOPT_REFERER,"http://www.iteye.com/index/human_test");
//执行并获取HTML文档内容
$output = curl_exec($ch);
//释放curl句柄
curl_close($ch);
//打印获得的数据
echo($output);
/**
curl -e "http://www.iteye.com/index/human_test" \
> -d "authenticity_token=nTuJqBNMfpdePtrct3rffRNHkVdyP6VSElo5qocxt9w=&captcha=ZUYECV" \
> http://www.iteye.com/index/human_test
**/
echo "请输入token";
$token= trim(fget(STDIN));
echo "\n";
echo "请输入图形验证码";
$code =trim(fget(STDIN));
echo "\n";
$commit="提交";
$curlPost='authenticity_token='.urlencode($token).'&captcha='.urlencode($code).'&commit='.urlencode($commit);
$ch=curl_init();


curl_setopt($ch,CURLOPT_URL,'http://www.xx.com/index/human_test');
curl_setopt($ch,CURLOPT_HEADER,1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$curlPost);
curl_setopt($ch,CURLOPT_REFERER,"http://www.xx.com/index/human_test");
$data=curl_exec();
echo $data;
curl_close($ch);