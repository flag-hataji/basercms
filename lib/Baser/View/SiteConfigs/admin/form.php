<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] サイト設定 フォーム
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
<script type="text/javascript">
$(window).load(function() {
	$("#SiteConfigFormalName").focus();
});
$(function(){
	var theme = $("#SiteConfigTheme").val();
	<?php if($safeModeOn): ?>
	var safeModeOn = 1;
	<?php else: ?>
	var safeModeOn = 0;
	<?php endif ?>
	<?php if($this->BcForm->value('SiteConfig.smart_url')): ?>
	var smartUrl = 1;
	<?php else: ?>
	var smartUrl = 0;
	<?php endif ?>
	var smartUrlAlert = 'スマートURLの設定を変更されていますが、ヘルプメッセージは読まれましたか？\nサーバー環境によっては、設定変更後、.htaccessファイルを手動で調整しないとアクセスできない場合もありますのでご注意ください。';
	var safemodeAlert = '機能制限のセーフモードで動作しています。テーマの切り替えを行う場合、あらかじめ切り替え対象のテーマ内に、データベースに登録されているページカテゴリ用のフォルダを作成しておき、書込権限を与えておく必要があります。\n'+
						'ページカテゴリ用のフォルダが存在しない状態でテーマの切り替えを実行すると、対象ページカテゴリ内のWebページは正常に表示できなくなりますのでご注意ください。';
	$("#BtnSubmit").click(function(){
		var result = true;
		if(smartUrl != $("#SiteConfigSmartUrl").val()) {
			if(!confirm(smartUrlAlert)){
				result = false;
			}
		}
		if(safeModeOn && (theme != $("#SiteConfigTheme").val())) {
			if(!confirm(safemodeAlert)) {
				result = false;
			}
		}
		return result;
	});
	
	$("#SiteConfigMobile").click(function(){
		if($("#SiteConfigMobile").attr('checked')) {
			$("#SpanLinkedPagesMobile").show();
			$("#SpanRootLayoutTemplateMobile").show();
			$("#SpanRootContentTemplateMobile").show();			
		} else {
			$("#SpanLinkedPagesMobile").hide();
			$("#SpanRootLayoutTemplateMobile").hide();
			$("#SpanRootContentTemplateMobile").hide();
			$('#SiteConfigLinkedPagesMobile0').attr('checked', 'checked'); 
		}
	});
	$("#SiteConfigSmartphone").click(function(){
		if($("#SiteConfigSmartphone").attr('checked')) {
			$("#SpanLinkedPagesSmartphone").show();
			$("#SpanRootLayoutTemplateSmartphone").show();
			$("#SpanRootContentTemplateSmartphone").show();
		} else {
			$("#SpanLinkedPagesSmartphone").hide();
			$("#SpanRootLayoutTemplateSmartphone").hide();
			$("#SpanRootContentTemplateSmartphone").hide();
			$('#SiteConfigLinkedPagesSmartphone0').attr('checked', 'checked'); 
		}
	});

	$('input[name="data[SiteConfig][editor]"]').click(siteConfigEditorClickHandler);
	
	if(!$("#SiteConfigMobile").attr('checked')) {
		$("#SpanLinkedPagesMobile").hide();
		$("#SpanRootLayoutTemplateMobile").hide();
		$("#SpanRootContentTemplateMobile").hide();
	}
	if(!$("#SiteConfigSmartphone").attr('checked')) {
		$("#SpanLinkedPagesSmartphone").hide();
		$("#SpanRootLayoutTemplateSmartphone").hide();
		$("#SpanRootContentTemplateSmartphone").hide();
	}
	
	siteConfigEditorClickHandler();
	
	function siteConfigEditorClickHandler() {
		if($('input[name="data[SiteConfig][editor]"]:checked').val() === 'BcCkeditor') {
			$(".ckeditor-option").show();
		} else {
			$(".ckeditor-option").hide();
		}
	}
	
});
</script>


<h2>基本項目</h2>


<?php echo $this->BcForm->create('SiteConfig',array('action'=>'form')) ?>
<?php echo $this->BcForm->hidden('SiteConfig.id') ?>

