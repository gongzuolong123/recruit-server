<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0"/>
  <script src='<?= $base_uri ?>/js/jquery-1.11.1.min.js' type='text/javascript'></script>
  <title>招聘列表</title>
</head>
<body>
<div id="Recruit_List">

</div>

</body>
</html>

<style>
  #Recruit_List{max-width:550px;margin:0px auto}
  .item{padding:15px 8px;box-shadow:0px 2px 10px 2px rgba(222,222,222,0.5);margin-bottom:10px;border-radius:6px}
  .item .left{display:inline-block;vertical-align:middle}
  .item .left span{display:block}
  .item .left .wages{color:orangered;font-size:15px;margin-top:7px}
  .item .left .name{color:#303030;font-size:18px;margin-bottom:7px;}
  .item .left .area{display:inline-block;font-size:11px;color:rgb(97,177,222);border-radius:5px;padding:1px 3px;margin-right:8px;border:1px solid #dedede}
  .item .left .industry{display:inline-block;font-size:11px;color:rgb(97,177,222);border-radius:5px;padding:1px 3px;border:1px solid #dedede}
</style>

<script>
  $(function() {
    var page = 0;
    var is_load_item = false;

    var apiUrl = 'http://101.132.65.153/api/enterprise/recruitList';

    function loadItems() {
      if(is_load_item) return;
      is_load_item = true;
      $.ajax({
        url: apiUrl + '?page=' + page,
        type: "GET",
        dataType: "json", success: function(result) {
          var html = '';
          for(var i = 0; i < result.data.length; i++) {
            var data = result.data[i];
            html += '<div class="item" data-id="' + data.id + '"><div class="left">';
            html += '<span class="name">' + data.enterpriseName + '</span>';
            html += '<span class="area">' + data.areaName + '</span>';
            html += '<span class="industry">' + data.industryName + '</span>';
            html += '<span class="wages">' + data.wages + '</span></div></div>';


          }
          if(html != '') {
            $('#Recruit_List').append(html);
            is_load_item = false;
            page++;
          }

          $('.item').click(function(){
            var id = $(this).attr('data-id');
            location.href = '<?= $base_uri?>' + '/web/recruitdetail?id=' + id;
          })

        }
      });
    }

    loadItems();

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


  })
</script>
