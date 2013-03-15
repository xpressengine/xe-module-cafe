<?php
    /**
     * @class  homepageSmartphone
     * @author NHN (developers@xpressengine.com)
     * @brief  homepage 모듈의 SmartPhone class
     **/

    class homepageSPhone extends homepage {

        function procSmartPhone(&$oSmartPhone) {
            $oTemplate = new TemplateHandler();
            $content = $oTemplate->compile($this->module_path.'tpl', 'smartphone');
            $oSmartPhone->setContent($content);
        }
    }
?>