<table cellpadding="0" cellspacing="0" class="form-table section">
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.formal_name', 'WEBサイト名') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.formal_name', array('type' => 'text', 'size' => 55,'maxlength' => 255, 'class' => 'full-width')) ?>
			<?php echo $this->Html->image('admin/icn_help.png',array('id' => 'helpFormalName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.formal_name') ?>
			<div id="helptextFormalName" class="helptext">
				<ul>
					<li>正式なWEBサイト名を指定します。</li>
					<li>メールの送信元等で利用します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.name', 'WEBサイトタイトル') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.name', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width')) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>Webサイトの基本タイトルとして利用されます。（タイトルタグに影響します）</li>
					<li>テンプレートで利用する場合は、<br />
						&lt;?php $this->BcBaser->title() ?&gt; で出力します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.keyword', 'サイト基本キーワード') ?></th>
		<td class="col-input"><?php echo $this->BcForm->input('SiteConfig.keyword', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width')) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpKeyword', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.keyword') ?>
			<div id="helptextKeyword" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $this->BcBaser->keywords() ?&gt; で出力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.description', 'サイト基本説明文') ?></th>
		<td class="col-input"><?php echo $this->BcForm->input('SiteConfig.description', array('type' => 'textarea', 'cols' => 36, 'rows' => 5, 'counter' => true)) ?>
			<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpDescription', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.description') ?>
			<div id="helptextDescription" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $this->BcBaser->description() ?&gt; で出力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.email', '管理者メールアドレス') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.email', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $this->BcForm->error('SiteConfig.email') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_list_num', '管理システムの<br />初期一覧件数') ?>&nbsp;<span class="required">*</span></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.admin_list_num', array('type' => 'select', 'options' => array(
				10	=> '10件',
				20	=> '20件',
				50	=> '50件',
				100 => '100件'
			))) ?>
			<?php echo $this->BcForm->error('SiteConfig.admin_list_num') ?>
		</td>
	</tr>
</table>

<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>

