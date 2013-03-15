<?php
    /**
     * @class  homepageAdminModel
     * @author NHN (developers@xpressengine.com)
     * @brief  homepage 모듈의 admin model class
     **/

    class homepageAdminModel extends homepage {

        function init() {
        }

		function getHomepageList($args)
		{
			if(!$args->page) $args->page = 1;
			$this->_setSearchOption($args);
			$output = executeQueryArray('homepage.getHomepageList', $args);
			return $output;
		}

		function _setSearchOption(&$args)
		{
			switch($args->search_target)
			{
				case 'title':
				case 'domain':
					$args->{'s_'.$args->search_target} = $args->search_keyword;
					break;
			}
		}

    }

?>
