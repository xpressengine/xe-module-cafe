jQuery(function ($){
	$('input:radio[name=defaultGroup]').click(function(){
		$('._deleteTD').show();
		if ($(this).attr('checked')){
			$(this).closest('tr').find('._deleteTD').hide();
		}
	});
	$('._deleteGroup').click(function (event){
		event.preventDefault();
		var $target = $(event.target).closest('tr');
		var group_srl = $(event.target).attr('href').substr(1); 
		if(!confirm(xe.lang.groupDeleteMessage)) return;

		if (group_srl.indexOf("new") >= 0){
			$target.remove();
			return;
		}

		exec_xml(
			'homepage',
			'procHomepageDeleteGroup',
			{group_srl:group_srl},
			function(){location.reload();},
			['error','message','tpl']
		);

	});

	$('._addGroup').click(function (event){
		var $tbody = $('._groupList');
		var index = 'new'+ new Date().getTime();

		$tbody.find('._template').clone(true)
			.removeClass('_template')
			.find('input').removeAttr('disabled').end()
			.find('input:radio').val(index).end()
			.find('input[name="group_srls[]"]').val(index).end()
			.show()
			.appendTo($tbody)
			.find('.lang_code').xeApplyMultilingualUI();

		return false;
	});
	//add plugin
	var CheckTitle = xe.createPlugin('checkTitle', {
		API_BEFORE_VALIDATE : function(sender, params){
			$('input[name="group_titles[]"]').each(function(index){
				if ($(this).val() == ""){
					$(this).val($(this).closest('td').find('input:text.vLang').val());
				}
			});
		}
	});
	
	var checkTitle = new CheckTitle();
	var v = xe.getApp('Validator')[0];
	v.registerPlugin(checkTitle);
});
