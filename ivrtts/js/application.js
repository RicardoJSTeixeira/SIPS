

/* IMAGE GALLERY */ 
  $(document).ready( function() {
                $('.tl').glisse({speed: 500, changeSpeed: 550, effect:'bounce', fullscreen: false}); 
                $('#changefx').change(function() {
                    var val = $(this).val();
                    $('.tl').each(function(){
                        $(this).data('glisse').changeEffect(val);
                    });
                });
            });

//*********************  Select all items checkbox using jQuery  *********************//	
    $(document).ready( function() {
       $("#maincheck").click( function() {
            if($('#maincheck').attr('checked')){
                $('.mc').attr('checked', true);
            } else {
                $('.mc').attr('checked', false);
            }
       });
    });


// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function ($) {

  $(function(){

    // Disable certain links in docs
    $('section [href^=#]').click(function (e) {
      e.preventDefault()
    })

    // make code pretty
    window.prettyPrint && prettyPrint()

    // add-ons
    $('.add-on :checkbox').on('click', function () {
      var $this = $(this)
        , method = $this.attr('checked') ? 'addClass' : 'removeClass'
      $(this).parents('.add-on')[method]('active')
    })

    // position static twipsies for components page
    if ($(".twipsies a").length) {
      $(window).on('load resize', function () {
        $(".twipsies a").each(function () {
          $(this)
            .tooltip({
              placement: $(this).attr('title')
            , trigger: 'manual'
            })
            .tooltip('show')
          })
      })
    }

    // add tipsies to grid for scaffolding
    if ($('#grid-system').length) {
      $('#grid-system').tooltip({
          selector: '.show-grid > div'
        , title: function () { return $(this).width() + 'px' }
      })
    }

    // fix sub nav on scroll
    var $win = $(window)
      , $nav = $('.subnav')
      , navTop = $('.subnav').length && $('.subnav').offset().top - 40
      , isFixed = 0

    processScroll()

    // hack sad times - holdover until rewrite for 2.1
    $nav.on('click', function () {
      if (!isFixed) setTimeout(function () {  $win.scrollTop($win.scrollTop() - 47) }, 10)
    })

    $win.on('scroll', processScroll)

    function processScroll() {
      var i, scrollTop = $win.scrollTop()
      if (scrollTop >= navTop && !isFixed) {
        isFixed = 1
        $nav.addClass('subnav-fixed')
      } else if (scrollTop <= navTop && isFixed) {
        isFixed = 0
        $nav.removeClass('subnav-fixed')
      }
    }

    // tooltip demo
    $('.tooltip-bottom').tooltip({
		placement:"bottom",
      selector: "a[data-t=tooltip]"
    })
	$('.tooltip-top').tooltip({
		placement:"top",
      selector: "a[data-t=tooltip]"
    })
	$('.tooltip-left').tooltip({
		placement:"left",
      selector: "a[data-t=tooltip]"
    })
	$('.tooltip-right').tooltip({
		placement:"right",
      selector: "a[data-t=tooltip]"
    })


    $('.popover-top').popover({
		placement:"top"
    })
	$('.popover-bottom').popover({
		placement:"bottom"
    })
	$('.popover-left').popover({
		placement:"left"
    })
	$('.popover-right').popover({
		placement:"right"
    })

    // popover demo
    $("a[data-t=popover]")
      .popover()
      .click(function(e) {
		  
        e.preventDefault()
      })

    // button state demo
    $('#fat-btn')
      .click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
          btn.button('reset')
        }, 3000)
      })

    // carousel demo
    $('#myCarousel').carousel()

    // javascript build logic
    var inputsComponent = $("#components.download input")
      , inputsPlugin = $("#plugins.download input")
      , inputsVariables = $("#variables.download input")

    // toggle all plugin checkboxes
    $('#components.download .toggle-all').on('click', function (e) {
      e.preventDefault()
      inputsComponent.attr('checked', !inputsComponent.is(':checked'))
    })

    $('#plugins.download .toggle-all').on('click', function (e) {
      e.preventDefault()
      inputsPlugin.attr('checked', !inputsPlugin.is(':checked'))
    })

    $('#variables.download .toggle-all').on('click', function (e) {
      e.preventDefault()
      inputsVariables.val('')
    })

    // request built javascript
    $('.download-btn').on('click', function () {

      var css = $("#components.download input:checked")
            .map(function () { return this.value })
            .toArray()
        , js = $("#plugins.download input:checked")
            .map(function () { return this.value })
            .toArray()
        , vars = {}
        , img = ['glyphicons-halflings.png', 'glyphicons-halflings-white.png']

    $("#variables.download input")
      .each(function () {
        $(this).val() && (vars[ $(this).prev().text() ] = $(this).val())
      })

      $.ajax({
        type: 'POST'
      , url: /\?dev/.test(window.location) ? 'http://localhost:3000' : 'http://bootstrap.herokuapp.com'
      , dataType: 'jsonpi'
      , params: {
          js: js
        , css: css
        , vars: vars
        , img: img
      }
      })
    })
  })

