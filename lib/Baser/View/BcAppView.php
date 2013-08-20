<?php
/* SVN FILE: $Id$ */
/**
 * view 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * view 拡張クラス
 *
 * @package			baser.views
 */
class BcAppView extends View {
/**
 * List of variables to collect from the associated controller
 *
 * @var array
 * @access protected
 */
	protected $_passedVars = array(
		'viewVars', 'autoLayout', 'ext', 'helpers', 'view', 'layout', 'name', 'theme',
		'layoutPath', 'viewPath', 'request', 'plugin', 'passedArgs', 'cacheAction', 
		'subDir', 'adminTheme', 'pageTitle'
	);
/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin Optional plugin name to scan for view files.
 * @param boolean $cached Set to true to force a refresh of view paths.
 * @return array paths
 */
	protected function _paths($plugin = null, $cached = true) {

		if ($plugin === null && $cached === true && !empty($this->_paths)) {
			return $this->_paths;
		}
		$paths = array();
		$viewPaths = App::path('View');
		$corePaths = array_merge(App::core('View'), App::core('Console/Templates/skel/View'));

		if (!empty($plugin)) {
			$count = count($viewPaths);
			for ($i = 0; $i < $count; $i++) {
				if (!in_array($viewPaths[$i], $corePaths)) {
					$paths[] = $viewPaths[$i] . 'Plugin' . DS . $plugin . DS;
				}
			}
			$paths = array_merge($paths, App::path('View', $plugin));
		}

		$paths = array_unique(array_merge($paths, $viewPaths));
		
		// CUSTOMIZE ADD 2013/08/17 ryuring
		// >>>
		$webroot = Configure::read('App.www_root');
		if (!empty($this->adminTheme)) {
			$adminThemePaths = array();
			foreach ($paths as $path) {
				if (strpos($path, DS . 'Plugin' . DS) === false) {
					if ($plugin) {
						$adminThemePaths[] = $path . 'Themed' . DS . $this->adminTheme . DS . 'Plugin' . DS . $plugin . DS;
					}
					$adminThemePaths[] = $path . 'Themed' . DS . $this->adminTheme . DS;
				}
			}
			$adminThemePaths = array_merge(array($webroot . 'theme' . DS . $this->adminTheme . DS), $adminThemePaths);
		}
		// <<<
		
		if (!empty($this->theme)) {
			$themePaths = array();
			foreach ($paths as $path) {
				if (strpos($path, DS . 'Plugin' . DS) === false) {
					if ($plugin) {
						$themePaths[] = $path . 'Themed' . DS . $this->theme . DS . 'Plugin' . DS . $plugin . DS;
					}
					$themePaths[] = $path . 'Themed' . DS . $this->theme . DS;
				}
			}
			
			// CUSTOMIZE MODIFY 2013/08/17 ryuring
			// >>>
			//$paths = array_merge($themePaths, $paths);
			// --
			$themePaths = array_merge(array($webroot . 'theme' . DS . $this->theme . DS), $themePaths);
			$paths = array_merge($themePaths, $adminThemePaths, $paths);
			// <<<
			
		}
		$paths = array_merge($paths, $corePaths);
		if ($plugin !== null) {
			return $paths;
		}
		return $this->_paths = $paths;
		
	}
/**
 * Returns filename of given action's template file (.ctp) as a string.
 * CamelCased action names will be under_scored! This means that you can have
 * LongActionNames that refer to long_action_names.ctp views.
 *
 * @param string $name Controller action to find template filename for
 * @return string Template filename
 * @throws MissingViewException when a view file could not be found.
 */
	protected function _getViewFileName($name = null) {
		
		// CUSTOMIZE ADD 2012/04/11 ryuring
		// プレフィックスが設定されている場合は、プレフィックスを除外する
		// >>>
		if(!$name) {
			$prefix = '';
			if(isset($this->request->params['prefix'])) {
				$prefix = $this->request->params['prefix'];
			}
			if($prefix && preg_match('/^'.$prefix.'_/', $this->request->action)) {
				$name = str_replace($prefix.'_','',$this->request->action);
			} elseif(preg_match('/^admin_/', $this->request->action)) {
				// プレフィックスをadminとしてすり替え
				$name = str_replace('admin_','',$this->request->action);
			}
		}
		if($this->name == 'CakeError' && $this->viewPath == 'Errors') {
			// CakeErrorの場合はサブフォルダを除外
			$subDir = $this->subDir;
			$this->subDir = null;
			$fileName = parent::_getViewFileName($name);
			$this->subDir = $subDir;
			return $fileName;
		}
		// <<<
		
		$subDir = null;

		if (!is_null($this->subDir)) {
			$subDir = $this->subDir . DS;
		}

		// CUSTOMIZE ADD 2012/10/11 ryuring
		// モバイルの固定ページの場合、PCの固定ページと連動する場合は、サブフォルダを空に設定
		// >>>
		$siteConfig = $this->getVar('siteConfig');
		if($this->name == 'Pages' && preg_match('/(.+)_display$/', $this->request->action, $maches)) {
			$Page = ClassRegistry::getObject('Page');
			$url = '/'.implode('/', $this->params['pass']);
			if($Page->isLinked($maches[1], $url)) { 
				$subDir = '';
			}
		}
		// <<<
		
		if ($name === null) {
			$name = $this->view;
		}
		
		$name = str_replace('/', DS, $name);
		list($plugin, $name) = $this->pluginSplit($name);

		if (strpos($name, DS) === false && $name[0] !== '.') {
			$name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
		} elseif (strpos($name, DS) !== false) {
			if ($name[0] === DS || $name[1] === ':') {
				if (is_file($name)) {
					return $name;
				}
				$name = trim($name, DS);
			} elseif ($name[0] === '.') {
				$name = substr($name, 3);
			} elseif (!$plugin || $this->viewPath !== $this->name) {
				$name = $this->viewPath . DS . $subDir . $name;
			}
		}
		$paths = $this->_paths($plugin);
		$exts = $this->_getExtensions();
		
		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 拡張子優先順位よりもパスの優先順位を優先する仕様に変更
		// @deprecated .php への移行を推奨
		// >>>
		/*foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $name . $ext)) {
					return $path . $name . $ext;
				}
			}
		}*/
		// ---
		foreach ($paths as $path) {
			foreach ($exts as $ext) {
				if (file_exists($path . $name . $ext)) {
					if($ext == '.ctp') {
						trigger_error('ビューテンプレートの拡張子 .ctp は非推奨です。.php を利用してください。<br />' . $path . $name . $ext, E_USER_WARNING);
					}
					return $path . $name . $ext;
				}
			}
		}
		// <<<
		
		$defaultPath = $paths[0];

		if ($this->plugin) {
			$pluginPaths = App::path('plugins');
			foreach ($paths as $path) {
				if (strpos($path, $pluginPaths[0]) === 0) {
					$defaultPath = $path;
					break;
				}
			}
		}
		throw new MissingViewException(array('file' => $defaultPath . $name . $this->ext));
		
	}

/**
 * Returns layout filename for this template as a string.
 *
 * @param string $name The name of the layout to find.
 * @return string Filename for layout file (.ctp).
 * @throws MissingLayoutException when a layout cannot be located
 */
	protected function _getLayoutFileName($name = null) {
		
		if ($name === null) {
			$name = $this->layout;
		}
		$subDir = null;

		if (!is_null($this->layoutPath)) {
			$subDir = $this->layoutPath . DS;
		}
		list($plugin, $name) = $this->pluginSplit($name);
		$paths = $this->_paths($plugin);
		$file = 'Layouts' . DS . $subDir . $name;

		$exts = $this->_getExtensions();
		
		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 拡張子優先順位よりもパスの優先順位を優先する仕様に変更
		// @deprecated .php への移行を推奨
		// >>>
		/*foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $file . $ext)) {
					return $path . $file . $ext;
				}
			}
		}*/
		// ---
		foreach ($paths as $path) {
			foreach ($exts as $ext) {
				if (file_exists($path . $file . $ext)) {
					if($ext == '.ctp') {
						trigger_error('レイアウトテンプレートの拡張子 .ctp は非推奨です。.php を利用してください。<br />'.$path . $file . $ext, E_USER_WARNING);
					}
					return $path . $file . $ext;
				}
			}
		}
		// <<<
		
		throw new MissingLayoutException(array('file' => $paths[0] . $file . $this->ext));
		
	}
/**
 * イベントを発火
 * 
 * @param string $name
 * @param array $params
 * @return mixed
 */
	public function dispatchEvent($name, $params = array(), $options = array()) {
		
		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch('View', $name, $this, $params);
		
	}
	
}
?>