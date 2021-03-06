<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] サイト基本設定　ヘルプ
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
?>


<p>WEBサイトの基本設定を行います。<br />
	各項目のヘルプメッセージをご確認ください。</p>
<ul>
	<li>画面上部ユーティリティの「サーバーキャッシュ削除」で、サーバー上のキャッシュファイルを全て削除する事ができます。
		また、画面下部オプションの「制作・開発モード」をデバッグモードに切り替えると、サーバーキャッシュを削除した上で、新たに生成しないようにする事ができます。</li>
	<li>画面上部ユーティリティの「データメンテナンス」から、データベースに格納されたデータのバックアップを行う事ができます。</li>
	<li>編集不可となっている項目が存在する場合、<?php echo $baseUrl ?>app/Config/install.php に書き込み権限がありません。書き込み権限を与えるか、該当ファイルを直接編集してください。</li>
	<li>管理システムよりスマートURLの切り替えを行うには、上記 install.php の他、下記二つのファイルにも書き込み権限が必要です。変更後は、必ず権限を元に戻してください。<br />
<pre><?php echo $baseUrl ?>.htaccess<?php if(!$writableHtaccess): ?>　<small>[現在書き込み権限がありません]</small><?php endif ?> 
<?php echo $baseUrl ?>app/webroot/.htaccess<?php if(!$writableHtaccess2): ?>　<small>[現在書き込み権限がありません]</small><?php endif ?></pre>
	</li>
</ul>