// Modified from the original jsonpi https://github.com/benvinegar/jquery-jsonpi
$.ajaxTransport('jsonpi', function(opts, originalOptions, jqXHR) {
  var url = opts.url;

  return {
    send: function(_, completeCallback) {
      var name = 'jQuery_iframe_' + jQuery.now()
        , iframe, form

      iframe = $('<iframe>')
        .attr('name', name)
        .appendTo('head')

      form = $('<form>')
        .attr('method', opts.type) // GET or POST
        .attr('action', url)
        .attr('target', name)

      $.each(opts.params, function(k, v) {

        $('<input>')
          .attr('type', 'hidden')
          .attr('name', k)
          .attr('value', typeof v == 'string' ? v : JSON.stringify(v))
          .appendTo(form)
      })

      form.appendTo('body').submit()
    }
  }
})

}(window.jQuery)
/*jslint unparam: true */
/*global window, document, $ */
$(function () {
    'use strict';

    // Start slideshow button:
    $('#start-slideshow').button().click(function () {
        var options = $(this).data(),
            modal = $(options.target),
            data = modal.data('modal');
        if (data) {
            $.extend(data.options, options);
        } else {
            options = $.extend(modal.data(), options);
        }
        modal.find('.modal-slideshow').find('i')
            .removeClass('icon-play')
            .addClass('icon-pause');
        modal.modal(options);
    });

    // Toggle fullscreen button:
    $('#toggle-fullscreen').button().click(function () {
        var button = $(this),
            root = document.documentElement;
        if (!button.hasClass('active')) {
            $('#modal-gallery').addClass('modal-fullscreen');
            if (root.webkitRequestFullScreen) {
                root.webkitRequestFullScreen(
                    window.Element.ALLOW_KEYBOARD_INPUT
                );
            } else if (root.mozRequestFullScreen) {
                root.mozRequestFullScreen();
            }
        } else {
            $('#modal-gallery').removeClass('modal-fullscreen');
            (document.webkitCancelFullScreen ||
                document.mozCancelFullScreen ||
                $.noop).apply(document);
        }
    });

  
  
});


/* ScrollPane */

