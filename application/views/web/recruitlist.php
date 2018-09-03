<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0"/>
  <script src='<?= $base_uri ?>/js/jquery-1.11.1.min.js' type='text/javascript'></script>
  <title>招聘列表</title>
</head>
<body>
<div id="Recruit_List">
<!--  <div class="item" data-id="">-->
<!--    <div class="left">-->
<!--      <span class="name">昆山开发区青春饭餐厅</span>-->
<!--      <span class="post">UI设计师</span>-->
<!--      <span class="tags">玉江镇</span>-->
<!--      <span class="tags">玉江镇</span>-->
<!--      <span class="tags">玉江镇</span>-->
<!--      <span class="wages">3-6k</span>-->
<!--    </div>-->
<!--  </div>-->

</div>

</body>
</html>

<style>
  body{margin:0px}
  #Recruit_List{max-width:550px;margin:0px auto}
  .item{padding:11px 8px;position:relative;border-bottom:3px solid rgb(242, 244, 247)}
  .item .left{display:inline-block;vertical-align:middle;max-width:80%}
  .item .left span{display:block}
  .item .left .name{color:#303030;font-size:16px;}
  .item .left .post{font-size:14px;margin:6px 0px;color:rgb(128, 128, 128)}
  .item .left .wages{color:#F38D81;font-size:15px;position:absolute;right:10px;top:15px}
  .item .left .tags{display:inline-block;background-color:rgb(244, 246, 249);padding:3px 5px;font-size:12px;margin-right:5px;color:rgb(128, 128, 128)}
</style>

<script>
  $(function() {
    var page = 0;
    var is_load_item = false;

    var apiUrl = 'http://www.yikework.com/api/enterprise/recruitList';

    function loadItems(all) {
      if(is_load_item) return;
      is_load_item = true;
      var url = apiUrl + '?status=1&page=' + page;
      if(all == true) url = apiUrl + '?status=1&page=' + page + '&offset=0';
      $.ajax({
        url: url,
        type: "GET",
        dataType: "json", success: function(result) {
          var html = '';
          for(var i = 0; i < result.data.length; i++) {
            var data = result.data[i];
            var wages = data.wages;
            var updated_at = '';
            if(data.updated_at) updated_at = data.updated_at.substr(0,10);
            if(data.wages1 == 0 && data.wages2 == 0) wages = '面议';
            else if(data.wages1 > 0  && data.wages2 == -1) wages = data.wages1 + '以上';
            else wages = data.wages1 + '-' + data.wages2;
            html += '<div class="item" data-id="' + data.id + '"><div class="left">';
            html += '<span class="name">' + data.shopName + '</span>';
            html += '<span class="post">' + data.workPost + '</span>';
            html += '<span class="tags">' + data.areaName + '</span>';
            html += '<span class="tags">' + data.industryName + '</span>';
            if(data.tagNames.lenght > 0) {
              html += '<span class="tags">' + data.tagNames[0] + '</span>';
            }
            html += '<span class="wages">' + wages + '</span>';
            html += '</div></div>';
          }
          if(html != '') {
            $('#Recruit_List').append(html);
            is_load_item = false;
            page++;
            if(all == true) {
              var scroll = parseInt(getCookie('scroll'));
              $(document).scrollTop(scroll);
            }
          }

          $('.item').click(function(){
            var id = $(this).attr('data-id');
            setCookie('page',page,60);
            setCookie('scroll',$(document).scrollTop(),160);
            location.href = '<?= $base_uri?>' + '/web/recruitdetail?id=' + id;
          })

        }
      });
    }

    if(getCookie('page') !== null) {
      page = parseInt(getCookie('page'));
      loadItems(true);
    } else {
      loadItems();
    }

    function getDocumentTop() {
      var scrollTop = 0, bodyScrollTop = 0, documentScrollTop = 0;
      if (document.body) {
        bodyScrollTop = document.body.scrollTop;
      }
      if (document.documentElement) {
        documentScrollTop = document.documentElement.scrollTop;
      }
      scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
      return scrollTop;
    }
    //可视窗口高度

    function getWindowHeight() {
      var windowHeight = 0;
      if (document.compatMode == "CSS1Compat") {
        windowHeight = document.documentElement.clientHeight;
      } else {
        windowHeight = document.body.clientHeight;
      }
      return windowHeight;
    }
    //滚动条滚动高度
    function getScrollHeight() {
      var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
      if (document.body) {
        bodyScrollHeight = document.body.scrollHeight;
      }
      if (document.documentElement) {
        documentScrollHeight = document.documentElement.scrollHeight;
      }
      scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
      return scrollHeight;
    }
    window.onscroll = function () {
      //监听事件内容
      if (getScrollHeight() == getWindowHeight() + getDocumentTop()) {
        //当滚动条到底时,这里是触发内容
        loadItems();
      }
    };

    function setCookie(name,value,s) {
      var exp = new Date();
      exp.setTime(exp.getTime() + s*1000);
      document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
    }

    function getCookie(name) {
      var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
      if(arr=document.cookie.match(reg))
        return unescape(arr[2]);
      else
        return null;
    }


  })
</script>
