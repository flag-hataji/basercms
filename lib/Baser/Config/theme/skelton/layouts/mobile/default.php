<?php
/**
 * [モバイル] レイアウト
 */
?>
<cake:nocache><?php $bcMobile->header() ?></cake:nocache><?php $this->bcBaser->xmlHeader() ?><?php $this->bcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<?php $this->bcBaser->charset() ?>
<?php $this->bcBaser->title() ?>
<?php $this->bcBaser->metaDescription() ?>
<?php $this->bcBaser->metaKeywords() ?>
</head>
<body bgcolor="#FFFFFF" id="<?php $this->bcBaser->contentsName() ?>">
	
	<?php echo $this->bcBaser->siteConfig['name'] ?>
	<?php echo $content_for_layout; ?><br />
	<?php $this->bcBaser->element('contents_navi') ?><br />

	<?php $this->bcBaser->link('トップへ','/'.Configure::read('BcAgent.mobile.alias').'/') ?>

	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	
	<center>
		<?php $this->bcBaser->img('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")); ?>
		<?php $this->bcBaser->img('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?>
		<font size="1">(C)baserCMS</font>
	</center>

<?php $this->bcBaser->element('google_analytics') ?>
<?php $this->bcBaser->func() ?>
</body>
</html>