$(function () {
    // 카메라장치 존재여부 확인
    if (navigator.mediaDevices == null || navigator.mediaDevices.getUserMedia == null) {
        alert("카메라장치에 접근할 수 없습니다.");
        if (opener) window.close();
        return;
    }

    // 변수들...
    var videoInterval = null;
    var imageData = null;
    var canvas = document.getElementById("canvas");
    var context = canvas.getContext("2d");
    var video = document.getElementById("video");
    var videoObj = {video: true}; // 오디오는 사용하지 않는다. 만약 사용한다면 'audio': true 추가


    // 비디오 출력 받기
    navigator.mediaDevices.getUserMedia(videoObj).then(function (stream) {
      try {
        video.srcObject=stream;
        video.onloadedmetadata = function(e) {
          video.play();
        };        
      } catch (error) {
          video.src = URL.createObjectURL(stream);
          video.play();
      }
    }).catch(function(err) {
      alert(err);
    });

    // 스냅샷 찍기
    $('#snap').click(function() {
        clearInterval(videoInterval);
        imageData = canvas.toDataURL();
        $('#snap').attr('disabled', true);
        $('#save').attr('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        $('#resume').attr('disabled', false);
    });

    function urldecode(url) {
      return decodeURIComponent(url.replace(/\+/g, ' '));
    }    

    // 저장
    $('#save').click(function() {
      var r_id=$("#v_id").val();

      switch($('#v_type').val()) {
        case 'employees' :
          var r_url='/employee-pictures/update-photo/'+r_id;
          var delete_url='/employee-pictures/delete';
          break;
        default :
          var r_url='/user-pictures/update-photo/'+r_id;
          var delete_url='/user-pictures/delete';          
      }

      $.post(r_url,{data_image:imageData,format:'json'},function(data){
        if(data.result=='success') {
          if (opener) {

            if(data.id!=true) {
              var form=$('<form action="'+delete_url+'/'+data.id+'" method="post" accept-charset="utf-8" id="delete-photo-form">');
              form.append('<input value="'+opener.jQuery("#delete-photo-layer span").text()+'" class="btn btn-sm btn-outline-secondary btn-block" type="submit">');
              opener.jQuery("#delete-photo-layer").empty().append(form);
            }

            opener.showPhoto(urldecode(data.photo));
            window.close();
          }
        } else {
          alert(data.message);
        }
      },'json');
    });

    // 새로
    $('#resume').click(function() {
        $('#snap').attr('disabled', false);
        $('#save').attr('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        $('#resume').attr('disabled', true);
        videoRun();
    });

    // 비디오 출력을 적당한 크기로 출력하기 위해서 사용
    function drawVideo () {
        if (!context || !context.drawImage) return;
        context.drawImage(video, 0, 0, 372, 372);
    }

    // 촬영하기
    function videoRun () {
        $('#snap').attr('disabled', false);
        $('#save').attr('disabled', true);
        $('#resume').attr('disabled', true);
        clearInterval(videoInterval);
        videoInterval = setInterval(drawVideo, 1000/30);
    }
    videoRun();
});
