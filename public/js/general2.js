;(function(){// search

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.callout .callout-messages').slimScroll({
        height: '200px',
        railVisible: true,
        alwaysVisible: true,
    });

    $('.tab-content .scrollable').slimScroll({
        height: (screen.height-$('#control-sidebar-home-tab').offset().top )+'px',
        alwaysVisible: true,
        disableFadeOut: false,
        size: 6,
        color: '#2c89ce',
        railVisible: true,
        railColor: '#868686',
        railOpacity: 0.4,
    });

	$.each($("select[name],.select2").not(".default"), function(index,value){
		var _this = $(this);
		var placeholder = _this.attr('placeholder')?_this.attr('placeholder'):'--select--';
		var allowClear = $(_this.find("option:first").get(0)).val()=='';

		_this.select2({
			placeholder: placeholder,
			allowClear: allowClear
		}).on("select2:select", function (e) { 
			$(this).focus(); 
		});
		
		$(document).on("keydown",".select2-selection",function(event){
			if(event.key==='ArrowUp' || event.key==='ArrowDown'){
				$(this).parent().parent().parent().find("select.select2-hidden-accessible").select2("open");
			}
		});
        $(document).on("keydown",".form-control",function(event){
            if(event.keyCode===13){
                event.preventDefault();
                // return false;
            }
        });
		$(document).on("reset",function(){
			$("select.select2-hidden-accessible").val(null).trigger("change");
		});
	});

	$(document).on("mouseover",'[data-toggle="tooltip"][data-title],.actionbox [data-title]',function () {
        $(this).tooltip({
            title: $(this).data('title')
        }).tooltip('show');
    });

    $('[data-toggle=master-delete-popover]').confirmation({
        rootSelector: '[data-toggle=master-delete-popover]',
        placement:'right',
        popout:true,
        singleton:true,
        title: "Are you sure to delete?",
        onConfirm: function() {
        },
        onCancel: function() {
        },
        buttons: [
            {
                class: 'btn btn-xs btn-primary',
                icon: 'glyphicon glyphicon-ok',
                label:'Yes',

                onClick: function() {
                $(this).closest('form').submit();
                }
            },
            {
                class: 'btn btn-xs btn-default',
                icon: 'glyphicon glyphicon-remove',
                label:'No',
                cancel: true
            }
    ]


       /* content: function()
        {
            return '<div class="btn-group"><a class="btn btn-sm btn-success" onclick="$(this).closest(\'form\').submit();">' +
                '<i class="fa fa-check"></i> Yes</a>' +
                '<a class="btn btn-sm btn-danger"><i class="fa fa-close"></i> No</a></div>'},*/

        // other options
    });
	$(document).on("click","[data-href]",function () {
		var url = $(this).data("href");
		if(url) {
			window.location.href = url;
        }
    });
	
	$(".sidebar-menu .hrefgo-nochild").click(function(event){

		var pull_right_container = $(this).find('.pull-right-container');
        if (pull_right_container.is(event.target) || pull_right_container.has(event.target).length !==0){
            return false;
		}
        else{
			window.location.href =  $(this).data("href");
		}
	});
	// $(".treeview-menu .myfavorite").click(function(event){
    $(".myfavorite").click(function(event){
        var me = $(this);
		// var favourite = !$(this).hasClass('ismyfavorite');
        var favourite = !$(this).hasClass('fa-star');
		// var item2 = $(this).parent().parent().data('href').replace(/^\/+/g,'');
		var item=me.data('menuid');
        var menutitle=me.data('menutitle');
        var menulink=me.data('menulink');
        if(!item) {
		    return false;
        }
		var actualClasses = me.attr('class');
		me.removeClass('fa-star fa-star-o ismyfavourite').addClass('fa-spinner fa-spin');
		$.ajax({
		  method: "POST",
            url: window.ajaxURL("ux/favourite"),
		  data: { favourite: favourite,item:item }
		})
		  .done(function( msg ) {
		      if(msg=="add"){
		          var $action=$("<ul id=\""+item+"\"><a href=\"javascript:;\" data-href=\"" +
                      menulink +
                      "\"><li class=\"fa fa-circle-o\">&nbsp;&nbsp; </li>" +
                      menutitle +
                      "</a></ul>");

                  $('#reload').append($action);
              }
              else if(msg=="remove")
              {
                  $("#reload #"+item).hide();
              }
              else{

              }
              // alert(msg);
			  if(favourite==true) me.removeClass('fa-spinner fa-spin').addClass('ismyfavorite fa-star').removeClass("fa-star-o");
		      else me.removeClass('fa-spinner fa-spin').addClass("fa-star-o").removeClass('ismyfavorite fa-star');
		  })
		.fail(function( msg ) {
		    alert(msg);
			me.attr('class',actualClasses);
		});
	});

    $('.table-responsive table').colFilter({
        'target': '.col-filter'
    });


    jQuery.validator.addMethod("length", function(value, element, params) {
        return this.optional(element) || value.length == params;
    }, jQuery.validator.format("Please enter value of length {0} "));

	$("form.validate").validate({
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
        	error.appendTo(element.closest(".control-input"));
        },

        highlight: function(element, errorClass, validClass) {
            if ( element.type === "radio" ) {
                this.findByName( element.name ).addClass( errorClass ).removeClass( validClass );
            } else {
                $( element ).addClass( errorClass ).removeClass( validClass );
            }
            $( element ).closest(".form-group").addClass("has-error");
        },
        unhighlight: function( element, errorClass, validClass ) {
            if (element.type === "radio") {
                this.findByName(element.name).removeClass(errorClass).addClass(validClass);
            } else {
                $(element).removeClass(errorClass).addClass(validClass);
            }
            $( element ).closest(".form-group").removeClass("has-error");
            $( element ).closest(".form-group").find(".help-block").hide();
        },

        submitHandler:function(form){
            if(form.id){
                var fn = window[form.id+'_beforeSave'];
                if( typeof fn==='function' ){
                    var response = fn();
                    if(response===false) return false;
                }
            }
            $('.btn-disable-on-save,.btn-save-form').button('loading');
            form.submit();
        }
    });

    $(".ajax-auto-load-branch_id").change(function () {

        var that = $('#branch_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;

        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/branch"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
                alert('Could not complete request');
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });
    $(".ajax-auto-load-department_id").change(function () {

        var that = $('#department_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/department"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-subdepartment_id").change(function () {

        var that = $('#subdepartment_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/subdepartment"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-subcategory_id").change(function () {

        var that = $('#subcategory_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/subcategory"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-parametername_id").change(function () {

        var that = $('#parametername_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/parametername"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-branch1_id").change(function () {

        var that = $('#branch1_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/branch1"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });


    $(".ajax-auto-load-department1_id").change(function () {

        var that = $('#department1_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/department1"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-subdepartment1_id").change(function () {

        var that = $('#subdepartment1_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/sub_department1"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-employee1_id").change(function () {

        var that = $('#employee1_id');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/employee1"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-transfer_no").change(function () {

        var that = $('#transfer_no');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/transfer_number"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });

    $(".ajax-auto-load-transfer_no_to").change(function () {

        var that = $('#transfer_no');

        that.val(null).html("").trigger('change');

        var key = $(this).attr('name');
        var value = $(this).val();
        var req = {};
        req[key]=value;
        $.ajax({
            type: 'POST',
            url: window.ajaxURL("ajax/transfer_number_to"),
            dataType: 'json',
            data: req,
            success: function(data, textStatus, jqXHR) {
                if(!that.prop('multiple')){
                    data.unshift({'id':'','text':''});
                }
                that.val(null).select2({
                    placeholder: ($(this).attr('placeholder'))?$(this).attr('placeholder'):'--select--',
                    allowClear: ($(this).find("option:first").length)?($(this).find("option:first").get(0).val()):''=='',
                    data: data
                }).on("select2:select", function (e) {
                    $(this).focus();
                });
            },
            error: function(jqXHR, textStatus, errorThrown ){
            },
            complete: function(jqXHR, textStatus){
            }
        });
    });



    $("form#quickfilter #search-btn").click(function (event) {
        $("form#quickfilter input[name='q']").triggerHandler('keyup');
    });
    $("form#quickfilter input[name='q']").keyup(function (event) {

        if(event.keyCode==27){
            $(this).val("");
            $(".treeview.menu-open").removeClass('menu-open').find('.treeview-menu').hide();
            $(".sidebar-menu>li,.treeview-menu>li").show();

            $('.treeview.active, .treeview.active .treeview-menu').show();
            return false;
        }

        var userneeds = $(this).val().toLowerCase();
        var sidebarLi = $('.sidebar-menu').find('li');

        // hide all first..
        $(".treeview.menu-open").removeClass('menu-open').find('.treeview-menu').hide();
        $(".sidebar-menu>li,.treeview-menu>li").hide();

        // search
        var anythingFound = false;
        for(var i=0;i<sidebarLi.length;i++){
            var $li = $(sidebarLi[i]);
            var aText = $li.find('a:first').text().toLowerCase();
            if(aText.indexOf(userneeds)!==-1){
                $li.parents('.treeview').addClass('menu-open').show();
                $li.parents('.treeview-menu').show();
                $li.show();
                anythingFound = true;
            }
        }
        if(userneeds===''){
            $(".sidebar-menu>li").show();
        }
    });

    $(document).ready(function () {
        $('.has-datepicker').daterangepicker({
            singleDatePicker: true,
            // autoUpdateInput: false,
            showDropdowns: true,
            locale: $.extend([],window.settingsDateLocale)
        });
        $('.has-datepicker1').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: $.extend([],window.settingsDateLocale)
        });
    });
})();

/**
 * Creates HTML safe hidden Input to append to grids
 * @param name
 * @param value
 * @param attr
 * @returns {*|jQuery|HTMLElement}
 */
function hiddenInput(name, value,attr) {
    var newInput = $("<input type='hidden' name='' value='' />");
    newInput.attr('name',name);
    newInput.val(value);

    attr = attr || {};
    if(attr.class) newInput.attr('class',attr.class);
    return newInput;
}
$.fn.tableContent = function () {
    var tableContent = this.find("tbody.tableContent");
    console.log(tableContent);
    return tableContent;
};
$.fn.clearContent = function () {
    var tableContent = this.find("tbody.tableContent").html('').hide();
    this.find("tbody.noContent").show();
    return tableContent;
};
$(document).on("click",".delete-row",function () {
    var table = $(this).closest("table");
    $(this).closest("tr").remove();
    table.reIndex();
});
$.fn.reIndex = function(numberIndex,slnoColumn) {
    if(!this.is('table') ) return this;
    if(!this.find('.tableContent').length) return this;

    if(numberIndex===undefined) numberIndex = true;
    var noContent = this.find("tbody.noContent");
    var tableContent = this.find("tbody.tableContent");
    var tableRows = tableContent.find("tr");
    if(slnoColumn===undefined) slnoColumn = 1;

    if(tableRows.length){
        noContent.hide();

        if(numberIndex==true) {
            for (var i = 0; i < tableRows.length; i++) {
                var row = $(tableRows[i]);
                row.find('td').eq(slnoColumn).text(i + 1);
            }
        }
        noContent.hide();
        tableContent.show();
    }
    else{
        noContent.show();
        tableContent.hide();
    }
    return this;
};

/**
 * @protected
 * @param number
 * @returns {*}
 */
function naturalNumber(number) {
    if(number=='') return 0;
    if(isNaN(number)) return 0;
    return parseInt(number,10);
}

/**
 * @protected
 * @param number
 * @param decimals
 * @returns {number}
 */
function rationalNumber(number,decimals) {
    if(number=='') return 0;
    if(isNaN(number)) return 0;
    number = parseFloat(number);

    if(decimals===undefined) decimals = window.settingsNumberLimit;

    return Number(Math.round(number+'e'+decimals)+'e-'+decimals);
}

function round(number,decimals) {
    return rationalNumber(number,decimals);
}

/**
 * Toggle fullscreen function who work with webkit and firefox.
 * @function toggleFullscreen
 * @param {Object} event
 */
function toggleFullscreen(event) {
    var element = document.body;

    if (event instanceof HTMLElement) {
        element = event;
    }

    var isFullscreen = document.webkitIsFullScreen || document.mozFullScreen || false;

    element.requestFullScreen = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || function () { return false; };
    document.cancelFullScreen = document.cancelFullScreen || document.webkitCancelFullScreen || document.mozCancelFullScreen || function () { return false; };

    isFullscreen ? document.cancelFullScreen() : element.requestFullScreen();
}
$(document).ready(function(){
    $(".toggleFullscreen").click(function (event) {
        toggleFullscreen(event);
    });
});

$(document).on("keypress keyup","#quick-search",function(event){
    if(event.keyCode==27){
        $(this).val('');
    }
});
$(document).on("keypress","#quick-search",function(event){
    if(event.keyCode==13){
        $(this).val('');
    }
});
$(document).ready(function () {
    $(".btn-save-form").click(function () {
        $(this).closest('form').submit();
    });
});


$.validator.addMethod('filesize', function (value, element, param) {
    size=param*1024;
    return this.optional(element) || (element.files[0].size <= size);
}, 'File size must be less than {0}');



