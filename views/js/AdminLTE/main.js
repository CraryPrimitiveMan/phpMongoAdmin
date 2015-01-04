$(function() {
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }
    var db = getUrlParam('db');
    var collection = getUrlParam('collection');

    // $('#save').click(function(){
    //     var content = $('#content').val();
    //     $.post('index.php?action=update&db=' + db + '&collection=' + collection,{content:content},function(result){
    //         console.log(result);
    //     });
    // });

    $('.edit-btn').click(function(){
        var id = $(this).attr('data-id');
        $.get('index.php?action=view&db=' + db + '&collection=' + collection + '&id=' + id, function(data){
            $('#content').val(jsl.format.formatJson(data));
        });
    });

    $('.add-btn').click(function(){
        $('#content').val('');
    });
});