<?php

$userInfo = userAdmin::getInfo($sys->uId);

$userInfo[TotalNum] = $userInfo[ApproveNum]+$userInfo[ContributionNum]+$userInfo[CallBackNum]+$userInfo[NoContributionNum];

$TPL->assign('userInfo', $userInfo);
$TPL->display('DM_right.html');

?>
