<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ツールバー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
App::uses('AuthComponent', 'Controller/Component');
$this->BcBaser->js(array('admin/outerClick','admin/jquery.fixedMenu'));
$loginUrl = '';
$currentAuthPrefix = Configure::read('BcAuthPrefix.'.$currentPrefix);
if(!empty($currentAuthPrefix['loginAction'])) {
	$loginUrl = preg_replace('/^\//', '', $currentAuthPrefix['loginAction']);
}
if(!empty($currentAuthPrefix['name']) && $currentPrefix != 'front') {
	$authName = $currentAuthPrefix['name'];
} elseif(isset($this->BcBaser->siteConfig['name'])) {
	$authName = $this->BcBaser->siteConfig['name'];
} else {
	$authName = '';
}
$userController = Inflector::tableize($this->Session->read(AuthComponent::$sessionKey . '.userModel'));
?>
<script type="text/javascript">
$(function(){
	$('#UserMenu').fixedMenu();
	$('#SystemMenu h2').click(function(){
		if($(this).next().css('display')=='none') {
			$(this).next().slideDown(200);
		} else {
			$(this).next().slideUp(200);
		}
	});
	$('#SystemMenu ul:first').show();
	$("#UserMenu ul li div ul li").each(function(){
		if(!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
			$(this).remove();
		}
	});
	$("#UserMenu ul li div ul").each(function(){
		if(!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
			$(this).prev().remove();
			$(this).remove();
		}
	});
});
</script>

<div id="ToolBar">
	<div id="ToolbarInner" class="clearfix">
		<div id="ToolMenu">
			<ul>
<?php if($this->name == 'Installations'): ?>
				<li><?php $this->BcBaser->link('インストールマニュアル', 'http://basercms.net/manuals/introductions/4.html', array('target' => '_blank')) ?></li>
<?php elseif(Configure::read('BcRequest.isUpdater')): ?>
				<li><?php $this->BcBaser->link('アップデートマニュアル', 'http://basercms.net/manuals/introductions/8.html', array('target' => '_blank')) ?></li>
<?php elseif(!empty($this->request->params['admin']) || $authPrefix == $currentPrefix || ('/'.$this->request->url) == $loginUrl): ?>	
				<li><?php $this->BcBaser->link($this->BcBaser->siteConfig['name'], '/') ?></li>	
<?php else: ?>
	<?php if($authPrefix == 'admin'): ?>
				<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_logo.png', array('alt' => 'baserCMS管理システム', 'class' => 'btn')), array('plugin' => null, 'admin' => true, 'controller' => 'dashboard', 'action' => 'index'), array('title' => 'baserCMS管理システム')) ?></li>
	<?php else: ?>
				<li><?php $this->BcBaser->link($authName, Configure::read('BcAuthPrefix.'.$currentPrefix.'.loginRedirect'), array('title' => $authName)) ?></li>
	<?php endif ?>
<?php endif ?>
<?php if($this->BcBaser->existsEditLink()): ?>
				<li><?php $this->BcBaser->editLink() ?></li>
<?php endif ?>
<?php if($this->BcBaser->existsPublishLink()): ?>
				<li><?php $this->BcBaser->publishLink() ?></li>
<?php endif ?>
<?php if(!$loginUrl || $this->request->url != $loginUrl): ?>
	<?php if(Configure::read('debug') == -1): ?>
				<li>&nbsp;&nbsp;<span id="DebugMode" title="インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。">インストールモード</span>&nbsp;&nbsp;</li>
	<?php elseif(Configure::read('debug') > 0): ?>
				<li>&nbsp;&nbsp;<span id="DebugMode" title="デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。">デバッグモード<?php echo mb_convert_kana(Configure::read('debug'), 'N') ?></span>&nbsp;&nbsp;</li>
	<?php endif; ?>
<?php endif ?>
			</ul>
		</div>
		<div id="UserMenu">
			<ul class="clearfix">
				<li>
<?php if(!empty($user)): ?>
					<?php $this->BcBaser->link($this->BcBaser->getUserName($user) . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'btn')), 'javascript:void(0)', array('class' => 'title')) ?>
					<ul>
	<?php if($this->Session->check('AuthAgent')): ?>
						<li><?php $this->BcBaser->link('元のユーザーに戻る', array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'back_agent')) ?></li>
	<?php endif ?>
	<?php if($authPrefix == 'admin'): ?>
		<?php if($authPrefix == 'front'): ?>
						<li><?php $this->BcBaser->link('アカウント設定', array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id'])) ?></li>
		<?php else: ?>
						<li><?php $this->BcBaser->link('アカウント設定', array($authPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id'])) ?></li>
		<?php endif ?>
	<?php endif ?>
		<?php if($authPrefix == 'front'): ?>
						<li><?php $this->BcBaser->link('ログアウト', array('plugin' => null, 'controller' => $userController, 'action' => 'logout')) ?></li>
		<?php else: ?>
						<li><?php $this->BcBaser->link('ログアウト', array($authPrefix => true, 'plugin' => null, 'controller' => $userController, 'action' => 'logout')) ?></li>
		<?php endif ?>
					</ul>
<?php elseif($this->name != 'Installations' && $this->request->url != $loginUrl && !Configure::read('BcRequest.isUpdater')): ?>
					<?php $this->BcBaser->link('ログインしていません '.$this->BcBaser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'btn')), 'javascript:void(0)', array('class' => 'title')) ?>
					<ul>
	<?php if($currentPrefix == 'front'): ?>
						<li><?php $this->BcBaser->link('ログイン', array('plugin' => null, 'controller' => 'users', 'action' => 'login')) ?></li>
	<?php else: ?>
						<li><?php $this->BcBaser->link('ログイン', array($currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login')) ?></li>
	<?php endif ?>
					</ul>
<?php endif ?>
				</li>
<?php if(!empty($user) && $authPrefix == 'admin'): ?>
				<li>
					<?php $this->BcBaser->link('システムナビ'.' '.$this->BcBaser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'btn')), 'javascript:void(0)', array('class' => 'title')) ?>
					<div id="SystemMenu"><div>
	<?php $adminSitemap = Configure::read('BcApp.adminNavi') ?>
	<?php foreach($adminSitemap as $key => $package): ?>
		<?php if(empty($package['name'])): ?>
			<?php $package['name'] = $key ?>
		<?php endif ?>
		<h2><?php echo $package['name'] ?></h2>
		<?php if(!empty($package['contents'])): ?>
						<ul class="clearfix">
			<?php foreach($package['contents'] as $contents): ?>
							<li><?php $this->BcBaser->link($contents['name'], $contents['url'], array('title' => $contents['name'])) ?></li>
			<?php endforeach ?>
						</ul>
		<?php endif ?>
	<?php endforeach ?>
					</div></div>
				</li>
<?php endif ?>
			</ul>
		</div>
	</div>
</div>
