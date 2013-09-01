// JavaScript Document

var xtralink = 'non';

function maFonctionAjax(texte){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=discussion',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById){
				document.getElementById('affichefichier').innerHTML = OAjax.responseText;
                window.location = "index.php?file=Admin";
			}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('texte='+texte+'');
	$(document).trigger('close.facebox');
}

function screenon(lien,lien2){
	xtralink = lien2;
	document.getElementById('iframe').innerHTML = '<iframe style="border: 0" width="100%" height="80%" src="'+lien+'"></iframe>';
	if(condition_js == 1) screenoff();
	else document.getElementById("screen").style.display="block";
}

function screenoff(){
	document.getElementById('screen').style.display='none';
	if (xtralink != 'non') window.location = xtralink;
}

function maFonctionAjax2(texte,type){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=notification',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById){
				document.getElementById('texte').value = '';
				document.getElementById('type').value = '';
			}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('texte='+texte+'&type='+type+'');
	$(document).trigger('close.facebox')
}

function maFonctionAjax3(texte){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','modules/'+texte+'/menu/'+lang_nuked+'/menu.php',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById) document.getElementById('1').innerHTML = OAjax.responseText;
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send();
}

function del(id){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=notification&op=delete',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById) {}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('id='+id+'');
}

$(document).ready(function(){
    $('#pageAll').change(function() {
        if ($(this).prop('checked') == true) {
            $('.pageCheckbox').prop('checked', true);
        }
        else {
            $('.pageCheckbox').prop('checked', false);
        }
    });

    $('#groupAll').change(function() {
        if ($(this).prop('checked') == true) {
            $('.groupCheckbox').prop('checked', true);
        }
        else {
            $('.groupCheckbox').prop('checked', false);
        }
    });

    $('.groupCheckbox').change(function() {
        if ($(this).prop('checked') == false) {
            $('#groupAll').prop('checked', false);
        }
        else if($(this).prop('checked') == true) {
            if($('.groupCheckbox:checked').length == $('.groupCheckbox').length) {
                $('#groupAll').prop('checked', true);
            }
        }
    });

    $('.pageCheckbox').change(function() {
        if ($(this).prop('checked') == false) {
            $('#pageAll').prop('checked', false);
        }
        else if($(this).prop('checked') == true) {
            if($('.pageCheckbox:checked').length == $('.pageCheckbox').length) {
                $('#pageAll').prop('checked', true);
            }
        }
    });

    if($('.groupCheckbox:checked').length == $('.groupCheckbox').length) {
        $('#groupAll').prop('checked', true);
    }

    if($('.pageCheckbox:checked').length == $('.pageCheckbox').length) {
        $('#pageAll').prop('checked', true);
    }
    
    // Return a helper with preserved width of cells
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
    		$(this).width($(this).width());
    	});
    	return ui;
    };
    
    $("#nkSortable tbody").sortable({
        helper: fixHelper,
        handle: '.icon-sortable',
        cursor: 'move',
        opacity: 0.75,
        revert: 200,
        tolerance: 'pointer',
        dropOnEmpty: true,
        connectWith: '#nkSortableTrash',
    }).disableSelection();
    
    $("#nkSortableTrash").sortable({
        dropOnEmpty : true,
        forcePlaceholderSize: false,
        update: function(event, ui) {
            if(this.id == 'nkSortableTrash') {
                name = $(ui.item).children('td:nth-child(2)').html();
                delGroup($.trim(name), $(ui.item).attr('id'));
            }
        },
        over: function (){
            $(this).addClass('nkSortableTrashHover');
        },
        out: function (){
            $(this).removeClass('nkSortableTrashHover');
        }
    }).disableSelection();
    
    $("#nkSortableTrash").click(function(){
        alert('Pour supprimer un groupe faites le glisser dans la corbeille.');
    });
});