//*******************  UI  *******************//
			$(function(){

				// Accordion
				$("#accordion").accordion({ header: "h3" });
	
				// Tabs
				$('#tabs').tabs();

				// Dialog			
				$('#dialog').dialog({
					autoOpen: false,
					width: 600,
					buttons: {
						"Ok": function() { 
							$(this).dialog("close"); 
						}, 
						"Cancel": function() { 
							$(this).dialog("close"); 
						} 
					}
				});
				
				// Dialog Link
				$('#dialog_link').click(function(){
					$('#dialog').dialog('open');
					return false;
				});

				// Datepicker
				$('#datepicker').datepicker({
					inline: true
				});
				$('#inline-datepicker').datepicker({
					inline: true
				});
				
				// Slider
				$( "#slider" ).slider(
					{
						slide: function( event, ui ) {
							$( "#amount" ).val( "$" + ui.value );
						}
					}
				);
				
				$( "#slider2" ).slider({
						value:100,
						min: 0,
						max: 500,
						step: 1,
						slide: function( event, ui ) {
							$( "#amount" ).val( "$" + ui.value );
						}
					});
				$( "#amount" ).val( "$" + $( "#slider" ).slider( "value" ) );
				$( "#slider-range" ).slider({
					range: true,
					min: 0,
					max: 500,
					values: [ 75, 300 ],
					slide: function( event, ui ) {
						$( "#amount2" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
					}
				});
				$( "#amount2" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
					" - $" + $( "#slider-range" ).slider( "values", 1 ) );
					// setup graphic EQ
				$( "#eq > span" ).each(function() {
					// read initial values from markup and remove that
					var value = parseInt( $( this ).text(), 10 );
					$( this ).empty().slider({
						value: value,
						range: "min",
						animate: true,
						orientation: "vertical"
					});
				});
				$( "#slider-range-min" ).slider({
					range: "min",
					value: 23,
					min: 23,
					max: 500,
					slide: function( event, ui ) {
						$( "#amount3" ).val( "$" + ui.value );
					}
				});
				$( ".demo_slider" ).slider({
					range: "min",
					value: 321,
					min: 23,
					max: 500,
					slide: function( event, ui ) {
						$( "#amountasf" ).val( "$" + ui.value );
					}
				});
				$( "#amount3" ).val( "$" + $( "#slider-range-min" ).slider( "value" ) );
				$( "#slider-range-max" ).slider({
					range: "max",
					value: 56,
					min: 1,
					max: 350,
					slide: function( event, ui ) {
						$( "#amount4" ).val( "$" + ui.value );
					}
				});
				$( "#amount4" ).val( "$" + $( "#slider-range-min" ).slider( "value" ) );
				// Progressbar
				$("#progressbar").progressbar({
					value: 20
				});
				
				//hover states on the static widgets
				$('#dialog_link, ul#icons li').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);
				
			});

//*******************  MENU LEFT  *******************//
jQuery.fn.initMenu = function() {  
    return this.each(function(){
        var theMenu = $(this).get(0);
        $('.acitem', this).hide();
        $('li.expand > .acitem', this).show();
        $('li.expand > .acitem', this).prev().addClass('active');
        $('li a', this).click(
            function(e) {
                e.stopImmediatePropagation();
                var theElement = $(this).next();
                var parent = this.parentNode.parentNode;
                if($(parent).hasClass('noaccordion')) {
                    if(theElement[0] === undefined) {
                        window.location.href = this.href;
                    }
                    $(theElement).slideToggle('normal', function() {
                        if ($(this).is(':visible')) {
                            $(this).prev().addClass('active');
                        }
                        else {
                            $(this).prev().removeClass('active');
                        }    
                    });
                    return false;
                }
                else {
                    if(theElement.hasClass('acitem') && theElement.is(':visible')) {
                        if($(parent).hasClass('collapsible')) {
                            $('.acitem:visible', parent).first().slideUp('normal', 
                            function() {
                                $(this).prev().removeClass('active');
                            }
                        );
                        return false;  
                    }
                    return false;
                }
                if(theElement.hasClass('acitem') && !theElement.is(':visible')) {         
                    $('.acitem:visible', parent).first().slideUp('normal', function() {
                        $(this).prev().removeClass('active');
                    });
                    theElement.slideDown('normal', function() {
                        $(this).prev().addClass('active');
                    });
                    return false;
                }
            }
        }
    );
});
};

$(document).ready(function() {$('.menu').initMenu();});

//*********************  CALENDAR  *********************//			
	$(document).ready(function() {
	
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			editable: true,
			events: [
				{
					title: 'All Day Event',
					start: new Date(y, m, 1)
				},
				{
					title: 'Long Event',
					start: new Date(y, m, d-5),
					end: new Date(y, m, d-2)
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d-3, 16, 0),
					allDay: false
				},
				{
					id: 999,
					title: 'Repeating Event',
					start: new Date(y, m, d+4, 16, 0),
					allDay: false
				},
				{
					title: 'Meeting',
					start: new Date(y, m, d, 10, 30),
					allDay: false
				},
				{
					title: 'Lunch',
					start: new Date(y, m, d, 12, 0),
					end: new Date(y, m, d, 14, 0),
					allDay: false
				},
				{
					title: 'Birthday Party',
					start: new Date(y, m, d+1, 19, 0),
					end: new Date(y, m, d+1, 22, 30),
					allDay: false
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'http://google.com/'
				}
			]
		});
		
	});




