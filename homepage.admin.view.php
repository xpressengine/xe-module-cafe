<?php
    /**
     * @class  homepageAdminView
     * @author NHN (developers@xpressengine.com)
     * @brief  homepage 모듈의 admin view class
     **/

    class homepageAdminView extends homepage {

        var $site_module_info = null;
        var $site_srl = 0;
        var $homepage_info = null;

        function init() {
            if(strpos($this->act,'HomepageAdminSite')!==false) {
				$oModuleModel = &getModel('module');
                // 현재 접속 권한 체크하여 사이트 관리자가 아니면 접근 금지
                $logged_info = Context::get('logged_info');
                if(!Context::get('is_logged') || !$oModuleModel->isSiteAdmin($logged_info)) return $this->stop('msg_not_permitted');

                // site_module_info값으로 홈페이지의 정보를 구함
                $this->site_module_info = Context::get('site_module_info');
                $this->site_srl = $this->site_module_info->site_srl;
                if(!$this->site_srl) return $this->stop('msg_invalid_request');

                // 홈페이지 정보를 추출하여 세팅
                $oHomepageModel = &getModel('homepage');
                $this->homepage_info = $oHomepageModel->getHomepageInfo($this->site_srl);
                Context::set('homepage_info', $this->homepage_info);

                // 모듈 번호가 있으면 해동 모듈의 정보를 구해와서 세팅
                $module_srl = Context::get('module_srl');
                if($module_srl) {
                    $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                    if(!$module_info || $module_info->site_srl != $this->site_srl) return new Object(-1,'msg_invalid_request');
                    $this->module_info = $module_info;
                    Context::set('module_info', $module_info);
                }
			}
			$template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);
        }

        function dispHomepageAdminContent() {
            $oHomepageAdminModel = &getAdminModel('homepage');

            // 생성된 카페 목록을 구함
            $args->page = Context::get('page');
			$args->search_target = Context::get('search_target');
			$args->search_keyword = Context::get('search_keyword');

            $output = $oHomepageAdminModel->getHomepageList($args);

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('homepage_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('index');
        }

        function dispHomepageAdminInsert() {
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_homepage.xml');
            $this->setTemplateFile('insert');

        }

        function dispHomepageAdminManage() {
            $oLayoutModel = &getModel('layout');
            $oHomepageModel = &getModel('homepage');
            $oModuleModel = &getModel('module');
            $oMemberModel = &getModel('member');

            // cafe 전체 설정을 구함
            $homepage_config = $oHomepageModel->getConfig();
            Context::set('homepage_config', $homepage_config);

            // 레이아웃 목록을 구함
            $layout_list = $oLayoutModel->getDownloadedLayoutList();
            Context::set('layout_list', $layout_list);

			// for mobile layout
            $mlayout_list = $oLayoutModel->getDownloadedLayoutList('M');
			Context::set('mlayout_list', $mlayout_list);

            // 카페 허브의 레이아웃을 구함
            $layout_list = $oLayoutModel->getLayoutList();
            Context::set('hub_layout_list', $layout_list);

			// for cafe hub's mobile layout 
			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('hub_mlayout_list', $mobile_layout_list);

			// for cafe hub's mobile skin
			$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
			Context::set('hub_mskin_list', $mskin_list);

            // 서비스 모듈을 구함
            $installed_module_list = $oModuleModel->getModulesXmlInfo();
            foreach($installed_module_list as $key => $val) {
                if($val->category != 'service') continue;
                $service_modules[] = $val;
            }
            Context::set('service_modules', $service_modules);

            // 기본 사이트의 그룹 구함
            $groups = $oMemberModel->getGroups(0);
            Context::set('groups', $groups);

            // 카페 메인 스킨 설정 
            Context::set('skins', $oModuleModel->getSkins($this->module_path));

			//메뉴 목록을 가져옴 - 11.08.02
			$oMenuAdminModel = &getAdminModel('menu');
			$menu_list = $oMenuAdminModel->getMenus();
			Context::set('menu_list',$menu_list);

            $this->setTemplateFile('manage');

        }

        function dispHomepageAdminSetup() {
            $oLayoutModel = &getModel('layout');
            $oHomepageAdminModel = &getAdminModel('homepage');
            $oModuleModel = &getModel('module');
            $oHomepageModel = &getModel('homepage');

			$oMemberModel = &getModel('member');
			$member_config = $oMemberModel->getMemberConfig();
			Context::set('member_config', $member_config);
			
            $site_srl = Context::get('site_srl');
            $homepage_info = $oHomepageModel->getHomepageInfo($site_srl);
            Context::set('homepage_info', $homepage_info);

            // cafe 전체 설정을 구함
            $homepage_config = $oHomepageModel->getConfig($site_srl);
            Context::set('homepage_config', $homepage_config);

            // 레이아웃 목록을 구함
            $layout_list = $oLayoutModel->getDownloadedLayoutList();
            Context::set('layout_list', $layout_list);

			// for mobile layout
            $mlayout_list = $oLayoutModel->getDownloadedLayoutList('M');
			Context::set('mlayout_list', $mlayout_list);

            // 서비스 모듈을 구함
            $installed_module_list = $oModuleModel->getModulesXmlInfo();
            foreach($installed_module_list as $key => $val) {
                if($val->category != 'service') continue;
                $service_modules[] = $val;
            }
            Context::set('service_modules', $service_modules);

            $oModuleModel = &getModel('module');
            $admin_list = $oModuleModel->getSiteAdmin($site_srl);
            Context::set('admin_list', $admin_list);

            $this->setTemplateFile('setup');
        }

        function dispHomepageAdminDelete() {
            $site_srl = Context::get('site_srl');
            $oHomepageModel = &getModel('homepage');
            $homepage_info = $oHomepageModel->getHomepageInfo($site_srl);
            Context::set('homepage_info', $homepage_info);

            $oModuleModel = &getModel('module');
            $admin_list = $oModuleModel->getSiteAdmin($site_srl);
            Context::set('admin_list', $admin_list);

            $this->setTemplateFile('delete');
        }

        function dispHomepageAdminSkinSetup() {
            $oModuleAdminModel = &getAdminModel('module');
            $oHomepageModel = &getModel('homepage');

            $homepage_config = $oHomepageModel->getConfig(0);
            $skin_content = $oModuleAdminModel->getModuleSkinHTML($homepage_config->module_srl);
            Context::set('skin_content', $skin_content);

            $this->setTemplateFile('skin_info');
        }
        function dispHomepageAdminMobileSkinSetup() {
            $oModuleAdminModel = &getAdminModel('module');
            $oHomepageModel = &getModel('homepage');

            $homepage_config = $oHomepageModel->getConfig(0);
            $skin_content = $oModuleAdminModel->getModuleMobileSkinHTML($homepage_config->module_srl);
            Context::set('skin_content', $skin_content);

            $this->setTemplateFile('skin_info');
        }
        /**
         * @brief 홈페이지 기본 관리
         **/
        function dispHomepageAdminSiteManage() {
            $oModuleModel = &getModel('module');
            $oMenuAdminModel = &getAdminModel('menu');
            $oLayoutModel = &getModel('layout');
            $oHomepageModel = &getModel('homepage');

            $homepage_config = $oHomepageModel->getConfig($this->site_srl);
            Context::set('homepage_config', $homepage_config);

            // 다운로드 되어 있는 레이아웃 목록을 구함
            $layout_list = $oLayoutModel->getDownloadedLayoutList();
            Context::set('layout_list', $layout_list);

            // 레이아웃 정보 가져옴
            $this->selected_layout = $oLayoutModel->getLayout($this->homepage_info->layout_srl);
            Context::set('selected_layout', $this->selected_layout);

            // 메뉴 목록을 가져옴
            $menu_list = $oMenuAdminModel->getMenus();
            Context::set('menu_list', $menu_list);

            if(!Context::get('act')) Context::set('act', 'dispHomepageManage');

            $args->site_srl = $this->site_srl;
            $mid_list = $oModuleModel->getMidList($args);
            Context::set('mid_list', $mid_list);

            $this->setTemplateFile('site_manage');
        }

        /**
         * @brief 홈페이지 회원 그룹 관리
         **/
        function dispHomepageAdminSiteMemberGroupManage() {
            // 멤버모델 객체 생성
            $oMemberModel = &getModel('member');

            // group_srl이 있으면 미리 체크하여 selected_group 세팅
            $group_srl = Context::get('group_srl');
            if($group_srl) {
                $selected_group = $oMemberModel->getGroup($group_srl);
                Context::set('selected_group',$selected_group);
            }

            // group 목록 가져오기
            $group_list = $oMemberModel->getGroups($this->site_srl);
            Context::set('group_list', $group_list);

            $this->setTemplateFile('site_group_list');
        }

        /**
         * @brief 홈페이지 모듈의 회원 관리
         **/
        function dispHomepageAdminSiteMemberManage() {
            $oMemberModel = &getModel('member');
			$oModuleModel = &getModel('module');

            // 회원 그룹을 구함
            $group_list = $oMemberModel->getGroups($this->site_srl);
            if(!$group_list) $group_list = array();
            Context::set('group_list', $group_list);

			// 회원 목록을 구함
            $args->selected_group_srl = Context::get('selected_group_srl');
            if(!isset($group_list[$args->selected_group_srl])) {
                $args->selected_group_srl = implode(',',array_keys($group_list));
            }

			//로그인 방식 확인
			$config = $oModuleModel->getModuleConfig('member');
			$identifier = ($config->identifier) ? $config->identifier : 'email_address';
			Context::set('identifier',$identifier);

            $search_target = trim(Context::get('search_target'));
            $search_keyword = trim(Context::get('search_keyword'));
            if($search_target && $search_keyword) {
                switch($search_target) {
                    case 'user_id' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_user_id = $search_keyword;
                        break;
                    case 'user_name' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_user_name = $search_keyword;
                        break;
                    case 'nick_name' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_nick_name = $search_keyword;
                        break;
                    case 'email_address' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_email_address = $search_keyword;
                        break;
                    case 'regdate' :
                            $args->s_regdate = ereg_replace("[^0-9]","",$search_keyword);
                        break;
                    case 'regdate_more' :
                            $args->s_regdate_more = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                    case 'regdate_less' :
                            $args->s_regdate_less = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                    case 'last_login' :
                            $args->s_last_login = $search_keyword;
                        break;
                    case 'last_login_more' :
                            $args->s_last_login_more = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                    case 'last_login_less' :
                            $args->s_last_login_less = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                    case 'extra_vars' :
                            $args->s_extra_vars = ereg_replace("[^0-9]","",$search_keyword);
                        break;
                }
            }

		    $query_id = 'member.getMemberListWithinGroup';
		    $args->sort_index = "member.member_srl";
		    $args->sort_order = "desc";
            $args->page = Context::get('page');
            $args->list_count = 40;
            $args->page_count = 5;
            $output = executeQuery($query_id, $args);

            $members = array();
            if(count($output->data)) {
                foreach($output->data as $key=>$val) {
                    $members[] = $val->member_srl;
                }
            }

            $members_groups = $oMemberModel->getMembersGroups($members, $this->site_srl);
            Context::set('members_groups',$members_groups);

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('member_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('site_member_list');
        }

        /**
         * @brief 홈페이지 상단 메뉴 관리
         **/
        function dispHomepageAdminSiteTopMenu() {
            $oMemberModel = &getModel('member');
            $oMenuModel = &getAdminModel('menu');
            $oModuleModel = &getModel('module');
            $oLayoutModel = &getModel('layout');
            $oHomepageModel = &getModel('homepage');

            // 메뉴 정보 가져오기
            $menu_srl = $this->homepage_info->first_menu_srl;

            $menu_info = $oMenuModel->getMenu($menu_srl);
            Context::set('menu_info', $menu_info);


            $selected_layout = $oLayoutModel->getLayout($this->homepage_info->layout_srl);

            $_menu_info = get_object_vars($selected_layout->menu);
            $menu = array_shift($_menu_info);
            Context::set('menu_max_depth', $menu->maxdepth);

            $this->setTemplateFile('site_menu_manage');
        }

        /**
         * @brief 애드온/ 컴포넌트 설정
         **/
        function dispHomepageAdminSiteComponent() {
            // 애드온 목록을 가져옴
            $oAddonModel = &getAdminModel('addon');
            $addon_list = $oAddonModel->getAddonList($this->site_srl);
            Context::set('addon_list', $addon_list);
			Context::set('addon_count',count($addon_list));
			
            // 에디터 컴포넌트 목록을 가져옴
            $oEditorModel = &getModel('editor');
			 $component_list =  $oEditorModel->getComponentList(false, $this->site_srl);
            Context::set('component_list',$component_list);
            // 표시
            $this->setTemplateFile('site_addition_config');
        }
        /**
         * @brief 접속 통계
         **/
        function dispHomepageAdminSiteCounter() {
            // 정해진 일자가 없으면 오늘자로 설정
            $selected_date = Context::get('selected_date');
            if(!$selected_date) $selected_date = date("Ymd");
            Context::set('selected_date', $selected_date);

            // counter model 객체 생성
            $oCounterModel = &getModel('counter');

            // 전체 카운터 및 지정된 일자의 현황 가져오기
            $status = $oCounterModel->getStatus(array(0,$selected_date),$this->site_srl);
            Context::set('total_counter', $status[0]);
            Context::set('selected_day_counter', $status[$selected_date]);

            // 시간, 일, 월, 년도별로 데이터 가져오기
            $type = Context::get('type');
            if(!$type) {
                $type = 'day';
                Context::set('type',$type);
            }
            $detail_status = $oCounterModel->getHourlyStatus($type, $selected_date, $this->site_srl);
            Context::set('detail_status', $detail_status);
            
            // 표시
            $this->setTemplateFile('site_status');
        }

        /**
         * @brief 홈페이지 모듈 목록
         **/
        function dispHomepageAdminSiteMidSetup() {
            // 현재 site_srl 에 등록된 것들을 가져오기 
            $args->site_srl = $this->site_srl;
            $oModuleModel = &getModel('module');
            $mid_list = $oModuleModel->getMidList($args);
            $installed_module_list = $oModuleModel->getModulesXmlInfo();
            foreach($installed_module_list as $key => $val) {
                if($val->category != 'service') continue;
                $service_modules[$val->module] = $val;
            }

            if(count($mid_list)) {
                foreach($mid_list as $key => $val) {
                    $mid_list[$key]->setup_index_act = $service_modules[$val->module]->setup_index_act;
                }
            }
            Context::set('mid_list', $mid_list);

            $this->setTemplateFile('site_mid_list');
        }


    }

?>
