<script src="js1/jquery.js"></script>

<div class="container-fluid">
    <div class="row-fluid">

        <div class="span12">

            <!-- BEGIN STYLE CUSTOMIZER -->

            <div class="color-panel hidden-phone">

                <div class="color-mode-icons icon-color"></div>

                <div class="color-mode-icons icon-color-close"></div>

                <div class="color-mode">

                    <p>THEME COLOR</p>

                    <ul class="inline">

                        <li class="color-black current color-default" data-style="default"></li>

                        <li class="color-blue" data-style="blue"></li>

                        <li class="color-brown" data-style="brown"></li>

                        <li class="color-purple" data-style="purple"></li>

                        <li class="color-grey" data-style="grey"></li>

                        <li class="color-white color-light" data-style="light"></li>

                    </ul>

                    <label>

                        <span>Layout</span>

                        <select class="layout-option m-wrap small">

                            <option value="fluid" selected>Fluid</option>

                            <option value="boxed">Boxed</option>

                        </select>

                    </label>

                    <label>

                        <span>Header</span>

                        <select class="header-option m-wrap small">

                            <option value="fixed" selected>Fixed</option>

                            <option value="default">Default</option>

                        </select>

                    </label>

                    <label>

                        <span>Sidebar</span>

                        <select class="sidebar-option m-wrap small">

                            <option value="fixed">Fixed</option>

                            <option value="default" selected>Default</option>

                        </select>

                    </label>

                    <label>

                        <span>Footer</span>

                        <select class="footer-option m-wrap small">

                            <option value="fixed">Fixed</option>

                            <option value="default" selected>Default</option>

                        </select>

                    </label>

                </div>

            </div>

            <!-- END BEGIN STYLE CUSTOMIZER -->

            <h3 class="page-title">

                公众号列表
                <small>form components and widgets</small>

            </h3>

            <ul class="breadcrumb">

                <li>

                    <i class="icon-home"></i>

                    <a href="index.html">主页</a>

                    <span class="icon-angle-right"></span>

                </li>

                <li>

                    <a href="#">公众号管理</a>

                    <span class="icon-angle-right"></span>

                </li>

                <li><a href="#">自定义文字消息回复</a></li>

            </ul>

        </div>

    </div>

    <div class="portlet-body form">

        <div class="tabbable portlet-tabs">

            <div class="tab-content">

                <div class="tab-pane active" id="portlet_tab1">

                    <!-- BEGIN FORM-->


                    <form name="form1" id="form1">
                        <div class="form-horizontal">
                            <div class="control-group">

                                <label class="control-label">Medium Dropdown</label>

                                <div class="controls">
                                    <!--下拉菜单-->
                                    <select class="medium m-wrap" tabindex="1" id="select" name="w_id">
                                        <option value="0">---请选择---</option>
                                        <?php foreach($info as $k=>$v):?>
                                            <option value="<?=$v['w_id']?>"><?=$v['w_name']?></option>
                                        <?php endforeach?>
                                    </select>

                                </div>


                            </div>
                            <div id="s1" hidden="hidden">

                                <div class="control-group">
                                    <label class="control-label">选择回复方式</label>
                                    <div class="controls">
                                        <input type="radio" name="g_type" value="1" checked />文字
                                        <input type="radio" name="g_type" value="2" />图片
                                        <input type="radio" name="g_type" value="3" />语音
                                        <div id="form_2_membership_error"></div>
                                    </div>
                                    <br/>

                                    <div class="control-group">
                                        <label class="control-label">定义规则</label>
                                        <div class="controls">
                                            <input type="text" class="m-wrap large"  name="g_rule"/>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">回复内容</label>
                                        <div class="controls" id="text">
                                            <textarea class="span6 m-wrap" rows="3" name="g_reply" ></textarea>
                                        </div>
                                        <!--图片上传的位置-->


                                        <div class="controls" id="images" hidden="hidden">
                                            <input type="file" name="file" id="file_upload" class="form-control";>
                                        </div>

                                    </div>


                                    <div class="form-actions"  id="button">

                                        <button type="button" class="btn blue" onclick="fsubmit()"><i class="icon-ok"></i>定义</button>
                                    </div>
                                    <!-- END FORM-->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
    <div id="s2">

    </div>


    <script>

        //        function uploadByForm(){
        //            var formData = new FormData($("#myForm")[0]);
        //
        //           // var id = $("#select").val();
        //            var url = "index.php?r=reply/photo";
        //            $.ajax({
        //                url : url,
        //                type : 'POST',
        //                data : formData,
        //                processData : false,
        //                contentType : false,
        //                success : function(responseStr) {
        //                    alert("成功"+responseStr);
        //
        //                },
        //                error : function(responseStr) {
        //
        //                    alert("失败:"+responseStr);
        //                }
        //            });
        //        }


        //点击下拉菜单
        $(document).delegate('#select','change',function(){
            var w_id = $(this).val();
            if(w_id==0){
                $("#s1").hide();
                $("#s2").html('');
                return false;
            }
//验证对接是否成功
            $.get('index.php?r=index/check',{w_id:w_id},function(msg){
                if(msg==0){
                    $("#s1").hide();
                    $("#s2").html('');
                    alert("对接失败")
                }else{
                    $("#s1").show();
                }
            })

//最底部的显示添加的东西
            $.getJSON('index.php?r=reply/guize',{w_id:w_id},function(msg){
                html='<div class="portlet-body"><table class="table table-striped table-bordered table-hover table-full-width" id="sample_1"> <thead> <tr> <th>序号</th> <th>关键字</th> <th class="hidden-480">回复内容</th> <th class="hidden-480">操作</th></tr> </thead>'

                for(var i= 0; i<msg.length;i++){
                    html+=' <tbody> <tr> <td> '+msg[i].g_id+'</td> <td class="hidden-480">'+msg[i].g_rule+'</td> <td class="hidden-480">'+msg[i].g_reply+'</td> <td class="hidden-480"><a href="javascript:;" id="dele" g_id="'+msg[i].g_id+'">删除</a></td></tr></tbody>'
                }
                html+=' </table></div>';
                $('#s2').html(html)
            })
        })
        //点击定义时进行ajax传值
        //        $(document).delegate('button','click',function(){
        //            var g_reply=$('#g_reply').val();
        //            var g_rule=$('#g_rule').val();
        //            var w_id=$('#select').val();
        //            var g_type= $('input:radio:checked').val();
        //            htmla='<div class="portlet-body"><table class="table table-striped table-bordered table-hover table-full-width" id="sample_1"> <thead> <tr> <th>序号</th> <th>关键字</th> <th class="hidden-480">回复内容</th> <th class="hidden-480">操作</th></tr> </thead>'
        //            $.getJSON('index.php?r=reply/hui',{w_id:w_id,g_rule:g_rule,g_reply:g_reply,g_type:g_type},function(msg){
        //                // alert(msg);
        //                for(var i= 0; i<msg.length;i++){
        //                    htmla+=' <tbody> <tr> <td> '+msg[i].g_id+'</td> <td class="hidden-480">'+msg[i].g_rule+'</td> <td class="hidden-480">'+msg[i].g_reply+'</td> <td class="hidden-480"><a href="javascript:;" id="dele" g_id="'+msg[i].g_id+'">删除</a></td></tr></tbody>'
        //                }
        //                html+=' </table></div>';
        //                $('#s2').html(htmla)
        //
        //            })
        //        })

        function fsubmit(){
            var data = new FormData($('#form1')[0]);
            htmla='<div class="portlet-body"><table class="table table-striped table-bordered table-hover table-full-width" id="sample_1"> <thead> <tr> <th>序号</th> <th>关键字</th> <th class="hidden-480">回复内容</th> <th class="hidden-480">操作</th></tr> </thead>'

            $.ajax({
                url: 'index.php?r=reply/hui',
                type: 'POST',
                data: data,
                dataType: 'JSON',
                cache: false,
                processData: false,
                contentType: false,
                success: function(msg){


                    if(msg==2){
                        alert('图片超过1兆的最大值');
                    }else if(msg==3){
                        alert('语音超过2兆的最大值');
                    }else if(msg==4){
                        alert('定义失败,请清空缓存在操作');

                    }else{
                        for(var i= 0; i<msg.length;i++){
                            htmla+=' <tbody> <tr> <td> '+msg[i].g_id+'</td> <td class="hidden-480">'+msg[i].g_rule+'</td> <td class="hidden-480">'+msg[i].g_reply+'</td> <td class="hidden-480"><a href="javascript:;" id="dele" g_id="'+msg[i].g_id+'">删除</a></td></tr></tbody>'
                        }
                        html+=' </table></div>';
                        $('#s2').html(htmla)
                    }


                }
            })

        }


        //最底部的删除
        $(document).delegate('#dele','click',function(){
//        alert(111)
            var g_id=$(this).attr('g_id');
            _this=$(this).parent().parent();
            $.get('index.php?r=reply/del',{id:g_id},function(info){
                _this.remove();
            })
        })
        //点击进行切换时图片回复....
        $(document).delegate('input:radio:checked','click',function(){
            var g_type= $(this).val()
            if(g_type==1){
                $('#text').show();
                $('#images').hide();
            }else{
                $('#text').hide();
                $('#images').show();
            }
        })

    </script>