/* pieChart (Round Infographic) */
            var initPieChart = function() {
                $('.percentage').easyPieChart({
                    animate: 1000
                });
                $('.percentage-light').easyPieChart({
                    barColor: function(percent) {
                        percent /= 100;
                        return "rgb(" + Math.round(255 * (1-percent)) + ", " + Math.round(255 * percent) + ", 0)";
                    },
                    trackColor: '#ebebeb',
                    scaleColor: false,
                    lineCap: 'butt',
                    lineWidth: 10,
                    animate: 1000
                });

                $('.updateEasyPieChart').on('click', function(e) {
                  e.preventDefault();
                  $('.percentage, .percentage-light').each(function() {
                    var newValue = Math.round(100*Math.random());
                    $(this).data('easyPieChart').update(newValue);
                    $('span', this).text(newValue);
                  });
                });
            };
/* calendar mini */
$(function(){
			window.prettyPrint && prettyPrint();
			$('#dp1').datepicker({
				format: 'mm-dd-yyyy'
			});
			$('#dp2').datepicker();
			$('#dp3').datepicker();
			
			
			var startDate = new Date(2012,1,20);
			var endDate = new Date(2012,1,25);
			$('#dp4').datepicker()
				.on('changeDate', function(ev){
					if (ev.date.valueOf() > endDate.valueOf()){
						$('#alert').show().find('strong').text('The start date can not be greater then the end date');
					} else {
						$('#alert').hide();
						startDate = new Date(ev.date);
						$('#startDate').text($('#dp4').data('date'));
					}
					$('#dp4').datepicker('hide');
				});
			$('#dp5').datepicker()
				.on('changeDate', function(ev){
					if (ev.date.valueOf() < startDate.valueOf()){
						$('#alert').show().find('strong').text('The end date can not be less then the start date');
					} else {
						$('#alert').hide();
						endDate = new Date(ev.date);
						$('#endDate').text($('#dp5').data('date'));
					}
					$('#dp5').datepicker('hide');
				});
		});
/* color picker */
$(function(){
		window.prettyPrint && prettyPrint()
			$('#cp1').colorpicker({
				format: 'hex'
			});
			$('#cp2').colorpicker();
			$('#cp3').colorpicker();
			var bodyStyle = $('body')[0].style;
			$('#cp4').colorpicker().on('changeColor', function(ev){
				bodyStyle.backgroundColor = ev.color.toHex();
			});
});
/*  TinyMCE - Javascript WYSIWYG Editor */
tinyMCE.init({
        // General options
        mode:"exact", // selected textareas
        elements: "i1", // id of selected textarea
        theme : "advanced",
		width : "98%",
		height : "300",

        plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
		force_br_newlines : true,


        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
		
});
//********************* Automatic Infinite Carousel (CHAT)  *********************//
$(document).ready(function(){ 
    
    // Gallery
    if(jQuery("#gallery").length){
        
        // Declare variables
        var totalImages = jQuery("#gallery > li").length, 
            imageWidth = jQuery("#gallery > li:first").outerWidth(true),
            totalWidth = imageWidth * totalImages,
            visibleImages = Math.round(jQuery("#gallery-wrap").width() / imageWidth),
            visibleWidth = visibleImages * imageWidth,
            stopPosition = (visibleWidth - totalWidth);
            
        jQuery("#gallery").width(totalWidth);
        
        jQuery("#gallery-prev").click(function(){
            if(jQuery("#gallery").position().left < 0 && !jQuery("#gallery").is(":animated")){
                jQuery("#gallery").animate({left : "+=" + imageWidth + "px"});
            }
            return false;
        });
        
        jQuery("#gallery-next").click(function(){
            if(jQuery("#gallery").position().left > stopPosition && !jQuery("#gallery").is(":animated")){
                jQuery("#gallery").animate({left : "-=" + imageWidth + "px"});
            }
            return false;
        });
    }
        
});
//*********************  File explorer  *********************//
	$(document).ready(function(){
			
			var f = $('#finder').elfinder({
				url : 'js/elfinder/connectors/php/connector.php',
				lang : 'en',
				docked : true

				// dialog : {
				// 	title : 'File manager',
				// 	height : 500
				// }

				// Callback example
				//editorCallback : function(url) {
				//	if (window.console && window.console.log) {
				//		window.console.log(url);
				//	} else {
				//		alert(url);
				//	}
				//},
				//closeOnEditorCallback : true
			})
			// window.console.log(f)
			$('#close,#open,#dock,#undock').click(function() {
				$('#finder').elfinder($(this).attr('id'));
			})
			
		});	
		
