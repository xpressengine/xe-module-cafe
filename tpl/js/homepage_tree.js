function homepageLoadMenuInfo(url){
	var $ = jQuery;
    
	// clear tree;
    $('#menu > ul > li > ul').remove();
    if($("ul.simpleTree > li > a").size() ==0) {
		$('<a href="#__menu_info" class="add modalAnchor"><img src="./common/js/plugins/ui.tree/images/iconAdd.gif" /></a>')
			.bind("click", function(e){
				homepageAddMenu(0,e);
			})
			.appendTo("ul.simpleTree > li")
			.xeModalWindow();
	}

    //ajax get data and transeform ul il
    $.get(url,function(data){
        $(data).find("node").each(function(i){
            var text = $(this).attr("text");
            var node_srl = $(this).attr("node_srl");
            var parent_srl = $(this).attr("parent_srl");
            var url = $(this).attr("url");

            // node
            var node = $('<li id="tree_'+node_srl+'"><span>'+text+'</span></li>');

            // button
            $('<a href="#__menu_info" class="add modalAnchor"><img src="./common/js/plugins/ui.tree/images/iconAdd.gif" /></a>').bind("click",function(e){
                $("#tree_"+node_srl+" > span").click();
                homepageAddMenu(node_srl,e);
                return false;
            })
			.appendTo(node)
			.xeModalWindow();

            $('<a href="#__menu_info" class="modify modalAnchor"><img src="./common/js/plugins/ui.tree/images/iconModify.gif" /></a>').bind("click",function(e){
				$("#tree_"+node_srl+" > span").click();
                homepageModifyNode(node_srl,e);
                return false;

            })
			.appendTo(node)
			.xeModalWindow();

            $('<a href="#" class="delete"><img src="./common/js/plugins/ui.tree/images/iconDel.gif" /></a>').bind("click",function(e){
                homepageDeleteMenu(node_srl);
                return false;
            }).appendTo(node);

            // insert parent child
            if(parent_srl>0){
                if($('#tree_'+parent_srl+'>ul').length==0) $('#tree_'+parent_srl).append($('<ul>'));
                $('#tree_'+parent_srl+'> ul').append(node);
            }else{
                if($('#menu ul.simpleTree > li > ul').length==0) $("<ul>").appendTo('#menu ul.simpleTree > li');
                $('#menu ul.simpleTree > li > ul').append(node);
            }

        });

        //button show hide
        $("#menu li").each(function(){
            if($(this).parents('ul').size() > max_menu_depth) $("a.add",this).hide();
            if($(">ul",this).size()>0) $(">a.delete",this).hide();
        });


        // draw tree
        simpleTreeCollection = $('.simpleTree').simpleTree({
            autoclose: false,
            afterClick:function(node){
                //alert("text-"+$('span:first',node).text());
            },
            afterDblClick:function(node){
                //alert("text-"+$('span:first',node).text());
            },
            afterMove:function(destination, source, pos){
                $('#menuItem').css("display",'none');
                if(destination.size() == 0){
                    homepageLoadMenuInfo(xml_url);
                    return;
                }
                var menu_srl = $("#fo_menu input[name=menu_srl]").val();
                var parent_srl = destination.attr('id').replace(/.*_/g,'');
                var target_srl = source.attr('id').replace(/.*_/g,'');
                var brothers = $('#'+destination.attr('id')+' > ul > li:not([class^=line])').length;
                var mode = brothers >1 ? 'move':'insert';
                var source_srl = pos == 0 ? 0: source.prevAll("li:not(.line)").get(0).id.replace(/.*_/g,'');

                $.exec_json("homepage.procHomepageMenuItemMove",{ "menu_srl":menu_srl,"parent_srl":parent_srl,"target_srl":target_srl,"source_srl":source_srl,"mode":mode},
                function(data){
                    if(data.error>0){
                        homepageLoadMenuInfo(xml_url);
                    }
                });
            },

            // i want you !! made by sol
            beforeMovedToLine : function(destination, source, pos){
                return ($(destination).parents('ul').size() + $('ul',source).size() <= max_menu_depth);
            },

            // i want you !! made by sol
            beforeMovedToFolder : function(destination, source, pos){
                return ($(destination).parents('ul').size() + $('ul',source).size() <= max_menu_depth-1);
            },
            afterAjax:function()
            {
                //alert('Loaded');
            },
            animate:true
            ,docToFolderConvert:true
        });

        // open all node
        nodeToggleAll();
    },"xml");
}
function doReloadTreeMenu(){
    var menu_srl = jQuery("#fo_menu input[name=menu_srl]").val();

    jQuery.exec_json("menu.procMenuAdminMakeXmlFile",{ "menu_srl":menu_srl},
            function(data){
                 homepageLoadMenuInfo(xml_url);
            }
    );
    jQuery('#menuItem').css("display",'none');
}

