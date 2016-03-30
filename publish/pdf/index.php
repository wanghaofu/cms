<?php
require('./fpdf/chinese.php');
$ContentID = $_GET[ContentID];
$ContentID = ($ContentID && is_numeric($ContentID)) ? $ContentID :exit('error');

@mysql_connect('localhost','root','');
@mysql_selectdb('database');
@mysql_query("SET NAMES 'gbk'");

$query = mysql_query("select a.Title,a.FromSite,a.Content,b.IndexID,b.URL from cms_content_1 as a,cms_content_index as b where a.ContentID = b.ContentID and b.IndexID = '$ContentID'");
$files = mysql_fetch_array($query);
if (!mysql_num_rows($query)) exit('没有找到文章');

$files['FromSite'] = !empty($files['FromSite']) ? strip_tags($files['FromSite']) : '不详';
$str = array('/ /','/&nbsp;/');
$rep = array('','');
$Content =  ereg_replace("\n{1,10}", "\n\n  ", preg_replace($str, $rep, strip_tags($files['Content'])));

$pdf=new PDF_Chinese();
$pdf->AddGBhwFont('simsun','宋体');
$pdf->AddGBFont('simhei','黑体');
// $pdf->AddGBFont('simkai','楷体_GB2312');
// $pdf->AddGBFont('sinfang','仿宋_GB2312');
$pdf->Open();
$pdf->AddPage();
$pdf->SetMargins(10,5,10);
$pdf->SetCreator("BetaNews");
$pdf->SetTitle('BetaNews PDF');
$pdf->SetAuthor('BetaNews.com.cn');
$pdf->SetSubject('BetaNews PDF');
$pdf->SetKeywords('BetaNews');
$pdf->SetCreator('BetaNews.com.cn');

$pdf->Image('logo.jpg',10,10,0,0,'JPG','http://www.sesoe.com');
$pdf->SetFont('simhei','',18);
$pdf->MultiCell(0,30,$files['Title'],'','C');
$pdf->SetFont('simsun','',12);
$pdf->MultiCell(0,6,$Content);
$pdf->Ln();
$pdf->Write(5,' -- 本文来源：'.$files['FromSite']);
$pdf->Ln();
$pdf->Write(5,' -- 本文地址：'.$files['URL'],$files['URL']);
$pdf->Ln();
$pdf->Write(5,' -- PDF File (c) BetaNews.com.cn','http://www.betanews.com.cn'); 

$pdf->SetTitle('BetaNews PDF');
$pdf->SetAuthor('BetaNews.com.cn');
$pdf->SetSubject('BetaNews PDF');
$pdf->SetCreator('BetaNews.com.cn');
$pdf->SetDisplayMode("fullwidth");

// $pdf->Output();
$pdf->Output("BetaNews-PDF{$ContentID}.pdf","D");
?>