<?php
    /**
     * @class  homepageView
     * @author NHN (developers@xpressengine.com)
     * @brief  homepage 모듈의 view class
     **/

    class homepageView extends homepage {

        var $site_module_info = null;
        var $site_srl = 0;
        var $homepage_info = null;

        /**
         * @brief 초기화 
         **/
        function init() {
			$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			if(!is_dir($template_path)||!$this->module_info->skin) {
				$this->module_info->skin = 'xe_cafe_v2';
				$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			}
			$this->setTemplatePath($template_path);
        }

        /**
         * @brief 카페 메인 출력
         **/
        function dispHomepageIndex() {
            $oHomepageAdminModel = &getAdminModel('homepage');
            $oHomepageModel = &getModel('homepage');
            $oModuleModel = &getModel('module');
            $oDocumentModel = &getModel('document');
            $oCommentModel = &getModel('comment');

            // 카페 목록을 구함
            $cafe_srls = array();
            $page = Context::get('page');
            $output = $oHomepageAdminModel->getHomepageList($page);
            if($output->data && count($output->data)) {
                foreach($output->data as $key => $val) {
                    $banner_src = 'files/attach/cafe_banner/'.$val->site_srl.'.jpg';
                    if(file_exists(_XE_PATH_.$banner_src)) $output->data[$key]->cafe_banner = $banner_src.'?rnd='.filemtime(_XE_PATH_.$banner_src);
                    else $output->data[$key]->cafe_banner = '';

                    $url = getSiteUrl($val->domain,'');
                    if(substr($url,0,1)=='/') $url = substr(Context::getRequestUri(),0,-1).$url;
                    $output->data[$key]->url = $url;
                    $cafe_srls[$val->site_srl] = $key;
                }
            }
            
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('homepage_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            // 카페 생성 권한 세팅
            if($oHomepageModel->isCreationGranted()) Context::set('isEnableCreateCafe', true);

            // 카페의 최신 글 추출
            $output = executeQueryArray('homepage.getNewestDocuments');
            if($output->data) {
                foreach($output->data as $key => $attribute) {
                    $document_srl = $attribute->document_srl;
                    if(!$GLOBALS['XE_DOCUMENT_LIST'][$document_srl]) {
                        unset($oDocument);
                        $oDocument = new documentItem();
                        $oDocument->setAttribute($attribute, false);
                        $GLOBALS['XE_DOCUMENT_LIST'][$document_srl] = $oDocument;
                    }
                    $output->data[$key] = $GLOBALS['XE_DOCUMENT_LIST'][$document_srl];
                }
            }
            Context::set('newest_documents', $output->data);
            
            // 카페의 최신 댓글 추출
            $output = executeQueryArray('homepage.getNewestComments');
            if($output->data) {
                foreach($output->data as $key => $val) {
                    unset($oComment);
                    $oComment = new commentItem(0);
                    $oComment->setAttribute($val);
                    $output->data[$key] = $oComment;
                }
            }
            Context::set('newest_comments', $output->data);

            $logged_info = Context::get('logged_info');
            if($logged_info->member_srl) {
                $myargs->member_srl = $logged_info->member_srl;
                $output = executeQueryArray('homepage.getMyCafes', $myargs);
                Context::set('my_cafes', $output->data);
            }

            $homepage_info = $oModuleModel->getModuleConfig('homepage');

            $this->setTemplateFile('index');
        }

        /**
         * @brief 홈페이지 생성
         **/
        function dispHomepageCreate() {
            $oHomepageModel = &getModel('homepage');
            if(!$oHomepageModel->isCreationGranted()) return new Object(-1,'msg_not_permitted');
            $this->setTemplateFile('create');
        }

		function dispHomepageManage()
		{
			header('location:'.getNotEncodedUrl('act','dispHomepageAdminSiteManage'));
			Context::close();
			exit();
		}
	}
?>
