<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0"/>
  <script src='<?= $base_uri ?>/js/jquery-1.11.1.min.js' type='text/javascript'></script>
  <title>招聘列表</title>
</head>
<body>
<div id="Recruit_Detail">
<!--  <div class="top">-->
<!--    <span class="title">昆山正新轮胎</span>-->
<!--    <span class="industry">服务业服务业</span>-->
<!--    <span class="wages">3500元-4200元</span>-->
<!--  </div>-->
<!--  <div class="explain_item">-->
<!--    <div class="label">基本说明</div>-->
<!--    <div class="explain">-->
<!--      <span class="key">地区:</span>-->
<!--      <span class="value">玉山镇</span>-->
<!--      <span class="key">工作地址:</span>-->
<!--      <span class="value">衡山路弥敦城5楼</span>-->
<!--      <span class="key">招聘岗位:</span>-->
<!--      <span class="value">经理、主管、领班、收银员、服务员、传菜员，以上人数若干 </span>-->
<!--      <span class="key">岗位要求:</span>-->
<!--      <span class="value">包吃住，要求：年龄18--35以下。男女不限，无不良嗜好，管理层须有1--2年的管理经验。</span>-->
<!--      <span class="key">联系人:</span>-->
<!--      <span class="value">徐经理</span>-->
<!--      <span class="key">电话:</span>-->
<!--      <span class="value">0512--50175077</span>-->
<!--    </div>-->
<!--  </div>-->
</div>

</body>
</html>

<style>
  #Recruit_Detail{max-width:550px;margin:0px auto}
  .top{padding:10px}
  .top span{display:block;}
  .top .title {font-size:17px;font-weight:600;display:inline-block;}
  .top .industry{display:inline-block;font-size:13px;color:rgb(97,177,222);border-radius:5px;padding:1px 3px;border:1px solid #dedede;margin-left:5px}
  .top .wages{font-size:18px;font-weight:800;color:#3C8EFA;}
  .explain_item{border:1px solid #D0D0D0;border-radius:10px 10px 5px 5px;color:#313131}
  .explain_item .label{background-color:#D0D0D0;padding:10px;border-radius:10px 10px 0px 0px;color:#3C8EFA;font-weight:600;font-size:18px}
  .explain_item .explain{padding:10px}
  .explain_item .explain span{display:block;}
  .explain_item .explain .key{font-size:16px;}
  .explain_item .explain .value{margin-bottom:10px;font-size:14px}

</style>

<script>
  $(function() {
    var apiUrl = 'http://212.64.10.159/api/enterprise/recruitDetail?id=' + '<?= $_GET['id']?>';

    function loadDetail() {
      $.ajax({
        url: apiUrl,
        type: "GET",
        dataType: "json", success: function(result) {
          var html = '';
          var data = result.data;
          html += '<div class="top"><span class="title">' + data.enterpriseName + '</span>';
          html += '<span class=industry>' + data.industryName + '</span>';
          html += '<span class="wages">' + data.wages + '</span></div>';
          html += '<div class="explain_item"><div class="label">基本说明</div>';
          html += '<div class="explain"><span class="key">地区:</span><span class="value">' + data.areaName + '</span>';
          html += '<span class="key">工作地址:</span><span class="value">' + data.wordAddress + '</span>';
          html += '<span class="key">招聘岗位:</span><span class="value">' + data.wordPost + '</span>';
          html += '<span class="key">岗位要求:</span><span class="value">' + data.wordRequire + '</span>';
          html += '<span class="key">联系人:</span><span class="value">' + data.contactsName + '</span>';
          html += '<span class="key">电话:</span><span class="value">' + data.contactsPhone + '</span></div></div>';
          if(html != '') {
            $('#Recruit_Detail').append(html);
          }

        }

      });
    }

    loadDetail();



  })
</script>
