/**
 * Created by steve on 8/15/15.
 */

var newFileName = '';

$(function() {
	$("#file-list").select2({
		placeholder: "Select a file . . .",
		allowClear: true
	});

	/* Bluezed Scroll to Top credit goes to https://github.com/bluezed/yii2-scroll-top */
	var btnScroller = $('#btn-top-scroller');
	var scrollerTriggerPoint = $('html, body').offset().top + 150;

	$(document).on('scroll', function() {
		var pos = $(window).scrollTop();
		if (pos > scrollerTriggerPoint && !btnScroller.is(':visible')) {
			btnScroller.fadeIn();
		} else if (pos < scrollerTriggerPoint && btnScroller.is(':visible')) {
			btnScroller.fadeOut();
		}
	});

	btnScroller.on('click', function(e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: 0 }, 300);
	});
});
$('#file-list').on("select2:select", function (e) {
	if($('#file-list').select2('val') == 'New File') {
		if(!newFileName.length) {
			swal({
				title: 'Please provide a file name:',
				html: '<p><i>(excluding the \'.desktop\' extension)</i><br><input id="input-field" class="form-control" value="' + newFileName + '">',
				showCancelButton: true,
				confirmButtonColor: '#77216F',//'#3085d6',
				cancelButtonColor: '#AEA79F',//'#d33',
				closeOnConfirm: true,
				animation: "slide-from-top"
			}, function(isConfirm){
				if (isConfirm) {
					var name = $('#input-field').val();
					if (name.length)
						newFileName = name;
				}
				else {
					$('#file-list').select2('val', '');
					$('#guts').html('');
					$('#save').hide();
					$('#new-section').hide();
					return;
				}
			});
		}
	}
	else
		newFileName = '';
	$.ajax({
		method:'POST',
		url:'index.php',
		//dataType: 'JSON',
		data: {file: $('#file-list').select2('val')}
	})
		.done(function(data) {
			//var stuff = JSON.parse(data);
			//alert(stuff);
			$('#guts').html(data);
			setWidths();
			bindIt();
			($('#file-list').select2('val') == 'New File') ? $('#save').show() : $('#save').hide();
			$('#new-section').removeClass('hide');
		});
});
$('#save').on('click', function(e){
	$.ajax({
		method:'POST',
		url:'index.php',
		data: {data: $('form').serializeArray(), file: (newFileName.length ? newFileName : $('#file-list').select2('val'))}
	})
		.done(function(data) {
			var note = (newFileName.length) ? '<p><i>Remember to <b>sudo chown root:root</b> the file bfore you move or copy it to <b>' + dir + '</b></i></p>' : '';
			swal({
				type: 'success',
				html: '<p>Your file has been saved to:</p><br><h3>' + data + '</h3>' + note
			});
		});
});
$('.close').on('click', function(){
	$(this).parent().hide();
});
$('body').on('click', '.btn-addnew', function() {
	var x = $('#guts').children().length;
	var theOrange = orange[x];
	x = $(this).parent().parent().next().children().length + 2;
	var j = $(this).parent().parent().next();
	var n = j.find('span').width();
	var v = j.find('input').width();
	j.append('<div class="input-group">' +
		'<span class="input-group-addon right-text" id="basic-addon' + x + '" style="background-color:'+ theOrange + ';">Enter a name . . .</span>' +
		'<input type="text" class="form-control" placeholder="Enter a value . . ." aria-describedby="basic-addon' + x + '" name="v' + x +'_name" style="border-color:'+ theOrange + ';">' +
		'</div>');
	var k = j.find('.input-group').last();
	k.find('span').width(n);
	k.find('input').last().width(v);
	swal({
		type: 'success',
		html: '<p>A new item has been added.'
	});
	$('#save').show();
});
$('#new-section').on('click', function(){
	var x = $('#guts').children().length;
	var theOrange = orange[x];
	$('#guts').append('<div class="panel panel-info" style="border-color:{theOrange};">' +
		'<div class="panel-heading" style="background-color:{theOrange};border-color:{theOrange};">' +
		'<div class="col-md-11">' +
		'<h4>New Section</h4><input type="hidden" name="s' + x + '_[New Section]" value="[New Section]">' +
		'</div>' +
		'<div class="col-md-1">' +
		'<button type="button" class="btn btn-addnew" aria-label="Add New Item"><span class="glyphicon glyphicon-plus"></span></button>' +
		'</div>' +
		'</div>' +
		'<div class="panel-body">');
	swal({
		type: 'success',
		html: '<p>A new section has been added.'
	});
	$('#save').show();
});
$('body').on('click', 'blockquote', function(){
	var $that = $(this);
	swal({
		title: "Edit Comment(s)",
		html: '<p><textarea id="input-field" class="form-control" rows="10">' + $that.text().replace(/#/g, '\n#') + '</textarea>',
		showCancelButton: true,
		confirmButtonColor: '#77216F',//'#3085d6',
		cancelButtonColor: '#AEA79F',//'#d33',
		closeOnConfirm: false,
		animation: "slide-from-top"
	}, function(){
		var name = $('#input-field').val();
		if(name.length)
			name = name.replace(/\n/g, '<br>');

		$that.find('p').html(name);
		$that.find('input').val(name);
		swal({
			type: 'success',
			html: '<p>The comment(s) has(have) been changed.'
		});
		$('#save').show();
	});
});
$('body').on('click', 'h4, .input-group-addon', function(){
	var $that = $(this);
	swal({
		title: "Edit " + $(this).prop('title'),
		html: '<p><input id="input-field" class="form-control" value="'+$(this).text()+'">',
		showCancelButton: true,
		confirmButtonColor: '#77216F',//'#3085d6',
		cancelButtonColor: '#AEA79F',//'#d33',
		closeOnConfirm: false,
		animation: "slide-from-top"
	}, function(){
		var name = $('#input-field').val();
		if(name.length) {
			$that.text(name);
			var p = $that.next();
			var newName = p.prop('name');
			newName = newName.substr(0, newName.indexOf('_')+1) + name;
			p.prop('name', newName);
			if($that.is('h4'))
				p.val('[' + name + ']');
			swal({
				type: 'success',
				html: '<p>Name has been changed to:<br><h3>' + name + '</h3>'
			});
		}
		$('#save').show();
	});
});
function setWidths() {
	var _width = 0;
	$('.input-group-addon').each(function (index) {
		_width = ($(this).width() > _width ? $(this).width() : _width);
	});
	$('.input-group-addon').each(function (index) {
		$(this).width(_width);
	});
}
function bindIt() {
	$('form :input').on('change', function(e){
		$('#save').show();
	});
}
