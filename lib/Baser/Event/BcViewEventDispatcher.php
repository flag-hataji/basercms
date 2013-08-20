<?php
class BcViewEventDispatcher extends Object implements CakeEventListener {
	
	public function implementedEvents() {
		return array(
			'View.beforeRenderFile' => 'beforeRenderFile',
			'View.afterRenderFile' => 'afterRenderFile',
			'View.beforeRender' => 'beforeRender',
			'View.afterRender' => 'afterRender',
			'View.beforeLayout' => 'beforeLayout',
			'View.afterLayout' => 'afterLayout'
		);
	}
/**
 * beforeRenderFile
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function beforeRenderFile(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent('beforeRenderFile', $event->data);
		}
	}
/**
 * afterRenderFile
 * 
 * @param CakeEvent $event
 * @return array
 */
	public function afterRenderFile(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$currentEvent = $event->subject->dispatchEvent('afterRenderFile', $event->data, array('modParams' => 1));
			if($currentEvent) {
				return $currentEvent->data[1];
			}
		}
		return $event->data[1];
	}
/**
 * beforeRender
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function beforeRender(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent('beforeRender', $event->data);
		}
	}
/**
 * afterRender
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function afterRender(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent('afterRender', $event->data);
		}
	}
/**
 * beforeLayout
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function beforeLayout(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent('beforeLayout', $event->data);
		}
	}
/**
 * afterLayout
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function afterLayout(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent('afterLayout', $event->data);
		}
	}
	
}