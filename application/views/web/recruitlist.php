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
<!--      <span class="post">UI设计师</span>-->
<!--      <span class="area">昆市市 | 玉江镇</span>-->
<!--      <span class="wages">3-6k</span>-->
<!--      <span class="date">07月31日</span>-->
<!--      <span class="name">昆山开发区青春饭餐厅</span>-->
<!--      <span class="ppnumber">150-500人 | </span>-->
<!--      <span class="industry">服务业</span>-->
<!--    </div>-->
<!--  </div>-->

</div>

</body>
</html>

<style>
  body{margin:0px}
  #Recruit_List{max-width:550px;margin:0px auto}
  .item{padding:15px 8px;position:relative;border-bottom:3px solid rgba(20,126,251,0.3)}
  .item .left{display:inline-block;vertical-align:middle;max-width:80%}
  .item .left span{display:block}
  .item .left .wages{color:rgb(128,137,254);font-size:17px;position:absolute;right:10px;top:15px}
  .item .left .date{color:rgb(122,122,122);font-size:14px;position:absolute;right:10px;top:42px;}
  .item .left .post{color:rgb(60,60,60);font-size:18px;font-weight:500}
  .item .left .name{color:#303030;font-size:16px;margin-top:10px}
  .item .left .area{font-size:14px;color:rgb(122,122,122);}
  .item .left .ppnumber{display:inline-block;font-size:14px;color:rgb(122,122,122)}
  .item .left .industry{display:inline-block;font-size:14px;color:rgb(122,122,122);margin-left:4px}
</style>

<script>
  $(function() {
    var page = 0;
    var is_load_item = false;

    var apiUrl = 'http://101.132.65.153/api/enterprise/recruitList';

    function loadItems(all) {
      if(is_load_item) return;
      is_load_item = true;
      var url = apiUrl + '?page=' + page;
      if(all == true) url = apiUrl + '?page=' + page + '&offset=0';
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
            if(data.wages1 && data.wages2) wages = data.wages1 / 1000 + '-' + data.wages2 / 1000 + 'k';
            html += '<div class="item" data-id="' + data.id + '"><div class="left">';
            html += '<span class="post">' + data.workPost + '</span>';
            html += '<span class="area">' + data.areaNameAll.replace(',',' | ') + '</span>';
            html += '<span class="wages">' + wages + '</span>';
            html += '<span class="date">' + updated_at + '</span>';
            html += '<span class="name">' + data.shopName + '</span>';
            html += '<span class="ppnumber">150-500人 |</span>';
            html += '<span class="industry">' + data.industryName + '</span>';
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
