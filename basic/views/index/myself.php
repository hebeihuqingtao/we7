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

                <li><a href="#">自定义菜单</a></li>

            </ul>

        </div>

    </div>

   <div class="portlet-body form">

    <div class="tabbable portlet-tabs">

        <div class="tab-content">

            <div class="tab-pane active" id="portlet_tab1">

                <!-- BEGIN FORM-->

                <form action="index.php?r=index/zdy" class="form-horizontal" method="post">


                    <div class="control-group">

                        <label class="control-label">Medium Dropdown</label>

                        <div class="controls">

                            <select class="medium m-wrap" tabindex="1" id="select" name="who">
                                <option value="0">---请选择---</option>
                                <?php foreach($info as $k=>$v):?>
                                    <option value="<?=$v['w_id']?>"><?=$v['w_name']?></option>
                                <?php endforeach?>
                            </select>

                        </div>


                    </div>

                    <div class="control-group">

                        <label class="control-label">左</label>

                        <div class="controls">
                            <input type="text" class="m-wrap large" name="zuo" disabled=""/>
                            <button name="button" type="button" disabled class="btn" onclick='additem("tb")'>添加</button>

                            <table id="tb"></table>


                        </div>

                    </div>

                    <div class="control-group">

                        <label class="control-label">中</label>

                        <div class="controls">

                            <input type="text" class="m-wrap large" disabled="" name="zhong"/>
                            <button name="button" type="button" disabled class="btn" onclick='additem("tr")'>添加</button>
                            <table id="tr"></table>

                        </div>

                    </div>

                    <div class="control-group">

                        <label class="control-label">右</label>

                        <div class="controls">

                            <input type="text"  class="m-wrap large" disabled="" name="you"/>
                            <button name="button" type="button" disabled class="btn" onclick='additem("tc")'>添加</button>
                            <table id="tc"></table>

                        </div>

                    </div>


                    <div class="form-actions" hidden="" id="button">

                        <button type="submit" class="btn blue"><i class="icon-ok"></i>定义</button>

                        <button type="reset" class="btn">重填</button>

                    </div>

                </form>


                <!-- END FORM-->

             </div>

         </div>

      </div>

    </div>
</div>

<script>

    $(document).delegate('#select','change',function(){
        var w_id = $(this).val();
        if(w_id==0){
            $("input").attr("disabled",true);
            $(".btn").attr("disabled",true);
            $("#button").hide();

            return false;
        }
        $.get('index.php?r=index/check',{w_id:w_id},function(msg){
         if(msg==0){
             $("input").attr("disabled",true);
             $(".btn").attr("disabled",true);
             $("#button").hide();

             alert("对接失败")
         }else{
            $("input").attr("disabled",false);
            $(".btn").attr("disabled",false);
            $("#button").show();

         }
        })
    })



    var count=0 ;

    function additem(id)
    {
        var row,cell,str;
        row = document.getElementById(id).insertRow();

        if(row != null )
        {
            cell = row.insertCell();
            cell.innerHTML="<input id=\"St"+count+"\" type=\"text\" name=\"St"+count+"\" ><button name=\"button\" type=\"button\"  class=\"btn\" onclick=\'deleteitem(this,"+id+")'>删除</button>";
            count ++;
        }
    }
    function deleteitem(obj,id)
    {
//        alert(obj)
        var curRow = obj.parentNode.parentNode;
        id.deleteRow(curRow.rowIndex);
    }

    function getsub()
    {
        var re="";
        for (var    i = 0 ;i<count;i++)
        {
            re += document.getElementsByName("St"+i)[0].value;

        }
        document.getElementById("Hidden1").value=re;
    }


</script>