/* Placeholder IE */ 
(function($) {
    function Placeholder(input) {
        this.input = input;
        if (input.attr('type') == 'password') {
            this.handlePassword();
        }
        // Prevent placeholder values from submitting
        $(input[0].form).submit(function() {
            if (input.hasClass('placeholder') && input[0].value == input.attr('placeholder')) {
                input[0].value = '';
            }
        });
    }
    Placeholder.prototype = {
        show : function(loading) {
            // FF and IE saves values when you refresh the page. If the user refreshes the page with
            // the placeholders showing they will be the default values and the input fields won't be empty.
            if (this.input[0].value === '' || (loading && this.valueIsPlaceholder())) {
                if (this.isPassword) {
                    try {
                        this.input[0].setAttribute('type', 'text');
                    } catch (e) {
                        this.input.before(this.fakePassword.show()).hide();
                    }
                }
                this.input.addClass('placeholder');
                this.input[0].value = this.input.attr('placeholder');
            }
        },
        hide : function() {
            if (this.valueIsPlaceholder() && this.input.hasClass('placeholder')) {
                this.input.removeClass('placeholder');
                this.input[0].value = '';
                if (this.isPassword) {
                    try {
                        this.input[0].setAttribute('type', 'password');
                    } catch (e) { }
                    // Restore focus for Opera and IE
                    this.input.show();
                    this.input[0].focus();
                }
            }
        },
        valueIsPlaceholder : function() {
            return this.input[0].value == this.input.attr('placeholder');
        },
        handlePassword: function() {
            var input = this.input;
            input.attr('realType', 'password');
            this.isPassword = true;
            // IE < 9 doesn't allow changing the type of password inputs
            if ($.browser.msie && input[0].outerHTML) {
                var fakeHTML = $(input[0].outerHTML.replace(/type=(['"])?password\1/gi, 'type=$1text$1'));
                this.fakePassword = fakeHTML.val(input.attr('placeholder')).addClass('placeholder').focus(function() {
                    input.trigger('focus');
                    $(this).hide();
                });
                $(input[0].form).submit(function() {
                    fakeHTML.remove();
                    input.show()
                });
            }
        }
    };
    var NATIVE_SUPPORT = !!("placeholder" in document.createElement( "input" ));
    $.fn.placeholder = function() {
        return NATIVE_SUPPORT ? this : this.each(function() {
            var input = $(this);
            var placeholder = new Placeholder(input);
            placeholder.show(true);
            input.focus(function() {
                placeholder.hide();
            });
            input.blur(function() {
                placeholder.show(false);
            });

            // On page refresh, IE doesn't re-populate user input
            // until the window.onload event is fired.
            if ($.browser.msie) {
                $(window).load(function() {
                    if(input.val()) {
                        input.removeClass("placeholder");
                    }
                    placeholder.show(true);
                });
                // What's even worse, the text cursor disappears
                // when tabbing between text inputs, here's a fix
                input.focus(function() {
                    if(this.value == "") {
                        var range = this.createTextRange();
                        range.collapse(true);
                        range.moveStart('character', 0);
                        range.select();
                    }
                });
            }
        });
    }
})(jQuery);	
jQuery('input[placeholder], textarea[placeholder]').placeholder();
	
/* Chosen */
$(".chzn-select").chosen();
/* autorisize */	
$(document).ready(function() {
	$('textarea.resize-text').autoResize({});
});
/* tags input */
$('.tags_input').tagsInput();
/* Auto TAB (Input) */
$(document).ready(function() {
		$('#autotab_example').submit(function() { return false; });
		$('#autotab_example :input').autotab_magic();
		// Number example
		$('#area_code, #number1, #number2').autotab_filter('numeric');
		$('#ssn1, #ssn2, #ssn3').autotab_filter('numeric');
		// Text example
		$('#text1, #text2, #text3').autotab_filter('text');
		// Alpha example
		$('#alpha1, #alpha2, #alpha3, #alpha4, #alpha5').autotab_filter('alpha');
		// Alphanumeric example
		$('#alphanumeric1, #alphanumeric2, #alphanumeric3, #alphanumeric4, #alphanumeric5').autotab_filter({ format: 'alphanumeric', uppercase: true });
		$('#regex').autotab_filter({ format: 'custom', pattern: '[^0-9\.]' });
});

/* toggle */
$(document).ready(function () {
     
    $('#toggle-view li').click(function () {
 
        var text = $(this).children('div.panel');
 
        if (text.is(':hidden')) {
            text.slideDown('200');
            $(this).children('span').html('-');     
        } else {
            text.slideUp('200');
            $(this).children('span').html('+');     
        }
         
    });
 
});
/* FORM VALIDATION */
jQuery(document).ready(function(){

			// binds form submission and fields to the validation engine

			jQuery("#formID").validationEngine();

		});


		function checkHELLO(field, rules, i, options){

			if (field.val() != "HELLO") {

				// this allows to use i18 for the error msgs

				return options.allrules.validate2fields.alertText;

			}

		}
	

/* tables */
			$(document).ready(function() {
				$('#datatable_1').dataTable();
			} );
			$(document).ready(function() {
				$('#datatable_2').dataTable( {
					"bPaginate": false,
					"bLengthChange": false,
					"bFilter": true,
					"bSort": false,
					"bInfo": false,
					"bAutoWidth": false
				} );
			} );
			$(document).ready(function() {
				$('#datatable_3').dataTable( {
					"sPaginationType": "full_numbers"
				} );
			} );
			$(document).ready(function() {
				$('#datatable_4').dataTable( {
					"sScrollX": "100%",
					"sScrollXInner": "145%",
					"bScrollCollapse": true
				} );
			} );
			$(document).ready(function() {
				$('#datatable_5').dataTable( {
					"sScrollY": "200px",
					"bPaginate": false,
					"bScrollCollapse": true
				} );
			} );
			$(document).ready( function () {
				$('#datatable_6').dataTable( {
					"sDom": 'T<"clear">lfrtip'
				} );
			} );

//********* WIZARD **************//
$(document).ready(function() {

				$('#default').stepy();

				$('#custom').stepy({
					backLabel:	'Backward',
					block:		true,
					errorImage:	true,
					nextLabel:	'Forward',
					titleClick:	true,
					validate:	true
				});

				$('div#step').stepy({
					finishButton: false
				});

				// Optionaly
				$('#custom').validate({
					errorPlacement: function(error, element) {
						$('#custom div.stepy-error').append(error);
					}, rules: {
						'user':			{ maxlength: 1 },
						'email':		'email',
						'checked':		'required',
						'newsletter':	'required',
						'password':		'required',
						'bio':			'required',
						'day':			'required'
					}, messages: {
						'user':			{ maxlength: 'User field should be less than 1!' },
						'email':		{ email: 	 'Invalid e-mail!' },
						'checked':		{ required:  'Checked field is required!' },
						'newsletter':	{ required:  'Newsletter field is required!' },
						'password':		{ required:  'Password field is requerid!' },
						'bio':			{ required:  'Bio field is required!' },
						'day':			{ required:  'Day field is requerid!' },
					}
				});

				$('#callback').stepy({
					back: function(index) {
						alert('Going to step ' + index + '...');
					}, next: function(index) {
						if ((Math.random() * 10) < 5) {
							alert('Invalid random value!');
							return false;
						}

						alert('Going to step ' + index + '...');
					}, select: function(index) {
						alert('Current step ' + index + '.');
					}, finish: function(index) {
						alert('Finishing on step ' + index + '...');
					}, titleClick: true
				});

				$('#target').stepy({
					description:	false,
					legend:			false,
					titleClick:		true,
					titleTarget:	'#title-target'
				});

			} );
//*********************   Charts   *********************//	


//*Interacting with the data points *//
$(function () {
   	// CHARts        


	/* table chart */
    $("table.chart2").each(function() {
        var colors = [];
        $("table.chart thead th:not(:first)").each(function() {
            colors.push($(this).css("color"));
        });
        $(this).graphTable({
            series: 'columns',  position: 'replace',width: '100%', height: '250px', colors: colors
        }, {  xaxis: {  tickSize: 1,  },
			yaxis: {
				 max: 1000,
				min:200,
            }	,	series: {
				points: {show: true },
                lines: { show: true, fill: false, steps: false },
			}
        });
    });
	
	

    function showTooltip(x, y, contents) {
        $('<div id="tooltip" >' + contents + '</div>"').css({
            position: 'absolute',
            display: 'none',
            top: y -13,
            left: x + 10
        }).appendTo("body").show();
    }

    var previousPoint = null;
    $(".chart_flot").bind("plothover", function(event, pos, item) {
												
        $("#x").text(pos.x);
        $("#y").text(pos.y);

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

			$(this).attr('title',item.series.label);
			$(this).trigger('click');
                $("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1];

                showTooltip(item.pageX, item.pageY,  "<p>Info for a day</p><b>" + item.series.label + "</b> : " + y);
            }
        }  else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });

});
//* BAR *//


$(function () {
    var previousPoint;
    var d1 = [];
    for (var i = 0; i <= 7; i += 1)
        d1.push([i, parseInt(Math.random() * 30)]);
 
    var d2 = [];
    for (var i = 0; i <= 7; i += 1)
        d2.push([i, parseInt(Math.random() * 30)]);
 
    var d3 = [];
    for (var i = 0; i <= 7; i += 1)
        d3.push([i, parseInt(Math.random() * 30)]);
 
    var ds = new Array();
 
    ds.push({
        data:d1,

        bars: {
            show: true, 
            barWidth: 0.2, 
            order: 1,
            lineWidth : 2
        }
		
    });
    ds.push({
        data:d2,
        bars: {
            show: true, 
            barWidth: 0.2, 
            order: 2
        }
    });
    ds.push({
        data:d3,
        bars: {
            show: true, 
            barWidth: 0.2, 
            order: 3
        }
    });
                
    //tooltip function
    function showTooltip(x, y, contents, areAbsoluteXY) {
        var rootElt = 'body';
	
        $('<div id="tooltip" class="tooltip-with-bg">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            'z-index':'1010',
            top: y,
            left: x,
			border: '1px solid #d5d5de',
            padding: '3px',
            'background-color': '#ffffff',
        }).prependTo(rootElt).show();
    }
                
    //Display graph
    $.plot($(".bars"), ds, {
        grid:{
            hoverable:true
        }
    });

    //Display horizontal graph
    var d1_h = [];
    for (var i = 0; i <= 5; i += 1)
        d1_h.push([parseInt(Math.random() * 30),i ]);

    var d2_h = [];
    for (var i = 0; i <= 5; i += 1)
        d2_h.push([parseInt(Math.random() * 30),i ]);

    var d3_h = [];
    for (var i = 0; i <= 5; i += 1)
        d3_h.push([ parseInt(Math.random() * 30),i]);
                
    var ds_h = new Array();
    ds_h.push({
        data:d1_h,
        bars: {
            horizontal:true, 
            show: true, 
            barWidth: 0.2, 
            order: 1,
            lineWidth : 2
			
        }
    });
ds_h.push({
    data:d2_h,
    bars: {
        horizontal:true, 
        show: true, 
        barWidth: 0.2, 
        order: 2
    }
});
ds_h.push({
    data:d3_h,
    bars: {
        horizontal:true, 
        show: true, 
        barWidth: 0.2, 
        order: 3
    }
});

 
//add tooltip event
$(".bars").bind("plothover", function (event, pos, item) {
    if (item) {
        if (previousPoint != item.datapoint) {
            previousPoint = item.datapoint;
 
            //delete de precedente tooltip
            $('.tooltip-with-bg').remove();
 
            var x = item.datapoint[0];
 
            //All the bars concerning a same x value must display a tooltip with this value and not the shifted value
            if(item.series.bars.order){
                for(var i=0; i < item.series.data.length; i++){
                    if(item.series.data[i][3] == item.datapoint[0])
                        x = item.series.data[i][0];
                }
            }
 
            var y = item.datapoint[1];
 
            showTooltip(item.pageX+5, item.pageY+5,x + " = " + y);
 
        }
    }
    else {
        $('.tooltip-with-bg').remove();
        previousPoint = null;
    }
 
});
 



/* Pie charts */
	
	$(function () {
		var data = [];
		var series = Math.floor(Math.random()*10)+1;
		for( var i = 0; i<series; i++)
		{
			data[i] = { label: "Series"+(i+1), data: Math.floor(Math.random()*100)+1 }
		}
	
	$.plot($("#graph1"), data, 
	{
			series: {
				pie: { 
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 2/3,
						formatter: function(label, series){
							return '<div style="font-size:11px;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						threshold: 0.1
					}
				}
			},
			legend: {
				show: false
			},
			grid: {
				hoverable: false,
				clickable: true
			},
	});
	$("#interactive").bind("plothover", pieHover);
	$("#interactive").bind("plotclick", pieClick);
	
	$.plot($("#graph2"), data, 
	{
			series: {
				pie: { 
					show: true,
					radius:300,
					label: {
						show: true,
						formatter: function(label, series){
							return '<div style="font-size:11px;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						threshold: 0.1
					}
				}
			},
			legend: {
				show: false
			},
			grid: {
				hoverable: false,
				clickable: true
			}
	});
	$("#interactive").bind("plothover", pieHover);
	$("#interactive").bind("plotclick", pieClick);
	});
	
	function pieHover(event, pos, obj) 
	{
		if (!obj)
					return;
		percent = parseFloat(obj.series.percent).toFixed(2);
		$("#hover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
	}
	function pieClick(event, pos, obj) 
	{
		if (!obj)
					return;
		percent = parseFloat(obj.series.percent).toFixed(2);
		alert(''+obj.series.label+': '+percent+'%');
	}



/* Updating graphs real-time */
$(function () {
    // we use an inline data source in the example, usually data would
    // be fetched from a server
    var data = [], totalPoints = 300;
    function getRandomData() {
        if (data.length > 0)
            data = data.slice(1);

        // do a random walk
        while (data.length < totalPoints) {
            var prev = data.length > 0 ? data[data.length - 1] : 50;
            var y = prev + Math.random() * 10 - 5;
            if (y < 0)
                y = 0;
            if (y > 100)
                y = 100;
            data.push(y);
        }

        // zip the generated y values with the x values
        var res = [];
        for (var i = 0; i < data.length; ++i)
            res.push([i, data[i]])
        return res;
    }

    // setup control widget
    var updateInterval = 1000;
    $("#updateInterval").val(updateInterval).change(function () {
        var v = $(this).val();
        if (v && !isNaN(+v)) {
            updateInterval = +v;
            if (updateInterval < 1)
                updateInterval = 1;
            if (updateInterval > 2000)
                updateInterval = 2000;
            $(this).val("" + updateInterval);
        }
    });

    // setup plot
    var options = {
        series: { shadowSize: 0 }, // drawing is faster without shadows
        yaxis: { min: 0, max: 120 },
        xaxis: { show: false },
		
   colors: ["#2686d2"],
			series: {
					   lines: { 
							lineWidth: 1, 
							fill: true,
							fillColor: { colors: [ { opacity: 0.5 }, { opacity: 1.0 } ] },
							steps: false ,
							show:true
	
						},points: {show: false }
				   }
		};
    var plot = $.plot($(".autoUpdate"), [ getRandomData() ], options);

    function update() {
        plot.setData([ getRandomData() ]);
        // since the axes don't change, we don't need to call plot.setupGrid()
        plot.draw();
        
        setTimeout(update, updateInterval);
    }

    update();
});

	
});
//////////////////////