function completeInsertMenuItem(data) {
    var xml_file = data['xml_file'];
    if(!xml_file) return;
	homepageLoadMenuInfo(xml_url);
	jQuery('#__menu_info').hide();
	jQuery('.x_modal-backdrop').hide();
}
function completeDeleteMenuItem(data) {
	doReloadTreeMenu();
}

function homepageAddMenu(node_srl,e) {
    jQuery('#menu_zone_info').html('');
    jQuery("#tree_"+node_srl+" > span").click();

    var params ={
            "menu_item_srl":0
            ,"parent_srl":node_srl
			,"mode":"insert"
            };

    jQuery.exec_json('homepage.getHomepageMenuTplInfo', params, function(data){
        jQuery('#menu_zone_info').html(data.tpl);
    });
}

function homepageModifyNode(node_srl,e){
    jQuery('#menu_zone_info').html('');
    jQuery("#tree_"+node_srl+" > span").click();

    var params ={
            "parent_srl":0
            ,"menu_item_srl":node_srl
			,"mode":"update"
            };

    jQuery.exec_json('homepage.getHomepageMenuTplInfo', params, function(data){
        jQuery('#menu_zone_info').html(data.tpl);
    });
}

function homepageDeleteMenu(node_srl) {
    if(confirm(lang_confirm_delete)){
        jQuery('#menuItem').css("display",'none');
        var fo_obj = jQuery('#menu_item_form').get(0);
        fo_obj.menu_item_srl.value = node_srl;
        procFilter(fo_obj, delete_menu_item);
    }
}
/* 각 메뉴의 버튼 이미지 등록 */
function doHomepageMenuUploadButton(obj) {
	// 이미지인지 체크
	if(!/\.(gif|jpg|jpeg|png)$/i.test(obj.value)) return alert(alertImageOnly);

	var fo_obj = jQuery("#fo_menu")[0];
	fo_obj.act.value = "procHomepageMenuUploadButton";
	fo_obj.target.value = obj.name;
	fo_obj.submit();
	fo_obj.act.value = "";
	fo_obj.target.value = "";
}

/* 메뉴 이미지 업로드 후처리 */
function completeMenuUploadButton(target, filename) {
	var column_name = target.replace(/^menu_/,'');
	var fo_obj = jQuery('#fo_menu')[0];
	var zone_obj = jQuery('#'+target+'_zone');
	var img_obj = jQuery('#'+target+'_img');

	fo_obj[column_name].value = filename;

	var img = new Image();
	img.src = filename;
	img_obj.attr('src', img.src);
	zone_obj.show();
}

/* 업로드된 메뉴 이미지 삭제 */
function doDeleteButton(target) {
	var fo_obj = jQuery("#fo_menu")[0];

	var col_name = target.replace(/^menu_/,'');

	var params = new Array();
	params['target'] = target;
	params['menu_srl'] = fo_obj.menu_srl.value;
	params['menu_item_srl'] = fo_obj.menu_item_srl.value;
	params['filename'] = fo_obj[col_name].value;

	var response_tags = new Array('error','message', 'target');

	exec_xml('menu','procMenuAdminDeleteButton', params, completeDeleteButton, response_tags);
}

/* 메뉴 이미지 삭제 후처리 */
function completeDeleteButton(ret_obj, response_tags) {
	var target = ret_obj['target'];
	var column_name = target.replace(/^menu_/,'');

	jQuery('#fo_menu')[0][column_name].value = '';
	jQuery('#'+target+'_img').attr('src', '');
	jQuery('#'+target+'_zone').hide();
}
