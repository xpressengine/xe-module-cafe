<?php
    /**
     * @class homepage 
     * @author NHN (developers@xpressengine.com)
     * @brief  homepage package
     **/

    class homepage extends ModuleObject {

        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            $oModuleController = &getController('module');

            $oModuleController->insertTrigger('display', 'homepage', 'controller', 'triggerMemberMenu', 'before');
			$oModuleController->insertTrigger('moduleHandler.proc', 'homepage', 'controller', 'triggerApplyLayout', 'after');
			$oModuleController->insertTrigger('moduleHandler.init', 'homepage', 'controller', 'triggerApplyMLayout', 'after');

            return new Object();
        }


        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');
            $oDB = &DB::getInstance();

            // 2009. 02. 11 가상 사이트의 로그인 정보 영역에 관리 기능이 추가되어 표시되도록 트리거 등록
            if(!$oModuleModel->getTrigger('display', 'homepage', 'controller', 'triggerMemberMenu', 'before')) return true;

            // 2009. 04. 23 카페의 설명
            if(!$oDB->isColumnExists("homepages","description")) return true;

            if(!$oModuleModel->getTrigger('moduleHandler.proc', 'homepage', 'controller', 'triggerApplyLayout', 'after')) return true;

			//2012. 08. 30 모바일 레이아웃 지원
			if(!$oDB->isColumnExists("homepages","mlayout_srl")) return true;
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'homepage', 'controller', 'triggerApplyMLayout', 'after')) return true;

            return false;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');
            $oDB = &DB::getInstance();

            // 2009. 02. 11 가상 사이트의 로그인 정보 영역에 관리 기능이 추가되어 표시되도록 트리거 등록
            if(!$oModuleModel->getTrigger('display', 'homepage', 'controller', 'triggerMemberMenu', 'before')) 
                $oModuleController->insertTrigger('display', 'homepage', 'controller', 'triggerMemberMenu', 'before');

            // 2009. 04. 23 카페의 설명
            if(!$oDB->isColumnExists("homepages","description")) 
                $oDB->addColumn("homepages","description","text");

            if(!$oModuleModel->getTrigger('moduleHandler.proc', 'homepage', 'controller', 'triggerApplyLayout', 'after') )
                $oModuleController->insertTrigger('moduleHandler.proc', 'homepage', 'controller', 'triggerApplyLayout', 'after');

			//2012. 08. 30 모바일 레이아웃
			if(!$oDB->isColumnExists("homepages","mlayout_srl")) {
				$oDB->addColumn('homepages',"mlayout_srl","number",11,0);
			}
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'homepage', 'controller', 'triggerApplyMLayout', 'after') )
                $oModuleController->insertTrigger('moduleHandler.init', 'homepage', 'controller', 'triggerApplyMLayout', 'after');
			

            return new Object(0, 'success_updated');
        }

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
        }
    }
?>
