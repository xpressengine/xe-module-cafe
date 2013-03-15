function doHomepageInsertAdmin() {
    var fo_obj = xGetElementById("cafeFo");
    var sel_obj = fo_obj.admin_list;
    var admin_id = fo_obj.admin_id.value;
    if(!admin_id) return;

    var opt = new Option(admin_id,admin_id,true,true);
    sel_obj.options[sel_obj.options.length] = opt;

    fo_obj.admin_id.value = '';
    sel_obj.size = sel_obj.options.length;
    sel_obj.selectedIndex = -1;
}

function doHomepageDeleteAdmin() {
    var fo_obj = xGetElementById("cafeFo");
    var sel_obj = fo_obj.admin_list;
    sel_obj.remove(sel_obj.selectedIndex);

    sel_obj.size = sel_obj.options.length;
    sel_obj.selectedIndex = -1;
}

function doUpdateHomepage(fo_obj, func) {
    var sel_obj = fo_obj.admin_list;
    var arr = new Array();
    for(var i=0;i<sel_obj.options.length;i++) {
        arr[arr.length] = sel_obj.options[i].value;
    }
    fo_obj.homepage_admin.value = arr.join(',');
    procFilter(fo_obj, func);
    return false;

}

function completeUpdateHomepage(ret_obj) {
    alert(ret_obj['message']);
    location.reload();
}

function completeDeleteHomepage(ret_obj) {
    alert(ret_obj['message']);
    location.href = current_url.setQuery('act','dispHomepageAdminContent').setQuery('site_srl','');
}



function nodeToggleAll(){
    jQuery("[class*=close]", simpleTreeCollection[0]).each(function(){
        simpleTreeCollection[0].nodeToggle(this);
    });
}

/* 모듈 생성 후 */
function completeInsertBoard(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    var page = ret_obj['page'];
    var module_srl = ret_obj['module_srl'];

    alert(message);

    var url = current_url.setQuery('act','dispHomepageBoardInfo');
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    if(page) url.setQuery('page',page);
    location.href = url;
}
function completeInsertGroup(ret_obj) {
    location.href = current_url.setQuery('group_srl','');
}

function completeDeleteGroup(ret_obj) {
    location.href = current_url.setQuery('group_srl','');

}

function completeInsertGrant(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var page = ret_obj['page'];
    var module_srl = ret_obj['module_srl'];

    alert(message);
}

function completeInsertPage(ret_obj) {
    alert(ret_obj['message']);
    location.reload();
}

function completeChangeLayout(ret_obj) {
    location.reload();
}

function doDeleteGroup(group_srl) {
    var fo_obj = xGetElementById('fo_group');
    fo_obj.group_srl.value = group_srl;
    procFilter(fo_obj, delete_group);
}

function changeMenuType(obj) {
    var sel = obj.options[obj.selectedIndex].value;
    if(sel == 'url') {
        jQuery('#urlForm').css("display","table-row");
    } else {
        jQuery('#urlForm').css("display","none");
    }

}

function doRemoveMember(confirm_msg) {
    var fo_obj = document.getElementById('siteMembers');
    var chk_obj = fo_obj.cart;
    if(!chk_obj) return;


    var values = new Array();
    if(typeof(chk_obj.length)=='undefined') {
        if(chk_obj.checked) values[values.length]=chk_obj.value;
    } else {
        for(var i=0;i<chk_obj.length;i++) {
            if(chk_obj[i].checked) values[values.length]=chk_obj[i].value;
        }
    }
    if(values.length<1) return;

    if(!confirm(confirm_msg)) return;

    params = new Array();
    params['member_srl'] = values.join(',');

    exec_xml('homepage','procHomepageDeleteMember', params, doCompleteRemoveMember);
}

function doCompleteRemoveMember(ret_obj) { 
    alert(ret_obj['message']); 
    location.reload(); 
}

function importModule(id) {
    popopen( request_uri.setQuery('module','module').setQuery('act','dispModuleSelectList').setQuery('id',id).setQuery('type','single'), 'ModuleSelect');
}

function insertSelectedModule(id, module_srl, mid, browser_title) {
    params = new Array();
    params['import_module_srl'] = module_srl;
    params['site_srl'] = xGetElementById('foImport').site_srl.value;
    exec_xml('homepage','procHomepageAdminImportModule', params, doComplenteInsertSelectedModule);
}

function doComplenteInsertSelectedModule(ret_obj) {
    location.reload();
}

jQuery(function($){
	$('#chkDomain, #chkVid').change(function(){
		if($('#chkDomain').is(':checked')){
			$('#accessDomain').show();
			$('#accessVid').hide();
		}else if($('#chkVid').is(':checked')){
			$('#accessDomain').hide();
			$('#accessVid').show();
		}
	});
	$('#chkCafeVid, #chkCafeDomain').change(function(){
		if($('#chkCafeVid').is(':checked')){
			$('#accessCafeDomain').hide();
		}else if($('#chkCafeDomain').is(':checked')){
			$('#accessCafeDomain').show();
		}
	});
});