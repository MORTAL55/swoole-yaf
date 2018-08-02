<?php
/**
 * @name IndexController
 * @author lancelot
 * @desc 默认控制器
 */
class IndexController extends Yaf_Controller_Abstract {

	public function indexAction() {

		//2. fetch model
		//$model = new SampleModel();

		//3. assign
		$this->getView()->assign("name", "MORTAL");

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        //return TRUE;
	}
}
