jQuery(function($){
	/*-- search box --*/
	var _this = this;
	$('.btn_srch').click(function(){
		$(this).next().toggle();
	});

	$('.searchkey').keydown(function(){
		$('.btn_cancel').css('display','inline-block');
	});

	$('.btn_cancel').click(function(){
		$('.searchkey')[0].value='';
		$(this).hide();
		$('.searchkey').focus();
	});

	// add a cafe to mine
	$('.btn_dislike').bind('click',function()
	{
	    var paramVid = this.getAttribute("data");
		doSiteSignUp(paramVid, this);
	});

	// remove a cafe from mine
	$('.btn_like').bind('click',function()
	{
		var paramVid = this.getAttribute("data");
		doSiteLeave(paramVid , this);
	});

	// sign up site
	$('.btn_sign_up').bind('click',function()
	{
	    var paramVid = this.getAttribute("data");
		var params = new Array();
        params['vid'] = paramVid;
		exec_xml('member','procMemberSiteSignUp',  params, function() { location.reload(); } );
	});

	// leave site
	$('.btn_leave').bind('click',function()
	{
		var paramVid = this.getAttribute("data");
		var params = new Array();
        params['vid'] = paramVid;
		exec_xml('member','procMemberSiteLeave',  params, function() { location.reload(); } );
	});

	// All cafes && My cafes pagination

	function changeData(linkObj)
	{		
		if(linkObj.className == 'btn_dislike')
	    {
	        $(linkObj).toggleClass('btn_dislike',false);
	        $(linkObj).toggleClass('btn_like');
            var mNumObj = $(linkObj).prev('.cafe_info').find('.memberNumber');
            mNumObj.html(parseInt(mNumObj.html()) + 1)

	    }
	    else
	    {
	        $(linkObj).toggleClass('btn_like',false);
	        $(linkObj).toggleClass('btn_dislike');
	        var mNumObj = $(linkObj).prev('.cafe_info').find('.memberNumber');
            mNumObj.html(parseInt(mNumObj.html()) - 1)
	    }
	}

	function doSiteSignUp(vid, cObj)
	{
	    var params = new Array();
        params['vid'] = vid;
        var response_tags = ['error','message'];
        var funcSuc = function(ret_obj, response_tags)
                     {
                        if(ret_obj['error']==='0')
                        {
                            var isAdd = true;
                            changeData(cObj, isAdd);
                            return;
                        }
                     };

        exec_xml('member',
                 'procMemberSiteSignUp',
                 params,
                 funcSuc,
                 response_tags
        );
    }

    function doSiteLeave(vid, cObj)
    {
        var params = new Array();
        params['vid'] = vid;
        var response_tags = ['error','message'];
        var funcSuc = function(ret_obj, response_tags)
         {
            if(ret_obj['error']==='0')
            {
                changeData(cObj);
                return;
            }
         };

        exec_xml('member',
                 'procMemberSiteLeave',
                 params,
                 funcSuc,
                 response_tags
        );
    }

	more_btn = $('#getMore');
	var hub_view_rage = 4;
	hubNewestDocs = 'ul.doc_list>:gt('+(hub_view_rage-1)+')';
	var cafe_view_rage = 5;
	cafeItems = 'ul.cafe_item>:gt('+ (cafe_view_rage-1)+')';
	$(hubNewestDocs).css('display','none');
	$(cafeItems).css('display','none');
	more_btn.click(function(){
		if($(hubNewestDocs).length>0){
			hub_view_rage +=4;
			hubNewestDocs = 'ul.doc_list>:lt('+(hub_view_rage)+')';
			$(hubNewestDocs).slideDown('slow');
		}
		if($(cafeItems).length>0){
			cafe_view_rage +=5;
			cafeItems = 'ul.cafe_item>:lt('+(cafe_view_rage)+')';
			$(cafeItems).slideDown('slow');
		}
	});
	
	var cafe_list = $('ul.cafe_lst');
	cafe_list.first().css('margin-top','5px');
	
	var doc_list = $('ul.doc_list');
	doc_list.first().css('margin-top','5px');



	

})