<div id ="formOptionBody" class="slide-body section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.login_credit', 'ログインページのクレジット表示') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.login_credit', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">ログインページに表示されているクレジット表示を利用するかどうか設定します。</div>
				<?php echo $this->BcForm->error('SiteConfig.login_credit') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.site_url', 'WebサイトURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.site_url', array_merge(array('type' => 'text', 'size' => 35, 'maxlength' => 255), $disableSettingInstallSetting)) ?><br />
				<?php echo $this->BcForm->input('SiteConfig.ssl_url', array_merge(array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'after' => '<small>[SSL]</small>'), $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSiteUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php if($disableSettingInstallSetting): ?>
				<?php echo $this->BcForm->input('SiteConfig.site_url', array('type' => 'hidden')) ?>
				<?php endif ?>
				<?php echo $this->BcForm->error('SiteConfig.site_url') ?>
				<?php echo $this->BcForm->error('SiteConfig.ssl_url') ?>
				<div id="helptextSiteUrl" class="helptext">baserCMSを設置しているURLを指定します。管理画面等でSSL通信を利用する場合は、SSL通信で利用するURLも指定します。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_ssl', '管理画面SSL設定') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.admin_ssl', array_merge(array('type' => 'radio', 'options' => $this->BcText->booleanDoList('SSL通信を利用'), 'separator' => '　', 'legend'=>false), $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpAdminSsl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('SiteConfig.admin_ssl') ?>
				<div id="helptextAdminSslOn" class="helptext">管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。<br />
					また、SSL用のWebサイトURLの指定が必要です。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.address', 'GoogleMaps住所') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.address', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id'=>'helpAddress', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('SiteConfig.address') ?>
				<div id="helptextAddress" class="helptext">GoogleMapを利用する場合は住所を入力してください。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.google_analytics_id', 'Google Analytics<br />ウェブプロパティID') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.google_analytics_id', array('type' => 'text', 'size' => 35, 'maxlength' => 16)) ?>
				<?php echo $this->Html->image('admin/icn_help.png',array('id' => 'helpGoogleAnalyticsId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('SiteConfig.google_analytics_id') ?>
				<div id="helptextGoogleAnalyticsId" class="helptext">
					<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> 利用時の「UA」から始まる「ウェブプロパティID」を入力します。<br />
					<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> を利用するにはあらかじめ Google アカウントの取得が必要です。<br />
					テンプレートで利用する場合は、 <pre>&lt;?php $this->BcBaser->element('google_analytics') ?&gt;</pre> で出力します。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.widget_area', '標準ウィジェットエリア') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.widget_area', array('type' => 'select', 'options' => $this->BcForm->getControlSource('WidgetArea.id'), 'empty' => 'なし')) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextWidgetArea" class="helptext">
					公開ページ全般で利用するウィジェットエリアを指定します。<br />
					ウィジェットエリアは「<?php $this->BcBaser->link('ウィジェットエリア管理',array('controller'=>'widget_areas','action'=>'index')) ?>」より追加できます。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.maintenance', '公開状態') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.maintenance', array('type' => 'select' , 'options' => array(0 => '公開中', 1 => 'メンテナンス中'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpMaintenance', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextMaintenance" class="helptext">
					公開状態を指定します。<br />
					メンテナンス中の場合に、公開ページを確認するには、管理画面にログインする必要があります。
					ただし、制作・開発モードがデバッグモードに設定されている場合は、メンテナンス中にしていても公開ページが表示されてしまいますので注意が必要です。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.mode', '制作・開発モード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mode', array_merge(array('type' => 'select' , 'options' => $this->BcForm->getControlSource('mode')), $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png',array('id' => 'helpDebug', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextDebug" class="helptext">制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br />
					※ CakePHPのデバッグモードを指します。<br />
					※ インストールモードはbaserCMSを初期化する場合にしか利用しませんので普段は利用しないようにしてください。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head">
				<?php echo $this->BcForm->label('SiteConfig.smart_url', 'スマートURL') ?><br />
			</th>
			<td class="col-input">
				<span>Rewriteモジュール利用可否：<strong>
				<?php if($rewriteInstalled === -1): ?>不明<?php elseif($rewriteInstalled): ?>可<?php else: ?>不可<?php endif ?></strong></span><br />
				<?php echo $this->BcForm->input('SiteConfig.smart_url', array_merge(array('type' => 'select', 'options' => array('0'=>'オフ', '1' => 'オン')), $disableSettingSmartUrl)) ?>
				<?php if($disableSettingSmartUrl): ?>
				<?php echo $this->BcForm->input('SiteConfig.smart_url', array('type' => 'hidden')) ?>
				<?php endif ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmartUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?><br />
				<div id="helptextSmartUrl" class="helptext">
					<p>短くスマートなURLを実現するにはApache Rewriteモジュールと.htaccessの利用許可が必要です。<br />
					・スマートURL無効例：<br />　　http://localhost/index.php/contact/form<br />
					・スマートURL有効例：<br />　　http://localhost/contact/form<br />
					</p><br />
					<p>スマートURLの設定はサーバー環境に深く依存します。<br />
						「オン」に変更した場合、サーバーエラーとなり画面にアクセスできなくなる可能性もありますので注意が必要です。<br />
						スマートURLをオンに切り替えた場合、/ フォルダ、/app/webroot/ フォルダ内の「.htaccess」ファイルに、
						RewriteBase設定を自動的に書き込みますが、うまく動作しない場合、この設定値を環境に合わせて調整する必要があります。<br />
						詳細については、各.htaccessファイルのコメントを確認してください。</p>
				</div>
				<?php if($writableInstall): ?><p class="annotation-text"><small>設定を変更する場合は「？」マークのヘルプを必ずお読みください</small></p><?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.mobile', 'モバイル') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mobile', array_merge(array('type' => 'checkbox', 'label' => '対応する'), $disableSettingInstallSetting)) ?>
				<span id="SpanLinkedPagesMobile">　（固定ページをPCと <?php echo $this->BcForm->input('SiteConfig.linked_pages_mobile', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('連動'))) ?>）</span>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.smartphone', 'スマートフォン') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.smartphone', array_merge(array('type' => 'checkbox', 'label' => '対応する'), $disableSettingInstallSetting)) ?>
				<span id="SpanLinkedPagesSmartphone">　（固定ページをPCと <?php echo $this->BcForm->input('SiteConfig.linked_pages_smartphone', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('連動'))) ?>）</span>
			</td>
		</tr>
<?php if($this->BcBaser->siteConfig['category_permission']): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.mobile', 'ルート管理グループ') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.root_owner_id', array('type' => 'select', 'options' => $userGroups, 'empty' => '指定しない')) ?>
			</td>
		</tr>
