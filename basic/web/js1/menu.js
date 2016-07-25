$(function(){
    $(document).on('click','.subcatalog',function(){
          var mid = $(this).attr('mid');//获取当前定位标记
       // alert(mid);
          var aid = $('#'+mid+'').attr('aid');//获取定位标记下的子级定位位置
      //  alert(aid);
        $('#del'+mid).html('<input type="button" class="del" mid="'+mid+'" style="width: 100px;height: 32px; background: #f00" value="删除子目录">');
        if(aid=="一"){

          //  $('#'+mid+'+').html('<input type="button" class="del" mid="'+mid+'" style="width: 100px;height: 32px; background: #f00" value="删除子目录">');

          var bid  = "二";
          var nid  = mid+1;//定位change事件效果位置
                           //fid  定自己类型值name值
            $('#'+mid+'').parent('span').html('<p style="margin-top:6px;margin-left:35px;"><span style="width: 120px; padding-top: 6px; margin-left: 160px;">子目录'+aid+' : </span><input  style="width: 200px;height: 32px;" type="text" name="'+mid+'[]" placeholder="子目录'+aid+'"><select style="width: 100px;height: 32px;margin-left:10px;" name="'+mid+'genre[]" class="genre" kid="'+nid+'genre" fid="'+mid+'mold"><option value="0">选择类型</option><option value="click">点击</option><option value="view">链接地址</option></select><span id="'+nid+'genre"> </span></p><span><span id="'+mid+'" aid="'+bid+'"></span></span>');

        }else if(aid=="二"){
             bid = "三";
            var nid  = mid+2;
            $('#'+mid+'').parent('span').html('<p style="margin-top:6px;margin-left:35px;"><span style="width: 120px; padding-top: 6px; margin-left: 160px;">子目录'+aid+' : </span><input  style="width: 200px;height: 32px;" type="text" name="'+mid+'[]" placeholder="子目录'+aid+'"><select style="width: 100px;height: 32px;margin-left:10px;" name="'+mid+'genre[]" class="genre" kid="'+nid+'genre" fid="'+mid+'mold"><option value="0">选择类型</option><option value="click">点击</option><option value="view">链接地址</option></select><span id="'+nid+'genre"> </span></p><span><span id="'+mid+'" aid="'+bid+'"></span></span>');

        }else if(aid=="三"){
             bid = "四";
            var nid  = mid+3;
            $('#'+mid+'').parent('span').html('<p style="margin-top:6px;margin-left:35px;"><span style="width: 120px; padding-top: 6px; margin-left: 160px;">子目录'+aid+' : </span><input  style="width: 200px;height: 32px;" type="text" name="'+mid+'[]" placeholder="子目录'+aid+'"><select style="width: 100px;height: 32px;margin-left:10px;" name="'+mid+'genre[]" class="genre" kid="'+nid+'genre" fid="'+mid+'mold"><option value="0">选择类型</option><option value="click">点击</option><option value="view">链接地址</option></select><span id="'+nid+'genre"> </span></p><span><span id="'+mid+'" aid="'+bid+'"></span></span>');

        }else if(aid=="四"){
             bid = "五";
            var nid  = mid+4;
            $('#'+mid+'').parent('span').html('<p style="margin-top:6px;margin-left:35px;"><span style="width: 120px; padding-top: 6px; margin-left: 160px;">子目录'+aid+' : </span><input  style="width: 200px;height: 32px;" type="text" name="'+mid+'[]" placeholder="子目录'+aid+'"><select style="width: 100px;height: 32px;margin-left:10px;" name="'+mid+'genre[]" class="genre" kid="'+nid+'genre" fid="'+mid+'mold"><option value="0">选择类型</option><option value="click">点击</option><option value="view">链接地址</option></select><span id="'+nid+'genre"> </span></p><span><span id="'+mid+'" aid="'+bid+'"></span></span>');

        }else if(aid=="五"){
            bid = "六";
            var nid  = mid+5;
            $('#'+mid+'').parent('span').html('<p style="margin-top:6px;margin-left:35px;"><span style="width: 120px; padding-top: 6px; margin-left: 160px;">子目录'+aid+' : </span><input  style="width: 200px;height: 32px;" type="text" name="'+mid+'[]" placeholder="子目录'+aid+'"><select style="width: 100px;height: 32px;margin-left:10px;" name="'+mid+'genre[]" class="genre" kid="'+nid+'genre" fid="'+mid+'mold"><option value="0">选择类型</option><option value="click">点击</option><option value="view">链接地址</option></select><span id="'+nid+'genre"> </span></p><span><span id="'+mid+'" aid="'+bid+'"></span></span>');

        }else{
            alert('最多存五个子目录哦!!!');
        }

       // alert(bid);
    })
    //删除子目录
    $(document).on('click','.del',function(){
        var mid = $(this).attr('mid');//获取当前定位标记
        var aid = $('#'+mid+'').attr('aid');//获取定位标记下的子级定位位置
       //alert(aid);
        if(aid=='六'){
         var bid = "五";
            $('#'+mid+'').parent('span').parent('span').html('<span id="'+mid+'" aid="'+bid+'"></span>');
        }else if(aid=='五'){
             bid = "四";
            $('#'+mid+'').parent('span').parent('span').html('<span id="'+mid+'" aid="'+bid+'"></span>');
        }else if(aid=='四'){
            bid = "三";
            $('#'+mid+'').parent('span').parent('span').html('<span id="'+mid+'" aid="'+bid+'"></span>');
        }else if(aid=='三'){
            bid = "二";
            $('#'+mid+'').parent('span').parent('span').html('<span id="'+mid+'" aid="'+bid+'"></span>');
        }else if(aid=='二'){
            $('#'+mid+'').parent('span').parent('span').html('<span id="'+mid+'" aid="一"></span>');
            $('#del'+mid).html('<select style="width: 100px;height: 32px;" name="'+mid+'genre[]" class="genre" kid="'+mid+'genre" fid="'+mid+'mold"><option value="0">选择类型</option><option value="click">点击</option><option value="view">链接地址</option></select><span id="'+mid+'genre"></span>');
        }
    })

    //类型样式
    $(document).on('change','.genre',function(){
        var genre = $(this).val();
        var kid   = $(this).attr('kid');
        var fid   = $(this).attr('fid');
       // var mid =
      //   alert(fid);
        if(genre==0){
            $('#'+kid+'').html('');
            alert('请重新选择此类型');

        }else if(genre=='click'){
            $('#'+kid+'').html('<input style="width: 50px;height: 32px;" type="hidden" name="'+fid+'[]" value="click" required=""/>&nbsp;&nbsp;&nbsp;&nbsp;点击');
        }else if(genre=='view'){
            $('#'+kid+'').html('&nbsp;&nbsp;&nbsp;&nbsp;<input style="width: 200px;height: 32px;" type="text" name="'+fid+'[]" required=""/>');
        }
    })
})
