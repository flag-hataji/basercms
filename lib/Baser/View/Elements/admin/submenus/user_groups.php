<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザーグループ管理メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php if($this->BcBaser->isAdminUser()): ?>
<tr>
	<th>ユーザーグループ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('一覧を表示する', array('controller' => 'user_groups', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('新規に登録する', array('controller' => 'user_groups', 'action' => 'add')) ?></li>
		</ul>
	</td>
</tr>
<?php endif ?>
