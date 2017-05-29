jQuery(document).ready(function($) {
	
	 $("body").on("click", ".close-btn", function(){		
		$( ".result" ).empty();
		$( ".view-parent" ).empty();
		$('.popup-bg').hide(); // hide the overlay
		});

	jQuery(function() {
				jQuery('#fields_dropper').sortable({
						 // axis: 'y',
						// containment: "parent" ,						
						update: function(event, ui) {
						$(".ajax-ani").css('display','block');
						$(".ajax-img").css('display','block');
						var sec_id = $("#sec_id").val();						
						var productOrder = $(this).sortable('toArray').toString();
						// $("#sortable-9").text (productOrder);
						var data = {
								action : 'emgt_fields_order_save',
						        order : productOrder,
								sec_id : sec_id,
								dataType: 'json'
						};
						jQuery.get(emgt.ajax,data,function(response){
							$(".ajax-ani").css('display','none');
							$(".ajax-img").css('display','none');
							$("#main-wrapper .alert_box:first-child").hide();
							$("#main-wrapper").prepend('<div class="alert alert-success alert-dismissible fade in alert_box" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>Success ! </strong>Operation saved successfully</div>');		
						});
					 }
				});
    });	
	
	$("body").on("click","#add_new_section",function(e){
		e.preventDefault();
		var sec_name = $("#field_name").val();
		var option_id = $("#option").val();
		if(sec_name == "" || sec_name == " " || option_id == "")
		{
			alert("Please Enter Section Name! OR Select section to group with!");
			return false;
		}
		else{
			var data = {
						action : 'emgt_add_new_section',
						sec_name : sec_name,
						option_id : option_id,
						dataType : 'json'
						};
			$.post(emgt.ajax,data,function(response){
				var data = $.parseJSON(response);
				
				$("#section_select_list").append(data[0]);
				$("#section_list_table").prepend(data[1]);
				var menu = $("#top_menu_bar li[class = 'active']").attr('id');			
				if(menu == option_id) // Append section box only, if user on selected group by[section] page.
				{
					$("#left_section_menu").append(data[2]);
				}				
			});
		}
	});	

	
	$("body").on("click",".delete_section",function(e){
		e.preventDefault();
		var sec_id = $(this).attr('id');
		if(confirm("Are you sure you want to delete?"))
		{
			var data = {
						action : 'emgt_delete_new_section',
						sec_id : sec_id,
						dataType : 'json'
						};
			$.post(emgt.ajax,data,function(response){
						if(response == 'true')	
						{								
							  $("#section_list_table").find("tr[id="+sec_id+"]").remove();
							  $("#section_select_list").find("option[value="+sec_id+"]").remove();
							  $("#left_section_menu").find("li[id="+sec_id+"]").remove();//li:contains('+sec_name+')').remove();
						}
			});
		}
	});	

		
	$("body").on("click","#add",function(e)
	{
		e.preventDefault(); // disable normal link function so that it doesn't refresh the page
		$(".ajax-ani").css('display','block');
		$(".ajax-img").css('display','block');	
		
		var docHeight = $(document).height(); //grab the height of the page
		var scrollTop = $(window).scrollTop();
		var type = $("#field_type").val();
		var sec_id = $(this).attr("sec_id");			
		
		var data = {
					action : 'emgt_show_add_field_form',
					type : type,
					sec_id : sec_id,
					dataType : 'json'
					};
				$.post(emgt.ajax,data,function(result){
					// alert(result);
					$(".ajax-ani").css('display','none');
					$(".ajax-img").css('display','none');
					$('.popup-bg').show().css({'height' : docHeight});
					$('.result').html(result);	
					// document.write(result);
				});
	});
	
	$('body').on('click','.field_edit',function(event){
		event.preventDefault();
		$(".ajax-ani").css('display','block');
		$(".ajax-img").css('display','block');	
		
		var docHeight = $(document).height();
		var id = $(this).attr('id');
		var type = $(this).attr("fld_type");
		var data = {
					action : 'emgt_custom_field_edit',
					id : id,
					type : type,
					dataType : 'json'
					};
					
					$.post(emgt.ajax,data,function(result){	
						$(".ajax-ani").css('display','none');
						$(".ajax-img").css('display','none');						
						$('.popup-bg').show().css({'height' : docHeight});
						$('.result').html(result);	
					});
	});
	
	var x = false;
	var htm1;
	var htm2;
	var sc1_id;
		$("body").on('click',".edit_section", function(){
		 if (!x){
				sc1_id = $(this).attr("id");
				htm1 = $(this).html();
				$(this).html('<i class="fa fa-times"></i>&nbsp;Cancel');				
				htm2 = $("#section_list_table tr[id="+sc1_id+"] td:first-child").html();
				$("#section_list_table tr[id="+sc1_id+"] td:first-child").replaceWith($('<td style="width:370px;"><input type="text" value="'+htm2+'">&nbsp;&nbsp;&nbsp;<button class="btn btn-success update_popup_section">Save</button></td>'));
	
			x = true;
		 }
		 else {
		  $(this).html(htm1);			
		  // $(this).closest('tr').find('td:first').replaceWith($('<td>'+htm2+'</td>')); //works
		  $("#section_list_table tr[id="+sc1_id+"] td:first-child").replaceWith($('<td>'+htm2+'</td>'));
		  x = false;
		 }
		});


	$("body").on("click",".update_popup_section",function(e){
		$(".ajax-ani").css('display','block');
		$(".ajax-img").css('display','block');
		var s_name = $(this).prev().val();
		var s_id = $(this).closest('tr').attr("id");
		var data = {
					action : 'emgt_popup_section_edit',
					s_id : s_id,
					s_name : s_name,
					dataType : 'json'			
					};
			$.post(emgt.ajax,data,function(result){	
				$(".ajax-ani").css('display','none');
				$(".ajax-img").css('display','none');
				if(result == "true")
				{					
					$("#section_list_table tr[id="+s_id+"] td:first-child").replaceWith($('<td>'+s_name+'</td>'));
					$(".rems_menu_link a[id="+s_id+"]").html(s_name);
					$(".edit_section").html(htm1);
					 x = false;
					 delete htm1;  //unset variable;
					 delete htm2;
					 delete sc1_id;
				}
			});
		
	});	
		
		
	$("body").on("click",".btn_fld_show",function(e){
		e.preventDefault();
		$(".ajax-ani").css('display','block');
		$(".ajax-img").css('display','block');
		var id = $(this).attr("id");
		var check = $("."+id).is(":checked");		
		check = check === true ? "yes" : "no";
		var data = {
					action:"emgt_show_field_status",
					id:id,
					check:check,
					dataType:"json"
					};
		$.post(emgt.ajax,data,function(response){			
			var element = '<div class="alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Success ! </strong>Changes Saved!</div>';
			$("#main-wrapper").prepend(element);
			$(".alert-success:nth-child(2)").remove();
			$(".ajax-ani").css('display','none');
			$(".ajax-img").css('display','none');			
		});
		
	});
		
	$("body").on("change","#property_id",function(){	
		// $(".t_search").css('display','block');
		var id = $(this).val();
		var data = {
					action : "emgt_get_property_title",
					id : id,
					dataType : "json"
			};
		$.post(emgt.ajax,data,function(response){
			$("#estate").val(response);			
			// $(".t_search").css('display','none');
		});
	});

		
	$("body").on("change","#user_type",function(){	
		$("#user").html("");
		var role = $(this).val();
		var data = {
					action : "emgt_get_user_by_role",
					role : role,
					dataType : "json"
					};
		$.post(emgt.ajax,data,function(response){
			$("#user").append(response);
		});
	});
	
	$("body").on("change","#user",function(){	
		var id = $(this).val();
		var data = {
					action : "emgt_get_user_data",
					id : id,
					dataType : "json"
					};
		$.post(emgt.ajax,data,function(response){
			var json_obj = $.parseJSON(response);			
			$("#"+json_obj.gender).attr("checked","checked");
			$("#email").val(json_obj.email);
			$("#address").val(json_obj.address);
			$("#phone").val(json_obj.phone);
		});
	});
	
	$("body").on("click","#view_task",function(){
		var task = $(this).attr('t_id');		
		var docHeight = $(document).height();
		var data = {action : "emgt_view_inquiry_task",	
					task : task,
					dataType : "json"};
		$.post(emgt.ajax,data,function(response){	
			$('.popup-bg').show().css({'height' : docHeight});
			$('.result').html(response);	
		});
					
	
	});
	
	$("body").on("click","#view_note",function(){
		var note = $(this).attr('n_id');		
		var docHeight = $(document).height();
		var data = {action : "emgt_view_inquiry_note",	
					note : note,
					dataType : "json"};
		$.post(emgt.ajax,data,function(response){	
			// alert(response);
			$('.popup-bg').show().css({'height' : docHeight});
			$('.result').html(response);	
		});		
	});
	
	$("body").on("change","#select_inquiry",function(){
		$("#user_type").html("");
		var property = $(":selected").attr("property");
		var data = {action : "emgt_get_user_by_property",	
					property : property,
					dataType : "json"};
					
		$.post(emgt.ajax,data,function(response){
			$("#user_type").html(response);
		});
	});
	
	$("body").on("click","#pay_now",function(){	
		var docHeight = $(document).height();
		var pay_id = $(this).attr('pay_id');
		var user = $(this).attr('user');
		var plan = $(this).attr('plan');		
		var data = {action : "emgt_add_user_payment",	
					pay_id : pay_id,
					user : user,
					plan : plan,
					dataType : "json"};
					
		$.post(emgt.ajax,data,function(response){
			$('.popup-bg').show().css({'height' : docHeight});
			$('.result').html(response);
		});
	});
	
	$("body").on("click","#view_bill",function(){	
		var docHeight = $(document).height();
		var user =  $(this).attr("user");
		var data = {
					action :"emgt_view_bill",
					user : user,
					dataType : "json"
					};
		$.post(emgt.ajax,data,function(response){
			$('.popup-bg').show().css({'height' : docHeight});
			$('.result').html(response);
		});
	});
}); //end of document.ready