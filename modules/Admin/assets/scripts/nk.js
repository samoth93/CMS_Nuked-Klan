$(document).ready(function(){
    // Initialize forms styling
    $(".select").chosen();
    $("select, .check, .check :checkbox, input:radio, input:file").uniform();


    // Initialize tooltips class
    $('.tipN').tipsy({gravity: 'n',fade: true, html:true});
    $('.tipS').tipsy({gravity: 's',fade: true, html:true});
    $('.tipW').tipsy({gravity: 'w',fade: true, html:true});
    $('.tipE').tipsy({gravity: 'e',fade: true, html:true});

    // Initialize modal
    $('a[rel=modal]').each(function(){
        $(this).click(function(){
            var titleDialog = $(this).data('title');
            var closeButton = 'Close';

            if ($(this).data('close') != undefined) {
                closeButton = $(this).data('close');
            }

            $.ajax({
                scriptCharset:"iso-8859-1",
                type: "GET",
                url: $(this).attr('href'),
                success:function(data){
                    $('#nkDialog').html(data);
                    $('#nkDialog').dialog({
                        title:titleDialog,
                        modal:true,
                        draggable:false,
                        resizable:false,
                        width:'auto',
                        maxHeight: $(window).innerHeight() - 50,
                        dialogClass: "ui-dialog-no-close",
                        buttons: [
                            {
                                text: closeButton,
                                click: function() {
                                    $( this ).dialog( "close" );
                                }
                            }
                        ]
                    });
                },
                error:function(){
                    $('#nkDialog').html("ERROR !");
                }
            });
            return false;
        });
    });

    // Initialize login form validation
    $(".validateForm").validationEngine();

    // Login ajax
    $('#login').submit(function(){
        if($("#login").validationEngine('validate') === true) {
            $('#loginMessage').dialog({
                modal:true,
                resizable:false,
                draggable:false,
                open: function(){
                    $.ajax({
                        scriptCharset:"iso-8859-1",
                        type: "POST",
                        url: "index.php?file=Admin&page=login&nuked_nude=admin",
                        data: {
                                mail:$('#loginMail').val(),
                                password:$('#loginPassword').val()
                              },
                        success:function(data){
                            $('#loginMessage').removeClass('ajaxLoading');
                            $('#loginMessage').html('<p>'+data+'</p>');
                        }
                    });
                }
            });
            return false;
        }
    });

    //===== Add class on #content resize. Needed for responsive grid =====//
    $('#content').resize(function () {
      var width = $(this).width();
        if (width < 769) {
            $(this).addClass('under');
        }
        else { $(this).removeClass('under') }
    }).resize(); // Run resize on window load

    //===== User nav dropdown =====//

    $('a.leftUserDrop').click(function () {
        $('.leftUser').slideToggle(200);
    });
    $(document).bind('click', function(e) {
        var $clicked = $(e.target);
        if (! $clicked.parents().hasClass("leftUserDrop"))
        $(".leftUser").slideUp(200);
    });

    //===== Add classes for sub sidebar detection =====//

    if ($('div').hasClass('secNav')) {
        $('#sidebar').addClass('with');
        //$('#content').addClass('withSide');
    }
    else {
        $('#sidebar').addClass('without');
        $('#content').css('margin-left','100px');//.addClass('withoutSide');
        $('#footer > .wrapper').addClass('fullOne');
        };

    //== Adding class to :last-child elements ==//

    $(".subNav li:last-child a, .formRow:last-child, .userList li:last-child, table tbody tr:last-child td, .breadLinks ul li ul li:last-child, .fulldd li ul li:last-child, .niceList li:last-child").addClass("noBorderB");

    //===== 2 responsive buttons (320px - 480px) =====//

    $('.iTop').click(function () {
        $('#sidebar').slideToggle(100);
    });

    $('.iButton').click(function () {
        $('.altMenu').slideToggle(100);
    });

    $('.userNav a.search').click(function () {
        $('.topSearch').fadeToggle(150);
    });

    //===== Collapsible elements management =====//

    $('.exp').collapsible({
        defaultOpen: 'current',
        cookieName: 'navAct',
        cssOpen: 'subOpened',
        cssClose: 'subClosed',
        speed: 200
    });

    $('.opened').collapsible({
        defaultOpen: 'opened,toggleOpened',
        cssOpen: 'inactive',
        cssClose: 'normal',
        speed: 200
    });

    $('.closed').collapsible({
        defaultOpen: '',
        cssOpen: 'inactive',
        cssClose: 'normal',
        speed: 200
    });

    //===== Notification boxes =====//

    $('.nNoteHideable').click(function() {
        $(this).fadeTo(200, 0.00, function(){ //fade
            $(this).slideUp(200, function() { //slide up
                $(this).remove(); //then remove from the DOM
            });
        });
    });

    //===== Breadcrumbs =====//

    $('#breadcrumbs').xBreadcrumbs();
});
