<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0"/>
  <meta name="format-detection" content="telephone=yes"/>
  <script src='<?= $base_uri ?>/js/jquery-1.11.1.min.js' type='text/javascript'></script>
  <link href="<?php echo $base_uri ?>/bootstrap-3.3.1/css/bootstrap.min.css" rel="stylesheet">
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
<!--    <div class="labell">基本说明</div>-->
<!--    <table class="table table-bordered">-->
<!--      <tr><td style="width:85px">地区</td><td>玉山镇</td></tr>-->
<!--      <tr><td>工作地址</td><td>衡山路弥敦城5楼</td></tr>-->
<!--      <tr><td>招聘岗位</td><td>经理、主管、领班、收银员、服务员、传菜员，以上人数若干</td></tr>-->
<!--      <tr><td>岗位要求</td><td>包吃住，要求：年龄18--35以下。男女不限，无不良嗜好，管理层须有1--2年的管理经验。</td></tr>-->
<!--      <tr><td>联系人</td><td>徐经理</td></tr>-->
<!--      <tr><td>电话</td><td>0512--50175077</td></tr>-->
<!--    </table>-->
<!--  </div>-->

  <div id="call_phone"><i class="glyphicon glyphicon-earphone" aria-hidden="true"></i></div>
</div>

</body>
</html>

<style>
  #Recruit_Detail{max-width:550px;margin:0px auto}
  .top{padding:10px;padding-top:25px}
  .top span{display:block;}
  .top .title {font-size:18px;font-weight:600;display:inline-block;}
  .top .industry{display:inline-block;font-size:13px;color:rgb(97,177,222);border-radius:5px;padding:1px 3px;border:1px solid #dedede;margin-left:5px}
  .top .wages{font-size:15px;color:orangered;margin-top:7px}
  .explain_item{color:#303030;margin:0px 10px}
  .explain_item .labell{padding:10px;color:#3C8EFA;font-weight:600;font-size:18px}

  #call_phone{position:fixed;bottom:40px;right:30px;width:50px;height:50px;text-align:center;background-color:rgb(97,177,222);border-radius:50px;box-shadow:0px 0px 10px 5px rgba(97,177,222,0.5);}
  #call_phone i{line-height:50px;font-size:30px;color:#FFF;}

</style>

<script>
  $(function() {
    var apiUrl = 'http://www.yikework.com/api/enterprise/recruitDetail?id=' + '<?= $_GET['id']?>';
    var phone = '';

    function loadDetail() {
      $.ajax({
        url: apiUrl,
        type: "GET",
        dataType: "json", success: function(result) {
          var html = '';
          var data = result.data;
          phone = data.contactsPhone;
          html += '<div class="top"><span class="title">' + data.enterpriseName + '</span>';
          html += '<span class=industry>' + data.industryName + '</span>';
          html += '<span class="wages">' + data.wages + '</span></div>';
          html += '<div class="explain_item"><div class="labell">基本说明</div>';
          html += '<table class="table table-bordered">';
          html += '<tr><td style="width:85px">地区</td><td>' + data.areaName + '</td></tr>';
          html += '<tr><td>工作地址</td><td>' + data.workAddress + '</td></tr>';
          html += '<tr><td>招聘岗位</td><td>' + data.workPost + '</td></tr>';
          html += '<tr><td>岗位要求</td><td>' + data.workRequire + '</td></tr>';
          html += '<tr><td>联系人</td><td>' + data.contactsName + '</td></tr>';
          html += '<tr><td>电话</td><td><a href="tel:>' + data.contactsPhone + '">' + data.contactsPhone + '</a></td></tr>';
          html += '</table></div>';
          if(html != '') {
            $('#Recruit_Detail').append(html);
          }

        }

      });
    }

    loadDetail();

    $("#call_phone").click(function(){
      location.href = 'tel:' + phone;
    })



  })
</script>
