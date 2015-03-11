$(function() {
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }
    var db = getUrlParam('db');
    var collection = getUrlParam('collection');
    var server = getUrlParam('server');

    $('textarea.show').each(function(){
        var content = $(this).attr('content');
        if (!!content) {
            $(this).val(jsl.format.formatJson(content.replace(/\\\//g, '/')));
        }
    });

    $('textarea.show').dblclick(function(){
        if($(this).attr('readonly')) {
            edit($(this));
        }
    });

    $('#shell').val($('#shell').attr('content'));

    $('.edit-btn').click(function(){
        edit($(this).parent().parent().find('textarea.show'));
    });

    $('.save-btn').click(function() {
        save($(this).parent().parent().find('textarea.show'));
    });

    $('.add-btn').click(function(){
        $('#content').val('');
        edit($('#content'));
    });

    $('.insert-btn').click(function(){
        save($('#content'));
    });

    $('.cancel-btn').click(function(){
        location.reload();
    });

    $('.execute-btn').click(function(){

    });

    var edit = function($elem) {
        // show one document
        $('.document').css('display', 'none');
        $elem.closest('.document').css('display', 'block');
        $elem.removeAttr('readonly').css('height', $elem[0].scrollHeight).focus();
        $('.edit-btn').css('display', 'none');
        $('.save-btn').css('display', 'inline-block');
        $('body').scrollTop(0);
    };

    var save = function($elem) {
        // show all documents
        var content = $elem.val();
        if (!!content) {
            $elem.val(jsl.format.formatJson(content.replace(/\\\//g, '/')));
            var url = 'index.php?action=update&db=' + db + '&collection=' + collection;
            if (!!server) {
                url += '&server=' + server;
            }
            $.post(url, {content:content}, function(result){
                console.log(result);
                result = JSON.parse(result);
                if (result['shell']) {
                    $('#shell').val(result['shell']);
                }
                if (result['updatedExisting']) {
                    $('.document').css('display', 'block');
                    $elem.attr('readonly', true).css('height', 150).blur();
                    $('.edit-btn').css('display', 'inline-block');
                    $('.save-btn').css('display', 'none');
                    $('.add-document').css('display', 'none');
                    $('body').scrollTop(0);
                } else {
                    $elem.val('');
                }
            });
        }
    };

    var triggerEdit = function($elem) {
        if ($elem.attr('readonly')) {
            edit($elem);
        } else {
            save($elem);
        }
    };

});