<?php endif ?>
	</table>
	
	<h2>固定ページ関連</h2>
	
	<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.root_layout_template', 'ルートレイアウトテンプレート') ?></th>
		<td class="col-input">
			<small>[PC]</small>&nbsp;
			<?php echo $this->BcForm->input('SiteConfig.root_layout_template', array('type' => 'select', 'options' => $this->BcPage->getTemplates())) ?>　
			<span id="SpanRootLayoutTemplateMobile"><small>[携帯]</small>&nbsp;
			<?php echo $this->BcForm->input('SiteConfig.root_layout_template_mobile', array('type' => 'select', 'options' => $this->BcPage->getTemplates('layout', 'mobile'))) ?>　</span>
			<span id="SpanRootLayoutTemplateSmartphone"><small>[スマートフォン]</small>&nbsp;
			<?php echo $this->BcForm->input('SiteConfig.root_layout_template_smartphone', array('type' => 'select', 'options' => $this->BcPage->getTemplates('layout', 'smartphone'))) ?></span>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.root_content_template', 'ルートコンテンツテンプレート') ?></th>
		<td class="col-input">
			<small>[PC]</small>&nbsp;
			<?php echo $this->BcForm->input('SiteConfig.root_content_template', array('type' => 'select', 'options' => $this->BcPage->getTemplates('content'))) ?>　
			<span id="SpanRootContentTemplateMobile"><small>[携帯]</small>&nbsp;
			<?php echo $this->BcForm->input('SiteConfig.root_content_template_mobile', array('type' => 'select', 'options' => $this->BcPage->getTemplates('content', 'mobile'))) ?>　</span>
			<span id="SpanRootContentTemplateSmartphone"><small>[スマートフォン]</small>&nbsp;
			<?php echo $this->BcForm->input('SiteConfig.root_content_template_smartphone', array('type' => 'select', 'options' => $this->BcPage->getTemplates('content', 'smartphone'))) ?></span>
		</td>
	</tr>
	</table>
	
	<h2>エディタ設定関連</h2>
	
	<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_enter_br', 'エディタタイプ') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.editor', array('type' => 'radio', 'options' => Configure::read('BcApp.editors'))) ?>
		</td>
	</tr>
	<tr class="ckeditor-option">
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_enter_br', '改行モード') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.editor_enter_br', array('type' => 'radio', 'options' => array(
				'0' => '改行時に段落を挿入する',
				'1' => '改行時にBRタグを挿入する'
			))) ?>
		</td>
	</tr>
	<tr class="ckeditor-option">
		<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_styles', 'エディタスタイルセット') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('SiteConfig.editor_styles', array('type' => 'textarea', 'cols' => 36, 'rows' => 10)) ?>
			<?php echo $this->Html->image('admin/icn_help.png',array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
			<?php echo $this->BcForm->error('SiteConfig.editor_styles') ?>
			<div id="helptextFormalName" class="helptext">
<p>固定ページなどで利用するエディタのスタイルセットをCSS形式で記述する事ができます。</p>
				<pre>
# タイトル
タグ {
	プロパティ名：プロパティ値
}

 《記述例》
 # 見出し
 h2 {
	font-size:20px;
	color:#333;
 }
</pre>
<p>タグにプロパティを設定しない場合は次のように記述します。</p>
<pre>
# 見出し
h2 {}
</pre>
			</div>
		</td>
	</tr>
	</table>
	
	<h2>メール設定関連</h2>
	
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.mail_encode', 'メール送信文字コード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mail_encode', array('type' => 'select', 'options' => Configure::read('BcEncode.mail'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpEncode', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextEncode" class="helptext">送信メールの文字コードを選択します。<br />受信したメールが文字化けする場合に変更します。</div>
				<?php echo $this->BcForm->error('SiteConfig.mail_encode') ?>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.smtp_host', 'SMTPホスト') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.smtp_host', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_host') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpHost', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpHost" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.smtp_port', 'SMTPポート') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.smtp_port', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_port') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。入力を省略した場合、25番ポートを利用します。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.smtp_user', 'SMTPユーザー') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.smtp_user', array('type' => 'text', 'size'=>35,'maxlength'=>255)) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_user') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpUsername', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpUsername" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.smtp_password', 'SMTPパスワード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.smtp_password', array('type' => 'password', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('SiteConfig.smtp_password') ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSmtpPassword', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpPassword" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
			</td>
		</tr>
	</table>
</div>

<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
</div>

<?php echo $this->BcForm->end() ?>
